<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\PerfilModulo;
use App\Models\Perfil;
use App\Models\Modulo;
use App\Traits\MaintainsFilters;


class PerfilDetalleController extends BaseController
{
    use MaintainsFilters;
    
    /**
     * Verificar si el usuario es Super Admin
     */
    private function esSuperAdmin()
    {
        $usuario = session()->get('usuario');
        return isset($usuario['poder']) && $usuario['poder'] == 3;
    }

    /**
     * Obtener empresa_id del usuario actual
     */
    private function getEmpresaIdUsuario()
    {
        $usuario = session()->get('usuario');
        return $usuario['empresa_id'] ?? null;
    }

    public function registro()
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $perfilModel = new Perfil();
        $moduloModel = new Modulo();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }

        // Obtener empresa_id del usuario
        $empresaId = $this->getEmpresaIdUsuario();
        
        // Obtener perfiles de la empresa
        $data['perfiles'] = $perfilModel->getActivePerfil($this->poder, $empresaId);
        
        // Obtener módulos del paquete de la empresa
        $data['modulos'] = $moduloModel->getActiveModulo($empresaId);
        
        $data['data'] = $menuTotal;
        echo view('Base/perfil_detalle/registro', $data);
    }

    public function registrar()
    {
        if (!$this->validate('formPerfilDetalleRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $perfilModulo = new PerfilModulo();
        $perfilModel = new Perfil();
        $moduloModel = new Modulo();
        
        $post = $this->request->getPost(['perfil', 'modulo', 'ver', 'registrar', 'editar', 'eliminar', 'orden']);
        
        // Validar que el perfil pertenezca a la empresa del usuario (si no es Super Admin)
        if (!$this->esSuperAdmin()) {
            $perfil = $perfilModel->find($post['perfil']);
            $empresaId = $this->getEmpresaIdUsuario();
            if ($perfil['empresa_id'] != $empresaId) {
                return redirect()->back()->withInput()->with('errors', 'No tienes permisos para asignar permisos a este perfil');
            }
            
            // Validar que el módulo esté en el paquete de la empresa
            $modulos = $moduloModel->getActiveModulo($empresaId);
            $modulosIds = array_column($modulos, 'id');
            if (!in_array($post['modulo'], $modulosIds)) {
                return redirect()->back()->withInput()->with('errors', 'El módulo seleccionado no está disponible en tu paquete');
            }
        }
        
        $data = [
            'perfil_id' => $post['perfil'],
            'modulo_id' => $post['modulo'],
            'ver' => $post['ver'],
            'registrar' => $post['registrar'],
            'editar' => $post['editar'],
            'eliminar' => $post['eliminar'],
            'estado' => 'A',
            'orden' => $post['orden']
        ];

        if ($perfilModulo->insert($data)) {
            return redirect()->to(base_url('dashboard/perfil-detalle/registro'))->with('success', 'Detalle Perfil registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el detalle perfil');
        }
    }
    public function getPerfilDetalle()
    {
        // Parámetros DataTables
        $draw      = (int) ($this->request->getGet('draw') ?? 1);
        $start     = (int) ($this->request->getGet('start') ?? 0);
        $length    = (int) ($this->request->getGet('length') ?? 10);
        $searchVal = (string) ($this->request->getGet('search')['value'] ?? '');
        $perfilId  = $this->request->getGet('perfil_id');
        $perfilId  = ($perfilId === '' || $perfilId === null) ? null : (int) $perfilId;

        // Orden
        $orderColIdx = (int) ($this->request->getGet('order')[0]['column'] ?? 0);
        $orderDir    = (string) ($this->request->getGet('order')[0]['dir'] ?? 'asc');
        $orderable   = [
            0 => 'p.nombre',                 // Perfil
            1 => 'm.nombre',                 // Módulo
            6 => 'pm.orden',                 // Orden
        ];
        $orderBy = $orderable[$orderColIdx] ?? 'pm.id';

        $poder = $this->poder;
        $empresaId = $this->getEmpresaIdUsuario();

        $model = new PerfilModulo();

        // Conteos - filtrar por empresa si no es Super Admin
        $recordsTotal    = $model->countAllByPower($poder, $empresaId);
        $recordsFiltered = $model->countFiltered($poder, $perfilId, $searchVal, $empresaId);

        // Página - filtrar por empresa si no es Super Admin
        $rows = $model->fetchPage($poder, $perfilId, $searchVal, $orderBy, $orderDir, $start, $length, $empresaId);

        // Mapear a columnas HTML (evitar XSS con esc())
        $data = array_map(static function (array $r): array {
            $badge = fn(bool $ok) => $ok
                ? '<span class="badge badge-success mb-2 me-4">Permitido</span>'
                : '<span class="badge badge-danger mb-2 me-4">No Permitido</span>';

            return [
                'id'              => (int)$r['id'],
                'perfil_nombre'   => esc($r['perfil_nombre']),
                'modulo_nombre'   => esc($r['modulo_nombre']),
                'ver_html'        => $badge((int)$r['ver'] === 1),
                'registrar_html'  => $badge((int)$r['registrar'] === 1),
                'editar_html'     => $badge((int)$r['editar'] === 1),
                'eliminar_html'   => $badge((int)$r['eliminar'] === 1),
                'orden'           => (int)$r['orden'],
                'estado_html'     => $r['estado'] === 'A'
                    ? '<span class="badge badge-success mb-2 me-4">Activo</span>'
                    : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                'acciones_html'   => '<a href="' . base_url('dashboard/perfil-detalle/editar/' . (int)$r['id']) . '" class="bs-tooltip" data-bs-toggle="tooltip" title="Editar">'
                    . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>'
                    . '<button type="button" value="' . (int)$r['id'] . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;">'
                    . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>',
            ];
        }, $rows);

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function lista()
    {
        $perfilId = (int) ($this->request->getGet('perfil_id') ?? 0);
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $perfilModel = new Perfil();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        $data['selectedPerfilId'] = $perfilId;
        
        // Obtener empresa_id del usuario
        $empresaId = $this->getEmpresaIdUsuario();
        
        // Obtener perfiles de la empresa
        $data['perfiles'] = $perfilModel->getActivePerfil($this->poder, $empresaId);
        
        echo view('Base/perfil_detalle/lista', $data);
    }

    public function editar($id)
    {
        $perfilModel = new Perfil();
        $perfilModulo = new PerfilModulo();
        $moduloModel = new Modulo();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $perfilDetalle = $perfilModulo->getPerfilModulo($id);
        
        // Validar que el perfil pertenezca a la empresa del usuario (si no es Super Admin)
        if (!$this->esSuperAdmin()) {
            $perfil = $perfilModel->find($perfilDetalle['perfil_id']);
            $empresaId = $this->getEmpresaIdUsuario();
            if ($perfil['empresa_id'] != $empresaId) {
                return redirect()->to(base_url('dashboard/perfil-detalle/lista'))
                    ->with('errors', 'No tienes permisos para editar este perfil detalle');
            }
        }
        
        $data['perfil'] = $perfilDetalle;
        
        // Obtener empresa_id del usuario
        $empresaId = $this->getEmpresaIdUsuario();
        
        // Obtener perfiles de la empresa
        $data['perfiles'] = $perfilModel->getActivePerfil($this->poder, $empresaId);
        
        // Obtener módulos del paquete de la empresa
        $data['modulos'] = $moduloModel->getActiveModulo($empresaId);
        
        echo view("Base/perfil_detalle/editar", $data);
    }

    public function update()
    {
        if (!$this->validate('formPerfilDetalleRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $perfilModulo = new PerfilModulo();
        $perfilModel = new Perfil();
        $moduloModel = new Modulo();
        
        $post = $this->request->getPost(['id', 'perfil', 'modulo', 'ver', 'registrar', 'editar', 'eliminar', 'orden', 'estado']);
        
        // Validar que el perfil pertenezca a la empresa del usuario (si no es Super Admin)
        if (!$this->esSuperAdmin()) {
            $perfil = $perfilModel->find($post['perfil']);
            $empresaId = $this->getEmpresaIdUsuario();
            if ($perfil['empresa_id'] != $empresaId) {
                return redirect()->back()->withInput()->with('errors', 'No tienes permisos para editar este perfil detalle');
            }
            
            // Validar que el módulo esté en el paquete de la empresa
            $modulos = $moduloModel->getActiveModulo($empresaId);
            $modulosIds = array_column($modulos, 'id');
            if (!in_array($post['modulo'], $modulosIds)) {
                return redirect()->back()->withInput()->with('errors', 'El módulo seleccionado no está disponible en tu paquete');
            }
        }
        
        $data = [
            'perfil_id' => $post['perfil'],
            'modulo_id' => $post['modulo'],
            'ver' => $post['ver'],
            'registrar' => $post['registrar'],
            'editar' => $post['editar'],
            'eliminar' => $post['eliminar'],
            'estado' => $post['estado'],
            'orden' => $post['orden']
        ];

        if ($perfilModulo->update($post['id'], $data)) {
            return redirect()->to(base_url('dashboard/perfil-detalle/editar/' . $post['id']))->with('success', 'Detalle Perfil editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el detalle perfil');
        }
    }

    public function eliminar()
    {
        $perfilModulo = new PerfilModulo();
        $perfilModel = new Perfil();
        $id = $this->request->getPost('id');
        
        // Obtener el perfil detalle para validar
        $perfilDetalle = $perfilModulo->find($id);
        if (!$perfilDetalle) {
            return redirect()->to(base_url('dashboard/perfil-detalle/lista'))
                ->with('errors', 'Perfil Detalle no encontrado.');
        }
        
        // Validar que el perfil pertenezca a la empresa del usuario (si no es Super Admin)
        if (!$this->esSuperAdmin()) {
            $perfil = $perfilModel->find($perfilDetalle['perfil_id']);
            $empresaId = $this->getEmpresaIdUsuario();
            if ($perfil['empresa_id'] != $empresaId) {
                return redirect()->to(base_url('dashboard/perfil-detalle/lista'))
                    ->with('errors', 'No tienes permisos para eliminar este perfil detalle');
            }
        }
        
        // Intenta eliminar el perfil detalle
        if ($perfilModulo->delete($id)) {
            // Obtener el filtro de perfil_id del POST para mantenerlo
            $perfilIdFilter = $this->request->getPost('perfil_id_filter');
            
            // Redirigir manteniendo el filtro
            return $this->redirectWithPostFilters(
                base_url('dashboard/perfil-detalle/lista'),
                ['perfil_id' => $perfilIdFilter],
                'Perfil Detalle eliminado con éxito.'
            );
        } else {
            // Error al eliminar
            return redirect()->to(base_url('dashboard/perfil-detalle/lista'))->with('errors', 'No se pudo eliminar el Perfil Detalle.');
        }
    }

    /**
     * Actualizar solo el orden vía AJAX (edición inline)
     */
    public function updateOrden()
    {
        // Verificar autenticación
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado'])->setStatusCode(401);
        }

        $id = $this->request->getPost('id');
        $orden = $this->request->getPost('orden');

        // Validar que sean números válidos
        if (!is_numeric($id) || !is_numeric($orden)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
        }

        $perfilModulo = new PerfilModulo();
        
        // Solo actualizar el campo orden
        if ($perfilModulo->update($id, ['orden' => (int)$orden])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Orden actualizado correctamente',
                'csrf_token' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el orden',
                'csrf_token' => csrf_hash()
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }
    }
}
