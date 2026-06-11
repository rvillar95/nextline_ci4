<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Documento;
use App\Models\UsuarioCredencial;
use App\Services\StorageService;

/**
 * Descarga de documentos privados (no expuestos en /uploads).
 */
class ArchivoController extends BaseController
{
    public function descargarCredencial(int $credencialId)
    {
        if (!session()->get('usuario')) {
            return redirect()->to(base_url('login'));
        }

        $credencialModel = new UsuarioCredencial();
        $row = $credencialModel->find($credencialId);
        if (!$row || empty($row['archivo_ruta'])) {
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado');
        }

        $usuarioId = (int) ($row['usuario_id'] ?? 0);
        if (!$this->puedeVerArchivoUsuario($usuarioId)) {
            return $this->response->setStatusCode(403)->setBody('No autorizado');
        }

        $storage = new StorageService();
        $nombre = $row['archivo_nombre'] ?? 'documento';
        $download = $storage->downloadResponse($row['archivo_ruta'], $nombre);
        if ($download === null) {
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado');
        }

        return $download;
    }

    public function descargarDocumento(int $documentoId)
    {
        if (!session()->get('usuario')) {
            return redirect()->to(base_url('login'));
        }

        $documentoModel = new Documento();
        $row = $documentoModel->find($documentoId);
        if (!$row || empty($row->archivo_ruta)) {
            return $this->response->setStatusCode(404)->setBody('Archivo no encontrado');
        }

        if (!$this->puedeVerDocumento($row)) {
            return $this->response->setStatusCode(403)->setBody('No autorizado');
        }

        $storage = new StorageService();
        $nombre = $row->archivo_nombre ?? 'documento';
        $download = $storage->downloadResponse($row->archivo_ruta, $nombre);

        return $download ?? $this->response->setStatusCode(404)->setBody('Archivo no encontrado');
    }

    /**
     * Enlace firmado temporal para compartir con el paciente (JSON).
     */
    public function enlaceCompartirDocumento(int $documentoId)
    {
        $this->response->setContentType('application/json');
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado'])->setStatusCode(401);
        }

        $documentoModel = new Documento();
        $row = $documentoModel->find($documentoId);
        if (!$row || empty($row->archivo_ruta)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Sin archivo adjunto'])->setStatusCode(404);
        }

        if (!$this->puedeVerDocumento($row)) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado'])->setStatusCode(403);
        }

        $storage = new StorageService();
        $url = $storage->signedUrl($row->archivo_ruta);
        if ($url === null) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'No se pudo generar el enlace. Verifique storage.driver=gcs en .env',
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'url'     => $url,
            'nombre'  => $row->archivo_nombre ?? $row->titulo,
            'dias'    => (int) round(config('Storage')->gcsSignedUrlSeconds / 86400),
        ]);
    }

    private function puedeVerDocumento(object $documento): bool
    {
        $sessionUser = session()->get('usuario');
        if ((int) ($sessionUser['poder'] ?? 0) === 3) {
            return true;
        }

        $usuarioId = (int) ($sessionUser['id'] ?? 0);
        if ($usuarioId <= 0) {
            return false;
        }

        $docNutri = (int) ($documento->nutricionista_id ?? 0);
        if ($docNutri > 0 && $docNutri === $usuarioId) {
            return true;
        }

        $pacienteModel = new \App\Models\Paciente();
        $paciente = $pacienteModel->find((int) ($documento->paciente_id ?? 0));

        return $paciente && (int) ($paciente->nutricionista_id ?? 0) === $usuarioId;
    }

    private function puedeVerArchivoUsuario(int $usuarioId): bool
    {
        $sessionUser = session()->get('usuario');
        if ((int) ($sessionUser['id'] ?? 0) === $usuarioId) {
            return true;
        }

        return (int) ($sessionUser['poder'] ?? 0) >= 90;
    }
}
