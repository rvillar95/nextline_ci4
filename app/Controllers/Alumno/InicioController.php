<?php

namespace App\Controllers\Alumno;

use App\Controllers\BaseController;

class InicioController extends BaseController
{
    public function index()
    {
        $usuario = session()->get('usuario');
        $empresaId = (int) ($usuario['empresa_id'] ?? 0);
        $usuarioId = (int) ($usuario['id'] ?? 0);

        $db = \Config\Database::connect();

        $asignaciones = $db->table('gym_programa_usuario gpu')
            ->select([
                'gpu.id AS asignacion_id',
                'gpu.estado',
                'gpu.fecha_inicio',
                'gpu.fecha_fin',
                'p.id AS programa_id',
                'p.nombre AS programa_nombre',
            ])
            ->join('gym_programa p', 'p.id = gpu.programa_id')
            ->where('gpu.usuario_id', $usuarioId)
            ->where('p.empresa_id', $empresaId)
            ->orderBy('gpu.id', 'DESC')
            ->get()
            ->getResult('object');

        return view('alumno/inicio', [
            'usuario' => $usuario,
            'asignaciones' => $asignaciones,
        ]);
    }
}

