<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Calorimetria;
use App\Models\PlanAlimentario;
use App\Models\IntercambioPorcion;
use App\Models\ActividadMet;
use App\Models\Paciente;
use App\Models\ModuloDetalle;
use App\Services\CalorimetriaService;
use App\Services\PlanAlimentarioService;

class PlanAlimentarioController extends BaseController
{
    protected $calorimetriaService;
    protected $planAlimentarioService;

    public function __construct()
    {
        $this->calorimetriaService = new CalorimetriaService();
        $this->planAlimentarioService = new PlanAlimentarioService();
    }

    /**
     * Cargar menú común
     */
    private function cargarMenu()
    {
        $menuTotal = [];
        $modulo = new ModuloDetalle();
        $menu = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($menu as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            $menuTotal[] = ["menu" => $entity, "submenu" => $submenu];
        }

        return $menuTotal;
    }

    /**
     * Vista principal del módulo Plan Alimentario
     */
    public function index()
    {
        $data['data'] = $this->cargarMenu();
        
        // Cargar pacientes para el select
        $pacienteModel = new Paciente();
        $usuario = session()->get('usuario');
        $data['pacientes'] = $pacienteModel->getPacientesSelect($usuario['id']);

        $data['autoPacienteId'] = (int) $this->request->getGet('paciente_id');
        $data['autoDetalleAgendaId'] = (int) $this->request->getGet('detalle_agenda_id');
        $data['autoTab'] = in_array($this->request->getGet('tab'), ['calorimetria', 'plan', 'distribucion'], true)
            ? $this->request->getGet('tab')
            : 'calorimetria';

        // Asegurar que el paciente de la URL aparezca en el select
        $data['consultasPreload'] = [];
        $data['mostrarContenido'] = false;
        $data['pacientePrecargado'] = null;
        $data['detalleAgendaIdActivo'] = null;
        $data['htmlCalorimetria'] = '';
        $data['htmlPlan'] = '';
        $data['htmlDistribucion'] = '';

        if ($data['autoPacienteId'] > 0) {
            $ids = array_map(static fn ($p) => (int) $p->id, $data['pacientes']);
            if (!in_array($data['autoPacienteId'], $ids, true)) {
                $extra = $pacienteModel->find($data['autoPacienteId']);
                if ($extra) {
                    $extra->nombre_completo = trim(($extra->nombre ?? '') . ' ' . ($extra->apellido ?? ''));
                    array_unshift($data['pacientes'], $extra);
                }
            }

            $paciente = $pacienteModel->find($data['autoPacienteId']);
            if ($paciente) {
                $data['pacientePrecargado'] = $paciente;
                $data['consultasPreload'] = $this->obtenerConsultasPaciente($data['autoPacienteId']);
                $detalleId = $data['autoDetalleAgendaId'] > 0 ? $data['autoDetalleAgendaId'] : null;
                $data['detalleAgendaIdActivo'] = $detalleId;
                $data['mostrarContenido'] = true;
                $data['htmlCalorimetria'] = $this->renderVistaCalorimetria($data['autoPacienteId'], $detalleId);
                $data['htmlPlan'] = $this->renderVistaPlan($data['autoPacienteId'], $detalleId);
                $data['htmlDistribucion'] = $this->renderVistaDistribucion($data['autoPacienteId'], $detalleId);
            }
        }
        
        return view('Modulos/plan_alimentario/index', $data);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function obtenerConsultasPaciente(int $pacienteId): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('detalle_agenda da')
            ->select('da.id, da.tipo_consulta, da.hora_inicio, da.hora_fin, da.estado_cita, a.fecha as fecha_agenda')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->where('da.paciente_id', $pacienteId)
            ->orderBy('a.fecha', 'DESC')
            ->orderBy('da.hora_inicio', 'DESC')
            ->limit(100)
            ->get()
            ->getResult();

        $lista = [];
        foreach ($rows as $r) {
            $lista[] = [
                'id'            => (int) $r->id,
                'fecha_agenda'  => $r->fecha_agenda ?? '',
                'hora_inicio'   => $r->hora_inicio ?? '',
                'hora_fin'      => $r->hora_fin ?? '',
                'tipo_consulta' => $r->tipo_consulta ?? 'consulta',
                'estado_cita'   => $r->estado_cita ?? '',
            ];
        }

        return $lista;
    }

    private function renderVistaCalorimetria(int $pacienteId, ?int $detalleAgendaId): string
    {
        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($pacienteId);
        if (!$paciente) {
            return '';
        }

        $cita = (object) [
            'id'               => $detalleAgendaId,
            'paciente_id'      => $pacienteId,
            'genero'           => $paciente->genero ?? null,
            'fecha_nacimiento' => $paciente->fecha_nacimiento ?? null,
        ];

        $historial = null;
        if ($detalleAgendaId) {
            $historialModel = new \App\Models\HistorialClinico();
            $historial = $historialModel->where('detalle_agenda_id', $detalleAgendaId)
                ->where('paciente_id', $pacienteId)
                ->first();
        }

        return view('Modulos/plan_alimentario/calorimetria', [
            'cita'      => $cita,
            'historial' => $historial,
        ]);
    }

    private function renderVistaPlan(int $pacienteId, ?int $detalleAgendaId): string
    {
        $cita = (object) [
            'id'          => $detalleAgendaId,
            'paciente_id' => $pacienteId,
        ];

        return view('Modulos/plan_alimentario/plan', ['cita' => $cita]);
    }

    private function renderVistaDistribucion(int $pacienteId, ?int $detalleAgendaId): string
    {
        $cita = (object) [
            'id'          => $detalleAgendaId,
            'paciente_id' => $pacienteId,
        ];

        return view('Modulos/plan_alimentario/distribucion_comidas', ['cita' => $cita]);
    }

    /**
     * Vista parcial de Calorimetría (para cargar con AJAX)
     */
    public function vistaCalorimetria()
    {
        $pacienteId = (int) $this->request->getGet('paciente_id');
        $detalleAgendaId = $this->request->getGet('detalle_agenda_id');
        $detalleAgendaId = $detalleAgendaId !== null && $detalleAgendaId !== '' ? (int) $detalleAgendaId : null;

        return $this->response->setBody($this->renderVistaCalorimetria($pacienteId, $detalleAgendaId));
    }

    /**
     * Vista parcial de Plan Alimentario (para cargar con AJAX)
     */
    public function vistaPlan()
    {
        $pacienteId = (int) $this->request->getGet('paciente_id');
        $detalleAgendaId = $this->request->getGet('detalle_agenda_id');
        $detalleAgendaId = $detalleAgendaId !== null && $detalleAgendaId !== '' ? (int) $detalleAgendaId : null;

        return $this->response->setBody($this->renderVistaPlan($pacienteId, $detalleAgendaId));
    }

    /**
     * Vista parcial de Distribución (para cargar con AJAX)
     */
    public function vistaDistribucion()
    {
        $pacienteId = (int) $this->request->getGet('paciente_id');
        $detalleAgendaId = $this->request->getGet('detalle_agenda_id');
        $detalleAgendaId = $detalleAgendaId !== null && $detalleAgendaId !== '' ? (int) $detalleAgendaId : null;

        return $this->response->setBody($this->renderVistaDistribucion($pacienteId, $detalleAgendaId));
    }

    /**
     * Obtener datos del paciente (GET)
     */
    public function getPacienteData()
    {
        $pacienteId = (int) $this->request->getGet('paciente_id');
        if ($pacienteId <= 0) {
            return $this->response->setJSON(['error' => 'ID de paciente requerido'])->setStatusCode(400);
        }

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($pacienteId);

        if (!$paciente) {
            return $this->response->setJSON(['error' => 'Paciente no encontrado'])->setStatusCode(404);
        }

        return $this->response->setJSON(['paciente' => $paciente]);
    }

    /**
     * Citas/consultas del paciente (detalle_agenda) para el selector del plan.
     */
    public function consultasPaciente()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado'])->setStatusCode(401);
        }

        $pacienteId = (int) $this->request->getGet('paciente_id');
        if ($pacienteId <= 0) {
            return $this->response->setJSON(['success' => false, 'error' => 'Paciente requerido'])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success'   => true,
            'consultas' => $this->obtenerConsultasPaciente($pacienteId),
        ]);
    }

    /**
     * Calcular Calorimetría (POST)
     */
    public function calcularCalorimetria()
    {
        // Forzar respuesta JSON desde el inicio
        $this->response->setContentType('application/json');
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Solo peticiones AJAX'])->setStatusCode(400);
        }

        $detalleAgendaId = $this->request->getPost('detalle_agenda_id');
        $peso = floatval($this->request->getPost('peso'));
        $talla = floatval($this->request->getPost('talla'));
        $edad = intval($this->request->getPost('edad'));
        $sexo = $this->request->getPost('sexo');
        $actividades = $this->request->getPost('actividades') ?? [];

        // Validaciones
        if (empty($detalleAgendaId) || $peso <= 0 || $talla <= 0 || $edad <= 0 || !in_array($sexo, ['M', 'F'])) {
            return $this->response->setJSON(['error' => 'Datos inválidos'])->setStatusCode(400);
        }

        // Obtener datos del detalle_agenda
        $db = \Config\Database::connect();
        $detalleAgenda = $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->get()
            ->getRow();

        if (!$detalleAgenda) {
            return $this->response->setJSON(['error' => 'Consulta no encontrada'])->setStatusCode(404);
        }

        $usuario = session()->get('usuario');

        try {
            $calorimetriaId = $this->calorimetriaService->guardarCalorimetria($detalleAgendaId, [
                'paciente_id' => $detalleAgenda->paciente_id,
                'nutricionista_id' => $usuario['id'],
                'peso' => $peso,
                'talla' => $talla,
                'edad' => $edad,
                'sexo' => $sexo,
                'actividades' => $actividades
            ]);

            // Obtener calorimetría guardada
            $calorimetriaModel = new Calorimetria();
            $calorimetria = $calorimetriaModel->getCalorimetriaCompleta($calorimetriaId);

            return $this->response->setJSON([
                'success' => true,
                'calorimetria_id' => $calorimetriaId,
                'calorimetria' => $calorimetria,
                'requerimiento_total' => $calorimetria->requerimiento_total
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al calcular calorimetría: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    /**
     * Obtener Calorimetría (GET)
     */
    public function getCalorimetria($detalleAgendaId)
    {
        $this->response->setContentType('application/json');

        try {
            $calorimetriaModel = new Calorimetria();
            $calorimetria = $calorimetriaModel->getPorDetalleAgenda($detalleAgendaId);

            if (!$calorimetria) {
                return $this->response->setJSON(['calorimetria' => null]);
            }

            // Armar respuesta con propiedades escalares y actividades enriquecidas con actividad_met_id
            $actividadMetModel = new ActividadMet();
            $actividades = [];
            if (!empty($calorimetria->actividades)) {
                foreach ($calorimetria->actividades as $act) {
                    $tipo = trim((string)($act->tipo ?? ''));
                    $nombre = trim((string)($act->actividad ?? ''));
                    $met = ($tipo !== '' && $nombre !== '')
                        ? $actividadMetModel->where('tipo', $tipo)->where('nombre', $nombre)->first()
                        : null;
                    $actividades[] = [
                        'actividad_met_id' => $met ? (int)$met->id : null,
                        'tipo' => $act->tipo,
                        'actividad' => $act->actividad,
                        'mets' => $act->mets,
                        'minutos_dia' => (int) $act->minutos_dia,
                        'calorias_hombre' => (float) $act->calorias_hombre,
                        'calorias_mujer' => (float) $act->calorias_mujer,
                        'orden' => (int) ($act->orden ?? 0),
                    ];
                }
            }
            $out = [
                'id' => (int) $calorimetria->id,
                'detalle_agenda_id' => (int) $calorimetria->detalle_agenda_id,
                'paciente_id' => (int) $calorimetria->paciente_id,
                'nutricionista_id' => (int) $calorimetria->nutricionista_id,
                'peso' => (float) $calorimetria->peso,
                'talla' => (float) $calorimetria->talla,
                'edad' => (int) $calorimetria->edad,
                'sexo' => $calorimetria->sexo,
                'tmb_hombres' => (float) $calorimetria->tmb_hombres,
                'tmb_mujeres' => (float) $calorimetria->tmb_mujeres,
                'tmb_usado' => (float) $calorimetria->tmb_usado,
                'total_minutos' => (int) $calorimetria->total_minutos,
                'cals_habituales' => (float) $calorimetria->cals_habituales,
                'cals_entrenamiento' => (float) $calorimetria->cals_entrenamiento,
                'requerimiento_total' => (float) $calorimetria->requerimiento_total,
                'actividades' => $actividades,
            ];

            return $this->response->setJSON(['calorimetria' => $out]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getCalorimetria: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage(), 'calorimetria' => null])->setStatusCode(500);
        }
    }

    /**
     * Obtener actividades con METs (GET)
     */
    public function getActividades()
    {
        // Forzar respuesta JSON desde el inicio para evitar HTML de error
        $this->response->setContentType('application/json');
        
        // Capturar cualquier output buffer que pueda existir
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        try {
            // Verificar sesión
            if (!session()->get('usuario')) {
                return $this->response->setJSON([
                    'error' => 'No autorizado',
                    'actividades_diarias' => [],
                    'actividades_deportes' => []
                ])->setStatusCode(401);
            }
            
            // Verificar que la tabla existe
            $db = \Config\Database::connect();
            $tables = $db->listTables();
            if (!in_array('actividad_met', $tables)) {
                return $this->response->setJSON([
                    'error' => 'La tabla actividad_met no existe. Ejecuta el script crear_sistema_plan_alimentario.sql',
                    'actividades_diarias' => [],
                    'actividades_deportes' => []
                ])->setStatusCode(500);
            }
            
            $actividadModel = new ActividadMet();
            $actividadesDiarias = $actividadModel->getPorTipo('diaria');
            $actividadesDeportes = $actividadModel->getPorTipo('deporte');

            return $this->response->setJSON([
                'actividades_diarias' => $actividadesDiarias,
                'actividades_deportes' => $actividadesDeportes
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getActividades: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'error' => 'Error al cargar actividades: ' . $e->getMessage(),
                'actividades_diarias' => [],
                'actividades_deportes' => []
            ])->setStatusCode(500);
        } catch (\Throwable $e) {
            log_message('error', 'Error fatal en getActividades: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Error fatal al cargar actividades: ' . $e->getMessage(),
                'actividades_diarias' => [],
                'actividades_deportes' => []
            ])->setStatusCode(500);
        }
    }

    /**
     * Crear Plan Alimentario (POST)
     */
    public function crearPlan()
    {
        // Forzar respuesta JSON desde el inicio
        $this->response->setContentType('application/json');
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Solo peticiones AJAX'])->setStatusCode(400);
        }

        $detalleAgendaId = $this->request->getPost('detalle_agenda_id');
        $requerimientoKcal = floatval($this->request->getPost('requerimiento_kcal'));
        $protPorcentaje = floatval($this->request->getPost('prot_porcentaje'));
        $grasaPorcentaje = floatval($this->request->getPost('grasa_porcentaje'));
        $choPorcentaje = floatval($this->request->getPost('cho_porcentaje'));
        $porciones = $this->request->getPost('porciones') ?? [];
        $calorimetriaId = $this->request->getPost('calorimetria_id');

        // Validaciones
        if (empty($detalleAgendaId) || $requerimientoKcal <= 0) {
            return $this->response->setJSON(['error' => 'Datos inválidos'])->setStatusCode(400);
        }

        // Obtener datos del detalle_agenda
        $db = \Config\Database::connect();
        $detalleAgenda = $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->get()
            ->getRow();

        if (!$detalleAgenda) {
            return $this->response->setJSON(['error' => 'Consulta no encontrada'])->setStatusCode(404);
        }

        $usuario = session()->get('usuario');

        try {
            $planId = $this->planAlimentarioService->guardarPlan($detalleAgendaId, [
                'paciente_id' => $detalleAgenda->paciente_id,
                'nutricionista_id' => $usuario['id'],
                'calorimetria_id' => $calorimetriaId ?: null,
                'requerimiento_kcal' => $requerimientoKcal,
                'prot_porcentaje' => $protPorcentaje,
                'grasa_porcentaje' => $grasaPorcentaje,
                'cho_porcentaje' => $choPorcentaje,
                'porciones' => $porciones,
                'observaciones' => $this->request->getPost('observaciones')
            ]);

            // Obtener plan guardado
            $planModel = new PlanAlimentario();
            $plan = $planModel->getPlanCompleto($planId);

            return $this->response->setJSON([
                'success' => true,
                'plan_id' => $planId,
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al crear plan alimentario: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    /**
     * Obtener Plan Alimentario (GET)
     */
    public function getPlan($detalleAgendaId)
    {
        // Forzar respuesta JSON desde el inicio
        $this->response->setContentType('application/json');
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Solo peticiones AJAX'])->setStatusCode(400);
        }

        try {
            $planModel = new PlanAlimentario();
            $plan = $planModel->getPorDetalleAgenda($detalleAgendaId);

            if (!$plan) {
                return $this->response->setJSON(['plan' => null]);
            }

            return $this->response->setJSON(['plan' => $plan]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getPlan: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage(), 'plan' => null])->setStatusCode(500);
        }
    }

    /**
     * Obtener intercambios disponibles (GET)
     */
    public function getIntercambios()
    {
        // Forzar respuesta JSON desde el inicio para evitar HTML de error
        $this->response->setContentType('application/json');
        
        // Capturar cualquier output buffer que pueda existir
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        try {
            // Verificar sesión
            if (!session()->get('usuario')) {
                return $this->response->setJSON([
                    'error' => 'No autorizado',
                    'intercambios' => []
                ])->setStatusCode(401);
            }
            
            // Verificar que la tabla existe
            $db = \Config\Database::connect();
            $tables = $db->listTables();
            if (!in_array('intercambio_porcion', $tables)) {
                return $this->response->setJSON([
                    'error' => 'La tabla intercambio_porcion no existe. Ejecuta el script crear_sistema_plan_alimentario.sql',
                    'intercambios' => []
                ])->setStatusCode(500);
            }
            
            $usuario = session()->get('usuario');
            $empresaId = $usuario['empresa_id'] ?? null;

            $intercambioModel = new IntercambioPorcion();
            $intercambios = $intercambioModel->getIntercambiosActivos($empresaId);

            return $this->response->setJSON(['intercambios' => $intercambios]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getIntercambios: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'error' => 'Error al cargar intercambios: ' . $e->getMessage(),
                'intercambios' => []
            ])->setStatusCode(500);
        } catch (\Throwable $e) {
            log_message('error', 'Error fatal en getIntercambios: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Error fatal al cargar intercambios: ' . $e->getMessage(),
                'intercambios' => []
            ])->setStatusCode(500);
        }
    }

    /**
     * Distribuir por Comidas (POST)
     */
    public function distribuirComidas()
    {
        // Forzar respuesta JSON desde el inicio
        $this->response->setContentType('application/json');
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Solo peticiones AJAX'])->setStatusCode(400);
        }

        $planId = $this->request->getPost('plan_id');
        $distribucion = $this->request->getPost('distribucion') ?? [];

        if (empty($planId)) {
            return $this->response->setJSON(['error' => 'ID de plan requerido'])->setStatusCode(400);
        }

        try {
            $this->planAlimentarioService->distribuirComidas($planId, $distribucion);

            // Obtener plan actualizado
            $planModel = new PlanAlimentario();
            $plan = $planModel->getPlanCompleto($planId);

            return $this->response->setJSON([
                'success' => true,
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al distribuir comidas: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage()])->setStatusCode(500);
        }
    }

    /**
     * Obtener Comidas del Plan (GET)
     */
    public function getComidas($planId)
    {
        // Forzar respuesta JSON desde el inicio
        $this->response->setContentType('application/json');
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Solo peticiones AJAX'])->setStatusCode(400);
        }

        try {
            $planModel = new PlanAlimentario();
            $plan = $planModel->getPlanCompleto($planId);

            if (!$plan) {
                return $this->response->setJSON(['error' => 'Plan no encontrado'])->setStatusCode(404);
            }

            return $this->response->setJSON(['comidas' => $plan->comidas ?? []]);
        } catch (\Exception $e) {
            log_message('error', 'Error en getComidas: ' . $e->getMessage());
            return $this->response->setJSON(['error' => $e->getMessage(), 'comidas' => []])->setStatusCode(500);
        }
    }
}
