<?php

namespace App\Controllers;

use App\Models\ModuloDetalle;
use App\Models\Perfil;
use App\Models\Usuario;

class ViewController extends BaseController
{
    public function __construct()
    {
        
    }

    public function index(): string
    {
        return view('template/header').
               view('dashboard').
               view('template/footer');

    }

    public function login(): string
    {
        return view('login');
    }

    
    public function layout(): string
    {
        return view('test/section');
    }

    public function menu()
    {
        $modulo = new ModuloDetalle();
        $data['modulos'] = $modulo->getModulos(session()->get('usuario')['perfil_id']);
        return view('layout/dashboard',$data);
    }

    public function registro()
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
        return view('usuarios/index', $data);
    }

    
}
