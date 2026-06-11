<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Paciente;
use App\Models\ModuloDetalle;
use App\Models\Region;
use App\Models\Comuna;
use App\Traits\MaintainsFilters;

class PacienteController extends BaseController
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

        return view('Modulos/paciente/lista', $data);
    }

    public function getPacientes()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $paciente = new Paciente();
        $draw = intval($this->request->getGet("draw"));
        
        $tipo_paciente = $this->request->getGet('tipo_paciente');
        $estado = $this->request->getGet('estado');
        $busqueda = $this->request->getGet('busqueda');
        $nutricionista_id = $this->request->getGet('nutricionista_id');
        
        // Por defecto, mostrar solo pacientes del nutricionista logueado
        $usuario_id = session()->get('usuario')['id'];
        if (empty($nutricionista_id)) {
            $nutricionista_id = $usuario_id;
        }
        
        $query = $paciente;
        
        if (!empty($estado)) {
            $query = $query->where('estado', $estado);
        } else {
            $query = $query->where('estado', 'A');
        }
        
        if (!empty($tipo_paciente)) {
            $query->where('tipo_paciente', $tipo_paciente);
        }

        if (!empty($nutricionista_id)) {
            $query->where('nutricionista_id', $nutricionista_id);
        }
        
        if (!empty($busqueda)) {
            $query->groupStart()
                  ->like('nombre', $busqueda)
                  ->orLike('apellido', $busqueda)
                  ->orLike('rut_dni', $busqueda)
                  ->orLike('email', $busqueda)
                  ->groupEnd();
        }
        
        $rows = $query->orderBy('nombre', 'ASC')
                     ->orderBy('apellido', 'ASC')
                     ->findAll();

        $data = array();
        foreach ($rows as $r) {
            $tipoBadge = match ($r->tipo_paciente) {
                'particular' => '<span class="badge bg-info">Particular</span>',
                'convenio' => '<span class="badge bg-success">Convenio</span>',
                'seguro' => '<span class="badge bg-warning">Seguro</span>',
                'fonasa' => '<span class="badge bg-primary">Fonasa</span>',
                'isapre' => '<span class="badge bg-primary">Isapre</span>',
                'otro' => '<span class="badge bg-secondary">Otro</span>',
                default => '<span class="badge bg-secondary">N/A</span>',
            };

            $nombreCompleto = trim(($r->nombre ?? '') . ' ' . ($r->apellido ?? ''));
            $contacto = '';
            if ($r->telefono) {
                $contacto = "Tel: {$r->telefono}";
            }
            if ($r->email) {
                $contacto .= $contacto ? " | Email: {$r->email}" : "Email: {$r->email}";
            }

            $estadoBadge = $r->estado == 'A' 
                ? '<span class="badge bg-success">Activo</span>' 
                : '<span class="badge bg-danger">Inactivo</span>';

            $imcInfo = '';
            if ($r->imc_inicial) {
                $imcInfo = "IMC: {$r->imc_inicial}";
            }

            $botones = '';
            if ($r->estado == 'A') {
                $botones = '<button class="btn btn-sm btn-outline-primary" onclick="editarPaciente(' . $r->id . ')">Editar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDetallePaciente(' . $r->id . ')">Ver</button> ' .
                          '<button class="btn btn-sm btn-outline-danger" onclick="eliminarPaciente(' . $r->id . ')">Desactivar</button>';
            } else {
                $botones = '<button class="btn btn-sm btn-outline-success" onclick="activarPaciente(' . $r->id . ')">Activar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDetallePaciente(' . $r->id . ')">Ver</button>';
            }

            $data[] = array(
                esc($nombreCompleto),
                esc($r->rut_dni ?? ''),
                $tipoBadge,
                esc($contacto),
                esc($imcInfo),
                $estadoBadge,
                $botones
            );
        }

        // Contar total de registros para el nutricionista y estado A
        $totalModel = new Paciente();
        $recordsTotal = $totalModel->where('estado', 'A')->where('nutricionista_id', $nutricionista_id)->countAllResults();

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
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

        // Cargar regiones y comunas
        $regionModel = new Region();
        $data['regiones'] = $regionModel->findAll();

        return view('Modulos/paciente/registro', $data);
    }

    public function registrar()
    {
        $paciente = new Paciente();

        $validationRules = [
            'nombre' => 'required|string|max_length[100]',
            'apellido' => 'required|string|max_length[100]',
            'email' => 'permit_empty|valid_email|max_length[150]',
            'tipo_paciente' => 'required|in_list[particular,convenio,seguro,fonasa,isapre,otro]'
        ];

        $validationMessages = [
            'nombre' => [
                'required' => 'El nombre del paciente es obligatorio.',
                'string' => 'El nombre debe ser una cadena de texto.',
                'max_length' => 'El nombre no puede exceder de 100 caracteres.'
            ],
            'apellido' => [
                'required' => 'El apellido del paciente es obligatorio.',
                'string' => 'El apellido debe ser una cadena de texto.',
                'max_length' => 'El apellido no puede exceder de 100 caracteres.'
            ],
            'email' => [
                'valid_email' => 'El email debe tener un formato válido.',
                'max_length' => 'El email no puede exceder de 150 caracteres.'
            ],
            'tipo_paciente' => [
                'required' => 'El tipo de paciente es obligatorio.',
                'in_list' => 'El tipo de paciente debe ser: particular, convenio, seguro, fonasa, isapre u otro.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'nutricionista_id', 'tipo_paciente', 'nombre', 'apellido', 'rut_dni', 'fecha_nacimiento',
            'genero', 'telefono', 'email', 'direccion', 'region_id', 'comuna_id', 'comuna', 'region',
            'peso_inicial', 'altura', 'objetivo', 'alergias', 'medicamentos', 'condiciones_medicas', 'observaciones'
        ]);

        // Calcular IMC si hay peso y altura
        $imc_inicial = null;
        if (!empty($post['peso_inicial']) && !empty($post['altura'])) {
            $imc_inicial = $paciente->calcularIMC($post['peso_inicial'], $post['altura']);
        }

        $data = [
            'nutricionista_id' => $post['nutricionista_id'] ?? session()->get('usuario')['id'],
            'tipo_paciente' => $post['tipo_paciente'],
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'rut_dni' => $post['rut_dni'] ?? null,
            'fecha_nacimiento' => $post['fecha_nacimiento'] ?? null,
            'genero' => $post['genero'] ?? null,
            'telefono' => $post['telefono'] ?? null,
            'email' => $post['email'] ?? null,
            'direccion' => $post['direccion'] ?? null,
            'region_id' => $post['region_id'] ?? null,
            'comuna_id' => $post['comuna_id'] ?? null,
            'comuna' => $post['comuna'] ?? null,
            'region' => $post['region'] ?? null,
            'peso_inicial' => $post['peso_inicial'] ?? null,
            'altura' => $post['altura'] ?? null,
            'imc_inicial' => $imc_inicial,
            'objetivo' => $post['objetivo'] ?? null,
            'alergias' => $post['alergias'] ?? null,
            'medicamentos' => $post['medicamentos'] ?? null,
            'condiciones_medicas' => $post['condiciones_medicas'] ?? null,
            'observaciones' => $post['observaciones'] ?? null,
            'estado' => 'A'
        ];

        if ($paciente->insert($data)) {
            return redirect()->to(base_url('dashboard/paciente/lista'))->with('success', 'Paciente registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', $paciente->errors());
        }
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

        $paciente = new Paciente();
        $data['paciente'] = $paciente->getPacienteCompleto($id);
        
        if (!$data['paciente']) {
            return redirect()->to(base_url('dashboard/paciente/lista'))->with('error', 'Paciente no encontrado');
        }

        // Cargar regiones y comunas
        $regionModel = new Region();
        $data['regiones'] = $regionModel->findAll();

        if ($data['paciente']->region_id) {
            $comunaModel = new Comuna();
            $data['comunas'] = $comunaModel->where('region_id', $data['paciente']->region_id)->findAll();
        }

        return view('Modulos/paciente/editar', $data);
    }

    public function update()
    {
        $paciente = new Paciente();
        $id = $this->request->getPost('id');

        $validationRules = [
            'nombre' => 'required|string|max_length[100]',
            'apellido' => 'required|string|max_length[100]',
            'email' => 'permit_empty|valid_email|max_length[150]'
        ];

        $validationMessages = [
            'nombre' => [
                'required' => 'El nombre del paciente es obligatorio.',
                'string' => 'El nombre debe ser una cadena de texto.',
                'max_length' => 'El nombre no puede exceder de 100 caracteres.'
            ],
            'apellido' => [
                'required' => 'El apellido del paciente es obligatorio.',
                'string' => 'El apellido debe ser una cadena de texto.',
                'max_length' => 'El apellido no puede exceder de 100 caracteres.'
            ],
            'email' => [
                'valid_email' => 'El email debe tener un formato válido.',
                'max_length' => 'El email no puede exceder de 150 caracteres.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'nutricionista_id', 'tipo_paciente', 'nombre', 'apellido', 'rut_dni', 'fecha_nacimiento',
            'genero', 'telefono', 'email', 'direccion', 'region_id', 'comuna_id', 'comuna', 'region',
            'peso_inicial', 'altura', 'objetivo', 'alergias', 'medicamentos', 'condiciones_medicas', 'observaciones'
        ]);

        // Calcular IMC si hay peso y altura
        $imc_inicial = null;
        if (!empty($post['peso_inicial']) && !empty($post['altura'])) {
            $imc_inicial = $paciente->calcularIMC($post['peso_inicial'], $post['altura']);
        }

        $data = [
            'nutricionista_id' => $post['nutricionista_id'] ?? null,
            'tipo_paciente' => $post['tipo_paciente'],
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'rut_dni' => $post['rut_dni'] ?? null,
            'fecha_nacimiento' => $post['fecha_nacimiento'] ?? null,
            'genero' => $post['genero'] ?? null,
            'telefono' => $post['telefono'] ?? null,
            'email' => $post['email'] ?? null,
            'direccion' => $post['direccion'] ?? null,
            'region_id' => $post['region_id'] ?? null,
            'comuna_id' => $post['comuna_id'] ?? null,
            'comuna' => $post['comuna'] ?? null,
            'region' => $post['region'] ?? null,
            'peso_inicial' => $post['peso_inicial'] ?? null,
            'altura' => $post['altura'] ?? null,
            'imc_inicial' => $imc_inicial,
            'objetivo' => $post['objetivo'] ?? null,
            'alergias' => $post['alergias'] ?? null,
            'medicamentos' => $post['medicamentos'] ?? null,
            'condiciones_medicas' => $post['condiciones_medicas'] ?? null,
            'observaciones' => $post['observaciones'] ?? null
        ];

        if ($paciente->update($id, $data)) {
            return redirect()->to(base_url('dashboard/paciente/lista'))->with('success', 'Paciente actualizado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al actualizar el paciente');
        }
    }

    public function eliminar($id = null)
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No autorizado',
            ])->setStatusCode(401);
        }

        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID de paciente requerido',
            ])->setStatusCode(400);
        }

        $pacienteModel = new Paciente();
        $usuarioId = (int) session()->get('usuario')['id'];
        $registro = $pacienteModel->find($id);

        if (!$registro || (int) $registro->nutricionista_id !== $usuarioId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Paciente no encontrado',
            ])->setStatusCode(404);
        }

        if ($registro->estado === 'I') {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'El paciente ya estaba inactivo',
            ]);
        }

        if ($pacienteModel->update($id, ['estado' => 'I'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Paciente desactivado con éxito',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'Error al desactivar el paciente',
        ])->setStatusCode(500);
    }

    public function activar($id = null)
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No autorizado',
            ])->setStatusCode(401);
        }

        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID de paciente requerido',
            ])->setStatusCode(400);
        }

        $pacienteModel = new Paciente();
        $usuarioId = (int) session()->get('usuario')['id'];
        $registro = $pacienteModel->find($id);

        if (!$registro || (int) $registro->nutricionista_id !== $usuarioId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Paciente no encontrado',
            ])->setStatusCode(404);
        }

        if ($registro->estado === 'A') {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'El paciente ya estaba activo',
            ]);
        }

        if ($pacienteModel->update($id, ['estado' => 'A'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Paciente activado con éxito',
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'Error al activar el paciente',
        ])->setStatusCode(500);
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

        $paciente = new Paciente();
        $data['paciente'] = $paciente->getPacienteCompleto($id);
        
        if (!$data['paciente']) {
            return redirect()->to(base_url('dashboard/paciente/lista'))->with('error', 'Paciente no encontrado');
        }

        // Cargar documentos
        $documentoModel = new \App\Models\Documento();
        $data['documentos'] = $documentoModel->getDocumentosPorPaciente($id);

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Planes alimentarios con fecha de consulta vinculada (si existe)
        $data['planes'] = $db->table('plan_alimentario pa')
            ->select(
                'pa.*, a.fecha as fecha_consulta, da.hora_inicio as hora_consulta,'
                . ' da.estado_cita as estado_consulta'
            )
            ->join('detalle_agenda da', 'da.id = pa.detalle_agenda_id', 'left')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->where('pa.paciente_id', $id)
            ->orderBy('pa.fcreacion', 'DESC')
            ->get()
            ->getResult();

        // Consultas unificadas (cita + datos de historial clínico si existen)
        $data['consultas'] = $db->table('detalle_agenda da')
            ->select(
                'da.id, da.estado_cita, da.tipo_consulta, da.hora_inicio, da.hora_fin, da.motivo,'
                . ' a.fecha as fecha_agenda, ma.nombre as modalidad_nombre,'
                . ' hc.id as historial_id, hc.peso_actual, hc.imc_actual, hc.motivo_consulta, hc.tipo_registro as historial_tipo'
            )
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
            ->join('historial_clinico hc', 'hc.detalle_agenda_id = da.id AND hc.estado = \'A\'', 'left')
            ->where('da.paciente_id', $id)
            ->where('da.usuario_id', $usuario_id)
            ->orderBy('a.fecha', 'DESC')
            ->orderBy('da.hora_inicio', 'DESC')
            ->get()
            ->getResult();

        $data['total_historiales'] = (int) $db->table('historial_clinico')
            ->where('paciente_id', $id)
            ->where('nutricionista_id', $usuario_id)
            ->where('estado', 'A')
            ->countAllResults();

        return view('Modulos/paciente/detalle', $data);
    }

    public function getPacientesSelect()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $paciente = new Paciente();
        $nutricionistaId = $this->request->getGet('nutricionista_id');
        $pacientes = $paciente->getPacientesSelect($nutricionistaId);

        $data = [];
        foreach ($pacientes as $p) {
            $data[] = [
                'id' => $p->id,
                'text' => $p->nombre_completo,
                'rut' => $p->rut_dni ?? '',
                'email' => $p->email ?? '',
                'telefono' => $p->telefono ?? ''
            ];
        }

        return $this->response->setJSON($data);
    }

    /**
     * Verifica si el RUT/DNI ya está registrado para el nutricionista en sesión.
     * GET ?rut=12.345.678-9
     */
    public function verificarRutNutricionista()
    {
        if (!session()->get('usuario')) {
            return $this->jsonPaciente(['error' => 'No autorizado'], 401);
        }

        $rut = trim((string) $this->request->getGet('rut'));
        $norm = Paciente::normalizarRutDni($rut);
        if ($norm === '' || strlen($norm) < 3) {
            return $this->jsonPaciente([
                'valido' => false,
                'disponible' => false,
                'message' => 'Ingrese un RUT o documento válido.',
            ]);
        }

        if (!Paciente::esRutDniValido($rut)) {
            return $this->jsonPaciente([
                'valido' => false,
                'disponible' => false,
                'message' => 'El RUT ingresado no es válido (revise el dígito verificador).',
            ]);
        }

        $nutricionistaId = (int) session()->get('usuario')['id'];
        $paciente = new Paciente();
        $existe = $paciente->existeRutParaNutricionista($norm, $nutricionistaId);

        return $this->jsonPaciente([
            'valido' => true,
            'disponible' => !$existe,
            'message' => $existe
                ? 'Ya tiene un paciente registrado con este RUT en su lista.'
                : 'RUT disponible para registrar.',
        ]);
    }

    /**
     * Alta rápida de paciente desde el calendario (modal Agendar Cita).
     */
    public function crearRapido()
    {
        if (!session()->get('usuario')) {
            return $this->jsonPaciente(['success' => false, 'message' => 'No autorizado'], 401);
        }

        if (!$this->request->is('post')) {
            return $this->jsonPaciente(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $nutricionistaId = (int) session()->get('usuario')['id'];
        $nombre = trim((string) $this->request->getPost('nombre'));
        $apellido = trim((string) $this->request->getPost('apellido'));
        $rutDni = trim((string) $this->request->getPost('rut_dni'));
        $tipoPaciente = trim((string) $this->request->getPost('tipo_paciente'));
        $telefono = trim((string) $this->request->getPost('telefono'));
        $email = trim((string) $this->request->getPost('email'));

        $errors = [];
        if ($nombre === '') {
            $errors['nombre'] = 'El nombre es obligatorio.';
        }
        if ($apellido === '') {
            $errors['apellido'] = 'El apellido es obligatorio.';
        }
        if ($rutDni === '') {
            $errors['rut_dni'] = 'El RUT o documento es obligatorio.';
        } elseif (!Paciente::esRutDniValido($rutDni)) {
            $errors['rut_dni'] = 'El RUT ingresado no es válido.';
        }
        if ($tipoPaciente === '') {
            $errors['tipo_paciente'] = 'Seleccione el tipo de paciente.';
        } elseif (!in_array($tipoPaciente, ['particular', 'convenio', 'seguro', 'fonasa', 'isapre', 'otro'], true)) {
            $errors['tipo_paciente'] = 'Tipo de paciente no válido.';
        }
        if ($telefono === '') {
            $errors['telefono'] = 'El teléfono es obligatorio para WhatsApp.';
        } elseif (strlen(preg_replace('/\D/', '', $telefono)) < 8) {
            $errors['telefono'] = 'Ingrese un teléfono válido (mínimo 8 dígitos).';
        }
        if ($email === '') {
            $errors['email'] = 'El correo es obligatorio para confirmación y cobros.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo no tiene un formato válido.';
        }

        if ($errors !== []) {
            return $this->jsonPaciente([
                'success' => false,
                'message' => 'Revise los datos del formulario.',
                'errors' => $errors,
            ], 400);
        }

        $rutNorm = Paciente::normalizarRutDni($rutDni);
        $pacienteModel = new Paciente();
        if ($pacienteModel->existeRutParaNutricionista($rutNorm, $nutricionistaId)) {
            return $this->jsonPaciente([
                'success' => false,
                'message' => 'Ya existe un paciente con este RUT en su lista. Búsquelo en el selector o edite la ficha existente.',
                'errors' => ['rut_dni' => 'RUT duplicado para su consulta.'],
            ], 409);
        }

        $data = [
            'nutricionista_id' => $nutricionistaId,
            'tipo_paciente' => $tipoPaciente,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'rut_dni' => $rutDni,
            'telefono' => $telefono,
            'email' => $email,
            'estado' => 'A',
        ];

        $pacienteModel->skipValidation(true);
        $newId = $pacienteModel->insert($data);
        $pacienteModel->skipValidation(false);

        if (!$newId) {
            return $this->jsonPaciente([
                'success' => false,
                'message' => 'No se pudo guardar el paciente. Intente nuevamente.',
                'errors' => $pacienteModel->errors(),
            ], 500);
        }

        $nombreCompleto = trim($nombre . ' ' . $apellido);

        return $this->jsonPaciente([
            'success' => true,
            'message' => 'Paciente registrado correctamente.',
            'paciente' => [
                'id' => (int) $newId,
                'text' => $nombreCompleto,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'rut_dni' => $rutDni,
                'telefono' => $telefono,
                'email' => $email,
            ],
        ]);
    }

    private function jsonPaciente(array $payload, int $status = 200)
    {
        $payload['csrf_token'] = csrf_hash();

        return $this->response
            ->setJSON($payload)
            ->setStatusCode($status)
            ->setHeader('X-CSRF-TOKEN', csrf_hash());
    }
}
