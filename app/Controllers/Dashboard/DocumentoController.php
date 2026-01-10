<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Documento;
use App\Models\Paciente;
use App\Models\ModuloDetalle;
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

        return view('Modulos/documento/lista', $data);
    }

    public function getDocumentos()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
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
                $botones = '<button class="btn btn-sm btn-outline-primary" onclick="editarDocumento(' . $r->id . ')">Editar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDocumento(' . $r->id . ')">Ver</button> ' .
                          '<button class="btn btn-sm btn-outline-success" onclick="enviarDocumento(' . $r->id . ')">Enviar</button> ' .
                          '<button class="btn btn-sm btn-outline-danger" onclick="eliminarDocumento(' . $r->id . ')">Eliminar</button>';
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

        // Cargar pacientes
        $pacienteModel = new Paciente();
        $data['pacientes'] = $pacienteModel->getPacientesSelect();

        return view('Modulos/documento/registro', $data);
    }

    public function registrar()
    {
        $documento = new Documento();

        $validationRules = [
            'paciente_id' => 'required|integer|greater_than[0]',
            'tipo_documento' => 'required|in_list[pauta_nutricional,receta,informe,consentimiento,otro]',
            'titulo' => 'required|string|max_length[200]',
            'fecha_documento' => 'required|valid_date'
        ];

        $validationMessages = [
            'paciente_id' => [
                'required' => 'El paciente es obligatorio.',
                'integer' => 'Debe seleccionar un paciente válido.',
                'greater_than' => 'Debe seleccionar un paciente válido.'
            ],
            'tipo_documento' => [
                'required' => 'El tipo de documento es obligatorio.',
                'in_list' => 'El tipo de documento debe ser: pauta nutricional, receta, informe, consentimiento u otro.'
            ],
            'titulo' => [
                'required' => 'El título del documento es obligatorio.',
                'string' => 'El título debe ser una cadena de texto.',
                'max_length' => 'El título no puede exceder de 200 caracteres.'
            ],
            'fecha_documento' => [
                'required' => 'La fecha del documento es obligatoria.',
                'valid_date' => 'La fecha del documento debe ser una fecha válida.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'paciente_id', 'tipo_documento', 'titulo', 'descripcion', 'contenido',
            'fecha_documento', 'fecha_vencimiento', 'observaciones'
        ]);

        $data = [
            'paciente_id' => $post['paciente_id'],
            'nutricionista_id' => session()->get('usuario')['id'],
            'tipo_documento' => $post['tipo_documento'],
            'titulo' => $post['titulo'],
            'descripcion' => $post['descripcion'] ?? null,
            'contenido' => $post['contenido'] ?? null,
            'fecha_documento' => $post['fecha_documento'],
            'fecha_vencimiento' => $post['fecha_vencimiento'] ?? null,
            'enviado' => 0,
            'estado' => 'A'
        ];

        if ($documento->insert($data)) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('success', 'Documento registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', $documento->errors());
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

        $documento = new Documento();
        $data['documento'] = $documento->getDocumentoCompleto($id);
        
        if (!$data['documento']) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'Documento no encontrado');
        }

        // Cargar pacientes
        $pacienteModel = new Paciente();
        $data['pacientes'] = $pacienteModel->getPacientesSelect();

        return view('Modulos/documento/editar', $data);
    }

    public function update()
    {
        $documento = new Documento();
        $id = $this->request->getPost('id');

        $validationRules = [
            'paciente_id' => 'required|integer|greater_than[0]',
            'tipo_documento' => 'required|in_list[pauta_nutricional,receta,informe,consentimiento,otro]',
            'titulo' => 'required|string|max_length[200]',
            'fecha_documento' => 'required|valid_date'
        ];

        $validationMessages = [
            'paciente_id' => [
                'required' => 'El paciente es obligatorio.',
                'integer' => 'Debe seleccionar un paciente válido.',
                'greater_than' => 'Debe seleccionar un paciente válido.'
            ],
            'tipo_documento' => [
                'required' => 'El tipo de documento es obligatorio.',
                'in_list' => 'El tipo de documento debe ser: pauta nutricional, receta, informe, consentimiento u otro.'
            ],
            'titulo' => [
                'required' => 'El título del documento es obligatorio.',
                'string' => 'El título debe ser una cadena de texto.',
                'max_length' => 'El título no puede exceder de 200 caracteres.'
            ],
            'fecha_documento' => [
                'required' => 'La fecha del documento es obligatoria.',
                'valid_date' => 'La fecha del documento debe ser una fecha válida.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'paciente_id', 'tipo_documento', 'titulo', 'descripcion', 'contenido',
            'fecha_documento', 'fecha_vencimiento', 'observaciones'
        ]);

        $data = [
            'paciente_id' => $post['paciente_id'],
            'tipo_documento' => $post['tipo_documento'],
            'titulo' => $post['titulo'],
            'descripcion' => $post['descripcion'] ?? null,
            'contenido' => $post['contenido'] ?? null,
            'fecha_documento' => $post['fecha_documento'],
            'fecha_vencimiento' => $post['fecha_vencimiento'] ?? null
        ];

        if ($documento->update($id, $data)) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('success', 'Documento actualizado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al actualizar el documento');
        }
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
        
        if ($documento->delete($id)) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('success', 'Documento eliminado con éxito');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar el documento');
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

        $documento = new Documento();
        $data['documento'] = $documento->getDocumentoCompleto($id);
        
        if (!$data['documento']) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'Documento no encontrado');
        }

        return view('Modulos/documento/detalle', $data);
    }

    public function enviar($id)
    {
        $documento = new Documento();
        $doc = $documento->find($id);
        
        if (!$doc) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('error', 'Documento no encontrado');
        }

        // Aquí se integraría con WhatsApp o Email
        // Por ahora solo marcamos como enviado
        if ($documento->marcarComoEnviado($id, 'sistema')) {
            return redirect()->to(base_url('dashboard/documento/lista'))->with('success', 'Documento marcado como enviado');
        } else {
            return redirect()->back()->with('error', 'Error al enviar el documento');
        }
    }
}
