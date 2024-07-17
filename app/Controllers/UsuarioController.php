<?php

namespace App\Controllers;

use App\Models\Usuario;
use App\Models\Modulo;
use App\Models\ModuloDetalle;
use App\Models\Perfil;

class UsuarioController extends BaseController
{

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
        $perfil = new Perfil();
        $data['perfiles'] = $perfil->findAll();
        echo view('usuarios/registro', $data);
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
        $perfil = new Perfil();
        $data['perfiles'] = $perfil->findAll();
        echo view('usuarios/lista',$data);
    }

    public function getUsuarios()
    {
        $usuarioModel = new Usuario();
        $draw = intval($this->request->getGet("draw"));
        $books = $usuarioModel->getData();
        $data = array();
        foreach ($books as $r) {
            $data[] = array(
                $r->nombre,
                $r->apellido,
                $r->correo,
                $r->telefono,
                $r->nombre_perfil,
                $r->fcreacion,
                '<a href="editar/'.$r->id.'" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 p-1 br-8 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a>'
            );
        }
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $usuarioModel->countAll(),
            "recordsFiltered" => 5,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }


    public function editar($id)
    {
        $menuTotal = array();
        $modulo = new ModuloDetalle();
        $data['menu'] = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($data['menu'] as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            array_push($menuTotal, array("menu" => $entity, "submenu" => $submenu));
        }
        $data['data'] = $menuTotal;
        $perfil = new Perfil();
        $data['perfiles'] = $perfil->findAll();
        $usuario = new Usuario();
        $data['usuario'] = $usuario->select('usuario.* , perfil.nombre as nombre_perfil')
            ->join('perfil', 'usuario.perfil_id = perfil.id')
            ->where('usuario.id', $id)->first();
        echo view("usuarios/editar", $data);
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

    public function update()
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
            return redirect()->to(base_url('dashboard/usuario/editar/' . $id))->with('success', 'Usuario editado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al editar el usuario');
        }
    }

    public function update_clave()
    {
        if (!$this->validate('formUserEditPassword')) {
            return redirect()->back()->withInput()->with('errorsPassword', $this->validator->getErrors());
        }
        $usuarioModel = new Usuario();
        $id = $this->request->getPost('id');
        $clave = $this->request->getPost('clave');
        if ($usuarioModel->update($id, [
            'clave' => $usuarioModel->contrasenaHash($clave), //password_hash($post['clave'], PASSWORD_DEFAULT),
        ])) {
            return redirect()->to(base_url('dashboard/usuario/detalle/' . $id))->with('successPassword', 'Clave editada con éxito');
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
