<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\HistorialClinico;
use App\Models\Paciente;
use App\Models\ModuloDetalle;
use App\Models\MetodoCalculo;
use App\Services\ComposicionCorporalService;
use App\Services\AccesoService;
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

        // Si se llegó desde la vista de consulta, permitir volver a ella
        $data['retorno_consulta_id'] = $this->request->getGet('retorno') === 'consulta' ? (int) $this->request->getGet('id') : 0;

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
        $usuario_id = $usuario['id'] ?? null;
        
        // Si hay búsqueda por tags, usar el método buscarPorTags
        if (!empty($tags_busqueda)) {
            $rows = $historial->buscarPorTags($tags_busqueda, $empresaId);
            // filtrar por nutricionista para que cada usuario vea solo sus historiales
            if ($usuario_id) {
                $rows = array_filter($rows, function($r) use ($usuario_id) {
                    return isset($r->nutricionista_id) && $r->nutricionista_id == $usuario_id;
                });
                // Reindex array
                $rows = array_values($rows);
            }
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
            
            // Asegurar que sólo se muestren historiales del nutricionista logueado
            if ($usuario_id) {
                $query->where('nutricionista_id', $usuario_id);
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

        // Calcular recordsTotal considerando nutricionista
        $totalModel = new HistorialClinico();
        $totalModel = $totalModel->where('estado', 'A');
        if ($usuario_id) {
            $totalModel = $totalModel->where('nutricionista_id', $usuario_id);
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $totalModel->countAllResults(),
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

        // Verificar si hay consulta activa (cronómetro andando) para asociar automáticamente
        $detalleAgendaIdActiva = null;
        $usuario_id = session()->get('usuario')['id'];
        $db = \Config\Database::connect();
        
        $consultaActiva = $db->table('detalle_agenda da')
            ->select('da.id, da.paciente_id')
            ->where('da.usuario_id', $usuario_id)
            ->where('da.fecha_inicio_real IS NOT NULL')
            ->where('da.fecha_fin_real IS NULL')
            ->where('da.estado_cita', 'en_proceso')
            ->where('da.paciente_id', $post['paciente_id']) // Debe ser del mismo paciente
            ->orderBy('da.fecha_inicio_real', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        
        if ($consultaActiva) {
            $detalleAgendaIdActiva = $consultaActiva->id;
        }

        // Calcular IMC si hay peso y altura
        $imc_actual = null;
        if (!empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            $imc_actual = $historial->calcularIMC($post['peso_actual'], $post['altura_actual']);
        }

        // Calcular suma de pliegues (igual que en agenda/guardarMediciones)
        $plieguesCampos = [
            'pliegue_tricipital', 'pliegue_bicipital', 'pliegue_subescapular', 'pliegue_suprailíaco',
            'pliegue_supraespinal', 'pliegue_abdominal', 'pliegue_muslo_anterior', 'pliegue_pantorrilla_medial',
            'pliegue_pectoral', 'pliegue_axilar_medio', 'pliegue_muslo_medial'
        ];
        $suma_pliegues = null;
        $suma = 0;
        foreach ($plieguesCampos as $campo) {
            $v = isset($post[$campo]) && $post[$campo] !== '' ? (float) $post[$campo] : 0;
            if ($v > 0) $suma += $v;
        }
        if ($suma > 0) {
            $suma_pliegues = round($suma, 2);
        }

        // Procesar tags
        $usuario = session()->get('usuario');
        $empresaId = $usuario['empresa_id'] ?? null;
        $tagsJson = $historial->procesarTags($post['tags'] ?? '', $empresaId);

        $data = [
            'paciente_id' => $post['paciente_id'],
            'nutricionista_id' => $usuario_id,
            'detalle_agenda_id' => $detalleAgendaIdActiva, // Asociar a consulta activa si existe
            'tipo_registro' => $post['tipo_registro'],
            'fecha_consulta' => $post['fecha_consulta'],
            'hora_consulta' => $post['hora_consulta'] ?? null,
            'peso_actual' => $post['peso_actual'] ?? null,
            'altura_actual' => $post['altura_actual'] ?? null,
            'altura_sentado' => $post['altura_sentado'] ?? null,
            'imc_actual' => $imc_actual,
            'circunferencia_cintura' => $post['circunferencia_cintura'] ?? null,
            'circunferencia_cadera' => $post['circunferencia_cadera'] ?? null,
            'circunferencia_brazo_relajado' => $post['circunferencia_brazo_relajado'] ?? null,
            'circunferencia_brazo_contraido' => $post['circunferencia_brazo_contraido'] ?? null,
            'circunferencia_muslo_medio' => $post['circunferencia_muslo_medio'] ?? null,
            'circunferencia_pantorrilla' => $post['circunferencia_pantorrilla'] ?? null,
            'circunferencia_cuello' => $post['circunferencia_cuello'] ?? null,
            'circunferencia_torax' => $post['circunferencia_torax'] ?? null,
            'circunferencia_cabeza' => $post['circunferencia_cabeza'] ?? null,
            'circunferencia_antebrazo_maximo' => $post['circunferencia_antebrazo_maximo'] ?? null,
            'circunferencia_muslo_maximo' => $post['circunferencia_muslo_maximo'] ?? null,
            'circunferencia_muneca' => $post['circunferencia_muneca'] ?? null,
            'diametro_biacromial' => $post['diametro_biacromial'] ?? null,
            'diametro_bi_iliocristal' => $post['diametro_bi_iliocristal'] ?? null,
            'diametro_torax_transverso' => $post['diametro_torax_transverso'] ?? null,
            'diametro_torax_anteroposterior' => $post['diametro_torax_anteroposterior'] ?? null,
            'diametro_humero' => $post['diametro_humero'] ?? null,
            'diametro_femur' => $post['diametro_femur'] ?? null,
            'diametro_muneca' => $post['diametro_muneca'] ?? null,
            'diametro_tobillo' => $post['diametro_tobillo'] ?? null,
            'grasa_corporal' => $post['grasa_corporal'] ?? null,
            'masa_muscular' => $post['masa_muscular'] ?? null,
            'pliegue_tricipital' => $post['pliegue_tricipital'] ?? null,
            'pliegue_bicipital' => $post['pliegue_bicipital'] ?? null,
            'pliegue_subescapular' => $post['pliegue_subescapular'] ?? null,
            'pliegue_suprailíaco' => $post['pliegue_suprailíaco'] ?? null,
            'pliegue_supraespinal' => $post['pliegue_supraespinal'] ?? null,
            'pliegue_abdominal' => $post['pliegue_abdominal'] ?? null,
            'pliegue_muslo_anterior' => $post['pliegue_muslo_anterior'] ?? null,
            'pliegue_pantorrilla_medial' => $post['pliegue_pantorrilla_medial'] ?? null,
            'pliegue_pectoral' => $post['pliegue_pectoral'] ?? null,
            'pliegue_axilar_medio' => $post['pliegue_axilar_medio'] ?? null,
            'pliegue_muslo_medial' => $post['pliegue_muslo_medial'] ?? null,
            'suma_pliegues' => $suma_pliegues,
            'motivo_consulta' => $post['motivo_consulta'] ?? null,
            'anamnesis' => $post['anamnesis'] ?? null,
            'diagnostico' => $post['diagnostico'] ?? null,
            'plan_tratamiento' => $post['plan_tratamiento'] ?? null,
            'recomendaciones' => $post['recomendaciones'] ?? null,
            'observaciones' => $post['observaciones'] ?? null,
            'tags' => $tagsJson,
            'proxima_cita' => $post['proxima_cita'] ?? null,
            'estado' => 'A',
            'anamnesis_clinica' => !empty($post['anamnesis_clinica']) ? $post['anamnesis_clinica'] : null,
            'anamnesis_alimentaria' => !empty($post['anamnesis_alimentaria']) ? $post['anamnesis_alimentaria'] : null,
            'recordatorio_24h' => !empty($post['recordatorio_24h']) ? $post['recordatorio_24h'] : null
        ];

        $nuevoId = $historial->insert($data);
        if ($nuevoId) {
            $db = \Config\Database::connect();
            // Exámenes bioquímicos
            $examenModel = new \App\Models\HistorialExamenBioquimico();
            $examenesRaw = $post['examenes_bioquimicos'] ?? '';
            if (is_string($examenesRaw) && $examenesRaw !== '') {
                $examenes = json_decode($examenesRaw, true);
                if (is_array($examenes)) {
                    foreach ($examenes as $row) {
                        if (empty($row['nombre']) && empty($row['valor']) && empty($row['fecha_interpretacion'])) continue;
                        $examenModel->insert([
                            'historial_clinico_id' => $nuevoId,
                            'nombre' => $row['nombre'] ?? null,
                            'valor' => $row['valor'] ?? null,
                            'fecha_interpretacion' => $row['fecha_interpretacion'] ?? null
                        ]);
                    }
                }
            }
            // Tendencia de consumo
            $tendenciaModel = new \App\Models\HistorialTendenciaConsumo();
            $tendenciaRaw = $post['tendencia_consumo'] ?? '';
            if (is_string($tendenciaRaw) && $tendenciaRaw !== '') {
                $tendenciaRows = json_decode($tendenciaRaw, true);
                if (is_array($tendenciaRows)) {
                    foreach ($tendenciaRows as $row) {
                        $grupo = $row['grupo'] ?? null;
                        if (empty($grupo)) continue;
                        $tendenciaModel->insert([
                            'historial_clinico_id' => $nuevoId,
                            'grupo' => $grupo,
                            'preferencia' => $row['preferencia'] ?? null,
                            'alergia_intolerancia' => $row['alergia_intolerancia'] ?? null
                        ]);
                    }
                }
            }
            return redirect()->to(base_url('dashboard/historial/lista'))->with('success', 'Consulta registrada con éxito');
        }
        return redirect()->back()->withInput()->with('errors', $historial->errors());
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

        // Cargar métodos de cálculo disponibles según el plan
        $perfilId = $usuario['perfil_id'];
        $rutasPermitidas = $modulo->getAllowedByPerfil($perfilId, $empresaId);
        
        // Filtrar solo las rutas de métodos de cálculo
        $metodosDisponibles = [];
        $metodosModel = new MetodoCalculo();
        $todosMetodos = $metodosModel->getMetodosActivos();
        
        foreach ($todosMetodos as $metodo) {
            $rutaMetodo = '/calcular-' . $metodo->slug;
            $tieneAcceso = false;
            
            foreach ($rutasPermitidas as $rutaPermitida) {
                if ($rutaPermitida['detalle_ruta'] === $rutaMetodo && $rutaPermitida['permisos']['ver']) {
                    $tieneAcceso = true;
                    break;
                }
            }
            
            $metodosDisponibles[] = [
                'id' => $metodo->id,
                'nombre' => $metodo->nombre,
                'slug' => $metodo->slug,
                'componentes' => $metodo->componentes,
                'descripcion' => $metodo->descripcion,
                'precio_mensual' => $metodo->precio_mensual,
                'es_addon' => $metodo->es_addon,
                'disponible' => $tieneAcceso
            ];
        }
        
        $data['metodos_calculo'] = $metodosDisponibles;

        // Ficha de ingreso: exámenes bioquímicos y tendencia de consumo (igual que agenda/consulta)
        $data['examenes_bioquimicos'] = [];
        $data['tendencia_consumo'] = [];
        $data['tendencia_grupos'] = \App\Models\HistorialTendenciaConsumo::getGrupos();
        if (!empty($data['historial']->id)) {
            $examenModel = new \App\Models\HistorialExamenBioquimico();
            $data['examenes_bioquimicos'] = $examenModel->getPorHistorial($data['historial']->id);
            $tendenciaModel = new \App\Models\HistorialTendenciaConsumo();
            $data['tendencia_consumo'] = $tendenciaModel->getPorHistorial($data['historial']->id);
        }

        // Objeto tipo "cita" para Calorimetría y Plan Alimentario (mismas vistas que en agenda/consulta)
        $paciente = $data['historial']->paciente ?? null;
        $data['cita'] = (object)[
            'id' => $data['historial']->detalle_agenda_id ?? 0,
            'nombre' => $paciente->nombre ?? '',
            'apellido' => $paciente->apellido ?? '',
            'genero' => $paciente->genero ?? null,
            'fecha_nacimiento' => $paciente->fecha_nacimiento ?? null,
        ];

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
            'altura_sentado' => $post['altura_sentado'] ?? null,
            'imc_actual' => $imc_actual,
            'circunferencia_cintura' => $post['circunferencia_cintura'] ?? null,
            'circunferencia_cadera' => $post['circunferencia_cadera'] ?? null,
            'circunferencia_brazo_relajado' => $post['circunferencia_brazo_relajado'] ?? null,
            'circunferencia_brazo_contraido' => $post['circunferencia_brazo_contraido'] ?? null,
            'circunferencia_muslo_medio' => $post['circunferencia_muslo_medio'] ?? null,
            'circunferencia_pantorrilla' => $post['circunferencia_pantorrilla'] ?? null,
            'circunferencia_cuello' => $post['circunferencia_cuello'] ?? null,
            'circunferencia_torax' => $post['circunferencia_torax'] ?? null,
            'circunferencia_cabeza' => $post['circunferencia_cabeza'] ?? null,
            'circunferencia_antebrazo_maximo' => $post['circunferencia_antebrazo_maximo'] ?? null,
            'circunferencia_muslo_maximo' => $post['circunferencia_muslo_maximo'] ?? null,
            'circunferencia_muneca' => $post['circunferencia_muneca'] ?? null,
            'diametro_biacromial' => $post['diametro_biacromial'] ?? null,
            'diametro_bi_iliocristal' => $post['diametro_bi_iliocristal'] ?? null,
            'diametro_torax_transverso' => $post['diametro_torax_transverso'] ?? null,
            'diametro_torax_anteroposterior' => $post['diametro_torax_anteroposterior'] ?? null,
            'diametro_humero' => $post['diametro_humero'] ?? null,
            'diametro_femur' => $post['diametro_femur'] ?? null,
            'diametro_muneca' => $post['diametro_muneca'] ?? null,
            'diametro_tobillo' => $post['diametro_tobillo'] ?? null,
            'grasa_corporal' => $post['grasa_corporal'] ?? null,
            'masa_muscular' => $post['masa_muscular'] ?? null,
            'pliegue_tricipital' => $post['pliegue_tricipital'] ?? null,
            'pliegue_bicipital' => $post['pliegue_bicipital'] ?? null,
            'pliegue_subescapular' => $post['pliegue_subescapular'] ?? null,
            'pliegue_suprailíaco' => $post['pliegue_suprailíaco'] ?? null,
            'pliegue_supraespinal' => $post['pliegue_supraespinal'] ?? null,
            'pliegue_abdominal' => $post['pliegue_abdominal'] ?? null,
            'pliegue_muslo_anterior' => $post['pliegue_muslo_anterior'] ?? null,
            'pliegue_pantorrilla_medial' => $post['pliegue_pantorrilla_medial'] ?? null,
            'pliegue_pectoral' => $post['pliegue_pectoral'] ?? null,
            'pliegue_axilar_medio' => $post['pliegue_axilar_medio'] ?? null,
            'pliegue_muslo_medial' => $post['pliegue_muslo_medial'] ?? null,
            'motivo_consulta' => $post['motivo_consulta'] ?? null,
            'plan_tratamiento' => $post['plan_tratamiento'] ?? null,
            'recomendaciones' => $post['recomendaciones'] ?? null,
            'observaciones' => $post['observaciones'] ?? null,
            'tags' => $tagsJson,
            'proxima_cita' => $post['proxima_cita'] ?? null,
            'anamnesis_clinica' => !empty($post['anamnesis_clinica']) ? $post['anamnesis_clinica'] : null,
            'anamnesis_alimentaria' => !empty($post['anamnesis_alimentaria']) ? $post['anamnesis_alimentaria'] : null,
            'recordatorio_24h' => !empty($post['recordatorio_24h']) ? $post['recordatorio_24h'] : null
        ];
        $existente = $historial->find($id);
        if ($existente) {
            if (!array_key_exists('anamnesis', $post)) $data['anamnesis'] = $existente->anamnesis;
            if (!array_key_exists('diagnostico', $post)) $data['diagnostico'] = $existente->diagnostico;
        }

        if ($historial->update($id, $data)) {
            $db = \Config\Database::connect();
            // Exámenes bioquímicos: reemplazar todos
            $examenModel = new \App\Models\HistorialExamenBioquimico();
            $db->table('historial_examen_bioquimico')->where('historial_clinico_id', $id)->delete();
            $examenesRaw = $post['examenes_bioquimicos'] ?? '';
            if (is_string($examenesRaw) && $examenesRaw !== '') {
                $examenes = json_decode($examenesRaw, true);
                if (is_array($examenes)) {
                    foreach ($examenes as $row) {
                        if (empty($row['nombre']) && empty($row['valor']) && empty($row['fecha_interpretacion'])) continue;
                        $examenModel->insert([
                            'historial_clinico_id' => $id,
                            'nombre' => $row['nombre'] ?? null,
                            'valor' => $row['valor'] ?? null,
                            'fecha_interpretacion' => $row['fecha_interpretacion'] ?? null
                        ]);
                    }
                }
            }
            // Tendencia de consumo: reemplazar todos
            $tendenciaModel = new \App\Models\HistorialTendenciaConsumo();
            $db->table('historial_tendencia_consumo')->where('historial_clinico_id', $id)->delete();
            $tendenciaRaw = $post['tendencia_consumo'] ?? '';
            if (is_string($tendenciaRaw) && $tendenciaRaw !== '') {
                $tendenciaRows = json_decode($tendenciaRaw, true);
                if (is_array($tendenciaRows)) {
                    foreach ($tendenciaRows as $row) {
                        $grupo = $row['grupo'] ?? null;
                        if (empty($grupo)) continue;
                        $tendenciaModel->insert([
                            'historial_clinico_id' => $id,
                            'grupo' => $grupo,
                            'preferencia' => $row['preferencia'] ?? null,
                            'alergia_intolerancia' => $row['alergia_intolerancia'] ?? null
                        ]);
                    }
                }
            }
            return redirect()->to(base_url('dashboard/historial/editar/' . $id))->with('success', 'Consulta actualizada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', $historial->errors());
        }
    }

    /**
     * Guardar información clínica vía AJAX (editar por historial id). Mismo comportamiento que agenda/guardarInformacionClinica.
     */
    public function guardarInformacionClinica()
    {
        $this->response->setContentType('application/json');
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }
        $id = (int) $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['error' => 'ID de historial requerido'])->setStatusCode(400);
        }
        $historialModel = new HistorialClinico();
        $existente = $historialModel->find($id);
        if (!$existente || ($existente->nutricionista_id ?? 0) != (session()->get('usuario')['id'] ?? 0)) {
            return $this->response->setJSON(['error' => 'No autorizado', 'message' => 'No tiene permiso para modificar este historial'])->setStatusCode(403);
        }
        $motivoConsulta = $this->request->getPost('motivo_consulta');
        $planTratamiento = $this->request->getPost('plan_tratamiento');
        $recomendaciones = $this->request->getPost('recomendaciones');
        $tags = $this->request->getPost('tags');
        $proximaCita = $this->request->getPost('proxima_cita');
        $pacienteId = $this->request->getPost('paciente_id');
        $tipoRegistro = $this->request->getPost('tipo_registro');
        $fechaConsulta = $this->request->getPost('fecha_consulta');
        $horaConsulta = $this->request->getPost('hora_consulta');

        $empresaId = session()->get('usuario')['empresa_id'] ?? null;
        $tagsInput = is_string($tags) ? trim($tags) : '';
        if ($tagsInput !== '') {
            $decoded = json_decode($tagsInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $tagsArray = [];
                foreach ($decoded as $item) {
                    if (is_string($item)) $tagsArray[] = $item;
                    elseif (is_array($item) && isset($item['value'])) $tagsArray[] = $item['value'];
                    elseif (is_array($item) && isset($item['tag'])) $tagsArray[] = $item['tag'];
                }
                $tagsInput = implode(',', $tagsArray);
            }
            $tagsJson = $historialModel->procesarTags($tagsInput, $empresaId);
        } else {
            $tagsJson = $existente->tags ?? json_encode([]);
        }

        $data = [
            'motivo_consulta' => $motivoConsulta ?: null,
            'plan_tratamiento' => $planTratamiento ?: null,
            'recomendaciones' => $recomendaciones ?: null,
            'tags' => $tagsJson,
            'proxima_cita' => $proximaCita !== null && $proximaCita !== '' ? $proximaCita : null,
        ];
        if ($pacienteId !== null && $pacienteId !== '') $data['paciente_id'] = (int) $pacienteId;
        if ($tipoRegistro !== null && $tipoRegistro !== '') $data['tipo_registro'] = $tipoRegistro;
        if ($fechaConsulta !== null && $fechaConsulta !== '') $data['fecha_consulta'] = $fechaConsulta;
        if ($horaConsulta !== null && $horaConsulta !== '') $data['hora_consulta'] = $horaConsulta;

        $historialModel->update($id, $data);
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Información clínica guardada correctamente',
            'csrf_token' => csrf_hash()
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    /**
     * Guardar mediciones y registro clínico vía AJAX (editar por historial id). Mismo comportamiento que agenda/guardarMediciones.
     */
    public function guardarMediciones()
    {
        $this->response->setContentType('application/json');
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }
        $post = $this->request->getPost();
        $id = (int) ($post['id'] ?? 0);
        if (!$id) {
            return $this->response->setJSON(['error' => 'ID de historial requerido'])->setStatusCode(400);
        }
        $historialModel = new HistorialClinico();
        $existente = $historialModel->find($id);
        if (!$existente || ($existente->nutricionista_id ?? 0) != (session()->get('usuario')['id'] ?? 0)) {
            return $this->response->setJSON(['error' => 'No autorizado', 'message' => 'No tiene permiso para modificar este historial'])->setStatusCode(403);
        }
        $pacienteId = (int) ($existente->paciente_id ?? 0);
        if (!$pacienteId) {
            return $this->response->setJSON(['error' => 'Paciente no asociado al historial'])->setStatusCode(400);
        }

        $imc_actual = null;
        if (!empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            $imc_actual = $historialModel->calcularIMC($post['peso_actual'], $post['altura_actual']);
        }
        $pliegues = ['pliegue_tricipital','pliegue_bicipital','pliegue_subescapular','pliegue_suprailíaco','pliegue_abdominal','pliegue_muslo_anterior','pliegue_pantorrilla_medial','pliegue_pectoral','pliegue_axilar_medio','pliegue_muslo_medial'];
        $suma = 0; $tiene_pliegues = false;
        foreach ($pliegues as $p) {
            if (!empty($post[$p])) { $suma += floatval($post[$p]); $tiene_pliegues = true; }
        }
        $suma_pliegues = $tiene_pliegues ? round($suma, 2) : null;
        $grasa_corporal_calculada = null;
        if ($suma_pliegues && !empty($post['peso_actual']) && !empty($post['altura_actual'])) {
            $grasa_corporal_calculada = round(($suma_pliegues * 0.5) + 5, 2);
        }
        $empresaId = session()->get('usuario')['empresa_id'] ?? null;
        $tagsInput = $post['tags'] ?? '';
        if (is_string($tagsInput) && $tagsInput !== '') {
            $decoded = json_decode($tagsInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $tagsArray = [];
                foreach ($decoded as $item) {
                    if (is_string($item)) $tagsArray[] = $item;
                    elseif (is_array($item) && isset($item['value'])) $tagsArray[] = $item['value'];
                    elseif (is_array($item) && isset($item['tag'])) $tagsArray[] = $item['tag'];
                }
                $tagsInput = implode(',', $tagsArray);
            }
        }
        $tagsJson = $historialModel->procesarTags($tagsInput, $empresaId);

        $data = [
            'peso_actual' => $post['peso_actual'] ?? null,
            'altura_actual' => $post['altura_actual'] ?? null,
            'altura_sentado' => $post['altura_sentado'] ?? null,
            'imc_actual' => $imc_actual,
            'circunferencia_cintura' => $post['circunferencia_cintura'] ?? null,
            'circunferencia_cadera' => $post['circunferencia_cadera'] ?? null,
            'circunferencia_brazo_relajado' => $post['circunferencia_brazo_relajado'] ?? null,
            'circunferencia_brazo_contraido' => $post['circunferencia_brazo_contraido'] ?? null,
            'circunferencia_muslo_medio' => $post['circunferencia_muslo_medio'] ?? null,
            'circunferencia_pantorrilla' => $post['circunferencia_pantorrilla'] ?? null,
            'circunferencia_cuello' => $post['circunferencia_cuello'] ?? null,
            'circunferencia_torax' => $post['circunferencia_torax'] ?? null,
            'circunferencia_cabeza' => $post['circunferencia_cabeza'] ?? null,
            'circunferencia_antebrazo_maximo' => $post['circunferencia_antebrazo_maximo'] ?? null,
            'circunferencia_muslo_maximo' => $post['circunferencia_muslo_maximo'] ?? null,
            'circunferencia_muneca' => $post['circunferencia_muneca'] ?? null,
            'diametro_biacromial' => $post['diametro_biacromial'] ?? null,
            'diametro_bi_iliocristal' => $post['diametro_bi_iliocristal'] ?? null,
            'diametro_torax_transverso' => $post['diametro_torax_transverso'] ?? null,
            'diametro_torax_anteroposterior' => $post['diametro_torax_anteroposterior'] ?? null,
            'diametro_humero' => $post['diametro_humero'] ?? null,
            'diametro_femur' => $post['diametro_femur'] ?? null,
            'diametro_muneca' => $post['diametro_muneca'] ?? null,
            'diametro_tobillo' => $post['diametro_tobillo'] ?? null,
            'grasa_corporal' => $post['grasa_corporal'] ?? null,
            'masa_muscular' => $post['masa_muscular'] ?? null,
            'suma_pliegues' => $suma_pliegues,
            'grasa_corporal_calculada' => $grasa_corporal_calculada,
            'pliegue_tricipital' => $post['pliegue_tricipital'] ?? null,
            'pliegue_bicipital' => $post['pliegue_bicipital'] ?? null,
            'pliegue_subescapular' => $post['pliegue_subescapular'] ?? null,
            'pliegue_suprailíaco' => $post['pliegue_suprailíaco'] ?? null,
            'pliegue_supraespinal' => $post['pliegue_supraespinal'] ?? null,
            'pliegue_abdominal' => $post['pliegue_abdominal'] ?? null,
            'pliegue_muslo_anterior' => $post['pliegue_muslo_anterior'] ?? null,
            'pliegue_pantorrilla_medial' => $post['pliegue_pantorrilla_medial'] ?? null,
            'pliegue_pectoral' => $post['pliegue_pectoral'] ?? null,
            'pliegue_axilar_medio' => $post['pliegue_axilar_medio'] ?? null,
            'pliegue_muslo_medial' => $post['pliegue_muslo_medial'] ?? null,
            'anamnesis_clinica' => !empty($post['anamnesis_clinica']) ? $post['anamnesis_clinica'] : null,
            'anamnesis_alimentaria' => !empty($post['anamnesis_alimentaria']) ? $post['anamnesis_alimentaria'] : null,
            'recordatorio_24h' => !empty($post['recordatorio_24h']) ? $post['recordatorio_24h'] : null,
            'tags' => $tagsJson,
        ];
        try {
            $historialModel->update($id, $data);
            $db = \Config\Database::connect();
            $examenModel = new \App\Models\HistorialExamenBioquimico();
            $db->table('historial_examen_bioquimico')->where('historial_clinico_id', $id)->delete();
            $examenesRaw = $post['examenes_bioquimicos'] ?? $post['examenes_bioquimicos_hidden'] ?? '';
            if (is_string($examenesRaw) && $examenesRaw !== '') {
                $examenes = json_decode($examenesRaw, true);
                if (is_array($examenes)) {
                    foreach ($examenes as $row) {
                        if (empty($row['nombre']) && empty($row['valor']) && empty($row['fecha_interpretacion'])) continue;
                        $examenModel->insert(['historial_clinico_id' => $id, 'nombre' => $row['nombre'] ?? null, 'valor' => $row['valor'] ?? null, 'fecha_interpretacion' => $row['fecha_interpretacion'] ?? null]);
                    }
                }
            }
            $tendenciaModel = new \App\Models\HistorialTendenciaConsumo();
            $db->table('historial_tendencia_consumo')->where('historial_clinico_id', $id)->delete();
            $tendenciaRaw = $post['tendencia_consumo'] ?? '';
            if (is_string($tendenciaRaw) && $tendenciaRaw !== '') {
                $tendenciaRows = json_decode($tendenciaRaw, true);
                if (is_array($tendenciaRows)) {
                    foreach ($tendenciaRows as $row) {
                        $grupo = $row['grupo'] ?? null;
                        if (empty($grupo)) continue;
                        $tendenciaModel->insert(['historial_clinico_id' => $id, 'grupo' => $grupo, 'preferencia' => $row['preferencia'] ?? null, 'alergia_intolerancia' => $row['alergia_intolerancia'] ?? null]);
                    }
                }
            }
            return $this->response->setJSON(['success' => true, 'message' => 'Mediciones y registro clínico guardados correctamente', 'csrf_token' => csrf_hash()])->setHeader('X-CSRF-TOKEN', csrf_hash());
        } catch (\Exception $e) {
            log_message('error', 'HistorialController::guardarMediciones ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Error al guardar', 'message' => $e->getMessage()])->setStatusCode(500);
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

        // Si se llegó desde la vista de consulta, permitir volver a ella
        $data['retorno_consulta_id'] = $this->request->getGet('retorno') === 'consulta' ? (int) $this->request->getGet('id') : 0;

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
     * Calcular composición corporal - Método 2 Componentes
     */
    public function calcular2Componentes()
    {
        return $this->calcularComposicion('2-componentes');
    }

    /**
     * Calcular composición corporal - Método 4 Componentes
     */
    public function calcular4Componentes()
    {
        return $this->calcularComposicion('4-componentes');
    }

    /**
     * Calcular composición corporal - Método 5 Componentes
     */
    public function calcular5Componentes()
    {
        return $this->calcularComposicion('5-componentes');
    }

    /**
     * Calcular Somatotipo
     */
    public function calcularSomatotipo()
    {
        return $this->calcularComposicion('somatotipo');
    }

    /**
     * Método genérico para calcular composición corporal
     */
    private function calcularComposicion($metodoSlug)
    {
        $this->response->setContentType('application/json');
        $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
        
        if (!session()->get('usuario')) {
            return $this->response->setJSON([
                'error' => 'No autorizado',
                'csrf_hash' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(401);
        }

        $historialId = $this->request->getPost('historial_id') ?? $this->request->getGet('historial_id');
        
        if (!$historialId) {
            return $this->response->setJSON([
                'error' => 'ID de historial requerido',
                'message' => 'Debe proporcionar el ID del historial clínico',
                'csrf_hash' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(400);
        }

        // Verificar acceso usando ModuloDetalle (igual que cualquier ruta)
        $moduloDetalle = new ModuloDetalle();
        $usuario = session()->get('usuario');
        $ruta = '/calcular-' . $metodoSlug;
        
        $rutasPermitidas = $moduloDetalle->getAllowedByPerfil(
            $usuario['perfil_id'],
            $usuario['empresa_id'] ?? null
        );
        
        // Verificar si la ruta específica está permitida
        $tieneAcceso = false;
        foreach ($rutasPermitidas as $rutaPermitida) {
            if ($rutaPermitida['detalle_ruta'] === $ruta && $rutaPermitida['permisos']['ver']) {
                $tieneAcceso = true;
                break;
            }
        }

        if (!$tieneAcceso) {
            // Obtener información del paquete para sugerir upgrade
            $db = \Config\Database::connect();
            $empresa = $db->table('empresa e')
                ->select('e.paquete_id, p.nombre as paquete_nombre')
                ->join('paquetes p', 'p.id = e.paquete_id', 'left')
                ->where('e.id', $usuario['empresa_id'])
                ->get()
                ->getRow();
            
            return $this->response->setJSON([
                'error' => 'Método no disponible en tu plan actual',
                'requiere_upgrade' => true,
                'paquete_actual' => $empresa->paquete_nombre ?? 'Sin paquete',
                'metodo' => $metodoSlug,
                'csrf_hash' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(403);
        }

        // Obtener historial y paciente (incluir soft-deleted para que calcule desde agenda/consulta)
        $historialModel = new HistorialClinico();
        $historial = $historialModel->withDeleted()->find($historialId);
        
        if (!$historial) {
            return $this->response->setJSON([
                'error' => 'Historial no encontrado',
                'message' => 'El historial clínico especificado no existe',
                'csrf_hash' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(404);
        }

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($historial->paciente_id);
        
        if (!$paciente) {
            return $this->response->setJSON([
                'error' => 'Paciente no encontrado',
                'message' => 'El paciente asociado al historial no existe',
                'csrf_hash' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(404);
        }

        // Verificar datos disponibles
        $service = new ComposicionCorporalService();
        $verificacion = $service->verificarDatosDisponibles($metodoSlug, $historial);
        
        if (!$verificacion['disponible']) {
            return $this->response->setJSON([
                'error' => 'Datos insuficientes',
                'message' => 'Faltan datos requeridos para calcular este método',
                'faltantes' => $verificacion['faltantes'],
                'metodo' => $metodoSlug,
                'csrf_hash' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(400);
        }

        try {
            // Calcular
            $resultado = $service->calcular($metodoSlug, $historial, $paciente);
            
            return $this->response->setJSON([
                'success' => true,
                'resultado' => $resultado,
                'metodo' => $metodoSlug,
                'historial_id' => $historialId,
                'csrf_hash' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Error al calcular',
                'message' => $e->getMessage(),
                'metodo' => $metodoSlug,
                'csrf_hash' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash())->setStatusCode(500);
        }
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