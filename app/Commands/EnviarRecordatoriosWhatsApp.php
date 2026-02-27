<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\WhatsAppService;

/**
 * Comando para enviar recordatorios de citas por WhatsApp.
 * Envía solo para citas del día próximo (mañana). Ejecutar 1 vez al día por cron (ej. 08:00).
 * Ejecutar: php spark enviarRecordatoriosWhatsApp
 * Cron: 0 8 * * * cd /ruta/proyecto && php spark enviarRecordatoriosWhatsApp
 */
class EnviarRecordatoriosWhatsApp extends BaseCommand
{
    protected $group       = 'WhatsApp';
    protected $name        = 'enviarRecordatoriosWhatsApp';
    protected $description = 'Envía recordatorios por WhatsApp para las citas del día próximo';

    public function run(array $params)
    {
        CLI::write('Iniciando envío de recordatorios por WhatsApp...', 'green');

        $db = \Config\Database::connect();
        $configuracionModel = new \App\Models\EmpresaConfiguracion();

        // Solo citas confirmadas del día próximo (mañana), sin recordatorio enviado.
        // a.fecha en d-m-Y; comparamos con DATE_ADD(CURDATE(), INTERVAL 1 DAY).
        $citas = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, da.usuario_id, a.fecha, p.nombre, p.apellido, p.telefono, 
                     da.estado_cita, da.recordatorio_enviado')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->where('da.estado_cita', 'confirmada')
            ->where('da.paciente_id IS NOT NULL')
            ->where('p.telefono IS NOT NULL')
            ->where('(da.recordatorio_enviado IS NULL OR da.recordatorio_enviado = 0)')
            ->where('DATE(STR_TO_DATE(a.fecha, "%d-%m-%Y")) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)', null, false)
            ->get()
            ->getResult();

        if (empty($citas)) {
            CLI::write('No hay citas para mañana; nada que recordar.', 'yellow');
            return;
        }

        CLI::write('Encontradas ' . count($citas) . ' citas de mañana para recordar.', 'cyan');
        CLI::write('', 'white');
        foreach ($citas as $i => $cita) {
            $fechaHora = trim(($cita->fecha ?? '') . ' ' . ($cita->hora_inicio ?? '00:00:00'));
            $paciente = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
            CLI::write(sprintf('  %d. %s | %s (ID %d)', $i + 1, $fechaHora, $paciente ?: '-', $cita->id), 'white');
        }
        CLI::write('', 'white');

        $enviados = 0;
        $errores = 0;
        $omitidos = 0;

        foreach ($citas as $cita) {
            if (empty($cita->telefono)) {
                CLI::write("  - Saltando cita ID {$cita->id}: paciente sin teléfono", 'yellow');
                $omitidos++;
                continue;
            }

            // Obtener configuración de la empresa del nutricionista
            $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($cita->usuario_id);
            
            // Verificar si los recordatorios están habilitados para este usuario/empresa
            if (!($configuracion['enviar_recordatorios_whatsapp'] ?? 1)) {
                CLI::write("  - Omitiendo cita ID {$cita->id}: recordatorios deshabilitados para usuario ID {$cita->usuario_id}", 'yellow');
                $omitidos++;
                continue;
            }

            try {
                $empresaRow = $db->table('usuario')->select('empresa_id')->where('id', $cita->usuario_id)->get()->getRow();
                $empresaId = $empresaRow ? ($empresaRow->empresa_id ?? null) : null;
                $whatsappService = new WhatsAppService($empresaId);
                $resultado = $whatsappService->enviarRecordatorioCita($cita->id, 24);

                if (!empty($resultado) && isset($resultado[0]['success']) && $resultado[0]['success']) {
                    $db->table('detalle_agenda')
                        ->where('id', $cita->id)
                        ->update(['recordatorio_enviado' => 1, 'fecha_recordatorio' => date('Y-m-d H:i:s')]);

                    CLI::write("  ✓ Recordatorio enviado para cita ID {$cita->id} - {$cita->nombre} {$cita->apellido} (mañana)", 'green');
                    $enviados++;
                } else {
                    CLI::write("  ✗ Error al enviar recordatorio para cita ID {$cita->id}", 'red');
                    $errores++;
                }
            } catch (\Exception $e) {
                CLI::write("  ✗ Excepción al enviar recordatorio para cita ID {$cita->id}: " . $e->getMessage(), 'red');
                $errores++;
            }
        }

        CLI::write("\nResumen:", 'cyan');
        CLI::write("  - Enviados: {$enviados}", 'green');
        CLI::write("  - Errores: {$errores}", $errores > 0 ? 'red' : 'green');
        CLI::write("  - Omitidos: {$omitidos}", $omitidos > 0 ? 'yellow' : 'green');
        CLI::write("  - Total procesadas: " . count($citas), 'cyan');
    }
}
