<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\WhatsAppService;

/**
 * Comando para enviar recordatorios de citas por WhatsApp
 * Ejecutar: php spark enviarRecordatoriosWhatsApp
 * O configurar en cron: 0 8 * * * cd /ruta/proyecto && php spark enviarRecordatoriosWhatsApp
 */
class EnviarRecordatoriosWhatsApp extends BaseCommand
{
    protected $group       = 'WhatsApp';
    protected $name        = 'enviarRecordatoriosWhatsApp';
    protected $description = 'Envía recordatorios de citas por WhatsApp (24 horas antes)';

    public function run(array $params)
    {
        CLI::write('Iniciando envío de recordatorios por WhatsApp...', 'green');

        $db = \Config\Database::connect();
        $configuracionModel = new \App\Models\EmpresaConfiguracion();

        // Obtener citas confirmadas para las próximas horas (según configuración de cada usuario)
        $citas = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, da.usuario_id, a.fecha, p.nombre, p.apellido, p.telefono, 
                     da.estado_cita, da.recordatorio_enviado')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->where('da.estado_cita', 'confirmada')
            ->where('da.paciente_id IS NOT NULL')
            ->where('p.telefono IS NOT NULL')
            ->where('(da.recordatorio_enviado IS NULL OR da.recordatorio_enviado = 0)')
            ->get()
            ->getResult();

        if (empty($citas)) {
            CLI::write('No hay citas para recordar hoy.', 'yellow');
            return;
        }

        CLI::write('Encontradas ' . count($citas) . ' citas candidatas para recordar.', 'cyan');

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
            
            // Verificar si los recordatorios están habilitados para este nutricionista
            if (!($configuracion['enviar_recordatorios_whatsapp'] ?? 1)) {
                CLI::write("  - Omitiendo cita ID {$cita->id}: recordatorios deshabilitados para usuario ID {$cita->usuario_id}", 'yellow');
                $omitidos++;
                continue;
            }

            // Obtener horas antes del recordatorio (por defecto 24)
            $horasAntes = $configuracion['horas_antes_recordatorio'] ?? 24;
            
            // Calcular fecha objetivo (fecha de la cita menos horas antes)
            $fechaCita = \DateTime::createFromFormat('d-m-Y', $cita->fecha);
            if (!$fechaCita) {
                CLI::write("  - Saltando cita ID {$cita->id}: fecha inválida", 'yellow');
                $omitidos++;
                continue;
            }
            
            $fechaObjetivo = clone $fechaCita;
            $fechaObjetivo->modify("-{$horasAntes} hours");
            $fechaObjetivo->setTime(0, 0, 0);
            
            $fechaActual = new \DateTime();
            $fechaActual->setTime(0, 0, 0);
            
            // Solo enviar si estamos en la fecha objetivo
            if ($fechaActual->format('Y-m-d') !== $fechaObjetivo->format('Y-m-d')) {
                continue; // No es el momento de enviar este recordatorio
            }

            try {
                $empresaRow = $db->table('usuario')->select('empresa_id')->where('id', $cita->usuario_id)->get()->getRow();
                $empresaId = $empresaRow ? ($empresaRow->empresa_id ?? null) : null;
                $whatsappService = new WhatsAppService($empresaId);
                $resultado = $whatsappService->enviarRecordatorioCita($cita->id, $horasAntes);
                
                if (!empty($resultado) && isset($resultado[0]['success']) && $resultado[0]['success']) {
                    // Marcar como recordatorio enviado
                    $db->table('detalle_agenda')
                        ->where('id', $cita->id)
                        ->update(['recordatorio_enviado' => 1, 'fecha_recordatorio' => date('Y-m-d H:i:s')]);
                    
                    CLI::write("  ✓ Recordatorio enviado para cita ID {$cita->id} - {$cita->nombre} {$cita->apellido} ({$horasAntes}h antes)", 'green');
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
