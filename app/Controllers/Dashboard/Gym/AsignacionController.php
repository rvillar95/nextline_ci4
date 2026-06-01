<?php

namespace App\Controllers\Dashboard\Gym;

use App\Models\Gym\Programa;
use App\Models\Gym\ProgramaUsuario;

class AsignacionController extends BaseGymController
{
    private function getPerfilAlumnoId(): int
    {
        $db = \Config\Database::connect();
        $perfil = $db->table('perfil')
            ->select('id')
            ->where('nombre', 'Alumno')
            ->where('poder', 1)
            ->where('estado', 'A')
            ->get()
            ->getRow();

        return (int) ($perfil->id ?? 0);
    }

    public function lista()
    {
        $data = $this->buildMenuData();
        $empresaId = $this->requireEmpresaId();

        $programaModel = new Programa();
        $data['programas'] = $programaModel->getAllByEmpresa($empresaId);

        $perfilAlumnoId = $this->getPerfilAlumnoId();
        $db = \Config\Database::connect();
        $data['alumnos'] = ($perfilAlumnoId > 0)
            ? $db->table('usuario')
                ->select('id, nombre, apellido, correo')
                ->where('empresa_id', $empresaId)
                ->where('perfil_id', $perfilAlumnoId)
                ->where('estado', 'A')
                ->orderBy('nombre', 'ASC')
                ->get()
                ->getResult('object')
            : [];

        return view('Modulos/gym/asignacion/lista', $data);
    }

    public function getAsignaciones()
    {
        $empresaId = $this->requireEmpresaId();
        $draw = (int) $this->request->getGet('draw');

        $model = new ProgramaUsuario();
        $rows = $model->getAsignacionesByEmpresa($empresaId);

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                esc($r->programa_nombre ?? ''),
                esc($r->alumno_nombre ?? ''),
                esc($r->alumno_correo ?? ''),
                esc($r->estado ?? ''),
                esc($r->fecha_inicio ?? ''),
                esc($r->fecha_fin ?? ''),
                '<button type="button" value="' . (int) $r->id . '" id="btnDesasignar" class="btn btn-sm btn-danger">Quitar</button>',
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => count($rows),
            'recordsFiltered' => count($rows),
            'data' => $data,
        ]);
    }

    public function asignar()
    {
        $empresaId = $this->requireEmpresaId();
        $usuarioId = $this->requireUsuarioId();

        $rules = [
            'programa_id' => 'required|integer',
            'usuario_id' => 'required|integer',
            'fecha_inicio' => 'permit_empty|valid_date',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $programaId = (int) $this->request->getPost('programa_id');
        $alumnoId = (int) $this->request->getPost('usuario_id');
        $fechaInicio = $this->request->getPost('fecha_inicio');

        // Validar que el programa sea de la empresa
        $programaModel = new Programa();
        $programa = $programaModel->where('empresa_id', $empresaId)->find($programaId);
        if (!$programa) {
            return redirect()->back()->withInput()->with('errors', 'Programa inválido');
        }

        // Validar que el alumno pertenezca a la empresa y sea perfil Alumno
        $perfilAlumnoId = $this->getPerfilAlumnoId();
        $db = \Config\Database::connect();
        $alumno = $db->table('usuario')
            ->where('id', $alumnoId)
            ->where('empresa_id', $empresaId)
            ->where('perfil_id', $perfilAlumnoId)
            ->get()
            ->getRow();
        if (!$alumno) {
            return redirect()->back()->withInput()->with('errors', 'Alumno inválido');
        }

        $gpu = new ProgramaUsuario();

        // Si ya existe asignación, la dejamos activa (update)
        $exist = $gpu->where('programa_id', $programaId)->where('usuario_id', $alumnoId)->first();
        if ($exist) {
            $ok = $gpu->update($exist->id, [
                'estado' => 'activa',
                'fecha_inicio' => $fechaInicio ?: null,
                'fecha_fin' => null,
                'asignado_por_usuario_id' => $usuarioId,
            ]);
        } else {
            $ok = (bool) $gpu->insert([
                'programa_id' => $programaId,
                'usuario_id' => $alumnoId,
                'asignado_por_usuario_id' => $usuarioId,
                'fecha_inicio' => $fechaInicio ?: null,
                'estado' => 'activa',
            ]);
        }

        if ($ok) {
            return redirect()->to(base_url('dashboard/gym/asignacion/lista'))->with('success', 'Programa asignado');
        }
        return redirect()->back()->withInput()->with('errors', 'Error al asignar programa');
    }

    public function desasignar()
    {
        $empresaId = $this->requireEmpresaId();
        $id = (int) $this->request->getPost('id');

        $gpu = new ProgramaUsuario();
        $row = $gpu->find($id);
        if (!$row) {
            return redirect()->to(base_url('dashboard/gym/asignacion/lista'))->with('errors', 'Asignación no encontrada');
        }

        // Validar pertenencia por empresa a través del programa
        $db = \Config\Database::connect();
        $programa = $db->table('gym_programa')->select('empresa_id')->where('id', (int) $row->programa_id)->get()->getRow();
        if (!$programa || (int) $programa->empresa_id !== $empresaId) {
            return redirect()->to(base_url('dashboard/gym/asignacion/lista'))->with('errors', 'No autorizado');
        }

        if ($gpu->delete($id)) {
            return redirect()->to(base_url('dashboard/gym/asignacion/lista'))->with('success', 'Asignación eliminada');
        }
        return redirect()->to(base_url('dashboard/gym/asignacion/lista'))->with('errors', 'Error al quitar asignación');
    }
}

