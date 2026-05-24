<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Libraries\WhatsAppService;
use App\Models\ModuloDetalle;
use App\Models\Paciente;
use App\Models\WhatsAppMensaje;

class MensajesController extends BaseController
{
    protected function menuData(): array
    {
        $menuTotal = [];
        $modulo = new ModuloDetalle();
        $menu = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($menu as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            $menuTotal[] = ['menu' => $entity, 'submenu' => $submenu];
        }

        return ['menu' => $menu, 'data' => $menuTotal];
    }

    public function index()
    {
        if (!session()->get('usuario')) {
            return redirect()->to(base_url('login'));
        }

        $data = $this->menuData();
        $nutricionistaId = (int) session()->get('usuario')['id'];
        $whatsappModel = new WhatsAppMensaje();
        $data['conversaciones'] = $whatsappModel->getConversacionesResumen($nutricionistaId);
        $data['paciente_id_inicial'] = (int) ($this->request->getGet('paciente_id') ?? 0);

        return view('Modulos/mensajes/index', $data);
    }

    /**
     * JSON: hilo de mensajes con un paciente.
     */
    public function hilo($pacienteId = null)
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $pacienteId = (int) $pacienteId;
        $nutricionistaId = (int) session()->get('usuario')['id'];
        $paciente = $this->obtenerPacienteDelNutricionista($pacienteId, $nutricionistaId);
        if (!$paciente) {
            return $this->response->setJSON(['success' => false, 'message' => 'Paciente no encontrado'])->setStatusCode(404);
        }

        $whatsappModel = new WhatsAppMensaje();
        $mensajes = $whatsappModel->getHiloPorPaciente($pacienteId, $nutricionistaId);
        $puedeResponderLibre = $whatsappModel->tieneVentana24Horas($pacienteId);

        return $this->response->setJSON([
            'success' => true,
            'paciente' => [
                'id' => (int) $paciente->id,
                'nombre' => trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? '')),
                'telefono' => $paciente->telefono ?? '',
                'email' => $paciente->email ?? '',
            ],
            'mensajes' => $mensajes,
            'puede_responder_libre' => $puedeResponderLibre,
            'csrf_token' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    /**
     * POST: enviar mensaje de texto al paciente (ventana 24 h).
     */
    public function enviar()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado'])->setStatusCode(401);
        }

        $pacienteId = (int) $this->request->getPost('paciente_id');
        $mensaje = trim((string) $this->request->getPost('mensaje'));
        $nutricionistaId = (int) session()->get('usuario')['id'];

        if ($pacienteId <= 0 || $mensaje === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Paciente y mensaje son obligatorios.',
            ])->setStatusCode(400);
        }

        $paciente = $this->obtenerPacienteDelNutricionista($pacienteId, $nutricionistaId);
        if (!$paciente) {
            return $this->response->setJSON(['success' => false, 'message' => 'Paciente no encontrado'])->setStatusCode(404);
        }

        if (empty($paciente->telefono)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El paciente no tiene teléfono registrado.',
            ])->setStatusCode(400);
        }

        $whatsappModel = new WhatsAppMensaje();
        if (!$whatsappModel->tieneVentana24Horas($pacienteId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sin ventana de 24 h: el paciente debe haber escrito recientemente, o use una plantilla desde Agenda/Configuración.',
            ])->setStatusCode(400);
        }

        $empresaId = session()->get('usuario')['empresa_id'] ?? null;
        $whatsappService = new WhatsAppService($empresaId);
        $resultado = $whatsappService->enviarMensaje(
            $paciente->telefono,
            $mensaje,
            $pacienteId,
            null,
            $nutricionistaId
        );

        if (!($resultado['success'] ?? false)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $resultado['error'] ?? 'No se pudo enviar el mensaje.',
            ])->setStatusCode(500);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Mensaje enviado.',
            'csrf_token' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    private function obtenerPacienteDelNutricionista(int $pacienteId, int $nutricionistaId): ?object
    {
        $pacienteModel = new Paciente();

        return $pacienteModel
            ->where('id', $pacienteId)
            ->where('nutricionista_id', $nutricionistaId)
            ->where('estado', 'A')
            ->first();
    }
}
