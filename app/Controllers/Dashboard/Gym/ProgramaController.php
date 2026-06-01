<?php

namespace App\Controllers\Dashboard\Gym;

use App\Models\Gym\Programa;
use App\Models\Gym\ProgramaRutina;
use App\Models\Gym\Rutina;

class ProgramaController extends BaseGymController
{
    public function lista()
    {
        $data = $this->buildMenuData();
        return view('Modulos/gym/programa/lista', $data);
    }

    public function registro()
    {
        $data = $this->buildMenuData();
        $empresaId = $this->requireEmpresaId();

        $rutinaModel = new Rutina();
        $data['rutinas'] = $rutinaModel->getAllByEmpresa($empresaId);
        return view('Modulos/gym/programa/registro', $data);
    }

    public function editar($id)
    {
        $data = $this->buildMenuData();
        $empresaId = $this->requireEmpresaId();

        $programaModel = new Programa();
        $data['programa'] = $programaModel->where('empresa_id', $empresaId)->find($id);
        if (!$data['programa']) {
            return redirect()->to(base_url('dashboard/gym/programa/lista'))->with('errors', 'Programa no encontrado');
        }

        $rutinaModel = new Rutina();
        $data['rutinas'] = $rutinaModel->getAllByEmpresa($empresaId);

        $prModel = new ProgramaRutina();
        $data['detalle'] = $prModel->getDetallePrograma((int) $id);

        return view('Modulos/gym/programa/editar', $data);
    }

    public function getProgramas()
    {
        $empresaId = $this->requireEmpresaId();
        $draw = (int) $this->request->getGet('draw');

        $programaModel = new Programa();
        $rows = $programaModel->getAllByEmpresa($empresaId);

        $data = [];
        foreach ($rows as $p) {
            $estado = ((int) $p->activo === 1)
                ? '<span class="badge badge-success mb-2 me-4">Activo</span>'
                : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>';

            $data[] = [
                esc($p->nombre),
                esc($p->creado_por ?? ''),
                esc($p->duracion_semanas ?? ''),
                $estado,
                '<a href="editar/' . (int) $p->id . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                 <button type="button" value="' . (int) $p->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>',
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
        $usuarioId = $this->requireUsuarioId();

        $rules = [
            'nombre' => 'required|string|max_length[160]',
            'descripcion' => 'permit_empty|string',
            'duracion_semanas' => 'permit_empty|integer',
            'activo' => 'permit_empty|in_list[0,1]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost(['nombre', 'descripcion', 'duracion_semanas', 'activo']);
        $data = [
            'empresa_id' => $empresaId,
            'creado_por_usuario_id' => $usuarioId,
            'nombre' => $post['nombre'],
            'descripcion' => $post['descripcion'] ?? null,
            'duracion_semanas' => ($post['duracion_semanas'] !== '' && $post['duracion_semanas'] !== null) ? (int) $post['duracion_semanas'] : null,
            'activo' => isset($post['activo']) ? (int) $post['activo'] : 1,
        ];

        $programaModel = new Programa();
        $programaId = $programaModel->insert($data);
        if ($programaId) {
            return redirect()->to(base_url('dashboard/gym/programa/editar/' . $programaId))
                ->with('success', 'Programa creado. Ahora agrega rutinas.');
        }
        return redirect()->back()->withInput()->with('errors', 'Error al crear el programa');
    }

    public function update()
    {
        $empresaId = $this->requireEmpresaId();

        $rules = [
            'id' => 'required|integer',
            'nombre' => 'required|string|max_length[160]',
            'descripcion' => 'permit_empty|string',
            'duracion_semanas' => 'permit_empty|integer',
            'activo' => 'permit_empty|in_list[0,1]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost(['id', 'nombre', 'descripcion', 'duracion_semanas', 'activo']);
        $id = (int) $post['id'];

        $programaModel = new Programa();
        $programa = $programaModel->where('empresa_id', $empresaId)->find($id);
        if (!$programa) {
            return redirect()->back()->withInput()->with('errors', 'Programa no encontrado');
        }

        $data = [
            'nombre' => $post['nombre'],
            'descripcion' => $post['descripcion'] ?? null,
            'duracion_semanas' => ($post['duracion_semanas'] !== '' && $post['duracion_semanas'] !== null) ? (int) $post['duracion_semanas'] : null,
            'activo' => isset($post['activo']) ? (int) $post['activo'] : 1,
        ];

        if ($programaModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Programa actualizado con éxito');
        }
        return redirect()->back()->withInput()->with('errors', 'Error al actualizar el programa');
    }

    public function eliminar()
    {
        $empresaId = $this->requireEmpresaId();
        $id = (int) $this->request->getPost('id');

        $programaModel = new Programa();
        $programa = $programaModel->where('empresa_id', $empresaId)->find($id);
        if (!$programa) {
            return redirect()->to(base_url('dashboard/gym/programa/lista'))->with('errors', 'Programa no encontrado');
        }

        if ($programaModel->delete($id)) {
            return redirect()->to(base_url('dashboard/gym/programa/lista'))->with('success', 'Programa eliminado con éxito');
        }
        return redirect()->to(base_url('dashboard/gym/programa/lista'))->with('errors', 'Error al eliminar el programa');
    }

    /**
     * Actualizar rutinas de un programa (JSON).
     * Espera JSON: { programa_id, items: [{rutina_id, orden, dia_semana}] }
     */
    public function updateRutinas()
    {
        $empresaId = $this->requireEmpresaId();
        $usuarioId = $this->requireUsuarioId();

        $payload = $this->request->getJSON(true) ?? [];
        $programaId = (int) ($payload['programa_id'] ?? 0);
        $items = $payload['items'] ?? null;

        if ($programaId <= 0 || !is_array($items)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Payload inválido',
                'csrf_token' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }

        $programaModel = new Programa();
        $programa = $programaModel->where('empresa_id', $empresaId)->find($programaId);
        if (!$programa) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'error' => 'Programa no encontrado',
                'csrf_token' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }

        $poder = (int) (session()->get('usuario')['poder'] ?? 0);
        if ($poder !== 3 && (int) $programa->creado_por_usuario_id !== $usuarioId) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'error' => 'No autorizado',
                'csrf_token' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('gym_programa_rutina')->where('programa_id', $programaId)->delete();

        $batch = [];
        foreach ($items as $it) {
            $rutinaId = (int) ($it['rutina_id'] ?? 0);
            if ($rutinaId <= 0) {
                continue;
            }
            $batch[] = [
                'programa_id' => $programaId,
                'rutina_id' => $rutinaId,
                'orden' => (int) ($it['orden'] ?? 0),
                'dia_semana' => isset($it['dia_semana']) && $it['dia_semana'] !== '' ? (int) $it['dia_semana'] : null,
            ];
        }

        if (!empty($batch)) {
            $db->table('gym_programa_rutina')->insertBatch($batch);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Error al guardar rutinas',
                'csrf_token' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Programa actualizado',
            'csrf_token' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }
}

