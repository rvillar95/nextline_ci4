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

    private function esSuperAdmin(): bool
    {
        $usuario = session()->get('usuario');

        return isset($usuario['poder']) && (int) $usuario['poder'] === 3;
    }

    private function nutricionistaIdSesion(): int
    {
        return (int) (session()->get('usuario')['id'] ?? 0);
    }

    /** IDs de pacientes del nutricionista en sesión (vacío → [0] para no devolver filas). */
    private function idsPacientesDelNutricionista(?int $nutricionistaId = null): array
    {
        $nutricionistaId = $nutricionistaId ?? $this->nutricionistaIdSesion();
        if ($nutricionistaId <= 0) {
            return [0];
        }

        $ids = (new Paciente())
            ->where('nutricionista_id', $nutricionistaId)
            ->findColumn('id');

        return !empty($ids) ? array_map('intval', $ids) : [0];
    }

    private function puedeAccederPaciente($paciente): bool
    {
        if ($this->esSuperAdmin()) {
            return true;
        }
        if (!$paciente) {
            return false;
        }

        return (int) ($paciente->nutricionista_id ?? 0) === $this->nutricionistaIdSesion();
    }

    private function puedeAccederDocumento($documento): bool
    {
        if ($this->esSuperAdmin()) {
            return true;
        }
        if (!$documento) {
            return false;
        }

        $nutricionistaId = $this->nutricionistaIdSesion();
        if ($nutricionistaId <= 0) {
            return false;
        }

        $docNutri = (int) ($documento->nutricionista_id ?? 0);
        if ($docNutri > 0 && $docNutri === $nutricionistaId) {
            return true;
        }

        $paciente = (new Paciente())->find((int) ($documento->paciente_id ?? 0));

        return $paciente && (int) ($paciente->nutricionista_id ?? 0) === $nutricionistaId;
    }

    /** Aplica alcance por pacientes del nutricionista (no aplica a super admin). */
    private function aplicarAlcanceNutricionista(Documento $documento): Documento
    {
        if ($this->esSuperAdmin()) {
            return $documento;
        }

        return $documento->whereIn('paciente_id', $this->idsPacientesDelNutricionista());
    }

    private function pacientesParaSelect(): array
    {
        $nutricionistaId = $this->esSuperAdmin() ? null : $this->nutricionistaIdSesion();

        return (new Paciente())->getPacientesSelect($nutricionistaId);
    }

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

        $data['pacientes'] = $this->pacientesParaSelect();

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

        $query = $this->aplicarAlcanceNutricionista($documento);

        if (!empty($estado)) {
            $query = $query->where('estado', $estado);
        } else {
            $query = $query->where('estado', 'A');
        }

        if (!empty($paciente_id)) {
            $pacienteId = (int) $paciente_id;
            if (!$this->esSuperAdmin()) {
                $paciente = (new Paciente())->find($pacienteId);
                if (!$this->puedeAccederPaciente($paciente)) {
                    return $this->response->setJSON([
                        'draw' => $draw,
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'data' => [],
                    ]);
                }
            }
            $query->where('paciente_id', $pacienteId);
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

            $fechaSort = date('Y-m-d', strtotime($r->fecha_documento));
            $fechaDisplay = date('d/m/Y', strtotime($r->fecha_documento));
            $fechaCell = '<span data-order="' . esc($fechaSort, 'attr') . '">' . esc($fechaDisplay) . '</span>';

            $data[] = array(
                esc($r->titulo),
                esc($nombrePaciente),
                $tipoBadge,
                $fechaCell,
                $enviadoBadge,
                $estadoBadge,
                $botones
            );
        }

        $totalModel = $this->aplicarAlcanceNutricionista(new Documento());

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $totalModel->where('estado', 'A')->countAllResults(),
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

        $data['pacientes'] = $this->pacientesParaSelect();

        return view('Modulos/documento/registro', $data);
    }

    public function registrar()
    {
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
            'fecha_documento', 'fecha_vencimiento',
        ]);

        $nutricionistaId = $ctx['nutricionista_id'] > 0
            ? $ctx['nutricionista_id']
            : (int) session()->get('usuario')['id'];

        $pacienteId = (int) $post['paciente_id'];
        $paciente = (new Paciente())->find($pacienteId);
        if (!$this->puedeAccederPaciente($paciente)) {
            return redirect()->back()->withInput()->with('errors', 'No tiene permiso para asociar documentos a ese paciente.');
        }

        $file = $this->request->getFile('archivo');
        $resultado = $this->crearDocumentoConArchivo(
            $post,
            $pacienteId,
            $nutricionistaId,
            $ctx['empresa_id'],
            $file && $file->isValid() && !$file->hasMoved() ? $file : null
        );

        if (isset($resultado['error'])) {
            return redirect()->back()->withInput()->with('errors', $resultado['error']);
        }

        return redirect()->to(base_url('dashboard/documento/lista'))
            ->with('success', 'Documento registrado con éxito');
    }

    /**
     * Registro en lote: un paciente, N documentos con metadatos y archivos opcionales.
     */
    public function registrarLote()
    {
        $ctx = storage_context_from_session();
        $pacienteId = (int) $this->request->getPost('paciente_id');
        $filas = $this->request->getPost('documentos');

        if ($pacienteId <= 0) {
            return redirect()->back()->with('errors', 'Seleccione un paciente.');
        }

        if (!is_array($filas) || $filas === []) {
            return redirect()->back()->with('errors', 'Agregue al menos un documento.');
        }

        if (count($filas) > 25) {
            return redirect()->back()->with('errors', 'Máximo 25 documentos por carga.');
        }

        $paciente = (new Paciente())->find($pacienteId);
        if (!$this->puedeAccederPaciente($paciente)) {
            return redirect()->back()->with('errors', 'No tiene permiso para asociar documentos a ese paciente.');
        }

        $nutricionistaId = $ctx['nutricionista_id'] > 0
            ? $ctx['nutricionista_id']
            : (int) session()->get('usuario')['id'];

        $tiposValidos = ['pauta_nutricional', 'receta', 'informe', 'consentimiento', 'otro'];
        $guardados = 0;
        $errores = [];

        foreach ($filas as $idx => $fila) {
            if (!is_array($fila)) {
                continue;
            }

            $titulo = trim((string) ($fila['titulo'] ?? ''));
            $tipo = (string) ($fila['tipo_documento'] ?? '');
            $fecha = (string) ($fila['fecha_documento'] ?? '');

            if ($titulo === '' || $fecha === '' || !in_array($tipo, $tiposValidos, true)) {
                $errores[] = 'Documento #' . ($idx + 1) . ': complete tipo, título y fecha.';
                continue;
            }

            $post = [
                'paciente_id'       => $pacienteId,
                'tipo_documento'    => $tipo,
                'titulo'            => $titulo,
                'descripcion'       => trim((string) ($fila['descripcion'] ?? '')) ?: null,
                'contenido'         => trim((string) ($fila['contenido'] ?? '')) ?: null,
                'fecha_documento'   => $fecha,
                'fecha_vencimiento' => trim((string) ($fila['fecha_vencimiento'] ?? '')) ?: null,
            ];

            $file = $this->request->getFile('archivo_' . $idx);
            $tieneArchivo = $file && $file->isValid() && !$file->hasMoved();

            if (!$tieneArchivo && empty($post['contenido']) && empty($post['descripcion'])) {
                $errores[] = 'Documento #' . ($idx + 1) . ' («' . $titulo . '»): adjunte archivo o complete descripción/contenido.';
                continue;
            }

            if ($tieneArchivo) {
                $ext = strtolower($file->getExtension());
                $permitidos = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
                if (!in_array($ext, $permitidos, true)) {
                    $errores[] = 'Documento #' . ($idx + 1) . ': formato no permitido.';
                    continue;
                }
                if ($file->getSize() > 5 * 1024 * 1024) {
                    $errores[] = 'Documento #' . ($idx + 1) . ': el archivo supera 5 MB.';
                    continue;
                }
            }

            $resultado = $this->crearDocumentoConArchivo(
                $post,
                $pacienteId,
                $nutricionistaId,
                $ctx['empresa_id'],
                $tieneArchivo ? $file : null
            );

            if (isset($resultado['error'])) {
                $errores[] = 'Documento #' . ($idx + 1) . ' («' . $titulo . '»): ' . $resultado['error'];
                continue;
            }

            $guardados++;
        }

        if ($guardados === 0) {
            return redirect()->back()->with('errors', $errores ?: ['No se pudo guardar ningún documento.']);
        }

        $mensaje = $guardados === 1
            ? '1 documento registrado con éxito.'
            : $guardados . ' documentos registrados con éxito.';

        if ($errores !== []) {
            return redirect()->to(base_url('dashboard/documento/lista'))
                ->with('success', $mensaje)
                ->with('warning', 'Algunos documentos no se guardaron: ' . implode(' ', array_slice($errores, 0, 3))
                    . (count($errores) > 3 ? ' (+' . (count($errores) - 3) . ' más)' : ''));
        }

        return redirect()->to(base_url('dashboard/documento/lista'))->with('success', $mensaje);
    }

    /**
     * @return array{id: int}|array{error: string}
     */
    private function crearDocumentoConArchivo(
        array $post,
        int $pacienteId,
        int $nutricionistaId,
        int $empresaId,
        $file = null
    ): array {
        $documento = new Documento();
        $data = [
            'paciente_id'       => $pacienteId,
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
            $errs = $documento->errors();
            $msg = is_array($errs) ? implode(' ', $errs) : 'Error al guardar.';

            return ['error' => $msg];
        }

        $documentoId = (int) $documento->getInsertID();

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $upload = $this->subirArchivoDocumento($file, $empresaId, $nutricionistaId, $pacienteId);
            if (isset($upload['error'])) {
                $documento->delete($documentoId);

                return ['error' => $upload['error']];
            }
            $documento->update($documentoId, [
                'archivo_ruta'   => $upload['key'],
                'archivo_nombre' => $upload['nombre'],
            ]);
        }

        return ['id' => $documentoId];
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

        if (!$data['documento'] || !$this->puedeAccederDocumento($data['documento'])) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'Documento no encontrado');
        }

        $data['pacientes'] = $this->pacientesParaSelect();

        return view('Modulos/documento/editar', $data);
    }

    public function update()
    {
        $documento = new Documento();
        $id = (int) $this->request->getPost('id');
        $row = $documento->find($id);
        if (!$row || !$this->puedeAccederDocumento($row)) {
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

        $paciente = (new Paciente())->find((int) $post['paciente_id']);
        if (!$this->puedeAccederPaciente($paciente)) {
            return redirect()->back()->withInput()->with('errors', 'No tiene permiso para asociar documentos a ese paciente.');
        }

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
        if (!$row || !$this->puedeAccederDocumento($row)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'error' => 'Documento no encontrado'])->setStatusCode(404);
            }

            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'Documento no encontrado');
        }

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

        if (!$data['documento'] || !$this->puedeAccederDocumento($data['documento'])) {
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
        if (!$paciente || !$this->puedeAccederPaciente($paciente)) {
            return $this->response->setJSON(['success' => false, 'error' => 'Paciente no encontrado'])->setStatusCode(404);
        }

        $documentoModel = new Documento();
        $nutricionistaId = $this->esSuperAdmin() ? null : $this->nutricionistaIdSesion();
        $rows = $documentoModel->getDocumentosPorPaciente($pacienteId, null, $nutricionistaId);
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
