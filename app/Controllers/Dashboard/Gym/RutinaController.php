<?php

namespace App\Controllers\Dashboard\Gym;

use App\Models\Gym\Ejercicio;
use App\Models\Gym\Rutina;
use App\Models\Gym\RutinaEjercicio;
use App\Services\Gym\ProgramaService;

class RutinaController extends BaseGymController
{
    public function lista()
    {
        $data = $this->buildMenuData();
        return view('Modulos/gym/rutina/lista', $data);
    }

    public function registro()
    {
        $data = $this->buildMenuData();
        $empresaId = $this->requireEmpresaId();

        $ejercicioModel = new Ejercicio();
        $data['ejercicios'] = $ejercicioModel->getAllByEmpresa($empresaId);
        return view('Modulos/gym/rutina/registro', $data);
    }

    public function editar($id)
    {
        $data = $this->buildMenuData();
        $empresaId = $this->requireEmpresaId();

        $rutinaModel = new Rutina();
        $data['rutina'] = $rutinaModel->where('empresa_id', $empresaId)->find($id);
        if (!$data['rutina']) {
            return redirect()->to(base_url('dashboard/gym/rutina/lista'))->with('errors', 'Rutina no encontrada');
        }

        $ejercicioModel = new Ejercicio();
        $data['ejercicios'] = $ejercicioModel->getAllByEmpresa($empresaId);

        $rutinaEjercicioModel = new RutinaEjercicio();
        $data['detalle'] = $rutinaEjercicioModel->getDetalleRutina((int) $id);

        return view('Modulos/gym/rutina/editar', $data);
    }

    public function getRutinas()
    {
        try {
            $empresaId = $this->requireEmpresaId();
            $draw = (int) $this->request->getGet('draw');

            $rutinaModel = new Rutina();
            $rows = $rutinaModel->getAllByEmpresa($empresaId);

            $data = [];
            foreach ($rows as $r) {
                $estado = ((int) $r->activo === 1)
                    ? '<span class="badge badge-success mb-2 me-4">Activa</span>'
                    : '<span class="badge badge-danger mb-2 me-4">Inactiva</span>';

                $data[] = [
                    esc($r->nombre),
                    esc($r->creado_por ?? ''),
                    $estado,
                    '<a href="' . base_url('dashboard/gym/rutina/editar/' . (int) $r->id) . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Editar" title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                     <button type="button" value="' . (int) $r->id . '" class="btnDuplicar" data-nombre="' . esc($r->nombre, 'attr') . '" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block; margin-right:5px;" title="Duplicar"><i class="fas fa-copy" style="font-size:18px;color:#555;"></i></button>
                     <button type="button" value="' . (int) $r->id . '" class="btnEliminarGym" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>',
                ];
            }

            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => count($rows),
                'recordsFiltered' => count($rows),
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'getRutinas: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'draw' => (int) $this->request->getGet('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error al cargar rutinas',
            ]);
        }
    }

    public function registrar()
    {
        $empresaId = $this->requireEmpresaId();
        $usuarioId = $this->requireUsuarioId();

        $rules = [
            'nombre' => 'required|string|max_length[160]',
            'descripcion' => 'permit_empty|string',
            'activo' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost(['nombre', 'descripcion', 'activo']);
        $data = [
            'empresa_id' => $empresaId,
            'creado_por_usuario_id' => $usuarioId,
            'nombre' => $post['nombre'],
            'descripcion' => $post['descripcion'] ?? null,
            'activo' => isset($post['activo']) ? (int) $post['activo'] : 1,
        ];

        $rutinaModel = new Rutina();
        $rutinaId = $rutinaModel->insert($data);
        if ($rutinaId) {
            return redirect()->to(base_url('dashboard/gym/rutina/editar/' . $rutinaId))
                ->with('success', 'Rutina creada. Ahora agrega ejercicios.');
        }
        return redirect()->back()->withInput()->with('errors', 'Error al crear la rutina');
    }

    public function update()
    {
        $empresaId = $this->requireEmpresaId();

        $rules = [
            'id' => 'required|integer',
            'nombre' => 'required|string|max_length[160]',
            'descripcion' => 'permit_empty|string',
            'activo' => 'permit_empty|in_list[0,1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost(['id', 'nombre', 'descripcion', 'activo']);
        $id = (int) $post['id'];

        $rutinaModel = new Rutina();
        $rutina = $rutinaModel->where('empresa_id', $empresaId)->find($id);
        if (!$rutina) {
            return redirect()->back()->withInput()->with('errors', 'Rutina no encontrada');
        }

        $data = [
            'nombre' => $post['nombre'],
            'descripcion' => $post['descripcion'] ?? null,
            'activo' => isset($post['activo']) ? (int) $post['activo'] : 1,
        ];

        if ($rutinaModel->update($id, $data)) {
            return redirect()->back()->with('success', 'Rutina actualizada con éxito');
        }
        return redirect()->back()->withInput()->with('errors', 'Error al actualizar la rutina');
    }

    public function eliminar()
    {
        $empresaId = $this->requireEmpresaId();
        $id = (int) $this->request->getPost('id');

        $rutinaModel = new Rutina();
        $rutina = $rutinaModel->where('empresa_id', $empresaId)->find($id);
        if (!$rutina) {
            return redirect()->to(base_url('dashboard/gym/rutina/lista'))->with('errors', 'Rutina no encontrada');
        }

        if ($rutinaModel->delete($id)) {
            return redirect()->to(base_url('dashboard/gym/rutina/lista'))->with('success', 'Rutina eliminada con éxito');
        }
        return redirect()->to(base_url('dashboard/gym/rutina/lista'))->with('errors', 'Error al eliminar la rutina');
    }

    public function duplicar()
    {
        $empresaId = $this->requireEmpresaId();
        $usuarioId = $this->requireUsuarioId();
        $id = (int) $this->request->getPost('id');
        $nombre = trim((string) $this->request->getPost('nombre'));

        $svc = new ProgramaService();
        $res = $svc->duplicarRutina($id, $empresaId, $usuarioId, $nombre !== '' ? $nombre : null);

        if (!$res['ok']) {
            return redirect()->back()->with('errors', $res['error'] ?? 'No se pudo duplicar');
        }

        return redirect()->to(base_url('dashboard/gym/rutina/editar/' . (int) $res['rutina_id']))
            ->with('success', 'Rutina duplicada. Puedes ajustar ejercicios.');
    }

    /**
     * Actualizar ejercicios de una rutina (builder).
     * Espera JSON: { rutina_id, items: [{ejercicio_id, orden, series, repeticiones, descanso_seg, notas}] }
     */
    public function updateEjercicios()
    {
        $empresaId = $this->requireEmpresaId();
        $usuarioId = $this->requireUsuarioId();

        $payload = $this->request->getJSON(true) ?? [];
        $rutinaId = (int) ($payload['rutina_id'] ?? 0);
        $items = $payload['items'] ?? null;

        if ($rutinaId <= 0 || !is_array($items)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Payload inválido',
                'csrf_token' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }

        $rutinaModel = new Rutina();
        $rutina = $rutinaModel->where('empresa_id', $empresaId)->find($rutinaId);
        if (!$rutina) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'error' => 'Rutina no encontrada',
                'csrf_token' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }

        // Solo permite a quien creó la rutina o super admin (poder=3)
        $poder = (int) (session()->get('usuario')['poder'] ?? 0);
        if ($poder !== 3 && (int) $rutina->empresa_id !== $empresaId) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'error' => 'No autorizado',
                'csrf_token' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $db->table('gym_rutina_ejercicio')->where('rutina_id', $rutinaId)->delete();

        $batch = [];
        $ordenSeq = 1;
        foreach ($items as $it) {
            $ejercicioId = (int) ($it['ejercicio_id'] ?? 0);
            if ($ejercicioId <= 0) {
                continue;
            }
            $batch[] = [
                'rutina_id' => $rutinaId,
                'ejercicio_id' => $ejercicioId,
                'orden' => $ordenSeq++,
                'series' => isset($it['series']) && $it['series'] !== '' ? (int) $it['series'] : null,
                'repeticiones' => isset($it['repeticiones']) && $it['repeticiones'] !== '' ? (string) $it['repeticiones'] : null,
                'descanso_seg' => isset($it['descanso_seg']) && $it['descanso_seg'] !== '' ? (int) $it['descanso_seg'] : null,
                'notas' => isset($it['notas']) && $it['notas'] !== '' ? (string) $it['notas'] : null,
            ];
        }

        if (!empty($batch)) {
            $db->table('gym_rutina_ejercicio')->insertBatch($batch);
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Error al guardar ejercicios',
                'csrf_token' => csrf_hash(),
            ])->setHeader('X-CSRF-TOKEN', csrf_hash());
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Rutina actualizada',
            'csrf_token' => csrf_hash(),
        ])->setHeader('X-CSRF-TOKEN', csrf_hash());
    }
}

