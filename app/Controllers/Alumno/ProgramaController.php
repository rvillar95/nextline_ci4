<?php

namespace App\Controllers\Alumno;

use App\Controllers\BaseController;

class ProgramaController extends BaseController
{
    public function ver($programaId)
    {
        $usuario = session()->get('usuario');
        $empresaId = (int) ($usuario['empresa_id'] ?? 0);
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $programaId = (int) $programaId;

        $db = \Config\Database::connect();

        // Validar que el programa esté asignado al alumno
        $asign = $db->table('gym_programa_usuario gpu')
            ->select('gpu.id, gpu.estado, p.id as programa_id, p.nombre as programa_nombre')
            ->join('gym_programa p', 'p.id = gpu.programa_id')
            ->where('gpu.usuario_id', $usuarioId)
            ->where('p.empresa_id', $empresaId)
            ->where('p.id', $programaId)
            ->get()
            ->getRow();

        if (!$asign) {
            return redirect()->to(base_url('alumno/inicio'))->with('errors', 'Programa no asignado');
        }

        $rutinas = $db->table('gym_programa_rutina pr')
            ->select('pr.orden, pr.dia_semana, r.id as rutina_id, r.nombre as rutina_nombre, r.descripcion as rutina_descripcion')
            ->join('gym_rutina r', 'r.id = pr.rutina_id')
            ->where('pr.programa_id', $programaId)
            ->orderBy('pr.orden', 'ASC')
            ->get()
            ->getResult('object');

        return view('alumno/programa_ver', [
            'usuario' => $usuario,
            'asign' => $asign,
            'rutinas' => $rutinas,
        ]);
    }
}

