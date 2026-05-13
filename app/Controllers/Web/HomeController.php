<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'NutriNext - Gestión integral para nutricionistas',
            'description' => 'Plataforma de gestión para nutricionistas: agenda de citas, historiales clínicos, planes alimentarios, pagos y recordatorios por WhatsApp.',
            'keywords' => 'nutrinext, nutrición, nutricionista, agenda, consultas, pacientes, plan alimentario, Chile',
        ];

        return view('Web/home', $data);
    }
}
