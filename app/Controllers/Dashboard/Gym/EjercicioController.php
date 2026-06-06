<?php

namespace App\Controllers\Dashboard\Gym;

use App\Models\Gym\Ejercicio;

class EjercicioController extends BaseGymController
{
    public function lista()
    {
        $data = $this->buildMenuData();
        return view('Modulos/gym/ejercicio/lista', $data);
    }

    public function registro()
    {
        $data = $this->buildMenuData();
        $db = \Config\Database::connect();
        $data['grupos'] = $db->table('gym_grupo_muscular')->where('activo', 1)->orderBy('orden', 'ASC')->get()->getResult('object');
        $data['tiposBase'] = $db->table('gym_tipo_base_ejercicio')->where('activo', 1)->orderBy('orden', 'ASC')->get()->getResult('object');
        return view('Modulos/gym/ejercicio/registro', $data);
    }

    public function editar($id)
    {
        $data = $this->buildMenuData();
        $empresaId = $this->requireEmpresaId();

        $ejercicioModel = new Ejercicio();
        $data['ejercicio'] = $ejercicioModel->where('empresa_id', $empresaId)->find($id);
        if (!$data['ejercicio']) {
            return redirect()->to(base_url('dashboard/gym/ejercicio/lista'))->with('errors', 'Ejercicio no encontrado');
        }

        $db = \Config\Database::connect();
        $data['grupos'] = $db->table('gym_grupo_muscular')->where('activo', 1)->orderBy('orden', 'ASC')->get()->getResult('object');
        $data['tiposBase'] = $db->table('gym_tipo_base_ejercicio')->where('activo', 1)->orderBy('orden', 'ASC')->get()->getResult('object');
        return view('Modulos/gym/ejercicio/editar', $data);
    }

    public function getEjercicios()
    {
        $empresaId = $this->requireEmpresaId();
        $draw = (int) $this->request->getGet('draw');

        $ejercicioModel = new Ejercicio();
        $rows = $ejercicioModel->getAllByEmpresa($empresaId);

        $data = [];
        foreach ($rows as $r) {
            $estado = ((int) $r->activo === 1)
                ? '<span class="badge badge-success mb-2 me-4">Activo</span>'
                : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>';

            $data[] = [
                esc($r->nombre),
                esc($r->grupo_muscular_principal ?? ''),
                esc($r->grupo_muscular_secundario ?? ''),
                esc($r->tipo_base_nombre ?? ''),
                $estado,
                '<a href="' . base_url('dashboard/gym/ejercicio/editar/' . (int) $r->id) . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                 <button type="button" value="' . (int) $r->id . '" class="btnEliminarGym" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>',
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => count($rows),
            'recordsFiltered' => count($rows),
            'data' => $data,
        ]);
    }

    public function registrar()
    {
        $empresaId = $this->requireEmpresaId();

        $rules = [
            'nombre' => 'required|string|max_length[160]',
            'grupo_muscular_principal_id' => 'required|integer',
            'tipo_base_id' => 'required|integer',
            'activo' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'nombre',
            'grupo_muscular_principal_id',
            'grupo_muscular_secundario_id',
            'tipo_base_id',
            'instrucciones',
            'video_url',
            'activo',
        ]);

        $data = [
            'empresa_id' => $empresaId,
            'nombre' => $post['nombre'],
            'grupo_muscular_principal_id' => (int) $post['grupo_muscular_principal_id'],
            'grupo_muscular_secundario_id' => ($post['grupo_muscular_secundario_id'] !== '' && $post['grupo_muscular_secundario_id'] !== null) ? (int) $post['grupo_muscular_secundario_id'] : null,
            'tipo_base_id' => (int) $post['tipo_base_id'],
            'instrucciones' => $post['instrucciones'] ?? null,
            'video_url' => gym_normalizar_video_url($post['video_url'] ?? null),
            'activo' => isset($post['activo']) ? (int) $post['activo'] : 1,
        ];

        $ejercicioModel = new Ejercicio();
        if ($ejercicioModel->insert($data)) {
            return redirect()->to(base_url('dashboard/gym/ejercicio/lista'))->with('success', 'Ejercicio registrado con éxito');
        }

        return redirect()->back()->withInput()->with('errors', 'Error al registrar el ejercicio');
    }

    public function update()
    {
        $empresaId = $this->requireEmpresaId();

        $rules = [
            'id' => 'required|integer',
            'nombre' => 'required|string|max_length[160]',
            'grupo_muscular_principal_id' => 'required|integer',
            'tipo_base_id' => 'required|integer',
            'activo' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost([
            'id',
            'nombre',
            'grupo_muscular_principal_id',
            'grupo_muscular_secundario_id',
            'tipo_base_id',
            'instrucciones',
            'video_url',
            'activo',
        ]);

        $id = (int) $post['id'];
        $ejercicioModel = new Ejercicio();

        $ejercicio = $ejercicioModel->where('empresa_id', $empresaId)->find($id);
        if (!$ejercicio) {
            return redirect()->back()->withInput()->with('errors', 'Ejercicio no encontrado');
        }

        $data = [
            'nombre' => $post['nombre'],
            'grupo_muscular_principal_id' => (int) $post['grupo_muscular_principal_id'],
            'grupo_muscular_secundario_id' => ($post['grupo_muscular_secundario_id'] !== '' && $post['grupo_muscular_secundario_id'] !== null) ? (int) $post['grupo_muscular_secundario_id'] : null,
            'tipo_base_id' => (int) $post['tipo_base_id'],
            'instrucciones' => $post['instrucciones'] ?? null,
            'video_url' => gym_normalizar_video_url($post['video_url'] ?? null),
            'activo' => isset($post['activo']) ? (int) $post['activo'] : 1,
        ];

        if ($ejercicioModel->update($id, $data)) {
            return redirect()->to(base_url('dashboard/gym/ejercicio/lista'))->with('success', 'Ejercicio actualizado con éxito');
        }

        return redirect()->back()->withInput()->with('errors', 'Error al actualizar el ejercicio');
    }

    public function eliminar()
    {
        $empresaId = $this->requireEmpresaId();
        $id = (int) $this->request->getPost('id');

        $ejercicioModel = new Ejercicio();
        $ejercicio = $ejercicioModel->where('empresa_id', $empresaId)->find($id);
        if (!$ejercicio) {
            return redirect()->to(base_url('dashboard/gym/ejercicio/lista'))->with('errors', 'Ejercicio no encontrado');
        }

        if ($ejercicioModel->delete($id)) {
            return redirect()->to(base_url('dashboard/gym/ejercicio/lista'))->with('success', 'Ejercicio eliminado con éxito');
        }
        return redirect()->to(base_url('dashboard/gym/ejercicio/lista'))->with('errors', 'Error al eliminar el ejercicio');
    }
}

