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
        // Verificar si WhatsApp está configurado
        if (empty(env('WHATSAPP_PROVIDER'))) {
            CLI::write('WhatsApp no está configurado. Omitiendo envío de recordatorios.', 'yellow');
            return;
        }

        CLI::write('Iniciando envío de recordatorios por WhatsApp...', 'green');

        $db = \Config\Database::connect();
        $whatsappService = new WhatsAppService();

        // Obtener citas confirmadas para las próximas 24 horas
        $fechaActual = date('d-m-Y');
        $fechaManana = date('d-m-Y', strtotime('+1 day'));
        
        // Convertir a formato para comparación en BD
        $fechaActualSQL = date('Y-m-d');
        $fechaMananaSQL = date('Y-m-d', strtotime('+1 day'));

        $citas = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, a.fecha, p.nombre, p.apellido, p.telefono, 
                     da.estado_cita, da.recordatorio_enviado')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->where('da.estado_cita', 'confirmada')
            ->where('da.paciente_id IS NOT NULL')
            ->where('p.telefono IS NOT NULL')
            ->where('(da.recordatorio_enviado IS NULL OR da.recordatorio_enviado = 0)')
            ->where("STR_TO_DATE(a.fecha, '%d-%m-%Y')", $fechaMananaSQL)
            ->get()
            ->getResult();

        if (empty($citas)) {
            CLI::write('No hay citas para recordar hoy.', 'yellow');
            return;
        }

        CLI::write('Encontradas ' . count($citas) . ' citas para recordar.', 'cyan');

        $enviados = 0;
        $errores = 0;

        foreach ($citas as $cita) {
            if (empty($cita->telefono)) {
                CLI::write("  - Saltando cita ID {$cita->id}: paciente sin teléfono", 'yellow');
                continue;
            }

            try {
                $resultado = $whatsappService->enviarRecordatorioCita($cita->id, 24);
                
                if (!empty($resultado) && isset($resultado[0]['success']) && $resultado[0]['success']) {
                    // Marcar como recordatorio enviado
                    $db->table('detalle_agenda')
                        ->where('id', $cita->id)
                        ->update(['recordatorio_enviado' => 1, 'fecha_recordatorio' => date('Y-m-d H:i:s')]);
                    
                    CLI::write("  ✓ Recordatorio enviado para cita ID {$cita->id} - {$cita->nombre} {$cita->apellido}", 'green');
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
        CLI::write("  - Total procesadas: " . count($citas), 'cyan');
    }
}
