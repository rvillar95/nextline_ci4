<?php

namespace App\Controllers;

use App\Models\PerfilModel;

class PerfilController extends BaseController
{

    public function getPerfil()
    {
        $perfilModel = new PerfilModel();
        $data['perfiles'] = $perfilModel->findAll();
        return $this->response->setJSON($data);
        //return view('perfil_view', $data);
    }

}
