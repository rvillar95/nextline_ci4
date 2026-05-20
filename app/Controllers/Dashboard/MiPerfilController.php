<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Usuario;

class MiPerfilController extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Normaliza un valor a color hex #RRGGBB (acepta 3–6 dígitos y rellena con 0)
     */
    private function normalizarHex($valor, string $default): string
    {
        if ($valor === null || $valor === '') {
            return $default;
        }
        $valor = preg_replace('/[^a-fA-F0-9]/', '', $valor);
        if (strlen($valor) < 3 || strlen($valor) > 6) {
            return $default;
        }
        $valor = str_pad($valor, 6, '0', STR_PAD_RIGHT);
        if (!preg_match('/^[a-fA-F0-9]{6}$/', $valor)) {
            return $default;
        }
        return '#' . $valor;
    }

    /**
     * Vista Mi perfil (foto + tema)
     */
    public function index()
    {
        if (!session()->get('usuario')) {
            return redirect()->to(base_url('login'));
        }

        $modulo = new \App\Models\ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        $menuTotal = [];
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            $menuTotal[] = ['menu' => $entity, 'submenu' => $submenu];
        }
        $data['data'] = $menuTotal;

        $usuarioId = session()->get('usuario')['id'];
        $usuario = $this->usuarioModel->find($usuarioId);
        if (!$usuario) {
            return redirect()->to(base_url('dashboard/menu'))->with('error', 'Usuario no encontrado');
        }
        $data['usuario'] = is_array($usuario) ? $usuario : (array) $usuario;
        $data['titulo'] = 'Mi perfil';
        $data['csrf_token'] = csrf_hash();

        return view('Modulos/mi_perfil/index', $data);
    }

    /**
     * Guardar tema (claro/oscuro) y color de acento
     */
    public function guardar()
    {
        $this->response->setContentType('application/json');
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado'])->setStatusCode(401);
        }

        $tema = $this->request->getPost('tema');
        if (!in_array($tema, ['claro', 'oscuro'], true)) {
            $tema = 'claro';
        }

        $colorPrimario = $this->normalizarHex($this->request->getPost('color_primario'), '#7bc143');
        $cardHeaderPorDefecto = (int) $this->request->getPost('card_header_por_defecto');
        $cardHeaderPorDefecto = $cardHeaderPorDefecto === 1 ? 1 : 0;
        $cardHeaderEsGradiente = (int) $this->request->getPost('card_header_es_gradiente');
        $cardHeaderEsGradiente = $cardHeaderEsGradiente === 1 ? 1 : 0;
        $colorCardHeaderBg = $this->normalizarHex($this->request->getPost('color_card_header_bg'), '#6c757d');
        $colorCardHeaderBg2 = $this->request->getPost('color_card_header_bg2');
        $colorCardHeaderBg2 = ($colorCardHeaderBg2 !== null && $colorCardHeaderBg2 !== '') ? $this->normalizarHex($colorCardHeaderBg2, $colorCardHeaderBg) : null;
        if ($cardHeaderEsGradiente !== 1) {
            $colorCardHeaderBg2 = null;
        }
        $colorCardHeaderText = $this->normalizarHex($this->request->getPost('color_card_header_text'), '#ffffff');

        $mainHeaderPorDefecto = (int) $this->request->getPost('main_header_por_defecto');
        $mainHeaderPorDefecto = $mainHeaderPorDefecto === 1 ? 1 : 0;
        $mainHeaderEsGradiente = (int) $this->request->getPost('main_header_es_gradiente');
        $mainHeaderEsGradiente = $mainHeaderEsGradiente === 1 ? 1 : 0;
        $colorMainHeaderBg = $this->normalizarHex($this->request->getPost('color_main_header_bg'), '#4A90E2');
        $colorMainHeaderBg2 = $this->request->getPost('color_main_header_bg2');
        $colorMainHeaderBg2 = ($colorMainHeaderBg2 !== null && $colorMainHeaderBg2 !== '') ? $this->normalizarHex($colorMainHeaderBg2, $colorMainHeaderBg) : null;
        if ($mainHeaderEsGradiente !== 1) {
            $colorMainHeaderBg2 = null;
        }
        $colorMainHeaderText = $this->normalizarHex($this->request->getPost('color_main_header_text'), '#ffffff');

        $usuarioId = (int) session()->get('usuario')['id'];
        $this->usuarioModel->update($usuarioId, [
            'tema' => $tema,
            'color_primario' => $colorPrimario,
            'card_header_por_defecto' => $cardHeaderPorDefecto,
            'card_header_es_gradiente' => $cardHeaderEsGradiente,
            'color_card_header_bg' => $colorCardHeaderBg,
            'color_card_header_bg2' => $colorCardHeaderBg2,
            'color_card_header_text' => $colorCardHeaderText,
            'main_header_por_defecto' => $mainHeaderPorDefecto,
            'main_header_es_gradiente' => $mainHeaderEsGradiente,
            'color_main_header_bg' => $colorMainHeaderBg,
            'color_main_header_bg2' => $colorMainHeaderBg2,
            'color_main_header_text' => $colorMainHeaderText
        ]);

        $usuario = session()->get('usuario');
        $usuario['tema'] = $tema;
        $usuario['color_primario'] = $colorPrimario;
        $usuario['card_header_por_defecto'] = $cardHeaderPorDefecto;
        $usuario['card_header_es_gradiente'] = $cardHeaderEsGradiente;
        $usuario['color_card_header_bg'] = $colorCardHeaderBg;
        $usuario['color_card_header_bg2'] = $colorCardHeaderBg2;
        $usuario['color_card_header_text'] = $colorCardHeaderText;
        $usuario['main_header_por_defecto'] = $mainHeaderPorDefecto;
        $usuario['main_header_es_gradiente'] = $mainHeaderEsGradiente;
        $usuario['color_main_header_bg'] = $colorMainHeaderBg;
        $usuario['color_main_header_bg2'] = $colorMainHeaderBg2;
        $usuario['color_main_header_text'] = $colorMainHeaderText;
        session()->set('usuario', $usuario);

        $payload = [
            'success' => true,
            'message' => 'Preferencias guardadas',
            'color_primario' => $colorPrimario,
            'card_header_por_defecto' => $cardHeaderPorDefecto,
            'card_header_es_gradiente' => $cardHeaderEsGradiente,
            'color_card_header_bg' => $colorCardHeaderBg,
            'color_card_header_text' => $colorCardHeaderText,
            'main_header_por_defecto' => $mainHeaderPorDefecto,
            'main_header_es_gradiente' => $mainHeaderEsGradiente,
            'color_main_header_bg' => $colorMainHeaderBg,
            'color_main_header_text' => $colorMainHeaderText,
            'csrf_token' => csrf_hash()
        ];
        if ($colorCardHeaderBg2 !== null) {
            $payload['color_card_header_bg2'] = $colorCardHeaderBg2;
        }
        if ($colorMainHeaderBg2 !== null) {
            $payload['color_main_header_bg2'] = $colorMainHeaderBg2;
        }
        return $this->response->setJSON($payload)->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    /**
     * Subir foto de perfil
     */
    public function subirFoto()
    {
        $this->response->setContentType('application/json');
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado'])->setStatusCode(401);
        }

        $file = $this->request->getFile('foto');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No se recibió una imagen válida'])->setStatusCode(400);
        }

        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowed, true)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Formato no permitido. Use JPG, PNG, GIF o WebP'])->setStatusCode(400);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return $this->response->setJSON(['success' => false, 'error' => 'La imagen no debe superar 2 MB'])->setStatusCode(400);
        }

        $dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'perfil';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $usuarioId = (int) session()->get('usuario')['id'];
        $usuario = $this->usuarioModel->find($usuarioId);
        $fotoAnterior = is_array($usuario) ? ($usuario['foto'] ?? '') : ($usuario->foto ?? '');
        if ($fotoAnterior) {
            $pathAnterior = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $fotoAnterior);
            if (is_file($pathAnterior)) {
                @unlink($pathAnterior);
            }
        }

        $ext = $file->getClientExtension() ?: 'jpg';
        $newName = $usuarioId . '_' . time() . '.' . $ext;
        if (!$file->move($dir, $newName)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al guardar la imagen'])->setStatusCode(500);
        }

        $rutaRelativa = 'uploads/perfil/' . $newName;
        $this->usuarioModel->update($usuarioId, ['foto' => $rutaRelativa]);

        $usuario = session()->get('usuario');
        $usuario['foto'] = $rutaRelativa;
        session()->set('usuario', $usuario);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Foto actualizada',
            'foto_url' => base_url($rutaRelativa),
            'csrf_token' => csrf_hash()
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }
}
