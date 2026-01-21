<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;
use App\Models\Perfil;

class PerfilController extends BaseController
{
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
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        echo view('Base/perfil/registro', $data);
    }

    public function registrar()
    {
        if (!$this->validate('formPerfilRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $perfilModel = new Perfil();
        $post = $this->request->getPost(['nombre', 'estado']);
        
        // Obtener empresa_id del usuario
        $empresaId = $this->getEmpresaIdUsuario();
        
        // Si es Super Admin, puede crear perfiles globales (empresa_id = NULL)
        // Si no es Super Admin, debe asignar empresa_id
        $data = [
            'nombre' => $post['nombre'],
            'estado' => $post['estado'],
            'poder' => 0,
            'empresa_id' => $this->esSuperAdmin() ? null : $empresaId
        ];
        
        // Validar que usuarios no-SA tengan empresa_id
        if (!$this->esSuperAdmin() && $empresaId === null) {
            return redirect()->back()->withInput()->with('errors', 'No se pudo determinar la empresa del usuario');
        }

        if ($perfilModel->insert($data)) {
            return redirect()->to(base_url('dashboard/perfil/registro'))->with('success', 'Perfil registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el perfil');
        }
    }

    public function getPerfiles()
    {
        $perfilModel = new Perfil();
        $draw = intval($this->request->getGet("draw"));
        
        // Obtener perfiles filtrados por empresa
        $empresaId = $this->getEmpresaIdUsuario();
        $books = $perfilModel->getPerfilAll($empresaId);

        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombre,
                $r->estado == 'A' ? '<span class="badge badge-success mb-2 me-4">Activo</span>' : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>',
                '<a href="editar/' . $r->id . '" style="display:inline-block;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                <button type="button" value="' . $r->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => count($books),
            "recordsFiltered" => count($books),
            "data" => $data
        );

        return $this->response->setJSON($output);
    }

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
        echo view('Base/perfil/lista', $data);
    }

    public function editar($id)
    {
        $perfilModel = new Perfil();
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);
        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;

        $perfil = $perfilModel->select('perfil.*')
            ->where('perfil.id', $id)->first();
        
        // Validar que el perfil pertenezca a la empresa del usuario (si no es Super Admin)
        if (!$this->esSuperAdmin()) {
            $empresaId = $this->getEmpresaIdUsuario();
            if ($perfil['empresa_id'] != $empresaId) {
                return redirect()->to(base_url('dashboard/perfil/lista'))
                    ->with('errors', 'No tienes permisos para editar este perfil');
            }
        }
        
        $data['perfil'] = $perfil;
        echo view("Base/perfil/editar", $data);
    }

    public function update()
    {
        if (!$this->validate('formPerfilEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $perfilModel = new Perfil();
        $id = $this->request->getPost('id');
        
        // Validar que el perfil pertenezca a la empresa del usuario (si no es Super Admin)
        if (!$this->esSuperAdmin()) {
            $perfil = $perfilModel->find($id);
            $empresaId = $this->getEmpresaIdUsuario();
            if ($perfil['empresa_id'] != $empresaId) {
                return redirect()->to(base_url('dashboard/perfil/lista'))
                    ->with('errors', 'No tienes permisos para editar este perfil');
            }
        }
        
        $nombre = $this->request->getPost('nombre');
        $estado = $this->request->getPost('estado');

        if ($perfilModel->update($id, [
            'nombre' => $nombre,
            'estado' => $estado
        ])) {
            return redirect()->to(base_url('dashboard/perfil/editar/' . $id))->with('success', 'Perfil editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el perfil');
        }
    }

    public function eliminar()
    {
        $perfilModel = new Perfil();
        $id = $this->request->getPost('id');
        
        // Validar que el perfil pertenezca a la empresa del usuario (si no es Super Admin)
        if (!$this->esSuperAdmin()) {
            $perfil = $perfilModel->find($id);
            $empresaId = $this->getEmpresaIdUsuario();
            if ($perfil['empresa_id'] != $empresaId) {
                return redirect()->to(base_url('dashboard/perfil/lista'))
                    ->with('errors', 'No tienes permisos para eliminar este perfil');
            }
        }
        
        // Verificar si hay usuarios asociados a este perfil
        $db = \Config\Database::connect();
        $usuariosAsociados = $db->table('usuario')->where('perfil_id', $id)->countAllResults();
        
        if ($usuariosAsociados > 0) {
            return redirect()->to(base_url('dashboard/perfil/lista'))
                ->with('errors', 'No se puede eliminar el perfil porque tiene ' . $usuariosAsociados . ' usuario(s) asociado(s). Primero debe reasignar o eliminar esos usuarios.');
        }
        
        // Verificar si hay módulos/permisos asociados a este perfil
        $modulosAsociados = $db->table('perfil_modulo')->where('perfil_id', $id)->countAllResults();
        
        if ($modulosAsociados > 0) {
            // Eliminar primero los permisos asociados
            $db->table('perfil_modulo')->where('perfil_id', $id)->delete();
        }
        
        // Intenta eliminar el perfil
        if ($perfilModel->delete($id)) {
            return redirect()->to(base_url('dashboard/perfil/lista'))->with('success', 'Perfil eliminado con éxito.');
        } else {
            return redirect()->to(base_url('dashboard/perfil/lista'))->with('errors', 'No se pudo eliminar el perfil.');
        }
    }
}
