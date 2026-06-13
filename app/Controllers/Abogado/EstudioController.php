<?php

declare(strict_types=1);

namespace App\Controllers\Abogado;

use App\Controllers\BaseController;
use App\Models\Abopech\AbogadoEstudioModel;
use App\Models\Abopech\AbogadoModel;
use App\Services\Abopech\DocumentoAbogadoService;

final class EstudioController extends BaseController
{
    public function guardar()
    {
        $usuario = abopech_usuario();
        $abogado = (new AbogadoModel())->findByUsuarioId((int) $usuario['id']);
        if (!$abogado) {
            return redirect()->to(base_url('abopech'));
        }

        $file = $this->request->getFile('documento');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Debes adjuntar un documento para el estudio.');
        }

        $docService = new DocumentoAbogadoService();
        $upload = $docService->storeEstudioDocument(
            $file,
            (int) $abogado['id'],
            (int) $usuario['id'],
            trim((string) $this->request->getPost('nombre'))
        );

        if (isset($upload['error'])) {
            return redirect()->back()->withInput()->with('error', $upload['error']);
        }

        (new AbogadoEstudioModel())->insert([
            'abogado_id'       => (int) $abogado['id'],
            'nombre'           => trim((string) $this->request->getPost('nombre')),
            'descripcion'      => trim((string) $this->request->getPost('descripcion')),
            'universidad'      => trim((string) $this->request->getPost('universidad')),
            'anio_titulacion'  => (int) $this->request->getPost('anio_titulacion'),
            'tipo_estudio_id'  => (int) $this->request->getPost('tipo_estudio_id'),
            'documento_id'     => (int) $upload['documento_id'],
        ]);

        return redirect()->to(base_url('abopech/mi-perfil'))->with('success', 'Estudio registrado.');
    }

    public function eliminar(int $id)
    {
        $usuario = abopech_usuario();
        $abogado = (new AbogadoModel())->findByUsuarioId((int) $usuario['id']);
        if (!$abogado) {
            return redirect()->to(base_url('abopech'));
        }

        $estudioModel = new AbogadoEstudioModel();
        $estudio = $estudioModel->find($id);
        if (!$estudio || (int) $estudio['abogado_id'] !== (int) $abogado['id']) {
            return redirect()->back()->with('error', 'Estudio no encontrado.');
        }

        $estudioModel->delete($id);

        return redirect()->back()->with('success', 'Estudio eliminado.');
    }
}
