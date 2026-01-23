<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Pago;
use App\Models\DetalleAgenda;
use App\Models\Paciente;
use App\Models\Empresa;
use App\Models\ModuloDetalle;
use App\Models\BotonPagoPlantilla;
use App\Services\MercadoPagoService;
use App\Models\EmpresaConfiguracion;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class BotonPagoController extends BaseController
{
    protected $empresaConfigModel;

    public function __construct()
    {
        $this->empresaConfigModel = new EmpresaConfiguracion();
    }

    /**
     * Obtener servicio de Mercado Pago con credenciales de la empresa
     */
    protected function getMercadoPagoService($empresaId)
    {
        $credenciales = $this->empresaConfigModel->obtenerCredencialesMercadoPago($empresaId);
        
        if (!$credenciales || !$credenciales['habilitado']) {
            throw new \Exception('Mercado Pago no está configurado o habilitado para esta empresa');
        }

        $webhookBaseUrl = rtrim(base_url(), '/');
        
        return new MercadoPagoService(
            $credenciales['access_token'],
            $credenciales['public_key'],
            $credenciales['mode'],
            $webhookBaseUrl
        );
    }

    /**
     * Vista principal: Lista de botones de pago creados
     */
    public function lista()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Obtener empresa del usuario
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;

        // Cargar las plantillas de botones de pago de esta empresa
        $plantillaModel = new BotonPagoPlantilla();
        if ($empresaId) {
            $plantillas = $plantillaModel->where('empresa_id', $empresaId)
                ->orderBy('orden', 'ASC')
                ->orderBy('titulo', 'ASC')
                ->findAll();
            
            // Convertir objetos a arrays si es necesario
            $data['plantillas'] = [];
            foreach ($plantillas as $plantilla) {
                if (is_object($plantilla)) {
                    $data['plantillas'][] = (array)$plantilla;
                } else {
                    $data['plantillas'][] = $plantilla;
                }
            }
        } else {
            $data['plantillas'] = [];
        }

        return view('Modulos/boton_pago/lista', $data);
    }

    /**
     * Vista: Crear botón de pago para una cita
     */
    public function crear($detalleAgendaId = null)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Si se proporciona detalle_agenda_id, cargar la cita
        if ($detalleAgendaId) {
            $detalleAgendaModel = new DetalleAgenda();
            $cita = $detalleAgendaModel->find($detalleAgendaId);
            
            if ($cita && $cita->paciente_id) {
                $pacienteModel = new Paciente();
                $cita->paciente = $pacienteModel->find($cita->paciente_id);
            }
            
            $data['cita'] = $cita;
        }

        // Cargar plantillas activas para esta empresa
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        if ($empresaId) {
            $plantillaModel = new BotonPagoPlantilla();
            $data['plantillas'] = $plantillaModel->getPlantillasActivas($empresaId);
        } else {
            $data['plantillas'] = [];
        }

        return view('Modulos/boton_pago/crear', $data);
    }

    /**
     * Vista: Editar plantilla de botón de pago
     */
    public function editar($id)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Obtener empresa del usuario
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;

        if (!$empresaId) {
            return redirect()->to(base_url('dashboard/boton-pago/lista'))
                ->with('error', 'No se pudo determinar la empresa del usuario');
        }

        // Cargar plantilla
        $plantillaModel = new BotonPagoPlantilla();
        $plantilla = $plantillaModel->getPlantilla($id, $empresaId);

        if (!$plantilla) {
            return redirect()->to(base_url('dashboard/boton-pago/lista'))
                ->with('error', 'Plantilla no encontrada');
        }

        $data['plantilla'] = $plantilla;
        $data['es_edicion'] = true;

        return view('Modulos/boton_pago/crear', $data);
    }

    /**
     * Endpoint AJAX: Generar botón de pago (crear preferencia)
     */
    public function generarBoton()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'csrf_hash' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(401);
        }

        $post = $this->request->getPost();
        $esPlantilla = !empty($post['es_plantilla']);
        
        // Validar datos según si es plantilla o pago
        $validation = \Config\Services::validation();
        if ($esPlantilla) {
            // Para plantillas, no se requiere email_pagador
            $validation->setRules([
                'monto' => 'required|decimal|greater_than[0]',
                'titulo' => 'required|min_length[3]',
                'detalle_agenda_id' => 'permit_empty|integer'
            ]);
        } else {
            // Para pagos normales, se requiere email
            $validation->setRules([
                'monto' => 'required|decimal|greater_than[0]',
                'titulo' => 'required|min_length[3]',
                'email_pagador' => 'required|valid_email',
                'detalle_agenda_id' => 'permit_empty|integer'
            ]);
        }

        if (!$validation->run($post)) {
            return $this->response->setJSON([
                'error' => 'Datos inválidos',
                'errors' => $validation->getErrors(),
                'csrf_hash' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(400);
        }

        try {
            $usuario = session()->get('usuario');
            $empresaId = $usuario['empresa_id'] ?? null;

            if (!$empresaId) {
                throw new \Exception('Usuario no tiene empresa asignada');
            }

            // Si es plantilla, crear o actualizar la plantilla
            if ($esPlantilla) {
                $plantillaModel = new BotonPagoPlantilla();
                $plantillaData = [
                    'titulo' => $post['titulo'],
                    'descripcion' => $post['descripcion'] ?? '',
                    'monto' => $post['monto'],
                    'moneda' => $post['moneda'] ?? 'CLP',
                    'activo' => $post['activo'] ?? 'A',
                    'orden' => $post['orden'] ?? 0
                ];

                // Si hay un ID, es una edición
                $plantillaId = $post['plantilla_id'] ?? null;
                if (!empty($plantillaId)) {
                    // Actualizar plantilla existente
                    $plantilla = $plantillaModel->getPlantilla($plantillaId, $empresaId);
                    if (!$plantilla) {
                        throw new \Exception('Plantilla no encontrada');
                    }
                    
                    $plantillaModel->update($plantillaId, $plantillaData);
                    $mensaje = 'Plantilla actualizada exitosamente.';
                } else {
                    // Crear nueva plantilla
                    $plantillaData['empresa_id'] = $empresaId;
                    $plantillaId = $plantillaModel->insert($plantillaData);
                    
                    if (!$plantillaId) {
                        throw new \Exception('Error al guardar la plantilla');
                    }
                    $mensaje = 'Plantilla creada exitosamente. Ahora puedes usarla al agendar citas.';
                }

                return $this->response->setJSON([
                    'success' => true,
                    'es_plantilla' => true,
                    'plantilla_id' => $plantillaId,
                    'message' => $mensaje,
                    'csrf_hash' => csrf_hash()
                ])->setHeader('X-CSRF-TOKEN', csrf_hash());
            }

            // Si no es plantilla, crear pago normal (código existente)
            $pagoModel = new Pago();
            $pagoData = [
                'empresa_id' => $empresaId,
                'detalle_agenda_id' => $post['detalle_agenda_id'] ?? null,
                'tipo_pago' => 'cita',
                'monto' => $post['monto'],
                'moneda' => $post['moneda'] ?? 'CLP',
                'estado_pago' => 'pendiente',
                'observaciones' => $post['descripcion'] ?? ''
            ];

            // Verificar que Mercado Pago esté habilitado para esta empresa
            if (!$this->empresaConfigModel->mercadoPagoHabilitado($empresaId)) {
                throw new \Exception('Mercado Pago no está configurado o habilitado para esta empresa. Por favor, configure las credenciales en Configuraciones.');
            }

            // Obtener servicio con credenciales de la empresa
            $mercadoPagoService = $this->getMercadoPagoService($empresaId);

            // Primero crear el registro de pago en BD (sin preference_id aún)
            $pagoId = $pagoModel->insert($pagoData);

            if (!$pagoId) {
                throw new \Exception('Error al guardar el pago');
            }

            // Crear preferencia en Mercado Pago
            $preferenciaData = [
                'title' => $post['titulo'],
                'description' => $post['descripcion'] ?? 'Pago de consulta nutricional',
                'unit_price' => $post['monto'],
                'quantity' => 1,
                'currency' => $post['moneda'] ?? 'CLP',
                'payer_email' => $post['email_pagador'],
                'payer_name' => $post['nombre_pagador'] ?? '',
                'payer_surname' => $post['apellido_pagador'] ?? '',
                'external_reference' => (string)$pagoId, // Usar ID del pago como referencia
                'statement_descriptor' => 'NextLine Nutrición'
            ];

            // Crear preferencia
            $preferencia = $mercadoPagoService->crearPreferencia($preferenciaData);

            // Actualizar pago con preference_id
            $pagoModel->update($pagoId, [
                'mp_preference_id' => $preferencia['preference_id']
            ]);

            // Obtener credenciales para determinar el modo
            $credenciales = $this->empresaConfigModel->obtenerCredencialesMercadoPago($empresaId);
            $initPoint = ($credenciales['mode'] === 'sandbox') 
                ? $preferencia['sandbox_init_point'] 
                : $preferencia['init_point'];

            return $this->response->setJSON([
                'success' => true,
                'es_plantilla' => false,
                'pago_id' => $pagoId,
                'preference_id' => $preferencia['preference_id'],
                'init_point' => $initPoint,
                'boton_url' => $initPoint,
                'csrf_hash' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        } catch (\Exception $e) {
            log_message('error', 'Error al generar botón de pago: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => $e->getMessage(),
                'csrf_hash' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(500);
        }
    }

    /**
     * Vista: Ver estado de un pago
     */
    public function ver($pagoId)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $pagoModel = new Pago();
        $pago = $pagoModel->find($pagoId);

        if (!$pago) {
            return redirect()->to(base_url('dashboard/boton-pago/lista'))
                ->with('error', 'Pago no encontrado');
        }

        // Cargar información adicional
        if ($pago->detalle_agenda_id) {
            $detalleAgendaModel = new DetalleAgenda();
            $pago->cita = $detalleAgendaModel->find($pago->detalle_agenda_id);
        }

        $data['pago'] = $pago;

        return view('Modulos/boton_pago/ver', $data);
    }

    /**
     * Página de éxito después del pago
     */
    public function success()
    {
        $preferenceId = $this->request->getGet('preference_id');
        
        if (!$preferenceId) {
            return redirect()->to(base_url('dashboard/boton-pago/lista'))
                ->with('error', 'No se recibió información del pago');
        }

        $pagoModel = new Pago();
        $pago = $pagoModel->where('mp_preference_id', $preferenceId)->first();

        if (!$pago) {
            return redirect()->to(base_url('dashboard/boton-pago/lista'))
                ->with('error', 'Pago no encontrado');
        }

        // Verificar estado actualizado en Mercado Pago
        try {
            $usuario = session()->get('usuario');
            $empresaId = $usuario['empresa_id'] ?? null;
            
            if ($empresaId) {
                $mercadoPagoService = $this->getMercadoPagoService($empresaId);
                $preferencia = $mercadoPagoService->obtenerPreferencia($preferenceId);
                // El webhook debería haber actualizado el estado, pero verificamos por si acaso
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al verificar preferencia: ' . $e->getMessage());
        }

        return redirect()->to(base_url('dashboard/boton-pago/ver/' . $pago->id))
            ->with('success', 'Pago procesado correctamente');
    }

    /**
     * Página de fallo después del pago
     */
    public function failure()
    {
        $preferenceId = $this->request->getGet('preference_id');
        
        if ($preferenceId) {
            $pagoModel = new Pago();
            $pago = $pagoModel->where('mp_preference_id', $preferenceId)->first();
            
            if ($pago) {
                return redirect()->to(base_url('dashboard/boton-pago/ver/' . $pago->id))
                    ->with('error', 'El pago no pudo ser procesado');
            }
        }

        return redirect()->to(base_url('dashboard/boton-pago/lista'))
            ->with('error', 'Error al procesar el pago');
    }

    /**
     * Página de pago pendiente
     */
    public function pending()
    {
        $preferenceId = $this->request->getGet('preference_id');
        
        if ($preferenceId) {
            $pagoModel = new Pago();
            $pago = $pagoModel->where('mp_preference_id', $preferenceId)->first();
            
            if ($pago) {
                return redirect()->to(base_url('dashboard/boton-pago/ver/' . $pago->id))
                    ->with('info', 'El pago está pendiente de confirmación');
            }
        }

        return redirect()->to(base_url('dashboard/boton-pago/lista'))
            ->with('info', 'El pago está pendiente');
    }
}
