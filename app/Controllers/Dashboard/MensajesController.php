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
        $limit = (int) ($this->request->getGet('limit') ?? WhatsAppMensaje::MENSAJES_POR_LOTE);
        $beforeId = (int) ($this->request->getGet('before_id') ?? 0);
        $hilo = $whatsappModel->getHiloPaginado(
            $pacienteId,
            $nutricionistaId,
            $limit,
            $beforeId > 0 ? $beforeId : null
        );
        $puedeResponderLibre = $whatsappModel->tieneVentana24Horas($pacienteId);

        if ($this->request->getGet('marcar_leido') !== '0') {
            $whatsappModel->marcarConversacionLeida($pacienteId, $nutricionistaId);
        }

        return $this->response->setJSON([
            'success' => true,
            'paciente' => [
                'id' => (int) $paciente->id,
                'nombre' => trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? '')),
                'telefono' => $paciente->telefono ?? '',
                'email' => $paciente->email ?? '',
            ],
            'mensajes' => $hilo['mensajes'],
            'has_more_older' => $hilo['has_more_older'],
            'oldest_id' => $hilo['oldest_id'],
            'puede_responder_libre' => $puedeResponderLibre,
            'ultimo_id' => $whatsappModel->getUltimoIdHilo($pacienteId, $nutricionistaId),
            'csrf_token' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    /**
     * JSON: sincronizar lista y mensajes nuevos (polling).
     */
    public function sync()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado'])->setStatusCode(401);
        }

        $nutricionistaId = (int) session()->get('usuario')['id'];
        $pacienteId = (int) ($this->request->getGet('paciente_id') ?? 0);
        $afterId = (int) ($this->request->getGet('after_id') ?? 0);

        $whatsappModel = new WhatsAppMensaje();
        $payload = [
            'success' => true,
            'conversaciones' => $whatsappModel->getConversacionesResumen($nutricionistaId),
            'csrf_token' => csrf_hash(),
        ];

        if ($pacienteId > 0) {
            $paciente = $this->obtenerPacienteDelNutricionista($pacienteId, $nutricionistaId);
            if ($paciente) {
                $nuevos = $whatsappModel->getMensajesDespuesDe($pacienteId, $nutricionistaId, $afterId);
                if (!empty($nuevos)) {
                    $whatsappModel->marcarConversacionLeida($pacienteId, $nutricionistaId);
                }
                $payload['nuevos_mensajes'] = $nuevos;
                $payload['puede_responder_libre'] = $whatsappModel->tieneVentana24Horas($pacienteId);
                $payload['ultimo_id'] = $whatsappModel->getUltimoIdHilo($pacienteId, $nutricionistaId);
            }
        }

        return $this->response->setJSON($payload)->setHeader('X-CSRF-TOKEN', csrf_hash());
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
        $usuarioSesion = session()->get('usuario') ?? [];
        $nombreNutri = trim(($usuarioSesion['nombre'] ?? '') . ' ' . ($usuarioSesion['apellido'] ?? ''));
        $mensajeEnviar = $whatsappService->mensajeConFirmaNutricionista($mensaje, $nutricionistaId, $nombreNutri !== '' ? $nombreNutri : null);

        $resultado = $whatsappService->enviarMensaje(
            $paciente->telefono,
            $mensajeEnviar,
            $pacienteId,
            null,
            $nutricionistaId,
            false
        );

        if (($resultado['success'] ?? false) && empty($resultado['historial_id'])) {
            log_message('warning', 'Mensajes enviar: API OK sin fila en whatsapp_mensajes. paciente_id=' . $pacienteId);
        }

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
