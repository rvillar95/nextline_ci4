<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Gym\PlanSuscripcion;

class GymHomeController extends BaseController
{
    public function index()
    {
        $empresaId = (int) ($this->request->getGet('empresa_id') ?? 0);
        $db = \Config\Database::connect();

        $empresa = null;
        if ($empresaId > 0) {
            $empresa = $db->table('empresa')->where('id', $empresaId)->where('estado', 'A')->get()->getRow();
        }

        // Planes
        $planes = [];
        if ($empresaId > 0) {
            $planModel = new PlanSuscripcion();
            $planes = $planModel->getActivosByEmpresa($empresaId);
        }

        // WOD simple: última rutina creada del box
        $wod = null;
        if ($empresaId > 0) {
            $wod = $db->table('gym_rutina')
                ->select('id, nombre, descripcion, fcreacion')
                ->where('empresa_id', $empresaId)
                ->where('activo', 1)
                ->orderBy('id', 'DESC')
                ->get()
                ->getRow();
        }

        return view('gym_web/home', [
            'empresa' => $empresa,
            'empresa_id' => $empresaId,
            'planes' => $planes,
            'wod' => $wod,
        ]);
    }
}

