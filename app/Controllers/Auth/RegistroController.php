<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class RegistroController extends BaseController
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

    public function form()
    {
        $empresaId = (int) ($this->request->getGet('empresa_id') ?? 0);
        return view('auth/registro_alumno', [
            'empresa_id' => $empresaId,
        ]);
    }

    public function registrar()
    {
        $rules = [
            'empresa_id' => 'required|integer',
            'nombre' => 'required|string|max_length[100]',
            'apellido' => 'required|string|max_length[100]',
            'correo' => 'required|valid_email|max_length[150]',
            'telefono' => 'permit_empty|string|max_length[50]',
            'clave' => 'required|string|min_length[6]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $empresaId = (int) $this->request->getPost('empresa_id');
        $perfilAlumnoId = $this->getPerfilAlumnoId();
        if ($perfilAlumnoId <= 0) {
            return redirect()->back()->withInput()->with('errors', 'Perfil Alumno no existe');
        }

        $post = $this->request->getPost(['nombre', 'apellido', 'correo', 'telefono', 'clave']);
        $db = \Config\Database::connect();

        $empresa = $db->table('empresa')->select('id')->where('id', $empresaId)->where('estado', 'A')->get()->getRow();
        if (!$empresa) {
            return redirect()->back()->withInput()->with('errors', 'Empresa inválida');
        }

        $exists = $db->table('usuario')->where('correo', $post['correo'])->countAllResults() > 0;
        if ($exists) {
            return redirect()->back()->withInput()->with('errors', 'El correo ya existe');
        }

        $db->table('usuario')->insert([
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'correo' => $post['correo'],
            'telefono' => $post['telefono'] ?? null,
            'clave' => password_hash($post['clave'], PASSWORD_DEFAULT),
            'perfil_id' => $perfilAlumnoId,
            'empresa_id' => $empresaId,
            'estado' => 'A',
            'fcreacion' => date('Y-m-d H:i:s'),
            'factualizacion' => date('Y-m-d H:i:s'),
        ]);

        $userId = (int) $db->insertID();
        if ($userId <= 0) {
            return redirect()->back()->withInput()->with('errors', 'No se pudo crear usuario');
        }

        // iniciar sesión inmediatamente
        $user = $db->table('usuario u')
            ->select('u.*, f.nombre as perfil_nombre, f.poder')
            ->join('perfil f', 'u.perfil_id = f.id', 'left')
            ->where('u.id', $userId)
            ->get()
            ->getRowArray();

        session()->set('usuario', $user);
        return redirect()->to(base_url('alumno/inicio'))->with('success', 'Cuenta creada');
    }
}

