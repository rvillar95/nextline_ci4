<?php

namespace App\Services\Gym;

use App\Models\EmpresaConfiguracion;
use App\Models\Gym\PagoSuscripcion;
use App\Models\Gym\PlanSuscripcion;
use App\Models\Gym\SuscripcionAlumno;
use App\Services\MercadoPagoService;
use Exception;

class SuscripcionService
{
    public function crearPreferenciaPago(int $empresaId, int $usuarioId, int $planId, string $payerEmail): array
    {
        $planModel = new PlanSuscripcion();
        $plan = $planModel->where('empresa_id', $empresaId)->where('activo', 1)->find($planId);
        if (!$plan) {
            throw new Exception('Plan inválido');
        }

        $pagoModel = new PagoSuscripcion();
        $pagoId = $pagoModel->insert([
            'empresa_id' => $empresaId,
            'usuario_id' => $usuarioId,
            'plan_id' => $planId,
            'monto' => $plan->monto_mensual,
            'moneda' => $plan->moneda ?? 'CLP',
            'estado' => 'pendiente',
        ]);

        if (!$pagoId) {
            throw new Exception('No se pudo crear pago');
        }

        $externalReference = 'gym_sub_' . $pagoId;

        $empresaConfig = new EmpresaConfiguracion();
        $cred = $empresaConfig->obtenerCredencialesMercadoPago($empresaId);
        if (!$cred || empty($cred['access_token'])) {
            throw new Exception('Mercado Pago no configurado para esta empresa');
        }
        if (isset($cred['habilitado']) && !$cred['habilitado']) {
            throw new Exception('Mercado Pago no está habilitado para esta empresa');
        }

        $mp = new MercadoPagoService($cred['access_token'], $cred['public_key'] ?? null, $cred['mode'] ?? 'sandbox');
        $pref = $mp->crearPreferencia([
            'title' => 'Suscripción: ' . $plan->nombre,
            'description' => 'Pago mensual suscripción gimnasio',
            'quantity' => 1,
            'unit_price' => (float) $plan->monto_mensual,
            'payer_email' => $payerEmail,
            'external_reference' => $externalReference,
            'currency' => $plan->moneda ?? 'CLP',
        ]);

        $pagoModel->update($pagoId, [
            'mp_preference_id' => $pref['preference_id'] ?? null,
            'external_reference' => $externalReference,
            'detalle' => json_encode(['preference' => $pref], JSON_UNESCAPED_UNICODE),
        ]);

        return [
            'pago_id' => $pagoId,
            'external_reference' => $externalReference,
            'preference' => $pref,
        ];
    }

    /**
     * Procesa un pago aprobado desde webhook: marca pago aprobado y activa/renueva suscripción.
     */
    public function procesarPagoAprobado(int $pagoId, string $mpPaymentId): void
    {
        $pagoModel = new PagoSuscripcion();
        $pago = $pagoModel->find($pagoId);
        if (!$pago) {
            throw new Exception('Pago gym no encontrado');
        }

        // idempotente
        if ($pago->estado === 'aprobado') {
            return;
        }

        $pagoModel->update($pagoId, [
            'estado' => 'aprobado',
            'mp_payment_id' => $mpPaymentId,
        ]);

        $susModel = new SuscripcionAlumno();
        $actual = $susModel->getByUsuario((int) $pago->usuario_id);

        $hoy = date('Y-m-d');
        $nuevoFin = date('Y-m-d', strtotime($hoy . ' +30 days'));
        $proxPago = date('Y-m-d', strtotime($hoy . ' +1 month'));

        if ($actual) {
            $susModel->update($actual->id, [
                'empresa_id' => $pago->empresa_id,
                'plan_id' => $pago->plan_id,
                'estado' => 'activa',
                'fecha_inicio' => $actual->fecha_inicio ?: $hoy,
                'fecha_fin' => $nuevoFin,
                'fecha_proximo_pago' => $proxPago,
                'mp_ultimo_payment_id' => $mpPaymentId,
            ]);
        } else {
            $susModel->insert([
                'empresa_id' => $pago->empresa_id,
                'usuario_id' => $pago->usuario_id,
                'plan_id' => $pago->plan_id,
                'estado' => 'activa',
                'fecha_inicio' => $hoy,
                'fecha_fin' => $nuevoFin,
                'fecha_proximo_pago' => $proxPago,
                'renovacion_automatica' => 0,
                'mp_ultimo_payment_id' => $mpPaymentId,
            ]);
        }
    }
}

