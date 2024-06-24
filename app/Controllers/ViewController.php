<?php

namespace App\Controllers;

use App\Models\PerfilModel;

class ViewController extends BaseController
{
  

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

    public function menu(): string
    {
        return view('menu/index');
    }

    public function registro_publico(): string
    {
        $perfilModel = new PerfilModel();
        $data['perfiles'] = $perfilModel->findAll();
        return view('template/header').
               view('registro',$data).
               view('template/footer');

    }
}
