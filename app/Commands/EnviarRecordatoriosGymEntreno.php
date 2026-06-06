<?php

namespace App\Commands;

use App\Services\Gym\GymRecordatorioService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Recordatorio email: alumnos con rutina hoy que aún no entrenaron.
 *
 * Cron: 0 8 * * * cd /ruta/proyecto && php spark gym:recordatorio-entreno
 */
class EnviarRecordatoriosGymEntreno extends BaseCommand
{
    protected $group       = 'Gym';
    protected $name        = 'gym:recordatorio-entreno';
    protected $description = 'Email diario a alumnos con rutina hoy sin sesión completada';

    public function run(array $params)
    {
        CLI::write('Recordatorios gym (email)...', 'green');
        CLI::write('  ' . date('Y-m-d H:i:s'), 'white');

        $svc = new GymRecordatorioService();
        $candidatos = $svc->candidatosRecordatorioHoy();

        if ($candidatos === []) {
            CLI::write('Sin candidatos hoy.', 'yellow');
            return;
        }

        CLI::write('Candidatos: ' . count($candidatos), 'cyan');
        $ok = 0;
        $fail = 0;

        foreach ($candidatos as $a) {
            $label = trim(($a->nombre ?? '') . ' ' . ($a->apellido ?? '')) . ' <' . ($a->correo ?? '') . '>';
            if ($svc->enviarRecordatorio($a)) {
                $ok++;
                CLI::write('  OK ' . $label, 'green');
            } else {
                $fail++;
                CLI::write('  FAIL ' . $label, 'red');
            }
        }

        CLI::write("Enviados: {$ok}, fallidos: {$fail}", $fail > 0 ? 'yellow' : 'green');
    }
}
