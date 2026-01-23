<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\EmpresaConfiguracion;
use App\Models\Empresa;
use App\Services\AccesoService;

class ConfiguracionController extends BaseController
{
    protected $configuracionModel;
    protected $empresaModel;

    public function __construct()
    {
        $this->configuracionModel = new EmpresaConfiguracion();
        $this->empresaModel = new Empresa();
    }

    /**
     * Verificar si el usuario es Super Admin
     */
    private function esSuperAdmin()
    {
        $usuario = session()->get('usuario');
        return isset($usuario['poder']) && $usuario['poder'] == 3;
    }

    /**
     * Obtener empresa_id del usuario actual
     */
    private function getEmpresaIdUsuario()
    {
        $usuario = session()->get('usuario');
        return $usuario['empresa_id'] ?? null;
    }

    /**
     * Mostrar página de configuraciones
     */
    public function index()
    {
        if (!session()->get('usuario')) {
            return redirect()->to(base_url('login'));
        }

        // Cargar menú (necesario para el layout)
        $menuTotal = array();
        $modulo = new \App\Models\ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Obtener empresa_id
        $empresaId = $this->getEmpresaIdUsuario();
        
        if (!$empresaId) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No se pudo determinar la empresa del usuario');
        }

        // Cargar configuraciones de la empresa
        $configuracion = $this->configuracionModel->obtenerConfiguracion($empresaId);
        
        // Cargar información de la empresa
        $empresa = $this->empresaModel->find($empresaId);
        
        // Verificar si tiene acceso al módulo "Botones de Pago" para mostrar configuración de Mercado Pago
        $accesoService = new AccesoService();
        $data['tieneAccesoBotonesPago'] = $accesoService->tieneAccesoModuloPorRuta($empresaId, '/dashboard/boton-pago');
        
        $data['titulo'] = 'Configuraciones del Sistema';
        $data['configuracion'] = $configuracion;
        $data['empresa'] = $empresa;
        $data['empresa_id'] = $empresaId;
        $data['csrf_token'] = csrf_hash();

        return view('Modulos/configuracion/index', $data);
    }

    /**
     * Guardar configuraciones (AJAX)
     */
    public function guardar()
    {
        log_message('info', 'ConfiguracionController::guardar() - Inicio');
        
        if (!session()->get('usuario')) {
            log_message('error', 'ConfiguracionController::guardar() - No hay sesión de usuario');
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No autorizado'
            ])->setStatusCode(401);
        }

        // Obtener empresa_id del usuario
        $empresaId = $this->getEmpresaIdUsuario();
        
        if (!$empresaId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No se pudo determinar la empresa del usuario'
            ])->setStatusCode(400);
        }
        
        log_message('info', 'ConfiguracionController::guardar() - Empresa ID: ' . $empresaId);
        
        $data = $this->request->getPost();
        log_message('info', 'ConfiguracionController::guardar() - Datos recibidos: ' . json_encode($data));

        // Verificar acceso a Botones de Pago antes de guardar config de Mercado Pago
        $accesoService = new AccesoService();
        $tieneAccesoBotonesPago = $accesoService->tieneAccesoModuloPorRuta($empresaId, '/dashboard/boton-pago');
        
        // Validar y limpiar datos
        $configuracion = [
            'enviar_whatsapp' => isset($data['enviar_whatsapp']) ? 1 : 0,
            'enviar_email' => isset($data['enviar_email']) ? 1 : 0,
            'crear_evento_calendario' => isset($data['crear_evento_calendario']) ? 1 : 0,
            'agregar_paciente_como_invitado' => isset($data['agregar_paciente_como_invitado']) ? 1 : 0,
            'enviar_recordatorios_whatsapp' => isset($data['enviar_recordatorios_whatsapp']) ? 1 : 0,
            'horas_antes_recordatorio' => isset($data['horas_antes_recordatorio']) ? (int)$data['horas_antes_recordatorio'] : 24,
            // Mensajes de cancelación masiva
            'mensaje_cancelacion_pendiente' => isset($data['mensaje_cancelacion_pendiente']) ? $data['mensaje_cancelacion_pendiente'] : null,
            'mensaje_cancelacion_confirmada' => isset($data['mensaje_cancelacion_confirmada']) ? $data['mensaje_cancelacion_confirmada'] : null,
            'mensaje_cancelacion_en_proceso' => isset($data['mensaje_cancelacion_en_proceso']) ? $data['mensaje_cancelacion_en_proceso'] : null
        ];
        
        // Solo guardar configuración de Mercado Pago si tiene acceso al módulo
        if ($tieneAccesoBotonesPago) {
            // Guardar credenciales de sandbox
            $configuracion['mp_access_token_sandbox'] = isset($data['mp_access_token_sandbox']) ? trim($data['mp_access_token_sandbox']) : null;
            $configuracion['mp_public_key_sandbox'] = isset($data['mp_public_key_sandbox']) ? trim($data['mp_public_key_sandbox']) : null;
            
            // Guardar credenciales de production
            $configuracion['mp_access_token_production'] = isset($data['mp_access_token_production']) ? trim($data['mp_access_token_production']) : null;
            $configuracion['mp_public_key_production'] = isset($data['mp_public_key_production']) ? trim($data['mp_public_key_production']) : null;
            
            // Guardar modo (sandbox o production)
            $configuracion['mp_mode'] = isset($data['mp_mode']) && in_array($data['mp_mode'], ['sandbox', 'production']) ? $data['mp_mode'] : 'sandbox';
            $configuracion['mp_habilitado'] = isset($data['mp_habilitado']) ? 1 : 0;
            
            // Mantener compatibilidad: también guardar en campos antiguos según el modo seleccionado
            $modoSeleccionado = $configuracion['mp_mode'];
            if ($modoSeleccionado === 'production') {
                $configuracion['mp_access_token'] = $configuracion['mp_access_token_production'];
                $configuracion['mp_public_key'] = $configuracion['mp_public_key_production'];
            } else {
                $configuracion['mp_access_token'] = $configuracion['mp_access_token_sandbox'];
                $configuracion['mp_public_key'] = $configuracion['mp_public_key_sandbox'];
            }
        }

        // Validar horas_antes_recordatorio
        if ($configuracion['horas_antes_recordatorio'] < 1 || $configuracion['horas_antes_recordatorio'] > 168) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Las horas antes del recordatorio deben estar entre 1 y 168 (7 días)'
            ])->setStatusCode(400);
        }

        try {
            $this->configuracionModel->actualizarConfiguracion($empresaId, $configuracion);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Configuraciones guardadas exitosamente',
                'csrf_token' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        } catch (\Exception $e) {
            log_message('error', 'Error al guardar configuraciones: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al guardar las configuraciones: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
