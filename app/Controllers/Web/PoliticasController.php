<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Empresa;

class PoliticasController extends BaseController
{
    public function privacidad()
    {
        // Obtener datos de la empresa desde la BD
        $empresaModel = new Empresa();
        $empresaData = $empresaModel->getDatosParaPDF();
        
        return view('Web/politica_privacidad', array_merge(seo_page([
            'title'       => 'Política de privacidad | NutriNext',
            'description' => 'Política de privacidad y tratamiento de datos personales de pacientes y usuarios en la plataforma NutriNext.',
            'keywords'    => 'privacidad, protección de datos, nutrinext, pacientes',
            'canonical'   => seo_canonical_url('politica-privacidad'),
        ]), ['empresa' => $empresaData]));
    }
    
    public function terminos()
    {
        // Obtener datos de la empresa desde la BD
        $empresaModel = new Empresa();
        $empresaData = $empresaModel->getDatosParaPDF();
        
        return view('Web/terminos_condiciones', array_merge(seo_page([
            'title'       => 'Términos y condiciones | NutriNext',
            'description' => 'Términos y condiciones de uso del sitio web y de la plataforma NutriNext para profesionales y pacientes.',
            'keywords'    => 'términos, condiciones, nutrinext, uso plataforma',
            'canonical'   => seo_canonical_url('terminos-condiciones'),
        ]), ['empresa' => $empresaData]));
    }
}

