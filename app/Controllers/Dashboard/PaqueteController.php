<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Paquete;
use App\Models\ModuloDetalle;
use App\Models\Modulo;
use App\Traits\MaintainsFilters;

class PaqueteController extends BaseController
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
     * Lista de paquetes (solo SA)
     */
    public function lista()
    {
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['titulo'] = 'Gestión de Paquetes';
        return view('Modulos/paquete/lista', $data);
    }

    /**
     * Obtener paquetes para DataTable (solo SA)
     */
    public function getPaquetes()
    {
        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(403);
        }

        $paquete = new Paquete();
        $draw = intval($this->request->getGet("draw")) ?: 1;
        
        $activo = $this->request->getGet('activo');
        $busqueda = $this->request->getGet('busqueda');
        
        $query = $paquete;
        
        // Aplicar filtro de estado solo si se especifica
        if (!empty($activo) && $activo !== '') {
            $query = $query->where('activo', $activo);
        }
        
        // Aplicar búsqueda
        if (!empty($busqueda) && trim($busqueda) !== '') {
            $query->groupStart()
                  ->like('nombre', $busqueda)
                  ->orLike('slug', $busqueda)
                  ->orLike('descripcion', $busqueda)
                  ->groupEnd();
        }
        
        $rows = $query->orderBy('orden', 'ASC')
                     ->orderBy('id', 'ASC')
                     ->findAll();

        $data = array();
        foreach ($rows as $r) {
            // Contar módulos del paquete
            $db = \Config\Database::connect();
            $modulosCount = $db->table('paquete_modulo')
                ->where('paquete_id', $r->id)
                ->where('incluido', 'S')
                ->countAllResults();

            // Contar empresas con este paquete
            $empresasCount = $db->table('empresa')
                ->where('paquete_id', $r->id)
                ->where('estado', 'A')
                ->countAllResults();

            $activoBadge = $r->activo == 'A' 
                ? '<span class="badge bg-success">Activo</span>' 
                : '<span class="badge bg-danger">Inactivo</span>';

            $precio = '';
            if ($r->precio_setup > 0 || $r->precio_mensual > 0) {
                $precio = '<small class="text-muted">';
                if ($r->precio_setup > 0) {
                    $precio .= 'Setup: $' . number_format($r->precio_setup, 0, ',', '.');
                }
                if ($r->precio_mensual > 0) {
                    if ($r->precio_setup > 0) $precio .= ' | ';
                    $precio .= 'Mensual: $' . number_format($r->precio_mensual, 0, ',', '.');
                }
                $precio .= '</small>';
            }

            $botones = '';
            if ($r->activo == 'A') {
                $botones = '<button class="btn btn-sm btn-outline-primary" onclick="editarPaquete(' . $r->id . ')">Editar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDetallePaquete(' . $r->id . ')">Ver</button> ' .
                          '<button class="btn btn-sm btn-outline-warning" onclick="gestionarModulos(' . $r->id . ')">Módulos</button> ' .
                          '<button class="btn btn-sm btn-outline-danger" onclick="eliminarPaquete(' . $r->id . ')">Desactivar</button>';
            } else {
                $botones = '<button class="btn btn-sm btn-outline-success" onclick="activarPaquete(' . $r->id . ')">Activar</button> ' .
                          '<button class="btn btn-sm btn-outline-info" onclick="verDetallePaquete(' . $r->id . ')">Ver</button>';
            }

            $data[] = array(
                esc($r->nombre),
                esc($r->slug),
                esc($r->descripcion ?? ''),
                $precio,
                '<span class="badge bg-info">' . $modulosCount . '</span>',
                '<span class="badge bg-secondary">' . $empresasCount . '</span>',
                $activoBadge,
                $botones
            );
        }

        // Para serverSide: false, DataTables espera solo el array de datos
        // Pero mantenemos el formato completo por si acaso
        $output = array(
            "draw" => $draw,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

    /**
     * Formulario de registro (crear nuevo paquete)
     */
    public function registro()
    {
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $data['paquete'] = null;
        $data['titulo'] = 'Crear Nuevo Paquete';
        return view('Modulos/paquete/registro', $data);
    }

    /**
     * Formulario de edición
     */
    public function editar($id = null)
    {
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $paquete = new Paquete();
        $data['paquete'] = $paquete->find($id);
        
        if (!$data['paquete']) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('error', 'Paquete no encontrado');
        }

        $data['titulo'] = 'Editar Paquete';
        return view('Modulos/paquete/registro', $data);
    }

    /**
     * Crear nuevo paquete
     */
    public function registrar()
    {
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('error', 'No tienes permisos para crear paquetes');
        }

        $paquete = new Paquete();
        
        $data = [
            // IMPORTANTE: incluir id para que la regla is_unique[paquetes.slug,id,{id}]
            // pueda excluir este mismo registro al validar en update()
            'id' => $id,
            'nombre' => $this->request->getPost('nombre'),
            'slug' => $this->request->getPost('slug'),
            'descripcion' => $this->request->getPost('descripcion'),
            'precio_setup' => $this->request->getPost('precio_setup') ?: 0,
            'precio_mensual' => $this->request->getPost('precio_mensual') ?: 0,
            'activo' => $this->request->getPost('activo') ?: 'A',
            'orden' => $this->request->getPost('orden') ?: 0
        ];

        if ($paquete->insert($data)) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('success', 'Paquete creado con éxito');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $paquete->errors());
        }
    }

    /**
     * Actualizar paquete existente
     */
    public function update()
    {
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('error', 'No tienes permisos para editar paquetes');
        }

        $id = $this->request->getPost('id');
        if (!$id) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('error', 'ID de paquete requerido');
        }
        $id = (int) $id;

        $paquete = new Paquete();
        $paqueteExistente = $paquete->find($id);
        
        if (!$paqueteExistente) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('error', 'Paquete no encontrado');
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'slug' => $this->request->getPost('slug'),
            'descripcion' => $this->request->getPost('descripcion'),
            'precio_setup' => $this->request->getPost('precio_setup') ?: 0,
            'precio_mensual' => $this->request->getPost('precio_mensual') ?: 0,
            'activo' => $this->request->getPost('activo') ?: 'A',
            'orden' => $this->request->getPost('orden') ?: 0
        ];

        // FIX: Forzar regla de unicidad excluyendo este mismo ID
        // (Evita falsos positivos cuando el placeholder {id} no es sustituido)
        $paquete->setValidationRules([
            'nombre' => 'required|min_length[3]|max_length[100]',
            'slug'   => "required|min_length[3]|max_length[100]|is_unique[paquetes.slug,id,{$id}]",
        ]);

        if ($paquete->update($id, $data)) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('success', 'Paquete actualizado con éxito');
        } else {
            return redirect()->back()
                ->withInput()
                ->with('errors', $paquete->errors());
        }
    }

    /**
     * Gestionar módulos del paquete
     */
    public function gestionarModulos($id)
    {
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $paquete = new Paquete();
        $data['paquete'] = $paquete->find($id);
        
        if (!$data['paquete']) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('error', 'Paquete no encontrado');
        }

        // Obtener todos los módulos disponibles (excluyendo SA)
        $moduloModel = new Modulo();
        $data['modulos'] = $moduloModel->where('estado', 'A')
            ->where('sa', 'N')
            ->orderBy('nombre', 'ASC')
            ->findAll();

        // Obtener módulos asignados al paquete
        $db = \Config\Database::connect();
        $modulosAsignados = $db->table('paquete_modulo')
            ->where('paquete_id', $id)
            ->where('incluido', 'S')
            ->get()
            ->getResultArray();

        $data['modulosAsignados'] = array_column($modulosAsignados, 'modulo_id');

        $data['titulo'] = 'Gestionar Módulos del Paquete';
        return view('Modulos/paquete/gestionar_modulos', $data);
    }

    /**
     * Guardar módulos del paquete
     */
    public function guardarModulos()
    {
        if (!$this->esSuperAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No autorizado'
            ])->setStatusCode(403);
        }

        $paquete_id = $this->request->getPost('paquete_id');
        $modulos = $this->request->getPost('modulos'); // Array de módulo_id

        if (!$paquete_id) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID de paquete requerido'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        // Eliminar todos los módulos del paquete
        $db->table('paquete_modulo')
            ->where('paquete_id', $paquete_id)
            ->delete();

        // Insertar módulos seleccionados
        if (!empty($modulos) && is_array($modulos)) {
            $dataInsert = [];
            foreach ($modulos as $modulo_id) {
                $dataInsert[] = [
                    'paquete_id' => $paquete_id,
                    'modulo_id' => $modulo_id,
                    'incluido' => 'S',
                    'fcreacion' => date('Y-m-d H:i:s')
                ];
            }
            
            if (!empty($dataInsert)) {
                $db->table('paquete_modulo')->insertBatch($dataInsert);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Módulos actualizados con éxito',
            'csrf_token' => csrf_hash()
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    /**
     * Ver detalle de paquete
     */
    public function detalle($id)
    {
        if (!$this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $paquete = new Paquete();
        $data['paquete'] = $paquete->find($id);
        
        if (!$data['paquete']) {
            return redirect()->to(base_url('dashboard/paquete/lista'))
                ->with('error', 'Paquete no encontrado');
        }

        // Cargar información adicional
        $db = \Config\Database::connect();
        
        // Obtener módulos del paquete
        $data['modulos'] = $db->table('paquete_modulo pm')
            ->select('m.id, m.nombre, m.ruta, m.descripcion, pm.incluido')
            ->join('modulo m', 'm.id = pm.modulo_id')
            ->where('pm.paquete_id', $id)
            ->where('pm.incluido', 'S')
            ->get()
            ->getResult('array');

        // Obtener empresas con este paquete
        $data['empresas'] = $db->table('empresa e')
            ->select('e.id, e.nombre, e.email, e.estado, COUNT(DISTINCT u.id) AS cantidad_usuarios')
            ->join('usuario u', 'u.empresa_id = e.id AND u.estado = "A"', 'left')
            ->where('e.paquete_id', $id)
            ->groupBy('e.id, e.nombre, e.email, e.estado')
            ->get()
            ->getResult('array');

        $data['titulo'] = 'Detalle de Paquete';
        
        return view('Modulos/paquete/detalle', $data);
    }

    /**
     * Eliminar/Desactivar paquete (solo SA)
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
                'error' => 'ID de paquete requerido'
            ])->setStatusCode(400);
        }

        $paquete = new Paquete();
        
        // Verificar si hay empresas usando este paquete
        $db = \Config\Database::connect();
        $empresasCount = $db->table('empresa')
            ->where('paquete_id', $id)
            ->where('estado', 'A')
            ->countAllResults();

        if ($empresasCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'error' => "No se puede desactivar el paquete porque está siendo usado por {$empresasCount} empresa(s) activa(s). Primero cambia el paquete de esas empresas."
            ])->setStatusCode(400);
        }

        // Desactivar paquete en lugar de eliminar
        if ($paquete->update($id, ['activo' => 'I'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Paquete desactivado con éxito'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al desactivar el paquete'
            ])->setStatusCode(500);
        }
    }

    /**
     * Activar paquete (solo SA)
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
                'error' => 'ID de paquete requerido'
            ])->setStatusCode(400);
        }

        $paquete = new Paquete();
        
        if ($paquete->update($id, ['activo' => 'A'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Paquete activado con éxito'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al activar el paquete'
            ])->setStatusCode(500);
        }
    }
}
