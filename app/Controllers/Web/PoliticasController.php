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
        
        $data = [
            'title' => 'Política de Privacidad - MANSANCHEZ',
            'description' => 'Política de privacidad y protección de datos personales de MANSANCHEZ Constructor',
            'keywords' => 'privacidad, protección de datos, mansanchez',
            'empresa' => $empresaData
        ];
        
        return view('Web/politica_privacidad', $data);
    }
    
    public function terminos()
    {
        // Obtener datos de la empresa desde la BD
        $empresaModel = new Empresa();
        $empresaData = $empresaModel->getDatosParaPDF();
        
        $data = [
            'title' => 'Términos y Condiciones - MANSANCHEZ',
            'description' => 'Términos y condiciones de uso del sitio web y servicios de MANSANCHEZ Constructor',
            'keywords' => 'términos, condiciones, mansanchez',
            'empresa' => $empresaData
        ];
        
        return view('Web/terminos_condiciones', $data);
    }
}

