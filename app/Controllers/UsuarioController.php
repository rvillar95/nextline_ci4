<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Modulo;
use App\Models\ModuloDetalle;
use App\Models\Perfil;

class UsuarioController extends BaseController
{

    public function index()
    {
        $modulo = new ModuloDetalle();
        $data['modulos'] = $modulo->getModulos(session()->get('usuario')['perfil_id']);
        $perfil = new Perfil();
        $data['perfiles'] = $perfil->findAll();
        $usuarioModel = new Usuario();
        $data['usuarios'] = [
            $usuarioModel->select('usuario.* , perfil.nombre as nombre_perfil')
                ->join('perfil', 'usuario.perfil_id = perfil.id')->paginate(),
            'pager' => $usuarioModel->pager
        ];
        echo view('usuarios/index', $data);
    }

    public function detalle($id)
    {
        $modulo = new ModuloDetalle();
        $data['modulos'] = $modulo->getModulos(session()->get('usuario')['perfil_id']);
        $perfil = new Perfil();
        $data['perfiles'] = $perfil->findAll();
        $usuario = new Usuario();
        $data['usuario'] = $usuario->select('usuario.* , perfil.nombre as nombre_perfil')
            ->join('perfil', 'usuario.perfil_id = perfil.id')
            ->where('usuario.id', $id)->first();
        echo view("usuarios/detalle", $data);
    }

    public function registrar()
    {
        if (!$this->validate('formUserRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuarioModel = new Usuario();

        $post = $this->request->getPost(['nombre', 'apellido', 'correo', 'clave', 'telefono', 'perfil']);
        //echo $post['clave'];
        //exit();
        $data = [
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'correo' => $post['correo'],
            'telefono' => $post['telefono'],
            'clave' => $usuarioModel->contrasenaHash($post['clave']),
            'perfil_id' => $post['perfil'],
            'estado' => 'A' // Estado inicial
        ];

        if ($usuarioModel->insert($data)) {
            return redirect()->to(base_url('dashboard/registro/usuario'))->with('success', 'Usuario registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el usuario');
        }
    }

    public function editar()
    {
    
        if (!$this->validate('formUserEdit')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $usuarioModel = new Usuario();
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');
        $apellido = $this->request->getPost('apellido');
        $correo = $this->request->getPost('correo');
        $telefono = $this->request->getPost('telefono');
        $perfil = $this->request->getPost('perfil');
        $estado = $this->request->getPost('estado');
  
       
        if ($usuarioModel->update($id, [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'telefono' => $telefono,
            'perfil_id' => $perfil,
            'estado' => $estado
        ])) {
            return redirect()->to(base_url('dashboard/usuario/detalle/'.$id))->with('success', 'Usuario editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el usuario');
        }
    }

    public function editar_clave(){
        if (!$this->validate('formUserEditPassword')) {
            return redirect()->back()->withInput()->with('errorsPassword', $this->validator->getErrors());
        }
        $usuarioModel = new Usuario();
        $id = $this->request->getPost('id');
        $clave = $this->request->getPost('clave');
        if ($usuarioModel->update($id, [
            'clave' => $usuarioModel->contrasenaHash($clave),//password_hash($post['clave'], PASSWORD_DEFAULT),
        ])) {
            return redirect()->to(base_url('dashboard/usuario/detalle/'.$id))->with('successPassword', 'Clave editada con éxito');
        } else {
            return redirect()->back()->withInput()->with('errorsPassword', 'Error al editar la clave');
        }
    }

    public function inicio_sesion()
    {
        if (!$this->validate('formUserLogin')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->listErrors());
        }

        $usuarioModel = new Usuario();

        $correo = $this->request->getPost('correo');
        $clave = $this->request->getPost('clave');

        //        $post = $this->request->getPost(['correo', 'clave']);

        $usuario = $usuarioModel->validateUser($correo, $clave);
        if ($usuario !== null) {
            $this->session->set('usuario', $usuario);
            return redirect()->to(base_url('dashboard/menu'))->with('mensaje', "Bienvenid@");
        }
        return redirect()->back()->withInput()->with('errors', 'El correo o contraseña son incorrectos.');
    }

    public function logout()
    {
        if ($this->session) {
            $this->session->destroy();
        }

        return redirect()->to(base_url('login'));
    }
}
