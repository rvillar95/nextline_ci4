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
                          '<button class="btn btn-sm btn-outline-danger" onclick="eliminarPaciente(' . $r->id . ')">Eliminar</button>';
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
        if (!$id) {
            $id = $this->request->getPost('id');
        }
        
        if (!$id) {
            return redirect()->to(base_url('dashboard/paciente/lista'))->with('error', 'ID de paciente requerido');
        }
        
        $paciente = new Paciente();
        
        if ($paciente->update($id, ['estado' => 'I'])) {
            return redirect()->to(base_url('dashboard/paciente/lista'))->with('success', 'Paciente eliminado con éxito');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar el paciente');
        }
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

        // Cargar historial clínico
        $historialModel = new \App\Models\HistorialClinico();
        $data['historial'] = $historialModel->getHistorialPorPaciente($id);

        // Cargar documentos
        $documentoModel = new \App\Models\Documento();
        $data['documentos'] = $documentoModel->getDocumentosPorPaciente($id);

        // Cargar planes alimentarios
        $planModel = new \App\Models\PlanAlimentario();
        $data['planes'] = $planModel->where('paciente_id', $id)
            ->orderBy('fcreacion', 'DESC')
            ->findAll();

        // Cargar detalle_agenda (citas) del paciente solo del usuario logueado (para que "Ver consulta" funcione)
        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];
        $data['citas'] = $db->table('detalle_agenda da')
            ->select('da.*, a.fecha as fecha_agenda, ma.nombre as modalidad_nombre')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
            ->where('da.paciente_id', $id)
            ->where('da.usuario_id', $usuario_id)
            ->orderBy('a.fecha', 'DESC')
            ->orderBy('da.hora_inicio', 'DESC')
            ->limit(50)
            ->get()
            ->getResult();

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
}
