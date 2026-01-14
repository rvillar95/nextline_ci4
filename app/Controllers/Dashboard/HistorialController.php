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
                'required' => 'El paciente es obligatorio.',
                'integer' => 'Debe seleccionar un paciente válido.',
                'greater_than' => 'Debe seleccionar un paciente válido.'
            ],
            'tipo_registro' => [
                'required' => 'El tipo de registro es obligatorio.',
                'in_list' => 'El tipo de registro debe ser: consulta, seguimiento, control o emergencia.'
            ],
            'fecha_consulta' => [
                'required' => 'La fecha de consulta es obligatoria.',
                'valid_date' => 'La fecha de consulta debe ser una fecha válida.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'paciente_id', 'tipo_registro', 'fecha_consulta', 'hora_consulta',
            'peso_actual', 'altura_actual', 'circunferencia_cintura', 'circunferencia_cadera',
            'grasa_corporal', 'masa_muscular', 'motivo_consulta', 'anamnesis', 'diagnostico',
            'plan_tratamiento', 'recomendaciones', 'observaciones', 'proxima_cita'
        ]);

        // Calcular IMC
        $imc_actual = null;
        if (!empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            $imc_actual = $historial->calcularIMC($post['peso_actual'], $post['altura_actual']);
        }

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
                'required' => 'El paciente es obligatorio.',
                'integer' => 'Debe seleccionar un paciente válido.',
                'greater_than' => 'Debe seleccionar un paciente válido.'
            ],
            'tipo_registro' => [
                'required' => 'El tipo de registro es obligatorio.',
                'in_list' => 'El tipo de registro debe ser: consulta, seguimiento, control o emergencia.'
            ],
            'fecha_consulta' => [
                'required' => 'La fecha de consulta es obligatoria.',
                'valid_date' => 'La fecha de consulta debe ser una fecha válida.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'paciente_id', 'tipo_registro', 'fecha_consulta', 'hora_consulta',
            'peso_actual', 'altura_actual', 'circunferencia_cintura', 'circunferencia_cadera',
            'grasa_corporal', 'masa_muscular', 'motivo_consulta', 'anamnesis', 'diagnostico',
            'plan_tratamiento', 'recomendaciones', 'observaciones', 'proxima_cita'
        ]);

        // Calcular IMC
        $imc_actual = null;
        if (!empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            $imc_actual = $historial->calcularIMC($post['peso_actual'], $post['altura_actual']);
        }

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
            'proxima_cita' => $post['proxima_cita'] ?? null
        ];

        if ($historial->update($id, $data)) {
            return redirect()->to(base_url('dashboard/historial/lista'))->with('success', 'Consulta actualizada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al actualizar la consulta');
        }
    }

    public function eliminar($id = null)
    {
        if (!$id) {
            $id = $this->request->getPost('id');
        }
        
        if (!$id) {
            return redirect()->to(base_url('dashboard/historial/lista'))->with('error', 'ID de registro requerido');
        }
        
        $historial = new HistorialClinico();
        
        if ($historial->delete($id)) {
            return redirect()->to(base_url('dashboard/historial/lista'))->with('success', 'Registro eliminado con éxito');
        } else {
            return redirect()->back()->with('error', 'Error al eliminar el registro');
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

        $historial = new HistorialClinico();
        $data['historial'] = $historial->getHistorialCompleto($id);
        
        if (!$data['historial']) {
            return redirect()->to(base_url('dashboard/historial/lista'))->with('error', 'Registro no encontrado');
        }

        return view('Modulos/historial/detalle', $data);
    }

    /**
     * Vista de comparación de historiales clínicos
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
     * Obtener historiales de un paciente (AJAX)
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

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Obtener historiales sin ordenar primero
        $historiales = $db->table('historial_clinico hc')
            ->select('hc.id, hc.fecha_consulta, hc.hora_consulta, hc.tipo_registro, 
                      hc.peso_actual, hc.altura_actual, hc.imc_actual,
                      hc.circunferencia_cintura, hc.circunferencia_cadera,
                      hc.grasa_corporal, hc.masa_muscular,
                      hc.suma_pliegues, hc.grasa_corporal_calculada,
                      da.fecha as fecha_detalle, da.hora_inicio')
            ->join('detalle_agenda da', 'da.id = hc.detalle_agenda_id', 'left')
            ->where('hc.paciente_id', $pacienteId)
            ->where('hc.nutricionista_id', $usuario_id)
            ->where('hc.estado', 'A')
            ->get()
            ->getResultArray();

        // Ordenar manualmente para asegurar orden correcto (más antigua a más nueva)
        usort($historiales, function($a, $b) {
            // Función auxiliar para convertir fecha a timestamp
            $convertirFechaATimestamp = function($fechaStr) {
                if (empty($fechaStr) || $fechaStr === '0000-00-00' || $fechaStr === 'N/A') {
                    return 0;
                }
                
                // Si ya está en formato YYYY-MM-DD, convertir directamente
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $fechaStr)) {
                    return strtotime($fechaStr);
                }
                
                // Si está en formato DD-MM-YYYY, convertir
                if (preg_match('/^(\d{2})-(\d{2})-(\d{4})/', $fechaStr, $matches)) {
                    return strtotime($matches[3] . '-' . $matches[2] . '-' . $matches[1]);
                }
                
                // Si está en formato DD/MM/YYYY, convertir
                if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})/', $fechaStr, $matches)) {
                    return strtotime($matches[3] . '-' . $matches[2] . '-' . $matches[1]);
                }
                
                // Intentar parseo directo
                $timestamp = strtotime($fechaStr);
                return $timestamp !== false ? $timestamp : 0;
            };
            
            // Usar fecha_consulta como prioridad, si no existe usar fecha_detalle
            $fechaA = $a['fecha_consulta'] ?? $a['fecha_detalle'] ?? null;
            $fechaB = $b['fecha_consulta'] ?? $b['fecha_detalle'] ?? null;
            
            // Convertir fechas a timestamps para comparar correctamente
            $timestampA = $convertirFechaATimestamp($fechaA);
            $timestampB = $convertirFechaATimestamp($fechaB);
            
            // Comparar timestamps
            if ($timestampA !== $timestampB) {
                return $timestampA <=> $timestampB; // Ordenar de más antigua (menor timestamp) a más nueva (mayor timestamp)
            }
            
            // Si las fechas son iguales, ordenar por hora
            $horaA = $a['hora_consulta'] ?? $a['hora_inicio'] ?? '00:00:00';
            $horaB = $b['hora_consulta'] ?? $b['hora_inicio'] ?? '00:00:00';
            
            return strcmp($horaA, $horaB);
        });

        // Log para debugging
        log_message('info', 'Historiales ordenados para paciente ID: ' . $pacienteId);
        foreach ($historiales as $idx => $h) {
            $fecha = $h['fecha_consulta'] ?? $h['fecha_detalle'] ?? 'N/A';
            log_message('info', '  [' . $idx . '] ID: ' . $h['id'] . ', Fecha: ' . $fecha);
        }

        return $this->response->setJSON($historiales);
    }

    /**
     * Obtener datos para comparación de múltiples historiales (AJAX)
     */
    public function compararHistoriales()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $historialIds = $this->request->getPost('historial_ids');
        if (!$historialIds || !is_array($historialIds) || count($historialIds) < 2) {
            return $this->response->setJSON(['error' => 'Se requieren al menos 2 historiales para comparar'])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $usuario_id = session()->get('usuario')['id'];

        // Obtener historiales sin ordenar primero
        $historiales = $db->table('historial_clinico hc')
            ->select('hc.id, hc.fecha_consulta, hc.hora_consulta, hc.tipo_registro,
                      hc.peso_actual, hc.altura_actual, hc.imc_actual,
                      hc.circunferencia_cintura, hc.circunferencia_cadera,
                      hc.grasa_corporal, hc.masa_muscular,
                      hc.suma_pliegues, hc.grasa_corporal_calculada,
                      hc.pliegue_tricipital, hc.pliegue_bicipital, hc.pliegue_subescapular,
                      hc.pliegue_suprailíaco, hc.pliegue_abdominal,
                      hc.pliegue_muslo_anterior, hc.pliegue_pantorrilla_medial,
                      da.fecha as fecha_detalle, da.hora_inicio')
            ->join('detalle_agenda da', 'da.id = hc.detalle_agenda_id', 'left')
            ->whereIn('hc.id', $historialIds)
            ->where('hc.nutricionista_id', $usuario_id)
            ->where('hc.estado', 'A')
            ->get()
            ->getResultArray();

        // Ordenar manualmente para asegurar orden correcto (más antigua a más nueva)
        usort($historiales, function($a, $b) {
            // Función auxiliar para convertir fecha a timestamp
            $convertirFechaATimestamp = function($fechaStr) {
                if (empty($fechaStr) || $fechaStr === '0000-00-00' || $fechaStr === 'N/A') {
                    return 0;
                }
                
                // Si ya está en formato YYYY-MM-DD, convertir directamente
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $fechaStr)) {
                    return strtotime($fechaStr);
                }
                
                // Si está en formato DD-MM-YYYY, convertir
                if (preg_match('/^(\d{2})-(\d{2})-(\d{4})/', $fechaStr, $matches)) {
                    return strtotime($matches[3] . '-' . $matches[2] . '-' . $matches[1]);
                }
                
                // Si está en formato DD/MM/YYYY, convertir
                if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})/', $fechaStr, $matches)) {
                    return strtotime($matches[3] . '-' . $matches[2] . '-' . $matches[1]);
                }
                
                // Intentar parseo directo
                $timestamp = strtotime($fechaStr);
                return $timestamp !== false ? $timestamp : 0;
            };
            
            // Usar fecha_consulta como prioridad, si no existe usar fecha_detalle
            $fechaA = $a['fecha_consulta'] ?? $a['fecha_detalle'] ?? null;
            $fechaB = $b['fecha_consulta'] ?? $b['fecha_detalle'] ?? null;
            
            // Convertir fechas a timestamps para comparar correctamente
            $timestampA = $convertirFechaATimestamp($fechaA);
            $timestampB = $convertirFechaATimestamp($fechaB);
            
            // Comparar timestamps
            if ($timestampA !== $timestampB) {
                return $timestampA <=> $timestampB; // Ordenar de más antigua (menor timestamp) a más nueva (mayor timestamp)
            }
            
            // Si las fechas son iguales, ordenar por hora
            $horaA = $a['hora_consulta'] ?? $a['hora_inicio'] ?? '00:00:00';
            $horaB = $b['hora_consulta'] ?? $b['hora_inicio'] ?? '00:00:00';
            
            return strcmp($horaA, $horaB);
        });

        // Log para debugging
        log_message('info', 'Historiales ordenados para comparación. IDs: ' . implode(', ', $historialIds));
        foreach ($historiales as $idx => $h) {
            $fecha = $h['fecha_consulta'] ?? $h['fecha_detalle'] ?? 'N/A';
            log_message('info', '  [' . $idx . '] ID: ' . $h['id'] . ', Fecha: ' . $fecha);
        }

        // Incluir el nuevo token CSRF en la respuesta para que el frontend lo actualice
        $response = [
            'historiales' => $historiales,
            'csrf_token' => csrf_hash()
        ];

        return $this->response->setJSON($response);
    }
}
