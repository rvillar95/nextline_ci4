<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Pago;
use App\Models\Empresa;
use App\Models\ModuloDetalle;
use App\Traits\MaintainsFilters;

class PagoController extends BaseController
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

        return view('Modulos/pago/lista', $data);
    }

    public function getPagos()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $pago = new Pago();
        $draw = intval($this->request->getGet("draw"));
        
        // Obtener empresa_id del usuario logueado por defecto
        $usuario = session()->get('usuario');
        $empresaIdUsuario = $usuario['empresa_id'] ?? null;
        
        // Permitir filtrar por otra empresa si se pasa como parámetro (solo para Super Admin)
        $empresa_id = $this->request->getGet('empresa_id');
        if (empty($empresa_id) && $empresaIdUsuario) {
            $empresa_id = $empresaIdUsuario;
        }
        
        // Verificar si el usuario es Super Admin (poder = 3)
        $esSuperAdmin = isset($usuario['poder']) && $usuario['poder'] == 3;
        
        // Log para debugging
        log_message('debug', 'PagoController::getPagos() - Usuario empresa_id: ' . ($empresaIdUsuario ?? 'NULL') . ', Filtro empresa_id: ' . ($empresa_id ?? 'NULL') . ', Es Super Admin: ' . ($esSuperAdmin ? 'Sí' : 'No'));
        
        $tipo_pago = $this->request->getGet('tipo_pago');
        $estado_pago = $this->request->getGet('estado_pago');
        $fecha_desde = $this->request->getGet('fecha_desde');
        $fecha_hasta = $this->request->getGet('fecha_hasta');
        
        // Construir query base usando Query Builder para mejor control
        $db = \Config\Database::connect();
        
        // Función auxiliar para aplicar filtros
        $applyFilters = function($builder) use ($empresa_id, $empresaIdUsuario, $tipo_pago, $estado_pago, $fecha_desde, $fecha_hasta, $esSuperAdmin) {
            // Filtrar por empresa (obligatorio si el usuario tiene empresa, a menos que sea Super Admin)
            if (!empty($empresa_id)) {
                $builder->where('empresa_id', $empresa_id);
            } elseif ($empresaIdUsuario && !$esSuperAdmin) {
                // Si el usuario tiene empresa pero no se pasó empresa_id, filtrar por la del usuario
                // (solo si NO es Super Admin)
                $builder->where('empresa_id', $empresaIdUsuario);
            }
            // Si es Super Admin y no se especificó empresa_id, mostrar todos los pagos

            if (!empty($tipo_pago)) {
                $builder->where('tipo_pago', $tipo_pago);
            }

            if (!empty($estado_pago)) {
                $builder->where('estado_pago', $estado_pago);
            }

            if (!empty($fecha_desde)) {
                $builder->where('fecha_pago >=', $fecha_desde);
            }

            if (!empty($fecha_hasta)) {
                $builder->where('fecha_pago <=', $fecha_hasta);
            }
        };
        
        // Contar total de registros con los filtros aplicados
        $countBuilder = $db->table('pagos');
        $applyFilters($countBuilder);
        $recordsTotal = $countBuilder->countAllResults(false);
        
        // Obtener registros ordenados (fecha_pago DESC, luego fcreacion DESC si fecha_pago es NULL)
        $dataBuilder = $db->table('pagos');
        $applyFilters($dataBuilder);
        $rows = $dataBuilder->orderBy('fecha_pago', 'DESC')
                           ->orderBy('fcreacion', 'DESC')
                           ->get()
                           ->getResult();

        $empresaModel = new Empresa();
        $data = array();
        foreach ($rows as $r) {
            $empresa = $empresaModel->find($r->empresa_id);
            $nombreEmpresa = $empresa ? $empresa->nombre : 'N/A';

            $tipoBadge = match ($r->tipo_pago) {
                'setup' => '<span class="badge bg-primary">Setup</span>',
                'mensual' => '<span class="badge bg-success">Mensual</span>',
                'anual' => '<span class="badge bg-info">Anual</span>',
                'extra' => '<span class="badge bg-warning">Extra</span>',
                'cita' => '<span class="badge bg-info">Cita</span>',
                default => '<span class="badge bg-secondary">N/A</span>',
            };

            $estadoBadge = match ($r->estado_pago) {
                'pendiente' => '<span class="badge bg-warning">Pendiente</span>',
                'procesando' => '<span class="badge bg-info">Procesando</span>',
                'completado' => '<span class="badge bg-success">Completado</span>',
                'fallido' => '<span class="badge bg-danger">Fallido</span>',
                'reembolsado' => '<span class="badge bg-secondary">Reembolsado</span>',
                default => '<span class="badge bg-secondary">N/A</span>',
            };

            $montoFormateado = '$' . number_format($r->monto, 0, ',', '.');

            $botones = '';
            if ($r->estado_pago == 'pendiente') {
                $botones = '<button type="button" class="btn btn-sm btn-outline-success" onclick="procesarPago(' . $r->id . ')">Procesar</button> ' .
                          '<a href="' . base_url('dashboard/pago/editar/' . $r->id) . '" class="btn btn-sm btn-outline-info">Ver</a>';
            } else {
                $botones = '<a href="' . base_url('dashboard/pago/editar/' . $r->id) . '" class="btn btn-sm btn-outline-info">Ver</a>';
            }

            $data[] = array(
                esc($nombreEmpresa),
                $montoFormateado,
                $tipoBadge,
                $estadoBadge,
                esc($r->fecha_pago ? date('d/m/Y', strtotime($r->fecha_pago)) : ($r->fcreacion ? date('d/m/Y', strtotime($r->fcreacion)) : '')),
                esc($r->referencia ?? ''),
                $botones
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => count($data),
            "data" => $data
        );

        // Log para debugging
        log_message('debug', 'PagoController::getPagos() - Empresa ID: ' . ($empresa_id ?? 'NULL') . ', Total registros: ' . $recordsTotal . ', Datos encontrados: ' . count($data));

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

        // Cargar empresas
        $empresaModel = new Empresa();
        $data['empresas'] = $empresaModel->where('estado', 'A')->findAll();

        return view('Modulos/pago/registro', $data);
    }

    public function registrar()
    {
        $pago = new Pago();

        $validationRules = [
            'empresa_id' => 'required|integer|greater_than[0]',
            'tipo_pago' => 'required|in_list[setup,mensual,anual,extra]',
            'monto' => 'required|decimal|greater_than[0]',
            'estado_pago' => 'required|in_list[pendiente,procesando,completado,fallido,reembolsado]'
        ];

        $validationMessages = [
            'empresa_id' => [
                'required' => 'La empresa es obligatoria.',
                'integer' => 'Debe seleccionar una empresa válida.',
                'greater_than' => 'Debe seleccionar una empresa válida.'
            ],
            'tipo_pago' => [
                'required' => 'El tipo de pago es obligatorio.',
                'in_list' => 'El tipo de pago debe ser: setup, mensual, anual o extra.'
            ],
            'monto' => [
                'required' => 'El monto es obligatorio.',
                'decimal' => 'El monto debe ser un número decimal válido.',
                'greater_than' => 'El monto debe ser mayor a 0.'
            ],
            'estado_pago' => [
                'required' => 'El estado del pago es obligatorio.',
                'in_list' => 'El estado del pago debe ser: pendiente, procesando, completado, fallido o reembolsado.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'empresa_id', 'paquete_id', 'tipo_pago', 'monto', 'moneda', 'metodo_pago',
            'estado_pago', 'fecha_pago', 'fecha_vencimiento', 'periodo_inicio', 'periodo_fin',
            'referencia', 'observaciones'
        ]);

        $data = [
            'empresa_id' => $post['empresa_id'],
            'paquete_id' => $post['paquete_id'] ?? null,
            'tipo_pago' => $post['tipo_pago'],
            'monto' => $post['monto'],
            'moneda' => $post['moneda'] ?? 'CLP',
            'metodo_pago' => $post['metodo_pago'] ?? null,
            'estado_pago' => $post['estado_pago'],
            'fecha_pago' => $post['fecha_pago'] ?? null,
            'fecha_vencimiento' => $post['fecha_vencimiento'] ?? null,
            'periodo_inicio' => $post['periodo_inicio'] ?? null,
            'periodo_fin' => $post['periodo_fin'] ?? null,
            'referencia' => $post['referencia'] ?? null,
            'observaciones' => $post['observaciones'] ?? null
        ];

        if ($pago->insert($data)) {
            return redirect()->to(base_url('dashboard/pago/lista'))->with('success', 'Pago registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', $pago->errors());
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

        $pago = new Pago();
        $data['pago'] = $pago->getPagoCompleto($id);
        
        if (!$data['pago']) {
            return redirect()->to(base_url('dashboard/pago/lista'))->with('error', 'Pago no encontrado');
        }

        // Cargar empresas
        $empresaModel = new Empresa();
        $data['empresas'] = $empresaModel->where('estado', 'A')->findAll();

        return view('Modulos/pago/editar', $data);
    }

    public function update()
    {
        $pago = new Pago();
        $id = $this->request->getPost('id');

        $validationRules = [
            'empresa_id' => 'required|integer|greater_than[0]',
            'tipo_pago' => 'required|in_list[setup,mensual,anual,extra]',
            'monto' => 'required|decimal|greater_than[0]',
            'estado_pago' => 'required|in_list[pendiente,procesando,completado,fallido,reembolsado]'
        ];

        $validationMessages = [
            'empresa_id' => [
                'required' => 'La empresa es obligatoria.',
                'integer' => 'Debe seleccionar una empresa válida.',
                'greater_than' => 'Debe seleccionar una empresa válida.'
            ],
            'tipo_pago' => [
                'required' => 'El tipo de pago es obligatorio.',
                'in_list' => 'El tipo de pago debe ser: setup, mensual, anual o extra.'
            ],
            'monto' => [
                'required' => 'El monto es obligatorio.',
                'decimal' => 'El monto debe ser un número decimal válido.',
                'greater_than' => 'El monto debe ser mayor a 0.'
            ],
            'estado_pago' => [
                'required' => 'El estado del pago es obligatorio.',
                'in_list' => 'El estado del pago debe ser: pendiente, procesando, completado, fallido o reembolsado.'
            ]
        ];

        if (!$this->validate($validationRules, $validationMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'empresa_id', 'paquete_id', 'tipo_pago', 'monto', 'moneda', 'metodo_pago',
            'estado_pago', 'fecha_pago', 'fecha_vencimiento', 'periodo_inicio', 'periodo_fin',
            'referencia', 'observaciones'
        ]);

        $data = [
            'empresa_id' => $post['empresa_id'],
            'paquete_id' => $post['paquete_id'] ?? null,
            'tipo_pago' => $post['tipo_pago'],
            'monto' => $post['monto'],
            'moneda' => $post['moneda'] ?? 'CLP',
            'metodo_pago' => $post['metodo_pago'] ?? null,
            'estado_pago' => $post['estado_pago'],
            'fecha_pago' => $post['fecha_pago'] ?? null,
            'fecha_vencimiento' => $post['fecha_vencimiento'] ?? null,
            'periodo_inicio' => $post['periodo_inicio'] ?? null,
            'periodo_fin' => $post['periodo_fin'] ?? null,
            'referencia' => $post['referencia'] ?? null,
            'observaciones' => $post['observaciones'] ?? null
        ];

        if ($pago->update($id, $data)) {
            return redirect()->to(base_url('dashboard/pago/lista'))->with('success', 'Pago actualizado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al actualizar el pago');
        }
    }

    public function procesar()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $id = $this->request->getPost('id');
        $referencia = $this->request->getPost('referencia');
        
        if (!$id) {
            return $this->response->setJSON(['error' => 'ID requerido'])->setStatusCode(400);
        }

        $pago = new Pago();
        
        if ($pago->procesarPago($id, $referencia)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Pago procesado con éxito']);
        } else {
            return $this->response->setJSON(['error' => 'Error al procesar el pago'])->setStatusCode(500);
        }
    }
}
