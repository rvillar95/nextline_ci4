<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Documento;
use App\Models\ModuloDetalle;
use App\Models\Paciente;
use App\Services\DocumentoEnvioService;
use App\Services\StorageService;
use App\Traits\MaintainsFilters;

class DocumentoController extends BaseController
{
    use MaintainsFilters;

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

        $pacienteModel = new Paciente();
        $data['pacientes'] = $pacienteModel->getPacientesSelect();

        return view('Modulos/documento/lista', $data);
    }

    public function getDocumentos()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        if ($this->request->getGet('para_envio') === '1') {
            return $this->documentosPorPaciente((int) $this->request->getGet('paciente_id'));
        }

        $documento = new Documento();
        $draw = intval($this->request->getGet("draw"));

        $paciente_id = $this->request->getGet('paciente_id');
        $tipo_documento = $this->request->getGet('tipo_documento');
        $estado = $this->request->getGet('estado');
        $busqueda = $this->request->getGet('busqueda');

        $query = $documento;

        if (!empty($estado)) {
            $query = $query->where('estado', $estado);
        } else {
            $query = $query->where('estado', 'A');
        }

        if (!empty($paciente_id)) {
            $query->where('paciente_id', $paciente_id);
        }

        if (!empty($tipo_documento)) {
            $query->where('tipo_documento', $tipo_documento);
        }

        if (!empty($busqueda)) {
            $query->groupStart()
                  ->like('titulo', $busqueda)
                  ->orLike('descripcion', $busqueda)
                  ->groupEnd();
        }

        $rows = $query->orderBy('fecha_documento', 'DESC')
                     ->findAll();

        $pacienteModel = new Paciente();
        $data = array();
        foreach ($rows as $r) {
            $paciente = $pacienteModel->find($r->paciente_id);
            $nombrePaciente = $paciente ? trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? '')) : 'N/A';

            $tipoBadge = match ($r->tipo_documento) {
                'pauta_nutricional' => '<span class="badge bg-primary">Pauta Nutricional</span>',
                'receta' => '<span class="badge bg-success">Receta</span>',
                'informe' => '<span class="badge bg-info">Informe</span>',
                'consentimiento' => '<span class="badge bg-warning">Consentimiento</span>',
                'otro' => '<span class="badge bg-secondary">Otro</span>',
                default => '<span class="badge bg-secondary">N/A</span>',
            };

            $enviadoBadge = $r->enviado
                ? '<span class="badge bg-success">Enviado</span>'
                : '<span class="badge bg-warning">Pendiente</span>';

            $estadoBadge = $r->estado == 'A'
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';

            $botones = '';
            if ($r->estado == 'A') {
                $botones = '<button class="btn btn-sm btn-outline-primary" onclick="editarDocumento(' . $r->id . ')">Editar</button> ';
                $botones .= '<button class="btn btn-sm btn-outline-info" onclick="verDocumento(' . $r->id . ')">Ver</button> ';
                if (!empty($r->archivo_ruta)) {
                    $botones .= '<a class="btn btn-sm btn-outline-secondary" href="' . base_url('dashboard/documento/' . $r->id . '/descargar') . '" target="_blank">Archivo</a> ';
                }
                $botones .= '<button class="btn btn-sm btn-outline-success" onclick="enviarDocumento(' . $r->id . ')">Enviar</button> ';
                $botones .= '<button class="btn btn-sm btn-outline-danger" onclick="eliminarDocumento(' . $r->id . ')">Eliminar</button>';
            } else {
                $botones = '<button class="btn btn-sm btn-outline-info" onclick="verDocumento(' . $r->id . ')">Ver</button>';
            }

            $data[] = array(
                esc($r->titulo),
                esc($nombrePaciente),
                $tipoBadge,
                esc(date('d/m/Y', strtotime($r->fecha_documento))),
                $enviadoBadge,
                $estadoBadge,
                $botones
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $documento->where('estado', 'A')->countAllResults(),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function registro()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $pacienteModel = new Paciente();
        $data['pacientes'] = $pacienteModel->getPacientesSelect();

        return view('Modulos/documento/registro', $data);
    }

    public function registrar()
    {
        $documento = new Documento();
        $ctx = storage_context_from_session();

        $validationRules = [
            'paciente_id' => 'required|integer|greater_than[0]',
            'tipo_documento' => 'required|in_list[pauta_nutricional,receta,informe,consentimiento,otro]',
            'titulo' => 'required|string|max_length[200]',
            'fecha_documento' => 'required|valid_date',
            'archivo' => 'permit_empty|uploaded[archivo]|max_size[archivo,5120]|ext_in[archivo,pdf,jpg,jpeg,png,webp]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'paciente_id', 'tipo_documento', 'titulo', 'descripcion', 'contenido',
            'fecha_documento', 'fecha_vencimiento', 'observaciones'
        ]);

        $nutricionistaId = $ctx['nutricionista_id'] > 0
            ? $ctx['nutricionista_id']
            : (int) session()->get('usuario')['id'];

        $data = [
            'paciente_id'       => (int) $post['paciente_id'],
            'nutricionista_id'  => $nutricionistaId,
            'tipo_documento'    => $post['tipo_documento'],
            'titulo'            => $post['titulo'],
            'descripcion'       => $post['descripcion'] ?? null,
            'contenido'         => $post['contenido'] ?? null,
            'fecha_documento'   => $post['fecha_documento'],
            'fecha_vencimiento' => $post['fecha_vencimiento'] ?? null,
            'enviado'           => 0,
            'estado'            => 'A',
        ];

        if (!$documento->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $documento->errors());
        }
        $documentoId = (int) $documento->getInsertID();

        $file = $this->request->getFile('archivo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $upload = $this->subirArchivoDocumento(
                $file,
                $ctx['empresa_id'],
                $nutricionistaId,
                (int) $post['paciente_id']
            );
            if (isset($upload['error'])) {
                $documento->delete($documentoId);

                return redirect()->back()->withInput()->with('errors', $upload['error']);
            }
            $documento->update($documentoId, [
                'archivo_ruta'   => $upload['key'],
                'archivo_nombre' => $upload['nombre'],
            ]);
        }

        return redirect()->to(base_url('dashboard/documento/lista'))
            ->with('success', 'Documento registrado con éxito');
    }

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

        $documento = new Documento();
        $data['documento'] = $documento->getDocumentoCompleto($id);

        if (!$data['documento']) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'Documento no encontrado');
        }

        $pacienteModel = new Paciente();
        $data['pacientes'] = $pacienteModel->getPacientesSelect();

        return view('Modulos/documento/editar', $data);
    }

    public function update()
    {
        $documento = new Documento();
        $id = (int) $this->request->getPost('id');
        $row = $documento->find($id);
        if (!$row) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'Documento no encontrado');
        }

        $validationRules = [
            'paciente_id' => 'required|integer|greater_than[0]',
            'tipo_documento' => 'required|in_list[pauta_nutricional,receta,informe,consentimiento,otro]',
            'titulo' => 'required|string|max_length[200]',
            'fecha_documento' => 'required|valid_date',
            'archivo' => 'permit_empty|uploaded[archivo]|max_size[archivo,5120]|ext_in[archivo,pdf,jpg,jpeg,png,webp]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'paciente_id', 'tipo_documento', 'titulo', 'descripcion', 'contenido',
            'fecha_documento', 'fecha_vencimiento', 'observaciones'
        ]);

        $nutricionistaId = (int) ($row->nutricionista_id ?? 0) ?: storage_context_from_session()['nutricionista_id'];
        $ctx = storage_context_for_usuario($nutricionistaId);
        $empresaId = $ctx['empresa_id'];

        $data = [
            'paciente_id'       => (int) $post['paciente_id'],
            'tipo_documento'    => $post['tipo_documento'],
            'titulo'            => $post['titulo'],
            'descripcion'       => $post['descripcion'] ?? null,
            'contenido'         => $post['contenido'] ?? null,
            'fecha_documento'   => $post['fecha_documento'],
            'fecha_vencimiento' => $post['fecha_vencimiento'] ?? null,
        ];

        $file = $this->request->getFile('archivo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $upload = $this->subirArchivoDocumento(
                $file,
                $empresaId,
                $nutricionistaId,
                (int) $post['paciente_id']
            );
            if (isset($upload['error'])) {
                return redirect()->back()->withInput()->with('errors', $upload['error']);
            }
            if (!empty($row->archivo_ruta)) {
                (new StorageService())->delete($row->archivo_ruta);
            }
            $data['archivo_ruta'] = $upload['key'];
            $data['archivo_nombre'] = $upload['nombre'];
        }

        if ($documento->update($id, $data)) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('success', 'Documento actualizado con éxito');
        }

        return redirect()->back()->withInput()->with('errors', 'Error al actualizar el documento');
    }

    public function eliminar($id = null)
    {
        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'ID de documento requerido');
        }

        $documento = new Documento();
        $row = $documento->find($id);
        $archivoRuta = is_array($row) ? ($row['archivo_ruta'] ?? null) : ($row->archivo_ruta ?? null);
        if ($archivoRuta) {
            (new StorageService())->delete($archivoRuta);
        }

        if ($documento->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Documento eliminado']);
            }

            return redirect()->to(base_url('dashboard/documento/lista'))->with('success', 'Documento eliminado con éxito');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Error al eliminar'])->setStatusCode(500);
        }

        return redirect()->back()->with('error', 'Error al eliminar el documento');
    }

    public function detalle($id)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $documento = new Documento();
        $data['documento'] = $documento->getDocumentoCompleto($id);

        if (!$data['documento']) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'Documento no encontrado');
        }

        $envioModel = new \App\Models\DocumentoEnvio();
        $data['historialEnvios'] = $envioModel->getHistorialPorDocumento((int) $id);

        return view('Modulos/documento/detalle', $data);
    }

    /**
     * Envía uno o más documentos por correo al paciente (JSON).
     */
    public function enviarCorreo()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado'])->setStatusCode(401);
        }

        $ids = $this->request->getPost('documento_ids');
        if (!is_array($ids)) {
            $single = $this->request->getPost('documento_id');
            $ids = $single ? [(int) $single] : [];
        }

        return $this->respuestaEnvioCorreo($ids);
    }

    /**
     * Lista documentos activos de un paciente (modal de envío).
     */
    public function documentosPorPaciente($pacienteId)
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado'])->setStatusCode(401);
        }

        $pacienteId = (int) $pacienteId;
        if ($pacienteId <= 0) {
            return $this->response->setJSON(['success' => false, 'error' => 'Seleccione un paciente'])->setStatusCode(400);
        }

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($pacienteId);
        if (!$paciente) {
            return $this->response->setJSON(['success' => false, 'error' => 'Paciente no encontrado'])->setStatusCode(404);
        }

        $documentoModel = new Documento();
        $rows = $documentoModel->getDocumentosPorPaciente($pacienteId);
        $lista = [];
        foreach ($rows as $r) {
            $lista[] = [
                'id'            => (int) $r->id,
                'titulo'        => $r->titulo,
                'tipo'          => $r->tipo_documento,
                'fecha'         => date('d/m/Y', strtotime($r->fecha_documento)),
                'tiene_archivo' => !empty($r->archivo_ruta),
                'enviado'       => (bool) $r->enviado,
            ];
        }

        return $this->response->setJSON([
            'success'  => true,
            'paciente' => [
                'id'     => $pacienteId,
                'nombre' => trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? '')),
                'email'  => trim((string) ($paciente->email ?? '')),
            ],
            'documentos' => $lista,
        ]);
    }

    public function enviar($id)
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado'])->setStatusCode(401);
        }

        return $this->respuestaEnvioCorreo([(int) $id]);
    }

    private function respuestaEnvioCorreo(array $ids)
    {
        $usuario = session()->get('usuario');
        $nombreNutricionista = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''));
        if ($nombreNutricionista === '') {
            $nombreNutricionista = $usuario['email'] ?? 'Su nutricionista';
        }

        $mensajePersonal = trim((string) $this->request->getPost('mensaje_personal'));

        $resultado = (new DocumentoEnvioService())->enviarAlPaciente(
            $ids,
            (int) ($usuario['id'] ?? 0),
            $nombreNutricionista,
            $mensajePersonal
        );

        if (!$resultado['ok']) {
            return $this->response->setJSON(['success' => false, 'error' => $resultado['error']])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $resultado['mensaje'],
            'email'   => $resultado['email'] ?? null,
        ]);
    }

    /**
     * @return array{key: string, nombre: string}|array{error: string}
     */
    private function subirArchivoDocumento($file, int $empresaId, int $nutricionistaId, int $pacienteId): array
    {
        $storage = new StorageService();
        $config = config('Storage');

        return $storage->putUploadedFile(
            $file,
            StorageService::VISIBILITY_PRIVATE,
            storage_folder_documento_paciente($empresaId, $nutricionistaId, $pacienteId),
            $config->documentMimeTypes,
            $config->maxDocumentBytes
        );
    }
}
