<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\WhatsAppService;

/**
 * Recordatorios WhatsApp para citas confirmadas de HOY y MAÑANA.
 *
 * - Cita mañana: primer envío hoy (ej. 20/05 08:00 para cita 21/05).
 * - Cita hoy: segundo envío si ya se envió el previo otro día; o un solo envío si solo existe hoy.
 *
 * Usa recordatorio_enviado + fecha_recordatorio (sin columnas extra).
 *
 * Cron: 0 8 * * * cd /ruta/proyecto && php spark enviarRecordatoriosWhatsApp
 */
class EnviarRecordatoriosWhatsApp extends BaseCommand
{
    protected $group       = 'WhatsApp';
    protected $name        = 'enviarRecordatoriosWhatsApp';
    protected $description = 'Recordatorios WhatsApp: citas de hoy y mañana (hasta 2 envíos por cita)';

    public function run(array $params)
    {
        CLI::write('Iniciando envío de recordatorios por WhatsApp...', 'green');
        CLI::write('  Fecha servidor: ' . date('Y-m-d H:i:s'), 'white');

        $db = \Config\Database::connect();
        $configuracionModel = new \App\Models\EmpresaConfiguracion();

        $fechaCitaSql = 'DATE(COALESCE(STR_TO_DATE(da.fecha, "%d-%m-%Y"), STR_TO_DATE(a.fecha, "%d-%m-%Y")))';

        // Hoy o mañana, y pendiente de algún envío según reglas abajo
        $citas = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, da.usuario_id, COALESCE(da.fecha, a.fecha) as fecha,
                     p.nombre, p.apellido, p.telefono, da.recordatorio_enviado, da.fecha_recordatorio')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->where('da.estado_cita', 'confirmada')
            ->where('da.paciente_id IS NOT NULL')
            ->where('p.telefono IS NOT NULL')
            ->where('p.telefono !=', '')
            ->groupStart()
                // Mañana: aún no se ha enviado nada
                ->where("{$fechaCitaSql} = DATE_ADD(CURDATE(), INTERVAL 1 DAY)", null, false)
                ->where('(da.recordatorio_enviado IS NULL OR da.recordatorio_enviado = 0)')
            ->groupEnd()
            ->orGroupStart()
                // Hoy: primer recordatorio (cita agendada para hoy sin aviso previo)
                ->where("{$fechaCitaSql} = CURDATE()", null, false)
                ->where('(da.recordatorio_enviado IS NULL OR da.recordatorio_enviado = 0)')
            ->groupEnd()
            ->orGroupStart()
                // Hoy: segundo recordatorio (ya se avisó ayer u otro día anterior)
                ->where("{$fechaCitaSql} = CURDATE()", null, false)
                ->where('da.recordatorio_enviado', 1)
                ->where('(da.fecha_recordatorio IS NULL OR DATE(da.fecha_recordatorio) < CURDATE())', null, false)
            ->groupEnd()
            ->get()
            ->getResult();

        if (empty($citas)) {
            CLI::write('No hay citas de hoy o mañana pendientes de recordatorio.', 'yellow');
            return;
        }

        CLI::write('Citas a procesar: ' . count($citas), 'cyan');
        foreach ($citas as $i => $cita) {
            $tipo = $this->tipoRecordatorio($cita);
            $linea = sprintf(
                '  %d. [%s] %s %s | %s (ID %d)',
                $i + 1,
                $tipo,
                trim($cita->fecha ?? ''),
                substr($cita->hora_inicio ?? '', 0, 5),
                trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? '')),
                $cita->id
            );
            CLI::write($linea, 'white');
        }
        CLI::write('', 'white');

        $enviados = 0;
        $errores = 0;
        $omitidos = 0;

        foreach ($citas as $cita) {
            if (empty($cita->telefono)) {
                CLI::write("  - Omitiendo ID {$cita->id}: sin teléfono", 'yellow');
                $omitidos++;
                continue;
            }

            $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($cita->usuario_id);
            if (! ($configuracion['enviar_recordatorios_whatsapp'] ?? 1)) {
                CLI::write("  - Omitiendo ID {$cita->id}: recordatorios deshabilitados", 'yellow');
                $omitidos++;
                continue;
            }

            $tipo = $this->tipoRecordatorio($cita);
            $horasAntes = $tipo === 'previo' ? 24 : 0;

            try {
                $empresaRow = $db->table('usuario')->select('empresa_id')->where('id', $cita->usuario_id)->get()->getRow();
                $empresaId = $empresaRow->empresa_id ?? null;
                $whatsappService = new WhatsAppService($empresaId);
                $resultado = $whatsappService->enviarRecordatorioCita($cita->id, $horasAntes);

                if (! empty($resultado) && ! empty($resultado[0]['success'])) {
                    $db->table('detalle_agenda')->where('id', $cita->id)->update([
                        'recordatorio_enviado' => 1,
                        'fecha_recordatorio'   => date('Y-m-d H:i:s'),
                    ]);
                    CLI::write("  ✓ [{$tipo}] ID {$cita->id} - {$cita->nombre} {$cita->apellido}", 'green');
                    $enviados++;
                } else {
                    CLI::write("  ✗ Error ID {$cita->id}", 'red');
                    $errores++;
                }
            } catch (\Exception $e) {
                CLI::write("  ✗ ID {$cita->id}: " . $e->getMessage(), 'red');
                $errores++;
            }
        }

        CLI::write("\nResumen: enviados {$enviados}, errores {$errores}, omitidos {$omitidos}", 'cyan');
    }

    /**
     * Etiqueta para el log según fecha de la cita y estado del recordatorio.
     */
    private function tipoRecordatorio(object $cita): string
    {
        $fechaCita = $this->parseFechaCita($cita->fecha ?? '');
        if (! $fechaCita) {
            return 'recordatorio';
        }

        $hoy = new \DateTime('today');
        $manana = (clone $hoy)->modify('+1 day');

        if ($fechaCita->format('Y-m-d') === $manana->format('Y-m-d')) {
            return 'previo';
        }

        if ($fechaCita->format('Y-m-d') === $hoy->format('Y-m-d')) {
            return ! empty($cita->recordatorio_enviado)
                && $this->fechaRecordatorioEsAnterior($cita->fecha_recordatorio ?? null)
                ? 'mismo día'
                : 'hoy';
        }

        return 'recordatorio';
    }

    private function parseFechaCita(?string $fecha): ?\DateTime
    {
        if (! $fecha) {
            return null;
        }
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', trim($fecha), $m)) {
            return \DateTime::createFromFormat('Y-m-d', $m[3] . '-' . $m[2] . '-' . $m[1]) ?: null;
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $fecha)) {
            return new \DateTime(substr($fecha, 0, 10));
        }
        return null;
    }

    private function fechaRecordatorioEsAnterior(?string $fechaRecordatorio): bool
    {
        if (! $fechaRecordatorio) {
            return true;
        }
        $fr = new \DateTime(substr($fechaRecordatorio, 0, 10));
        $hoy = new \DateTime('today');
        return $fr < $hoy;
    }
}
