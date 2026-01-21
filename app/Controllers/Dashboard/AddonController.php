<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Empresa;
use App\Models\EmpresaAddon;
use App\Models\MetodoCalculo;
use App\Models\Modulo;
use App\Models\ModuloDetalle;

class AddonController extends BaseController
{
    private function esSuperAdmin(): bool
    {
        $usuario = session()->get('usuario');
        return isset($usuario['poder']) && (int) $usuario['poder'] === 3;
    }

    public function lista()
    {
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $menuTotal = [];
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            $menuTotal[] = ["menu" => $entity, "submenu" => $submenu];
        }
        $data['data'] = $menuTotal;

        // Catálogos
        $empresaModel = new Empresa();
        $data['empresas'] = $empresaModel->getEmpresasCompletas();

        $metodosModel = new MetodoCalculo();
        $data['metodos'] = $metodosModel->where('activo', 'A')
            ->orderBy('orden', 'ASC')
            ->findAll();

        $moduloModel = new Modulo();
        // Si existe columna es_addon, filtrar; si no, traer todos activos
        $db = \Config\Database::connect();
        $hasEsAddon = $db->fieldExists('es_addon', 'modulo');
        $q = $moduloModel->where('estado', 'A');
        if ($hasEsAddon) {
            $q = $q->where('es_addon', 'S');
        }
        $data['modulos_addon'] = $q->orderBy('nombre', 'ASC')->findAll();

        $data['titulo'] = 'Gestión de Add-ons';
        return view('Modulos/addon/lista', $data);
    }

    public function getAddons()
    {
        $this->response->setContentType('application/json');
        $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());

        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado', 'csrf_hash' => csrf_hash()])->setStatusCode(401);
        }
        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON(['error' => 'No autorizado', 'csrf_hash' => csrf_hash()])->setStatusCode(403);
        }

        $draw = (int) ($this->request->getGet('draw') ?: 1);
        $empresaId = $this->request->getGet('empresa_id');
        $tipo = $this->request->getGet('tipo');
        $estado = $this->request->getGet('estado');
        $busqueda = trim((string) $this->request->getGet('busqueda'));

        $db = \Config\Database::connect();

        $baseSql = "FROM empresa_addon ea
                    JOIN empresa e ON e.id = ea.empresa_id
                    LEFT JOIN metodos_calculo mc ON (ea.tipo = 'metodo_calculo' AND mc.id = ea.referencia_id)
                    LEFT JOIN modulo m ON (ea.tipo = 'modulo' AND m.id = ea.referencia_id)
                    WHERE 1=1";
        $params = [];

        if (!empty($empresaId)) {
            $baseSql .= " AND ea.empresa_id = :empresa_id:";
            $params['empresa_id'] = (int) $empresaId;
        }
        if (!empty($tipo)) {
            $baseSql .= " AND ea.tipo = :tipo:";
            $params['tipo'] = $tipo;
        }
        if (!empty($estado)) {
            $baseSql .= " AND ea.estado = :estado:";
            $params['estado'] = $estado;
        }
        if ($busqueda !== '') {
            $baseSql .= " AND (
                e.nombre LIKE :q:
                OR e.email LIKE :q:
                OR mc.nombre LIKE :q:
                OR mc.slug LIKE :q:
                OR m.nombre LIKE :q:
            )";
            $params['q'] = '%' . $busqueda . '%';
        }

        $total = (int) $db->query("SELECT COUNT(*) AS c {$baseSql}", $params)->getRow()->c;

        $sql = "SELECT
                    ea.id,
                    e.nombre AS empresa_nombre,
                    e.email  AS empresa_email,
                    ea.tipo,
                    ea.referencia_id,
                    COALESCE(mc.nombre, m.nombre, CONCAT('#', ea.referencia_id)) AS item_nombre,
                    COALESCE(mc.slug, '') AS item_slug,
                    ea.precio_mensual,
                    ea.fecha_inicio,
                    ea.fecha_fin,
                    ea.estado,
                    ea.fcreacion
                {$baseSql}
                ORDER BY ea.fcreacion DESC";

        $rows = $db->query($sql, $params)->getResultArray();

        $data = [];
        foreach ($rows as $r) {
            $badge = match ($r['estado']) {
                'activo' => '<span class="badge bg-success">Activo</span>',
                'suspendido' => '<span class="badge bg-warning text-dark">Suspendido</span>',
                'cancelado' => '<span class="badge bg-secondary">Cancelado</span>',
                default => '<span class="badge bg-light text-dark">' . esc($r['estado']) . '</span>',
            };

            $acciones = '<div class="btn-group" role="group">';
            if ($r['estado'] !== 'activo') {
                $acciones .= '<button class="btn btn-sm btn-success" onclick="activarAddon(' . (int) $r['id'] . ')">Activar</button>';
            }
            if ($r['estado'] !== 'cancelado') {
                $acciones .= '<button class="btn btn-sm btn-danger" onclick="cancelarAddon(' . (int) $r['id'] . ')">Cancelar</button>';
            }
            $acciones .= '</div>';

            $data[] = [
                esc($r['empresa_nombre']) . '<br><small class="text-muted">' . esc($r['empresa_email']) . '</small>',
                esc($r['tipo']),
                esc($r['item_nombre']) . ($r['item_slug'] ? '<br><small class="text-muted">' . esc($r['item_slug']) . '</small>' : ''),
                '$' . number_format((float) $r['precio_mensual'], 0, ',', '.'),
                esc((string) $r['fecha_inicio']),
                esc((string) ($r['fecha_fin'] ?? '')),
                $badge,
                $acciones,
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
            'csrf_hash' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    public function registrar()
    {
        $this->response->setContentType('application/json');
        $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());

        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado', 'csrf_hash' => csrf_hash()])->setStatusCode(401);
        }
        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado', 'csrf_hash' => csrf_hash()])->setStatusCode(403);
        }

        $post = $this->request->getPost([
            'empresa_id', 'tipo', 'referencia_id', 'precio_mensual', 'fecha_inicio', 'fecha_fin', 'estado'
        ]);

        // Validaciones mínimas
        $empresaId = (int) ($post['empresa_id'] ?? 0);
        $tipo = (string) ($post['tipo'] ?? '');
        $refId = (int) ($post['referencia_id'] ?? 0);

        if ($empresaId <= 0 || $refId <= 0 || !in_array($tipo, ['metodo_calculo', 'modulo'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Datos inválidos',
                'csrf_hash' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $estado = (string) ($post['estado'] ?? 'activo');
        if (!in_array($estado, ['activo', 'suspendido', 'cancelado'], true)) {
            $estado = 'activo';
        }

        $fechaInicio = $post['fecha_inicio'] ?: date('Y-m-d');
        $fechaFin = $post['fecha_fin'] ?: null;

        $precio = (float) ($post['precio_mensual'] ?? 0);

        $model = new EmpresaAddon();
        $model->upsertAddon([
            'empresa_id' => $empresaId,
            'tipo' => $tipo,
            'referencia_id' => $refId,
            'precio_mensual' => $precio,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => $estado,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Add-on guardado correctamente',
            'csrf_hash' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    public function cancelar($id = null)
    {
        $this->response->setContentType('application/json');
        $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());

        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado', 'csrf_hash' => csrf_hash()])->setStatusCode(401);
        }
        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado', 'csrf_hash' => csrf_hash()])->setStatusCode(403);
        }

        $id = (int) $id;
        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'error' => 'ID inválido', 'csrf_hash' => csrf_hash()])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $db->table('empresa_addon')
            ->where('id', $id)
            ->update([
                'estado' => 'cancelado',
                'fecha_fin' => date('Y-m-d'),
                'factualizacion' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Add-on cancelado',
            'csrf_hash' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    public function activar($id = null)
    {
        $this->response->setContentType('application/json');
        $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());

        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado', 'csrf_hash' => csrf_hash()])->setStatusCode(401);
        }
        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON(['success' => false, 'error' => 'No autorizado', 'csrf_hash' => csrf_hash()])->setStatusCode(403);
        }

        $id = (int) $id;
        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'error' => 'ID inválido', 'csrf_hash' => csrf_hash()])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $db->table('empresa_addon')
            ->where('id', $id)
            ->update([
                'estado' => 'activo',
                'fecha_fin' => null,
                'factualizacion' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Add-on activado',
            'csrf_hash' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }
}

