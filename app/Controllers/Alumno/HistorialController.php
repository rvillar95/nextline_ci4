<?php

namespace App\Controllers\Alumno;

use App\Controllers\BaseController;
use App\Services\Gym\EntrenamientoService;
use App\Services\Gym\ProgresoService;

class HistorialController extends BaseController
{
    protected EntrenamientoService $entrenamiento;

    public function __construct()
    {
        $this->entrenamiento = new EntrenamientoService();
    }

    public function index()
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);

        $historial = $this->entrenamiento->historialAlumno($usuarioId, 30);

        return view('alumno/historial_lista', [
            'usuario'   => $usuario,
            'historial' => $historial,
            'navActive' => 'historial',
            'pageTitle' => 'Historial',
        ]);
    }

    public function detalle($id)
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $id = (int) $id;

        $data = $this->entrenamiento->obtenerSesionCompleta($id, $usuarioId);
        if (!$data) {
            return redirect()->to(base_url('alumno/historial'))->with('errors', 'Sesión no encontrada');
        }

        $progreso = new ProgresoService();
        $prSeries = $progreso->prSerieIdsEnEntrenamiento($usuarioId, $id);
        $mejorVolumenSeries = $progreso->seriesMejorVolumenEnEntrenamiento($id);

        return view('alumno/historial_detalle', [
            'usuario'            => $usuario,
            'sesion'             => $data,
            'prSeries'           => $prSeries,
            'mejorVolumenSeries' => $mejorVolumenSeries,
            'navActive' => 'historial',
            'pageTitle' => 'Detalle sesión',
        ]);
    }
}
