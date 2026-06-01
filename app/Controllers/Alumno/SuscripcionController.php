<?php

namespace App\Controllers\Alumno;

use App\Controllers\BaseController;
use App\Models\Gym\PlanSuscripcion;
use App\Models\Gym\SuscripcionAlumno;
use App\Services\Gym\SuscripcionService;

class SuscripcionController extends BaseController
{
    public function index()
    {
        $usuario = session()->get('usuario');
        $empresaId = (int) ($usuario['empresa_id'] ?? 0);
        $usuarioId = (int) ($usuario['id'] ?? 0);

        $planModel = new PlanSuscripcion();
        $planes = $planModel->getActivosByEmpresa($empresaId);

        $susModel = new SuscripcionAlumno();
        $suscripcion = $susModel->getByUsuario($usuarioId);

        return view('alumno/suscripcion', [
            'usuario' => $usuario,
            'planes' => $planes,
            'suscripcion' => $suscripcion,
        ]);
    }

    public function pagar()
    {
        $usuario = session()->get('usuario');
        $empresaId = (int) ($usuario['empresa_id'] ?? 0);
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $planId = (int) $this->request->getPost('plan_id');
        $payerEmail = (string) ($usuario['correo'] ?? '');

        try {
            $svc = new SuscripcionService();
            $pref = $svc->crearPreferenciaPago($empresaId, $usuarioId, $planId, $payerEmail);

            $initPoint = $pref['preference']['init_point'] ?? null;
            $sandboxInitPoint = $pref['preference']['sandbox_init_point'] ?? null;
            $url = $initPoint ?: $sandboxInitPoint;

            if (!$url) {
                return redirect()->back()->with('errors', 'No se pudo obtener link de pago');
            }
            return redirect()->to($url);
        } catch (\Throwable $e) {
            log_message('error', 'Suscripcion pagar error: ' . $e->getMessage());
            return redirect()->back()->with('errors', $e->getMessage());
        }
    }
}

