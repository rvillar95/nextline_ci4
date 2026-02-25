<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Empresa;
use App\Models\ModuloDetalle;
use App\Models\Paquete;
use App\Traits\MaintainsFilters;

class EmpresaController extends BaseController
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

    /**
     * Lista de empresas
     * - SA: Ve todas las empresas
     * - Usuarios normales: Redirige a editar su propia empresa
     */
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

        // Si no es Super Admin, redirigir a editar su propia empresa
        if (!$this->esSuperAdmin()) {
            $empresaId = $this->getEmpresaIdUsuario();
            if ($empresaId) {
                return redirect()->to(base_url('dashboard/empresa/editar/' . $empresaId));
            } else {
                return redirect()->to(base_url('dashboard/empresa/registro'));
            }
        }

        // Super Admin: mostrar lista de todas las empresas
        $data['titulo'] = 'Gestión de Empresas';
        return view('Modulos/empresa/lista', $data);
    }

    /**
     * Obtener empresas para DataTable (solo SA)
     */
    public function getEmpresas()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        // Solo Super Admin puede ver todas las empresas
        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(403);
        }

        $draw = intval($this->request->getGet("draw")) ?: 1;
        $data = array();

        try {
            $empresa = new Empresa();
            $estado = $this->request->getGet('estado');
            $paquete_id = $this->request->getGet('paquete_id');
            $busqueda = $this->request->getGet('busqueda');
            
            $filtros = [];
            if (!empty($estado)) {
                $filtros['estado'] = $estado;
            }
            if (!empty($paquete_id)) {
                $filtros['paquete_id'] = $paquete_id;
            }
            if (!empty($busqueda)) {
                $filtros['busqueda'] = $busqueda;
            }
            
            $rows = $empresa->getEmpresasCompletas($filtros);
        } catch (\Throwable $e) {
            log_message('error', 'EmpresaController::getEmpresas: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error al cargar empresas: ' . $e->getMessage()
            ])->setStatusCode(500);
        }

        foreach ($rows as $r) {
            $estadoBadge = $r->estado == 'A' 
                ? '<span class="badge bg-success">Activo</span>' 
                : '<span class="badge bg-danger">Inactivo</span>';

            $paqueteBadge = $r->paquete_nombre 
                ? '<span class="badge bg-info">' . esc($r->paquete_nombre) . '</span>'
                : '<span class="badge bg-secondary">Sin Paquete</span>';

            $botones = '';
            if ($r->estado == 'A') {
                $botones = '<button class="btn btn-sm btn-outline-primary" onclick="editarEmpresa(' . $r->id . ')">Editar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDetalleEmpresa(' . $r->id . ')">Ver</button> ' .
                          '<button class="btn btn-sm btn-outline-danger" onclick="eliminarEmpresa(' . $r->id . ')">Eliminar</button>';
            } else {
                $botones = '<button class="btn btn-sm btn-outline-success" onclick="activarEmpresa(' . $r->id . ')">Activar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDetalleEmpresa(' . $r->id . ')">Ver</button>';
            }

            $data[] = array(
                esc($r->nombre),
                esc($r->nombre_comercial ?? ''),
                esc($r->rut ?? ''),
                esc($r->email ?? ''),
                esc($r->telefono ?? ''),
                $paqueteBadge,
                '<span class="badge bg-secondary">' . $r->cantidad_usuarios . '</span>',
                $estadoBadge,
                $botones
            );
        }

        $output = array(
            "draw" => $draw,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    /**
     * Formulario de registro (crear nueva empresa)
     * Solo para Super Admin
     */
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

        // Si no es Super Admin, obtener su propia empresa
        if (!$this->esSuperAdmin()) {
            $empresa = new Empresa();
            $empresaId = $this->getEmpresaIdUsuario();
            if ($empresaId) {
                $data['empresa'] = $empresa->find($empresaId);
            } else {
                $data['empresa'] = null;
            }
            $data['es_super_admin'] = false;
        } else {
            $data['empresa'] = null;
            $data['es_super_admin'] = true;
        }

        // Cargar paquetes para el selector (solo SA)
        $paqueteModel = new Paquete();
        $data['paquetes'] = $paqueteModel->where('activo', 'A')->findAll();

        $data['titulo'] = $data['empresa'] ? 'Editar Empresa' : 'Crear Nueva Empresa';
        return view('Modulos/empresa/registro', $data);
    }

    /**
     * Formulario de edición
     */
    public function editar($id = null)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $empresa = new Empresa();
        
        // Verificar permisos
        if (!$this->esSuperAdmin()) {
            // Usuario normal: solo puede editar su propia empresa
            $empresaId = $this->getEmpresaIdUsuario();
            if ($id != $empresaId) {
                return redirect()->to(base_url('dashboard/empresa/editar/' . $empresaId))
                    ->with('error', 'No tienes permisos para editar esta empresa');
            }
        }

        $data['empresa'] = $empresa->find($id);
        if (!$data['empresa']) {
            return redirect()->to(base_url('dashboard/empresa/lista'))
                ->with('error', 'Empresa no encontrada');
        }

        $data['es_super_admin'] = $this->esSuperAdmin();

        // Cargar paquetes para el selector (solo SA)
        $paqueteModel = new Paquete();
        $data['paquetes'] = $paqueteModel->where('activo', 'A')->findAll();

        $data['titulo'] = 'Editar Empresa';
        return view('Modulos/empresa/registro', $data);
    }

    /**
     * Crear nueva empresa
     */
    public function registrar()
    {
        // Solo Super Admin puede crear empresas
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/empresa/registro'))
                ->with('error', 'No tienes permisos para crear empresas');
        }

        $empresa = new Empresa();
        
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'nombre_comercial' => $this->request->getPost('nombre_comercial'),
            'rut' => $this->request->getPost('rut'),
            'direccion' => $this->request->getPost('direccion'),
            'url_google_maps' => $this->request->getPost('url_google_maps') ?: null,
            'telefono' => $this->request->getPost('telefono'),
            'email' => $this->request->getPost('email'),
            'sitio_web' => $this->request->getPost('sitio_web'),
            'logo_path' => $this->request->getPost('logo_path'),
            'descripcion' => $this->request->getPost('descripcion'),
            'mision' => $this->request->getPost('mision'),
            'vision' => $this->request->getPost('vision'),
            'valores' => $this->request->getPost('valores'),
            'paquete_id' => $this->request->getPost('paquete_id') ?: null,
            'estado' => 'A'
        ];

        if ($empresa->insert($data)) {
            return redirect()->to(base_url('dashboard/empresa/lista'))
                ->with('success', 'Empresa creada con éxito');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $empresa->errors());
        }
    }

    /**
     * Actualizar empresa existente
     */
    public function update()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return redirect()->to(base_url('dashboard/empresa/lista'))
                ->with('error', 'ID de empresa requerido');
        }

        $empresa = new Empresa();
        $empresaExistente = $empresa->find($id);
        
        if (!$empresaExistente) {
            return redirect()->to(base_url('dashboard/empresa/lista'))
                ->with('error', 'Empresa no encontrada');
        }

        // Verificar permisos
        if (!$this->esSuperAdmin()) {
            // Usuario normal: solo puede editar su propia empresa
            $empresaId = $this->getEmpresaIdUsuario();
            if ($id != $empresaId) {
                return redirect()->to(base_url('dashboard/empresa/editar/' . $empresaId))
                    ->with('error', 'No tienes permisos para editar esta empresa');
            }
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'nombre_comercial' => $this->request->getPost('nombre_comercial'),
            'rut' => $this->request->getPost('rut'),
            'direccion' => $this->request->getPost('direccion'),
            'url_google_maps' => $this->request->getPost('url_google_maps') ?: null,
            'telefono' => $this->request->getPost('telefono'),
            'email' => $this->request->getPost('email'),
            'sitio_web' => $this->request->getPost('sitio_web'),
            'logo_path' => $this->request->getPost('logo_path'),
            'descripcion' => $this->request->getPost('descripcion'),
            'mision' => $this->request->getPost('mision'),
            'vision' => $this->request->getPost('vision'),
            'valores' => $this->request->getPost('valores'),
        ];

        // Solo SA puede cambiar el paquete
        if ($this->esSuperAdmin()) {
            $data['paquete_id'] = $this->request->getPost('paquete_id') ?: null;
        }

        if ($empresa->update($id, $data)) {
            $redirectUrl = $this->esSuperAdmin() 
                ? base_url('dashboard/empresa/lista')
                : base_url('dashboard/empresa/editar/' . $id);
            
            return redirect()->to($redirectUrl)
                ->with('success', 'Empresa actualizada con éxito');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $empresa->errors());
        }
    }

    /**
     * Eliminar empresa (solo SA)
     */
    public function eliminar($id = null)
    {
        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No autorizado'
            ])->setStatusCode(403);
        }

        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID de empresa requerido'
            ])->setStatusCode(400);
        }

        $empresa = new Empresa();
        
        // Verificar si hay usuarios asociados
        $db = \Config\Database::connect();
        $usuariosCount = $db->table('usuario')
            ->where('empresa_id', $id)
            ->where('estado', 'A')
            ->countAllResults();

        if ($usuariosCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'error' => "No se puede eliminar la empresa porque tiene {$usuariosCount} usuario(s) activo(s). Primero elimina o desactiva los usuarios."
            ])->setStatusCode(400);
        }

        // Desactivar empresa en lugar de eliminar
        if ($empresa->update($id, ['estado' => 'I'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Empresa desactivada con éxito'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al desactivar la empresa'
            ])->setStatusCode(500);
        }
    }

    /**
     * Activar empresa (solo SA)
     */
    public function activar($id = null)
    {
        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No autorizado'
            ])->setStatusCode(403);
        }

        if (!$id) {
            $id = $this->request->getPost('id');
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID de empresa requerido'
            ])->setStatusCode(400);
        }

        $empresa = new Empresa();
        
        if ($empresa->update($id, ['estado' => 'A'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Empresa activada con éxito'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al activar la empresa'
            ])->setStatusCode(500);
        }
    }

    /**
     * Ver detalle de empresa
     */
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

        $empresa = new Empresa();
        $data['empresa'] = $empresa->find($id);
        
        if (!$data['empresa']) {
            return redirect()->to(base_url('dashboard/empresa/lista'))
                ->with('error', 'Empresa no encontrada');
        }

        // Verificar permisos
        if (!$this->esSuperAdmin()) {
            $empresaId = $this->getEmpresaIdUsuario();
            if ($id != $empresaId) {
                return redirect()->to(base_url('dashboard/empresa/detalle/' . $empresaId))
                    ->with('error', 'No tienes permisos para ver esta empresa');
            }
        }

        // Cargar información adicional
        $db = \Config\Database::connect();
        
        // Obtener paquete
        if ($data['empresa']->paquete_id) {
            $paqueteModel = new Paquete();
            $data['paquete'] = $paqueteModel->find($data['empresa']->paquete_id);
        } else {
            $data['paquete'] = null;
        }

        // Obtener usuarios de la empresa
        $data['usuarios'] = $db->table('usuario u')
            ->select('u.id, u.nombre, u.apellido, u.correo, u.estado, p.nombre AS perfil_nombre')
            ->join('perfil p', 'p.id = u.perfil_id', 'left')
            ->where('u.empresa_id', $id)
            ->get()
            ->getResult('array');

        // Obtener módulos del paquete
        if ($data['empresa']->paquete_id) {
            $data['modulos'] = $db->table('paquete_modulo pm')
                ->select('m.id, m.nombre, m.ruta, pm.incluido')
                ->join('modulo m', 'm.id = pm.modulo_id')
                ->where('pm.paquete_id', $data['empresa']->paquete_id)
                ->where('pm.incluido', 'S')
                ->get()
                ->getResult('array');
        } else {
            $data['modulos'] = [];
        }

        $data['es_super_admin'] = $this->esSuperAdmin();
        $data['titulo'] = 'Detalle de Empresa';
        
        return view('Modulos/empresa/detalle', $data);
    }
}
