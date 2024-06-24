<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuarioController extends BaseController
{
   
    public function registro_usuario(){
        if (!$this->validate('formUserRegister')) {
            return redirect()->back()->withInput()->with('errors', $this->validator->listErrors());
        }

        $usuarioModel = new UsuarioModel();

        $post = $this->request->getPost(['nombre','apellido','correo','clave','telefono','perfil']);
        //echo $post['clave'];
        //exit();
        $data = [
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'correo' => $post['correo'],
            'telefono' => $post['telefono'],
            'clave' => password_hash($post['clave'], PASSWORD_DEFAULT),
            'perfil_id' => $post['perfil'],
            'estado' => 'A' // Estado inicial
        ];

        if ($usuarioModel->insert($data)) {
            return redirect()->to(base_url('registro_publico'))->with('success', 'Usuario registrado con éxito');
        } else {
            return redirect()->back()->withInput()->with('errors', 'Error al registrar el usuario');
        }
    }

    public function inicio_sesion()
    {
        if (!$this->validate('formUserLogin')) {
            return redirect()->back()->withInput()->with('errors',$this->validator->listErrors());
        }

        $usuarioModel = new UsuarioModel();
        $post = $this->request->getPost(['correo','clave']);

        $user = $usuarioModel->validateUser2($post['correo'],$post['clave']);
        if ($user !== null) {
            $this->setSession($user);
            return redirect()->to(base_url('menu'));
        }
        return redirect()->back()->withInput()->with('errors','El correo o contraseña son incorrectos.');
    }

    private function setSession($userData){
        $data = [
            'logged_id' => true,
            'user' => $userData['correo'],
            'name' => $userData['nombre'],
            'all' => $userData
        ];
        $this->session->set($data);
    }

    private function logout(){
        if($this->session){
            $this->session->destroy();
        }

        return redirect()->to(base_url('login'));
    }

}
