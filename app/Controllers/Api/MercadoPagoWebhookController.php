<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\Pago;
use App\Models\EmpresaConfiguracion;
use App\Models\Gym\PagoSuscripcion as GymPagoSuscripcion;
use App\Services\MercadoPagoService;
use App\Services\Gym\SuscripcionService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador para recibir webhooks de Mercado Pago
 * Esta ruta debe ser pública (sin autenticación) para que Mercado Pago pueda enviar notificaciones
 */
class MercadoPagoWebhookController extends BaseController
{
    protected $mercadoPagoService;
    protected $pagoModel;
    protected $empresaConfigModel;
    protected $gymPagoModel;

    public function __construct()
    {
        $this->mercadoPagoService = new MercadoPagoService();
        $this->pagoModel = new Pago();
        $this->empresaConfigModel = new EmpresaConfiguracion();
        $this->gymPagoModel = new GymPagoSuscripcion();
    }

    /**
     * Endpoint para recibir notificaciones de Mercado Pago
     * 
     * Mercado Pago envía notificaciones POST con:
     * - type: tipo de notificación (payment, preference, etc.)
     * - data: { id: ID del recurso }
     * - Headers: x-signature (firma para validar autenticidad)
     * 
     * IMPORTANTE: La validación de firma es opcional pero recomendada para producción.
     * En sandbox, podemos omitirla para facilitar las pruebas.
     */
    public function webhook()
    {
        // Obtener datos del POST
        $data = $this->request->getJSON(true);
        
        // Si no viene JSON, intentar obtener del POST normal
        if (empty($data)) {
            $data = $this->request->getPost();
        }

        // Obtener headers importantes
        $xSignature = $this->request->getHeaderLine('x-signature');
        $xRequestId = $this->request->getHeaderLine('x-request-id');
        
        // Obtener query params (data.id viene en la URL)
        $queryParams = $this->request->getGet();
        $dataIdFromUrl = $queryParams['data.id'] ?? null;

        // Log para debugging
        log_message('info', 'Webhook recibido de Mercado Pago');
        log_message('info', 'Body: ' . json_encode($data));
        log_message('info', 'x-signature: ' . ($xSignature ?: 'NO PRESENTE'));
        log_message('info', 'x-request-id: ' . ($xRequestId ?: 'NO PRESENTE'));
        log_message('info', 'Query params: ' . json_encode($queryParams));
        
        // NOTA: La validación de firma se puede implementar más adelante
        // cuando se configure el webhook secret en el panel de Mercado Pago
        // Por ahora, procesamos la notificación sin validar la firma

        try {
            $type = $data['type'] ?? null;
            $dataId = $data['data']['id'] ?? null;

            // Validar que el ID no sea un ID de prueba obvio
            if ($type === 'payment' && $dataId) {
                // Si es un ID de prueba obvio (como "123456"), solo loguear y responder OK
                if ($dataId === '123456' || empty($dataId) || !is_numeric($dataId)) {
                    log_message('info', 'Webhook recibido con ID de prueba: ' . $dataId . ' - Ignorando (esto es normal en pruebas)');
                    return $this->response->setJSON([
                        'status' => 'ok',
                        'message' => 'Webhook de prueba recibido'
                    ])->setStatusCode(200);
                }
                
                log_message('info', 'Procesando webhook de pago - ID: ' . $dataId);
                
                // Buscar pagos pendientes recientes (últimas 24 horas)
                // y verificar cuál corresponde a este payment_id
                $fechaDesde = date('Y-m-d H:i:s', strtotime('-24 hours'));
                
                $pagosPendientes = $this->pagoModel->where('estado_pago', 'pendiente')
                    ->where('mp_preference_id IS NOT NULL')
                    ->where('fcreacion >=', $fechaDesde)
                    ->orderBy('fcreacion', 'DESC')
                    ->findAll();

                // Pagos pendientes gym (suscripción alumno)
                $gymPagosPendientes = $this->gymPagoModel->where('estado', 'pendiente')
                    ->where('mp_preference_id IS NOT NULL')
                    ->where('fcreacion >=', $fechaDesde)
                    ->orderBy('fcreacion', 'DESC')
                    ->findAll();
                
                log_message('info', 'Pagos pendientes encontrados: ' . count($pagosPendientes));
                log_message('info', 'Pagos gym pendientes encontrados: ' . count($gymPagosPendientes));
                
                $pagoEncontrado = null;
                $gymPagoEncontrado = null;
                
                // Primero intentar obtener el pago directamente de Mercado Pago
                // para evitar iterar sobre todas las empresas si el pago no existe
                $payment = null;
                $empresaIdParaPago = null;
                
                foreach ($pagosPendientes as $pagoTemp) {
                    // Obtener credenciales de la empresa del pago
                    $credenciales = $this->empresaConfigModel->obtenerCredencialesMercadoPago($pagoTemp->empresa_id);
                    
                    if ($credenciales && $credenciales['habilitado']) {
                        try {
                            $mercadoPagoService = new MercadoPagoService(
                                $credenciales['access_token'],
                                $credenciales['public_key'],
                                $credenciales['mode']
                            );
                            
                            // Intentar obtener el pago solo una vez por empresa
                            if (!$payment) {
                                $payment = $mercadoPagoService->obtenerPago($dataId);
                                if ($payment) {
                                    $empresaIdParaPago = $pagoTemp->empresa_id;
                                    log_message('info', 'Pago encontrado en Mercado Pago para empresa: ' . $empresaIdParaPago);
                                }
                            }
                            
                            // Si encontramos el pago y es de esta empresa, verificar si corresponde
                            if ($payment && $empresaIdParaPago == $pagoTemp->empresa_id) {
                                $externalRef = $payment->external_reference ?? null;
                                
                                if (($externalRef && $externalRef == $pagoTemp->id) || 
                                    ($payment->preference_id && $payment->preference_id == $pagoTemp->mp_preference_id)) {
                                    $pagoEncontrado = $pagoTemp;
                                    
                                    // Actualizar pago con información de Mercado Pago
                                    $this->pagoModel->actualizarDesdeMercadoPago(
                                        $pagoEncontrado->id,
                                        $payment->id,
                                        $payment->status
                                    );
                                    
                                    log_message('info', 'Pago actualizado: ' . $pagoEncontrado->id . ' - Estado: ' . $payment->status);
                                    break;
                                }
                            }
                        } catch (\Exception $e) {
                            log_message('debug', 'Error al procesar pago para empresa ' . $pagoTemp->empresa_id . ': ' . $e->getMessage());
                            continue;
                        }
                    }
                }

                // Si no se encontró un pago legacy, intentar asociar con pagos gym
                if (!$pagoEncontrado) {
                    foreach ($gymPagosPendientes as $gymPagoTemp) {
                        $credenciales = $this->empresaConfigModel->obtenerCredencialesMercadoPago($gymPagoTemp->empresa_id);

                        if ($credenciales && $credenciales['habilitado']) {
                            try {
                                $mercadoPagoService = new MercadoPagoService(
                                    $credenciales['access_token'],
                                    $credenciales['public_key'],
                                    $credenciales['mode']
                                );

                                if (!$payment) {
                                    $payment = $mercadoPagoService->obtenerPago($dataId);
                                    if ($payment) {
                                        $empresaIdParaPago = $gymPagoTemp->empresa_id;
                                        log_message('info', 'Pago (gym) encontrado en Mercado Pago para empresa: ' . $empresaIdParaPago);
                                    }
                                }

                                if ($payment && $empresaIdParaPago == $gymPagoTemp->empresa_id) {
                                    $externalRef = $payment->external_reference ?? null;

                                    if (($externalRef && $externalRef == $gymPagoTemp->external_reference) ||
                                        ($payment->preference_id && $payment->preference_id == $gymPagoTemp->mp_preference_id)) {
                                        $gymPagoEncontrado = $gymPagoTemp;

                                        // Actualizar estado local según MP
                                        $mpStatus = (string) ($payment->status ?? '');
                                        $estado = match ($mpStatus) {
                                            'approved' => 'aprobado',
                                            'rejected' => 'rechazado',
                                            'cancelled' => 'cancelado',
                                            'refunded', 'charged_back' => 'devuelto',
                                            default => 'pendiente',
                                        };

                                        $this->gymPagoModel->update($gymPagoEncontrado->id, [
                                            'estado' => $estado,
                                            'mp_payment_id' => (string) ($payment->id ?? null),
                                            'detalle' => json_encode(['mp_status' => $mpStatus], JSON_UNESCAPED_UNICODE),
                                        ]);

                                        if ($estado === 'aprobado') {
                                            $svc = new SuscripcionService();
                                            $svc->procesarPagoAprobado((int) $gymPagoEncontrado->id, (string) ($payment->id ?? ''));
                                        }

                                        log_message('info', 'Pago gym actualizado: ' . $gymPagoEncontrado->id . ' - Estado: ' . $mpStatus);
                                        break;
                                    }
                                }
                            } catch (\Exception $e) {
                                log_message('debug', 'Error al procesar pago gym para empresa ' . $gymPagoTemp->empresa_id . ': ' . $e->getMessage());
                                continue;
                            }
                        }
                    }
                }
                
                if (!$pagoEncontrado && !$gymPagoEncontrado) {
                    if ($payment) {
                        log_message('warning', 'Pago encontrado en MP pero no se pudo asociar con ningún pago local. Payment ID: ' . $dataId);
                    } else {
                        log_message('info', 'Pago no encontrado en Mercado Pago (ID: ' . $dataId . ') - Puede ser un ID de prueba o un pago que no existe');
                    }
                }
            } else {
                log_message('info', 'Webhook recibido con tipo: ' . ($type ?? 'null') . ' - No es un pago, ignorando');
            }

            // Responder 200 OK a Mercado Pago (siempre responder 200 para que no reenvíe)
            return $this->response->setJSON([
                'status' => 'ok',
                'message' => 'Webhook procesado'
            ])->setStatusCode(200);

        } catch (\Exception $e) {
            log_message('error', 'Error al procesar webhook de Mercado Pago: ' . $e->getMessage());
            
            // Aún así responder 200 para que Mercado Pago no reenvíe
            // (pero logueamos el error para revisar)
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(200);
        }
    }

    /**
     * Endpoint para verificar estado de un pago manualmente
     * (Útil para debugging o para verificar pagos que no se actualizaron por webhook)
     */
    public function verificarPago($pagoId)
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        try {
            $pago = $this->pagoModel->find($pagoId);

            if (!$pago) {
                return $this->response->setJSON(['error' => 'Pago no encontrado'])->setStatusCode(404);
            }

            if (!$pago->mp_preference_id) {
                return $this->response->setJSON(['error' => 'Este pago no tiene preferencia de Mercado Pago']);
            }

            // Obtener credenciales de la empresa
            $credenciales = $this->empresaConfigModel->obtenerCredencialesMercadoPago($pago->empresa_id);
            
            if (!$credenciales || !$credenciales['habilitado']) {
                return $this->response->setJSON(['error' => 'Mercado Pago no está configurado para esta empresa']);
            }

            $mercadoPagoService = new MercadoPagoService(
                $credenciales['access_token'],
                $credenciales['public_key'],
                $credenciales['mode']
            );

            // Obtener preferencia de Mercado Pago
            $preferencia = $mercadoPagoService->obtenerPreferencia($pago->mp_preference_id);

            if (!$preferencia) {
                return $this->response->setJSON(['error' => 'No se pudo obtener la preferencia de Mercado Pago']);
            }

            // Si hay un payment_id, obtener el pago
            if ($pago->mp_payment_id) {
                $payment = $mercadoPagoService->obtenerPago($pago->mp_payment_id);
                
                if ($payment) {
                    // Actualizar estado
                    $this->pagoModel->actualizarDesdeMercadoPago(
                        $pago->id,
                        $payment->id,
                        $payment->status
                    );

                    return $this->response->setJSON([
                        'success' => true,
                        'payment' => [
                            'id' => $payment->id,
                            'status' => $payment->status,
                            'status_detail' => $payment->status_detail ?? null
                        ],
                        'csrf_hash' => csrf_hash()
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'preference' => [
                    'id' => $preferencia->id,
                    'status' => $preferencia->status ?? null
                ],
                'csrf_hash' => csrf_hash()
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al verificar pago: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
