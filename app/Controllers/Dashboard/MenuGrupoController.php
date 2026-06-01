<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\MenuGrupo;
use App\Models\ModuloDetalle;

class MenuGrupoController extends BaseController
{
    private function esSuperAdmin(): bool
    {
        $usuario = session()->get('usuario');

        return isset($usuario['poder']) && (int) $usuario['poder'] === 3;
    }

    private function cargarLayoutData(): array
    {
        $menuTotal = [];
        $modulo = new ModuloDetalle();
        $menu = $modulo->getMenu(session()->get('usuario')['perfil_id'] ?? 0);
        foreach ($menu as $entity) {
            $menuTotal[] = ['menu' => $entity, 'submenu' => $modulo->getSubMenu($entity['id'])];
        }

        return ['data' => $menuTotal];
    }

    public function lista()
    {
        if (! $this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        return view('Base/menu_grupo/lista', $this->cargarLayoutData());
    }

    public function getMenuGrupos()
    {
        if (! $this->esSuperAdmin()) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(403);
        }

        $model = new MenuGrupo();
        $draw = (int) ($this->request->getGet('draw') ?? 1);
        $rows = $model->getTodosOrdenados();

        $data = [];
        foreach ($rows as $r) {
            $id = (int) $r['id'];
            $modulosCount = 0;
            if ($model->db->fieldExists('menu_grupo_id', 'modulo')) {
                $modulosCount = (int) $model->db->table('modulo')->where('menu_grupo_id', $id)->countAllResults();
            }

            $data[] = [
                esc($r['etiqueta']),
                esc($r['slug']),
                (int) $r['orden'],
                $r['estado'] === 'A'
                    ? '<span class="badge bg-success">Activo</span>'
                    : '<span class="badge bg-secondary">Inactivo</span>',
                (string) $modulosCount,
                '<a href="' . base_url('dashboard/menu-grupo/editar/' . $id) . '" class="bs-tooltip" title="Editar">'
                . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>'
                . '<button type="button" value="' . $id . '" class="btn-eliminar-grupo" style="background:none;border:none;padding:0;cursor:pointer;display:inline-block;">'
                . '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>',
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => count($rows),
            'recordsFiltered' => count($rows),
            'data' => $data,
        ]);
    }

    public function registro()
    {
        if (! $this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        return view('Base/menu_grupo/registro', $this->cargarLayoutData());
    }

    public function registrar()
    {
        if (! $this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))->with('error', 'No autorizado');
        }

        if (! $this->validate('formMenuGrupo')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new MenuGrupo();
        $post = $this->request->getPost(['slug', 'etiqueta', 'orden', 'estado']);

        if ($model->insert([
            'slug' => strtolower(trim((string) $post['slug'])),
            'etiqueta' => trim((string) $post['etiqueta']),
            'orden' => (int) $post['orden'],
            'estado' => $post['estado'],
        ])) {
            return redirect()->to(base_url('dashboard/menu-grupo/lista'))
                ->with('success', 'Sección de menú creada con éxito');
        }

        return redirect()->back()->withInput()->with('errors', 'Error al crear la sección');
    }

    public function editar($id)
    {
        if (! $this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $model = new MenuGrupo();
        $grupo = $model->find($id);
        if (! $grupo) {
            return redirect()->to(base_url('dashboard/menu-grupo/lista'))
                ->with('error', 'Sección no encontrada');
        }

        $data = $this->cargarLayoutData();
        $data['grupo'] = $grupo;

        return view('Base/menu_grupo/editar', $data);
    }

    public function update()
    {
        if (! $this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu'))->with('error', 'No autorizado');
        }

        if (! $this->validate('formMenuGrupo')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id = (int) $this->request->getPost('id');
        $model = new MenuGrupo();
        $post = $this->request->getPost(['slug', 'etiqueta', 'orden', 'estado']);

        if ($model->update($id, [
            'slug' => strtolower(trim((string) $post['slug'])),
            'etiqueta' => trim((string) $post['etiqueta']),
            'orden' => (int) $post['orden'],
            'estado' => $post['estado'],
        ])) {
            return redirect()->to(base_url('dashboard/menu-grupo/editar/' . $id))
                ->with('success', 'Sección actualizada con éxito');
        }

        return redirect()->back()->withInput()->with('errors', 'Error al actualizar la sección');
    }

    public function eliminar()
    {
        if (! $this->esSuperAdmin()) {
            return redirect()->to(base_url('dashboard/menu-grupo/lista'))->with('error', 'No autorizado');
        }

        $id = (int) $this->request->getPost('id');
        $model = new MenuGrupo();

        if ($model->db->fieldExists('menu_grupo_id', 'modulo')) {
            $model->db->table('modulo')->where('menu_grupo_id', $id)->update(['menu_grupo_id' => null]);
        }

        if ($model->delete($id)) {
            return redirect()->to(base_url('dashboard/menu-grupo/lista'))
                ->with('success', 'Sección eliminada. Los módulos quedaron sin sección asignada.');
        }

        return redirect()->to(base_url('dashboard/menu-grupo/lista'))
            ->with('error', 'No se pudo eliminar la sección');
    }
}
