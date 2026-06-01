<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $funcionalidades = (new \App\Models\ServicioNutrinext())->getDestacadosWeb(6);

        return view('Web/home', array_merge(seo_page([
            'title'       => 'NutriNext | Software de gestión para nutricionistas en Chile',
            'description' => 'Agenda online, historial clínico, planes alimentarios y recordatorios WhatsApp. Todo en una plataforma para tu consulta nutricional.',
            'keywords'    => 'nutrinext, software nutricionista, agenda citas nutrición, historial clínico, plan alimentario, Chile',
            'canonical'   => seo_canonical_url(''),
        ]), [
            'funcionalidades_home' => $funcionalidades,
        ]));
    }
}
