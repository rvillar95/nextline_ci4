<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\HistorialClinico;
use App\Models\Paciente;
use App\Models\ModuloDetalle;
use App\Traits\MaintainsFilters;

class HistorialController extends BaseController
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

        // Cargar tags sugeridos para el autocompletado
        $historialModel = new HistorialClinico();
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        $data['tags_sugeridos'] = $historialModel->getTagsMasUsados($empresaId, 20);

        return view('Modulos/historial/lista', $data);
    }

    public function getHistorial()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $historial = new HistorialClinico();
        $draw = intval($this->request->getGet("draw"));
        
        $paciente_id = $this->request->getGet('paciente_id');
        $tipo_registro = $this->request->getGet('tipo_registro');
        $estado = $this->request->getGet('estado');
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');
        $tags_busqueda = $this->request->getGet('tags');
        
        // Obtener empresa_id del usuario
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        
        // Si hay búsqueda por tags, usar el método buscarPorTags
        if (!empty($tags_busqueda)) {
            $rows = $historial->buscarPorTags($tags_busqueda, $empresaId);
        } else {
            $query = $historial;
            
            if (!empty($estado)) {
                $query = $query->where('estado', $estado);
            } else {
                $query = $query->where('estado', 'A');
            }
            
            if (!empty($paciente_id)) {
                $query->where('paciente_id', $paciente_id);
            }

            if (!empty($tipo_registro)) {
                $query->where('tipo_registro', $tipo_registro);
            }

            if (!empty($fecha_desde)) {
                $query->where('fecha_consulta >=', $fecha_desde);
            }

            if (!empty($fecha_hasta)) {
                $query->where('fecha_consulta <=', $fecha_hasta);
            }
            
            $rows = $query->orderBy('fecha_consulta', 'DESC')
                         ->orderBy('hora_consulta', 'DESC')
                         ->findAll();
        }
        
        // Aplicar filtros adicionales si hay búsqueda por tags
        if (!empty($tags_busqueda) && !empty($rows)) {
            $rowsFiltrados = [];
            foreach ($rows as $row) {
                $incluir = true;
                
                if (!empty($paciente_id) && $row->paciente_id != $paciente_id) {
                    $incluir = false;
                }
                
                if (!empty($tipo_registro) && $row->tipo_registro != $tipo_registro) {
                    $incluir = false;
                }
                
                if (!empty($estado) && $row->estado != $estado) {
                    $incluir = false;
                } elseif (empty($estado) && $row->estado != 'A') {
                    $incluir = false;
                }
                
                if (!empty($fecha_desde) && $row->fecha_consulta < $fecha_desde) {
                    $incluir = false;
                }
                
                if (!empty($fecha_hasta) && $row->fecha_consulta > $fecha_hasta) {
                    $incluir = false;
                }
                
                if ($incluir) {
                    $rowsFiltrados[] = $row;
                }
            }
            $rows = $rowsFiltrados;
        }
        
        // Ordenar resultados por fecha y hora (descendente)
        if (!empty($rows)) {
            usort($rows, function($a, $b) {
                $fechaA = strtotime($a->fecha_consulta . ' ' . ($a->hora_consulta ?? '00:00:00'));
                $fechaB = strtotime($b->fecha_consulta . ' ' . ($b->hora_consulta ?? '00:00:00'));
                if ($fechaA == $fechaB) {
                    return 0;
                }
                return ($fechaA > $fechaB) ? -1 : 1;
            });
        }

        $pacienteModel = new Paciente();
        $data = array();
        foreach ($rows as $r) {
            $paciente = $pacienteModel->find($r->paciente_id);
            $nombrePaciente = $paciente ? trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? '')) : 'N/A';

            $tipoBadge = match ($r->tipo_registro) {
                'consulta' => '<span class="badge bg-primary">Consulta</span>',
                'seguimiento' => '<span class="badge bg-info">Seguimiento</span>',
                'control' => '<span class="badge bg-success">Control</span>',
                'emergencia' => '<span class="badge bg-danger">Emergencia</span>',
                default => '<span class="badge bg-secondary">N/A</span>',
            };

            $pesoInfo = $r->peso_actual ? "Peso: {$r->peso_actual} kg" : '';
            $imcInfo = $r->imc_actual ? "IMC: {$r->imc_actual}" : '';
            $medidas = trim($pesoInfo . ($imcInfo ? ' | ' . $imcInfo : ''));

            // Obtener tags como badges
            $tagsHtml = '';
            if (!empty($r->tags)) {
                $tags = json_decode($r->tags, true);
                if (is_array($tags) && !empty($tags)) {
                    $tagsArray = [];
                    foreach ($tags as $tag) {
                        $tagsArray[] = '<span class="badge bg-secondary me-1">' . esc($tag) . '</span>';
                    }
                    $tagsHtml = implode('', $tagsArray);
                }
            }

            $estadoBadge = $r->estado == 'A' 
                ? '<span class="badge bg-success">Activo</span>' 
                : '<span class="badge bg-danger">Inactivo</span>';

            $botones = '';
            if ($r->estado == 'A') {
                $botones = '<button class="btn btn-sm btn-outline-primary" onclick="editarHistorial(' . $r->id . ')">Editar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verHistorial(' . $r->id . ')">Ver</button> ' .
                          '<button class="btn btn-sm btn-outline-danger" onclick="eliminarHistorial(' . $r->id . ')">Eliminar</button>';
            } else {
                $botones = '<button class="btn btn-sm btn-outline-info" onclick="verHistorial(' . $r->id . ')">Ver</button>';
            }

            $data[] = array(
                esc($nombrePaciente),
                esc(date('d/m/Y', strtotime($r->fecha_consulta))),
                esc($r->hora_consulta ?? ''),
                $tipoBadge,
                esc($medidas),
                esc(substr($r->motivo_consulta ?? '', 0, 50)) . (strlen($r->motivo_consulta ?? '') > 50 ? '...' : ''),
                $tagsHtml ?: '<span class="text-muted">-</span>',
                $estadoBadge,
                $botones
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $historial->where('estado', 'A')->countAllResults(),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    public function getHistorialPaciente($pacienteId)
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $historial = new HistorialClinico();
        $registros = $historial->getHistorialPorPaciente($pacienteId);

        $data = [];
        foreach ($registros as $r) {
            $data[] = [
                'id' => $r->id,
                'fecha' => $r->fecha_consulta,
                'hora' => $r->hora_consulta,
                'tipo' => $r->tipo_registro,
                'peso' => $r->peso_actual,
                'imc' => $r->imc_actual,
                'motivo' => $r->motivo_consulta
            ];
        }

        return $this->response->setJSON($data);
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

        // Cargar tags sugeridos
        $historialModel = new HistorialClinico();
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        $data['tags_sugeridos'] = $historialModel->getTagsMasUsados($empresaId, 20);

        return view('Modulos/historial/registro', $data);
    }

    public function registrar()
    {
        $historial = new HistorialClinico();

        $validationRules = [
            'paciente_id' => 'required|integer|greater_than[0]',
            'tipo_registro' => 'required|in_list[consulta,seguimiento,control,emergencia]',
            'fecha_consulta' => 'required|valid_date'
        ];

        $validationMessages = [
            'paciente_id' => [
                'required' => 'El paciente es obligatorio.'
            ],
            'fecha_consulta' => [
                'required' => 'La fecha de consulta es obligatoria.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost();

        // Calcular IMC si hay peso y altura
        $imc_actual = null;
        if (!empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            $imc_actual = $historial->calcularIMC($post['peso_actual'], $post['altura_actual']);
        }

        // Procesar tags
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        $tagsJson = $historial->procesarTags($post['tags'] ?? '', $empresaId);

        $data = [
            'paciente_id' => $post['paciente_id'],
            'nutricionista_id' => session()->get('usuario')['id'],
            'tipo_registro' => $post['tipo_registro'],
            'fecha_consulta' => $post['fecha_consulta'],
            'hora_consulta' => $post['hora_consulta'] ?? null,
            'peso_actual' => $post['peso_actual'] ?? null,
            'altura_actual' => $post['altura_actual'] ?? null,
            'imc_actual' => $imc_actual,
            'circunferencia_cintura' => $post['circunferencia_cintura'] ?? null,
            'circunferencia_cadera' => $post['circunferencia_cadera'] ?? null,
            'grasa_corporal' => $post['grasa_corporal'] ?? null,
            'masa_muscular' => $post['masa_muscular'] ?? null,
            'motivo_consulta' => $post['motivo_consulta'] ?? null,
            'anamnesis' => $post['anamnesis'] ?? null,
            'diagnostico' => $post['diagnostico'] ?? null,
            'plan_tratamiento' => $post['plan_tratamiento'] ?? null,
            'recomendaciones' => $post['recomendaciones'] ?? null,
            'observaciones' => $post['observaciones'] ?? null,
            'tags' => $tagsJson,
            'proxima_cita' => $post['proxima_cita'] ?? null,
            'estado' => 'A'
        ];

        if ($historial->insert($data)) {
            return redirect()->to(base_url('dashboard/historial/lista'))->with('success', 'Consulta registrada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', $historial->errors());
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

        $historial = new HistorialClinico();
        $data['historial'] = $historial->getHistorialCompleto($id);
        
        if (!$data['historial']) {
            return redirect()->to(base_url('dashboard/historial/lista'))->with('error', 'Registro no encontrado');
        }

        // Cargar pacientes
        $pacienteModel = new Paciente();
        $data['pacientes'] = $pacienteModel->getPacientesSelect();

        // Cargar tags como string para el formulario
        $data['tags_string'] = $historial->getTagsAsString($id);

        // Cargar tags sugeridos
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        $data['tags_sugeridos'] = $historial->getTagsMasUsados($empresaId, 20);

        return view('Modulos/historial/editar', $data);
    }

    public function update()
    {
        $historial = new HistorialClinico();
        $id = $this->request->getPost('id');

        $validationRules = [
            'paciente_id' => 'required|integer|greater_than[0]',
            'tipo_registro' => 'required|in_list[consulta,seguimiento,control,emergencia]',
            'fecha_consulta' => 'required|valid_date'
        ];

        $validationMessages = [
            'paciente_id' => [
                'required' => 'El paciente es obligatorio.'
            ],
            'fecha_consulta' => [
                'required' => 'La fecha de consulta es obligatoria.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost();

        // Calcular IMC si hay peso y altura
        $imc_actual = null;
        if (!empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            $imc_actual = $historial->calcularIMC($post['peso_actual'], $post['altura_actual']);
        }

        // Procesar tags
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        $tagsJson = $historial->procesarTags($post['tags'] ?? '', $empresaId);

        $data = [
            'paciente_id' => $post['paciente_id'],
            'tipo_registro' => $post['tipo_registro'],
            'fecha_consulta' => $post['fecha_consulta'],
            'hora_consulta' => $post['hora_consulta'] ?? null,
            'peso_actual' => $post['peso_actual'] ?? null,
            'altura_actual' => $post['altura_actual'] ?? null,
            'imc_actual' => $imc_actual,
            'circunferencia_cintura' => $post['circunferencia_cintura'] ?? null,
            'circunferencia_cadera' => $post['circunferencia_cadera'] ?? null,
            'grasa_corporal' => $post['grasa_corporal'] ?? null,
            'masa_muscular' => $post['masa_muscular'] ?? null,
            'motivo_consulta' => $post['motivo_consulta'] ?? null,
            'anamnesis' => $post['anamnesis'] ?? null,
            'diagnostico' => $post['diagnostico'] ?? null,
            'plan_tratamiento' => $post['plan_tratamiento'] ?? null,
            'recomendaciones' => $post['recomendaciones'] ?? null,
            'observaciones' => $post['observaciones'] ?? null,
            'tags' => $tagsJson,
            'proxima_cita' => $post['proxima_cita'] ?? null
        ];

        if ($historial->update($id, $data)) {
            return redirect()->to(base_url('dashboard/historial/editar/' . $id))->with('success', 'Consulta actualizada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', $historial->errors());
        }
    }

    /**
     * Obtener tags sugeridos para autocompletado
     */
    public function getTagsSugeridos()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $historial = new HistorialClinico();
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        
        $q = $this->request->getGet('q') ?? '';
        $tags = $historial->getTagsMasUsados($empresaId, 50);

        $results = [];
        foreach ($tags as $tag) {
            $tagDisplay = $tag['tag_display'] ?? $tag['tag'];
            
            // Si hay búsqueda, filtrar
            if (empty($q) || stripos($tagDisplay, $q) !== false || stripos($tag['tag'], $q) !== false) {
                $results[] = [
                    'id' => $tagDisplay,
                    'text' => $tagDisplay,
                    'usos' => $tag['usos'] ?? 0
                ];
            }
        }

        // Ordenar por usos (más usados primero)
        usort($results, function($a, $b) {
            return ($b['usos'] ?? 0) - ($a['usos'] ?? 0);
        });

        return $this->response->setJSON(['results' => $results]);
    }

    /**
     * Vista para comparar historiales
     */
    public function comparar()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        // Obtener lista de pacientes con historiales
        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];
        
        // Obtener lista de pacientes con historiales
        $pacientes = $db->query("
            SELECT p.id, p.nombre, p.apellido, COUNT(hc.id) as total_historiales
            FROM historial_clinico hc
            INNER JOIN pacientes p ON p.id = hc.paciente_id
            WHERE hc.nutricionista_id = ?
              AND hc.estado = 'A'
            GROUP BY p.id, p.nombre, p.apellido
            HAVING COUNT(hc.id) > 0
            ORDER BY p.nombre ASC
        ", [$usuario_id])->getResultArray();
        
        $data['pacientes'] = $pacientes;

        return view('Modulos/historial/comparar', $data);
    }

    /**
     * Obtener historiales de un paciente para comparación
     */
    public function getHistorialesPaciente()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $pacienteId = $this->request->getGet('paciente_id');
        if (!$pacienteId) {
            return $this->response->setJSON(['error' => 'ID de paciente requerido'])->setStatusCode(400);
        }

        $historial = new HistorialClinico();
        $registros = $historial->getHistorialPorPaciente($pacienteId);

        $data = [];
        foreach ($registros as $r) {
            $data[] = [
                'id' => $r->id,
                'fecha_consulta' => $r->fecha_consulta,
                'hora_consulta' => $r->hora_consulta,
                'tipo_registro' => $r->tipo_registro,
                'peso_actual' => $r->peso_actual,
                'altura_actual' => $r->altura_actual,
                'imc_actual' => $r->imc_actual,
                'circunferencia_cintura' => $r->circunferencia_cintura,
                'circunferencia_cadera' => $r->circunferencia_cadera,
                'grasa_corporal' => $r->grasa_corporal,
                'masa_muscular' => $r->masa_muscular,
                'motivo_consulta' => $r->motivo_consulta,
                'anamnesis' => $r->anamnesis,
                'diagnostico' => $r->diagnostico,
                'plan_tratamiento' => $r->plan_tratamiento,
                'recomendaciones' => $r->recomendaciones,
                'observaciones' => $r->observaciones
            ];
        }

        return $this->response->setJSON($data);
    }

    /**
     * Comparar historiales seleccionados
     */
    public function compararHistoriales()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $historialIds = $this->request->getPost('historial_ids');
        if (!$historialIds || !is_array($historialIds) || count($historialIds) < 2) {
            return $this->response->setJSON(['error' => 'Debe seleccionar al menos 2 historiales para comparar'])->setStatusCode(400);
        }

        $historial = new HistorialClinico();
        $historiales = [];

        foreach ($historialIds as $id) {
            $h = $historial->getHistorialCompleto($id);
            if ($h) {
                $historiales[] = [
                    'id' => $h->id,
                    'fecha_consulta' => $h->fecha_consulta,
                    'hora_consulta' => $h->hora_consulta,
                    'tipo_registro' => $h->tipo_registro,
                    'peso_actual' => $h->peso_actual,
                    'altura_actual' => $h->altura_actual,
                    'imc_actual' => $h->imc_actual,
                    'circunferencia_cintura' => $h->circunferencia_cintura,
                    'circunferencia_cadera' => $h->circunferencia_cadera,
                    'grasa_corporal' => $h->grasa_corporal,
                    'masa_muscular' => $h->masa_muscular,
                    'motivo_consulta' => $h->motivo_consulta,
                    'anamnesis' => $h->anamnesis,
                    'diagnostico' => $h->diagnostico,
                    'plan_tratamiento' => $h->plan_tratamiento,
                    'recomendaciones' => $h->recomendaciones,
                    'observaciones' => $h->observaciones,
                    'paciente' => $h->paciente ? [
                        'id' => $h->paciente->id,
                        'nombre' => trim(($h->paciente->nombre ?? '') . ' ' . ($h->paciente->apellido ?? ''))
                    ] : null
                ];
            }
        }

        // Ordenar por fecha (más antigua primero)
        usort($historiales, function($a, $b) {
            $fechaA = strtotime($a['fecha_consulta']);
            $fechaB = strtotime($b['fecha_consulta']);
            if ($fechaA == $fechaB) {
                return strtotime($a['hora_consulta'] ?? '00:00:00') - strtotime($b['hora_consulta'] ?? '00:00:00');
            }
            return $fechaA - $fechaB;
        });

        return $this->response->setJSON([
            'historiales' => $historiales,
            'csrf_token' => csrf_hash()
        ]);
    }
}