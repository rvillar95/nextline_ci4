<?php

namespace App\Controllers\Alumno;

use App\Controllers\BaseController;
use App\Services\Gym\EntrenamientoService;

class RutinaController extends BaseController
{
    protected EntrenamientoService $entrenamiento;

    public function __construct()
    {
        $this->entrenamiento = new EntrenamientoService();
    }

    public function ver($rutinaId)
    {
        $usuario = session()->get('usuario');
        $empresaId = (int) ($usuario['empresa_id'] ?? 0);
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $rutinaId = (int) $rutinaId;

        $asign = $this->entrenamiento->rutinaAsignada($usuarioId, $empresaId, $rutinaId);
        if (!$asign) {
            return redirect()->to(base_url('alumno/inicio'))->with('errors', 'Rutina no disponible en tus programas.');
        }

        $db = \Config\Database::connect();
        $items = $db->table('gym_rutina_ejercicio re')
            ->select('re.orden, re.series, re.repeticiones, re.descanso_seg, re.notas, e.nombre as ejercicio_nombre, e.id as ejercicio_id')
            ->join('gym_ejercicio e', 'e.id = re.ejercicio_id')
            ->where('re.rutina_id', $rutinaId)
            ->orderBy('re.orden', 'ASC')
            ->get()
            ->getResult('object');

        $sesion = $this->entrenamiento->sesionEnCurso($usuarioId);

        return view('alumno/rutina_ver', [
            'usuario'   => $usuario,
            'asign'     => $asign,
            'items'     => $items,
            'sesion'    => $sesion,
            'navActive' => 'programas',
            'pageTitle' => $asign->rutina_nombre ?? 'Rutina',
        ]);
    }

    public function iniciar($rutinaId)
    {
        $usuario = session()->get('usuario');
        $empresaId = (int) ($usuario['empresa_id'] ?? 0);
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $rutinaId = (int) $rutinaId;

        $abandonar = $this->request->getPost('abandonar_anterior') === '1'
            || $this->request->getGet('abandonar') === '1';

        $res = $this->entrenamiento->iniciar($usuarioId, $empresaId, $rutinaId, $abandonar);

        if (!$res['ok']) {
            if (($res['error'] ?? '') === 'conflicto') {
                return redirect()->to(base_url('alumno/rutina/' . $rutinaId))
                    ->with('errors', 'Ya tienes un entrenamiento en curso.')
                    ->with('conflicto_id', $res['conflicto_id'] ?? null);
            }
            return redirect()->to(base_url('alumno/rutina/' . $rutinaId))->with('errors', $res['error'] ?? 'No se pudo iniciar');
        }

        return redirect()->to(base_url('alumno/entrenamiento/' . $res['entrenamiento_id']));
    }
}
