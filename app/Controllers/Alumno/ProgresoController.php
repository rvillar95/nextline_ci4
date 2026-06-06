<?php

namespace App\Controllers\Alumno;

use App\Controllers\BaseController;
use App\Services\Gym\ProgresoService;

class ProgresoController extends BaseController
{
    protected ProgresoService $progreso;

    public function __construct()
    {
        $this->progreso = new ProgresoService();
    }

    public function index()
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);

        $ejercicios = $this->progreso->ejerciciosConHistorial($usuarioId);
        $ejercicioId = (int) ($this->request->getGet('ejercicio') ?? ($ejercicios[0]->ejercicio_id ?? 0));

        $historialEj = [];
        $pr = null;
        if ($ejercicioId > 0) {
            $historialEj = $this->progreso->historialPesoEjercicio($usuarioId, $ejercicioId);
            $pr = $this->progreso->recordPersonal($usuarioId, $ejercicioId);
        }

        return view('alumno/progreso', [
            'usuario'        => $usuario,
            'resumen'        => $this->progreso->resumen30Dias($usuarioId),
            'racha'          => $this->progreso->rachaDias($usuarioId),
            'volumenSemanal' => $this->progreso->volumenSemanal($usuarioId, 8),
            'ejercicios'     => $ejercicios,
            'ejercicioId'    => $ejercicioId,
            'historialEj'    => $historialEj,
            'pr'             => $pr,
            'ultimosPr'      => $this->progreso->ultimosPr($usuarioId, 10),
            'navActive'      => 'progreso',
            'pageTitle'      => 'Mi progreso',
        ]);
    }

    public function ejercicioJson($ejercicioId)
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $ejercicioId = (int) $ejercicioId;

        if ($ejercicioId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['ok' => false]);
        }

        return $this->response->setJSON([
            'ok'        => true,
            'historial' => $this->progreso->historialPesoEjercicio($usuarioId, $ejercicioId),
            'pr'        => $this->progreso->recordPersonal($usuarioId, $ejercicioId),
        ]);
    }
}
