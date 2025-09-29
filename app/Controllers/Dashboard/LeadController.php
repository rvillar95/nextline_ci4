<?php
declare(strict_types=1);

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\LeadModel;
use App\Models\ModuloDetalle;
use App\Traits\MaintainsFilters;

final class LeadController extends BaseController
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

        return view('Modulos/leads/lista', $data);
    }

    public function getLeads()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $m = new LeadModel();
        
        // Obtener filtros
        $estado_id = $this->request->getGet('estado_id');
        $servicio_id = $this->request->getGet('servicio_id');
        
        // Construir la consulta base
        $query = $m->select('lead_contacto.*, servicio.nombre as servicio_nombre, lead_estado.nombre as estado_nombre')
                  ->join('servicio', 'servicio.id = lead_contacto.servicio_id', 'left')
                  ->join('lead_estado', 'lead_estado.id = lead_contacto.estado_id', 'left');
        
        // Aplicar filtros
        if (!empty($estado_id)) {
            $query->where('lead_contacto.estado_id', $estado_id);
        }
        
        if (!empty($servicio_id)) {
            $query->where('lead_contacto.servicio_id', $servicio_id);
        }
        
        $rows = $query->orderBy('lead_contacto.id','DESC')->findAll(500);

        $data = [];
        foreach ($rows as $r) {
            $estado = (int) $r['estado_id'];
            $badge = match ($estado) {
                1 => '<span class="badge badge-info">Nuevo</span>',
                2 => '<span class="badge badge-warning">En gestión</span>',
                3 => '<span class="badge badge-primary">Contactado</span>',
                4 => '<span class="badge badge-success">Cerrado</span>',
                default => '<span class="badge badge-secondary">N/A</span>',
            };

            $fecha = 'N/A';
            if (!empty($r['fcreacion']) && $r['fcreacion'] !== '0000-00-00 00:00:00') {
                try {
                    $fecha = date('d/m/Y H:i', strtotime($r['fcreacion']));
                } catch (Exception $e) {
                    $fecha = 'Fecha inválida';
                }
            }

            $data[] = [
                esc($r['nombre']),
                esc($r['correo']),
                esc($r['telefono'] ?? ''),
                esc(mb_substr((string)$r['mensaje'], 0, 80)) . '...',
                esc($r['servicio_nombre'] ?? 'Sin servicio'),
                $badge,
                $fecha,
                '<button class="btn btn-sm btn-outline-primary btn-cambiar-estado" data-id="'.$r['id'].'" data-estado="3">Contactado</button> ' .
                '<button class="btn btn-sm btn-outline-success btn-cambiar-estado" data-id="'.$r['id'].'" data-estado="4">Cerrado</button> ' .
                '<button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="'.$r['id'].'">Eliminar</button>',
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function getServicios()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $servicioModel = new \App\Models\Servicio();
        $servicios = $servicioModel->select('id, nombre')
                                 ->where('estado', 'A')
                                 ->orderBy('nombre', 'ASC')
                                 ->findAll();

        // Debug: log para ver qué se está devolviendo
        log_message('debug', 'Servicios encontrados: ' . json_encode($servicios));

        return $this->response->setJSON($servicios);
    }

    public function cambiarEstado()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $id = (int) $this->request->getPost('id');
        $estado = (int) $this->request->getPost('estado_id');

        if ($id <= 0 || $estado <= 0) {
            return $this->response->setJSON(['error' => 'Datos inválidos'])->setStatusCode(400);
        }

        $m = new LeadModel();
        $result = $m->update($id, ['estado_id' => $estado, 'factualizacion' => date('Y-m-d H:i:s')]);

        if ($result) {
            return $this->response->setJSON([
                'ok' => true,
                'csrf_token' => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON(['error' => 'Error al actualizar'])->setStatusCode(500);
        }
    }

    public function eliminar()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->redirectWithFilters(base_url('dashboard/login'), 'Sesión expirada', 'errors');
        }

        $id = (int) $this->request->getPost('id');
        $m = new LeadModel();
        
        if ($m->delete($id)) {
            return $this->redirectWithFilters(base_url('dashboard/leads/lista'), 'Lead eliminado con éxito', 'success');
        } else {
            return $this->redirectWithFilters(base_url('dashboard/leads/lista'), 'Error al eliminar el lead', 'errors');
        }
    }
}
