<?php

namespace App\Services\Gym;

use Config\Services;

class GymRecordatorioService
{
    protected $db;
    protected EntrenamientoService $entrenamiento;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->entrenamiento = new EntrenamientoService();
    }

    /**
     * Alumnos con rutina hoy, sin sesión completada hoy, opt-in email.
     *
     * @return list<object>
     */
    public function candidatosRecordatorioHoy(): array
    {
        $dia = (int) date('N');
        $hoy = date('Y-m-d');

        $rows = $this->db->table('gym_programa_usuario gpu')
            ->select('u.id AS usuario_id, u.nombre, u.apellido, u.correo, u.empresa_id,
                r.nombre AS rutina_nombre, p.nombre AS programa_nombre, e.nombre AS empresa_nombre')
            ->join('usuario u', 'u.id = gpu.usuario_id')
            ->join('empresa e', 'e.id = u.empresa_id')
            ->join('gym_programa p', 'p.id = gpu.programa_id')
            ->join('gym_programa_rutina pr', 'pr.programa_id = p.id')
            ->join('gym_rutina r', 'r.id = pr.rutina_id')
            ->where('gpu.estado', 'activa')
            ->where('u.estado', 'A')
            ->where('pr.dia_semana', $dia);

        if ($this->db->fieldExists('recibir_recordatorios_gym', 'usuario')) {
            $rows = $rows->groupStart()
                ->where('u.recibir_recordatorios_gym', 'S')
                ->orWhere('u.recibir_recordatorios_gym IS NULL', null, false)
            ->groupEnd();
        }

        $rows = $rows->where('u.correo IS NOT NULL', null, false)
            ->where('u.correo !=', '')
            ->orderBy('pr.orden', 'ASC')
            ->get()
            ->getResult();

        $vistos = [];
        $out = [];
        foreach ($rows as $r) {
            $uid = (int) $r->usuario_id;
            if (isset($vistos[$uid])) {
                continue;
            }
            $vistos[$uid] = true;

            $entrenoHoy = (int) $this->db->table('gym_entrenamiento')
                ->where('usuario_id', $uid)
                ->where('estado', 'completado')
                ->where('DATE(COALESCE(finalizado_en, iniciado_en))', $hoy)
                ->countAllResults();

            if ($entrenoHoy > 0) {
                continue;
            }

            $out[] = $r;
        }

        return $out;
    }

    public function enviarRecordatorio(object $alumno): bool
    {
        $correo = trim((string) ($alumno->correo ?? ''));
        if ($correo === '') {
            return false;
        }

        $nombre = trim(($alumno->nombre ?? '') . ' ' . ($alumno->apellido ?? ''));
        $portalUrl = base_url('alumno/inicio');

        $emailConfig = config(\Config\Email::class);
        $email = Services::email();
        $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
        $email->setTo($correo);
        $email->setSubject('Hoy toca entrenar — ' . ($alumno->rutina_nombre ?? 'tu rutina'));
        $email->setMailType('html');
        $email->setMessage(view('emails/gym_recordatorio_entreno', [
            'nombre'         => $nombre,
            'rutina'         => $alumno->rutina_nombre ?? 'Entrenamiento',
            'programa'       => $alumno->programa_nombre ?? '',
            'empresa'        => $alumno->empresa_nombre ?? 'Tu gimnasio',
            'portal_url'     => $portalUrl,
        ]));

        return $email->send();
    }
}
