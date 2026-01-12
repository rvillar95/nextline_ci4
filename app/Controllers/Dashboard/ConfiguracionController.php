<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\UsuarioConfiguracion;

class ConfiguracionController extends BaseController
{
    protected $configuracionModel;

    public function __construct()
    {
        $this->configuracionModel = new UsuarioConfiguracion();
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

        // Cargar configuraciones del usuario
        $usuarioId = session()->get('usuario')['id'];
        $configuracion = $this->configuracionModel->obtenerConfiguracion($usuarioId);

        $data['titulo'] = 'Configuraciones del Sistema';
        $data['configuracion'] = $configuracion;
        $data['csrf_token'] = csrf_hash();

        return view('modulos/configuracion/index', $data);
    }

    /**
     * Guardar configuraciones (AJAX)
     */
    public function guardar()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No autorizado'
            ])->setStatusCode(401);
        }

        $usuarioId = session()->get('usuario')['id'];
        $data = $this->request->getPost();

        // Validar y limpiar datos
        $configuracion = [
            'enviar_whatsapp' => isset($data['enviar_whatsapp']) ? 1 : 0,
            'enviar_email' => isset($data['enviar_email']) ? 1 : 0,
            'crear_evento_calendario' => isset($data['crear_evento_calendario']) ? 1 : 0,
            'agregar_paciente_como_invitado' => isset($data['agregar_paciente_como_invitado']) ? 1 : 0,
            'enviar_recordatorios_whatsapp' => isset($data['enviar_recordatorios_whatsapp']) ? 1 : 0,
            'horas_antes_recordatorio' => isset($data['horas_antes_recordatorio']) ? (int)$data['horas_antes_recordatorio'] : 24
        ];

        // Validar horas_antes_recordatorio
        if ($configuracion['horas_antes_recordatorio'] < 1 || $configuracion['horas_antes_recordatorio'] > 168) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Las horas antes del recordatorio deben estar entre 1 y 168 (7 días)'
            ])->setStatusCode(400);
        }

        try {
            $this->configuracionModel->actualizarConfiguracion($usuarioId, $configuracion);

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
