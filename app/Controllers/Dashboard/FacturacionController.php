<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Empresa;
use App\Models\ModuloDetalle;
use App\Models\Paquete;

class FacturacionController extends BaseController
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

        // Catálogos para filtros
        $empresaModel = new Empresa();
        $data['empresas'] = $empresaModel->getEmpresasCompletas();

        $paqueteModel = new Paquete();
        $data['paquetes'] = $paqueteModel->getPaquetesActivos();

        $data['titulo'] = 'Facturación e Ingresos';
        return view('Modulos/facturacion/lista', $data);
    }

    public function getFacturacion()
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
        $paqueteId = $this->request->getGet('paquete_id');
        $estadoEmpresa = $this->request->getGet('estado_empresa');
        $busqueda = trim((string) $this->request->getGet('busqueda'));

        $db = \Config\Database::connect();

        // Query principal: empresas con su paquete y cálculo de totales
        $baseSql = "FROM empresa e
                    LEFT JOIN paquetes p ON p.id = e.paquete_id
                    WHERE 1=1";
        $params = [];

        if (!empty($empresaId)) {
            $baseSql .= " AND e.id = :empresa_id:";
            $params['empresa_id'] = (int) $empresaId;
        }
        if (!empty($paqueteId)) {
            $baseSql .= " AND e.paquete_id = :paquete_id:";
            $params['paquete_id'] = (int) $paqueteId;
        }
        if (!empty($estadoEmpresa)) {
            $baseSql .= " AND e.estado = :estado_empresa:";
            $params['estado_empresa'] = $estadoEmpresa;
        }
        if ($busqueda !== '') {
            $baseSql .= " AND (
                e.nombre LIKE :q:
                OR e.email LIKE :q:
                OR e.rut LIKE :q:
                OR p.nombre LIKE :q:
            )";
            $params['q'] = '%' . $busqueda . '%';
        }

        $total = (int) $db->query("SELECT COUNT(*) AS c {$baseSql}", $params)->getRow()->c;

        $sql = "SELECT
                    e.id AS empresa_id,
                    e.nombre AS empresa_nombre,
                    e.email AS empresa_email,
                    e.rut AS empresa_rut,
                    e.estado AS empresa_estado,
                    e.paquete_id,
                    p.nombre AS paquete_nombre,
                    COALESCE(p.precio_mensual, 0) AS precio_plan
                {$baseSql}
                ORDER BY e.nombre ASC";

        $rows = $db->query($sql, $params)->getResultArray();

        $data = [];
        $totalGlobal = 0;

        foreach ($rows as $r) {
            $empresaId = (int) $r['empresa_id'];
            $precioPlan = (float) $r['precio_plan'];

            // Obtener add-ons activos de esta empresa
            $addonsSql = "SELECT 
                            ea.id,
                            ea.tipo,
                            ea.referencia_id,
                            ea.precio_mensual,
                            COALESCE(mc.nombre, m.nombre, CONCAT('#', ea.referencia_id)) AS item_nombre,
                            COALESCE(mc.slug, '') AS item_slug
                          FROM empresa_addon ea
                          LEFT JOIN metodos_calculo mc ON (ea.tipo = 'metodo_calculo' AND mc.id = ea.referencia_id)
                          LEFT JOIN modulo m ON (ea.tipo = 'modulo' AND m.id = ea.referencia_id)
                          WHERE ea.empresa_id = :empresa_id: AND ea.estado = 'activo'
                          ORDER BY ea.tipo, item_nombre";
            
            $addonsParams = ['empresa_id' => $empresaId];
            $addons = $db->query($addonsSql, $addonsParams)->getResultArray();

            $totalAddons = 0;
            $addonsLista = [];
            foreach ($addons as $a) {
                $precio = (float) $a['precio_mensual'];
                $totalAddons += $precio;
                $addonsLista[] = [
                    'nombre' => $a['item_nombre'],
                    'slug' => $a['item_slug'],
                    'tipo' => $a['tipo'],
                    'precio' => $precio
                ];
            }

            $totalMensual = $precioPlan + $totalAddons;
            $totalGlobal += $totalMensual;

            // Formatear lista de add-ons para mostrar
            $addonsHtml = '';
            if (count($addonsLista) > 0) {
                $addonsHtml = '<ul class="list-unstyled mb-0">';
                foreach ($addonsLista as $a) {
                    $badgeTipo = $a['tipo'] === 'metodo_calculo' 
                        ? '<span class="badge bg-info">Método</span>' 
                        : '<span class="badge bg-secondary">Módulo</span>';
                    $addonsHtml .= '<li class="mb-1">' . esc($a['nombre']);
                    if ($a['slug']) {
                        $addonsHtml .= ' <small class="text-muted">(' . esc($a['slug']) . ')</small>';
                    }
                    $addonsHtml .= ' ' . $badgeTipo . ' <strong>$' . number_format($a['precio'], 0, ',', '.') . '</strong></li>';
                }
                $addonsHtml .= '</ul>';
            } else {
                $addonsHtml = '<span class="text-muted">Sin add-ons</span>';
            }

            $estadoBadge = match ($r['empresa_estado']) {
                'A' => '<span class="badge bg-success">Activa</span>',
                'I' => '<span class="badge bg-danger">Inactiva</span>',
                default => '<span class="badge bg-secondary">' . esc($r['empresa_estado']) . '</span>',
            };

            $data[] = [
                esc($r['empresa_nombre']) . '<br><small class="text-muted">' . esc($r['empresa_email']) . '</small>',
                esc($r['paquete_nombre'] ?? 'Sin paquete'),
                '$' . number_format($precioPlan, 0, ',', '.'),
                $addonsHtml,
                '$' . number_format($totalAddons, 0, ',', '.'),
                '<strong class="text-primary">$' . number_format($totalMensual, 0, ',', '.') . '</strong>',
                $estadoBadge,
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
            'totalGlobal' => $totalGlobal,
            'csrf_hash' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }
}
