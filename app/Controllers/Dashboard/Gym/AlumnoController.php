<?php

namespace App\Controllers\Dashboard\Gym;

class AlumnoController extends BaseGymController
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
        return view('Modulos/gym/alumno/lista', $data);
    }

    public function registro()
    {
        $data = $this->buildMenuData();
        return view('Modulos/gym/alumno/registro', $data);
    }

    public function editar($id)
    {
        $data = $this->buildMenuData();
        $empresaId = $this->requireEmpresaId();
        $perfilAlumnoId = $this->getPerfilAlumnoId();
        if ($perfilAlumnoId <= 0) {
            return redirect()->to(base_url('dashboard/gym/alumno/lista'))->with('errors', 'Perfil Alumno no existe (ejecuta seed_gym_modulos_perfiles.sql)');
        }

        $db = \Config\Database::connect();
        $data['alumno'] = $db->table('usuario')
            ->where('id', (int) $id)
            ->where('empresa_id', $empresaId)
            ->where('perfil_id', $perfilAlumnoId)
            ->get()
            ->getRow();

        if (!$data['alumno']) {
            return redirect()->to(base_url('dashboard/gym/alumno/lista'))->with('errors', 'Alumno no encontrado');
        }

        return view('Modulos/gym/alumno/editar', $data);
    }

    public function getAlumnos()
    {
        $empresaId = $this->requireEmpresaId();
        $draw = (int) $this->request->getGet('draw');

        $perfilAlumnoId = $this->getPerfilAlumnoId();
        if ($perfilAlumnoId <= 0) {
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
        }

        $db = \Config\Database::connect();
        $rows = $db->table('usuario u')
            ->select('u.id, u.nombre, u.apellido, u.correo, u.telefono, u.estado, u.fcreacion')
            ->where('u.empresa_id', $empresaId)
            ->where('u.perfil_id', $perfilAlumnoId)
            ->orderBy('u.id', 'DESC')
            ->get()
            ->getResult('object');

        $data = [];
        foreach ($rows as $u) {
            $estado = ($u->estado === 'A')
                ? '<span class="badge badge-success mb-2 me-4">Activo</span>'
                : '<span class="badge badge-danger mb-2 me-4">Inactivo</span>';

            $data[] = [
                esc($u->nombre . ' ' . $u->apellido),
                esc($u->correo),
                esc($u->telefono ?? ''),
                $estado,
                '<a href="editar/' . (int) $u->id . '" style="display:inline-block; margin-right: 5px;" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 25 25" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 table-cancel"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>
                 <button type="button" value="' . (int) $u->id . '" id="btnEliminar" style="background:none; border:none; padding:0; cursor:pointer; display:inline-block;" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 table-cancel"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></button>',
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
        $perfilAlumnoId = $this->getPerfilAlumnoId();
        if ($perfilAlumnoId <= 0) {
            return redirect()->back()->withInput()->with('errors', 'Perfil Alumno no existe (ejecuta seed_gym_modulos_perfiles.sql)');
        }

        $rules = [
            'nombre' => 'required|string|max_length[100]',
            'apellido' => 'required|string|max_length[100]',
            'correo' => 'required|valid_email|max_length[150]',
            'telefono' => 'permit_empty|string|max_length[50]',
            'clave' => 'required|string|min_length[6]|max_length[100]',
            'estado' => 'required|in_list[A,I]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost(['nombre', 'apellido', 'correo', 'telefono', 'clave', 'estado']);
        $db = \Config\Database::connect();

        // evitar duplicado de correo dentro de toda la tabla (regla actual del sistema)
        $exists = $db->table('usuario')->where('correo', $post['correo'])->countAllResults() > 0;
        if ($exists) {
            return redirect()->back()->withInput()->with('errors', 'El correo ya existe');
        }

        $insert = [
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'correo' => $post['correo'],
            'telefono' => $post['telefono'] ?? null,
            'clave' => password_hash($post['clave'], PASSWORD_DEFAULT),
            'perfil_id' => $perfilAlumnoId,
            'empresa_id' => $empresaId,
            'estado' => $post['estado'],
            'fcreacion' => date('Y-m-d H:i:s'),
            'factualizacion' => date('Y-m-d H:i:s'),
        ];

        if ($db->table('usuario')->insert($insert)) {
            return redirect()->to(base_url('dashboard/gym/alumno/lista'))->with('success', 'Alumno creado con éxito');
        }

        return redirect()->back()->withInput()->with('errors', 'Error al crear el alumno');
    }

    public function update()
    {
        $empresaId = $this->requireEmpresaId();
        $perfilAlumnoId = $this->getPerfilAlumnoId();
        if ($perfilAlumnoId <= 0) {
            return redirect()->back()->withInput()->with('errors', 'Perfil Alumno no existe');
        }

        $rules = [
            'id' => 'required|integer',
            'nombre' => 'required|string|max_length[100]',
            'apellido' => 'required|string|max_length[100]',
            'correo' => 'required|valid_email|max_length[150]',
            'telefono' => 'permit_empty|string|max_length[50]',
            'estado' => 'required|in_list[A,I]',
            'clave' => 'permit_empty|string|min_length[6]|max_length[100]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost(['id', 'nombre', 'apellido', 'correo', 'telefono', 'estado', 'clave']);
        $id = (int) $post['id'];
        $db = \Config\Database::connect();

        $alumno = $db->table('usuario')
            ->where('id', $id)
            ->where('empresa_id', $empresaId)
            ->where('perfil_id', $perfilAlumnoId)
            ->get()
            ->getRow();

        if (!$alumno) {
            return redirect()->back()->withInput()->with('errors', 'Alumno no encontrado');
        }

        // si cambia correo, validar que no exista
        if ($post['correo'] !== $alumno->correo) {
            $exists = $db->table('usuario')->where('correo', $post['correo'])->countAllResults() > 0;
            if ($exists) {
                return redirect()->back()->withInput()->with('errors', 'El correo ya existe');
            }
        }

        $update = [
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'correo' => $post['correo'],
            'telefono' => $post['telefono'] ?? null,
            'estado' => $post['estado'],
            'factualizacion' => date('Y-m-d H:i:s'),
        ];
        if (!empty($post['clave'])) {
            $update['clave'] = password_hash($post['clave'], PASSWORD_DEFAULT);
        }

        if ($db->table('usuario')->where('id', $id)->update($update)) {
            return redirect()->back()->with('success', 'Alumno actualizado');
        }
        return redirect()->back()->withInput()->with('errors', 'Error al actualizar el alumno');
    }

    public function eliminar()
    {
        $empresaId = $this->requireEmpresaId();
        $perfilAlumnoId = $this->getPerfilAlumnoId();
        if ($perfilAlumnoId <= 0) {
            return redirect()->to(base_url('dashboard/gym/alumno/lista'))->with('errors', 'Perfil Alumno no existe');
        }

        $id = (int) $this->request->getPost('id');
        $db = \Config\Database::connect();

        $alumno = $db->table('usuario')
            ->where('id', $id)
            ->where('empresa_id', $empresaId)
            ->where('perfil_id', $perfilAlumnoId)
            ->get()
            ->getRow();
        if (!$alumno) {
            return redirect()->to(base_url('dashboard/gym/alumno/lista'))->with('errors', 'Alumno no encontrado');
        }

        // Soft-delete “manual”: marcar estado I
        if ($db->table('usuario')->where('id', $id)->update(['estado' => 'I', 'factualizacion' => date('Y-m-d H:i:s')])) {
            return redirect()->to(base_url('dashboard/gym/alumno/lista'))->with('success', 'Alumno desactivado');
        }
        return redirect()->to(base_url('dashboard/gym/alumno/lista'))->with('errors', 'Error al desactivar el alumno');
    }
}

