<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Empresa;
use App\Models\Testimonio;
use App\Models\Proyecto;

class NosotrosController extends BaseController
{
    public function index()
    {
        $empresaModel = new Empresa();
        $testimonioModel = new Testimonio();
        $proyectoModel = new Proyecto();
        
        // Obtener datos de la empresa
        $empresa = $empresaModel->getDatosParaPDF();
        
        // Obtener testimonios destacados
        $testimonios = $testimonioModel->getTestimoniosDestacados(6);
        
        // Obtener estadísticas de proyectos
        $estadisticas = [
            'total' => $proyectoModel->where('estado_publico', 'A')->countAllResults(),
            'completados' => $proyectoModel->where('estado', 'completado')->where('estado_publico', 'A')->countAllResults(),
            'destacados' => $proyectoModel->where('destacado', 1)->where('estado_publico', 'A')->countAllResults(),
            'anos_experiencia' => 15 // Puedes calcular esto dinámicamente si tienes fecha de fundación
        ];
        
        return view('Web/nosotros', array_merge(seo_page([
            'title'       => 'Nosotros | NutriNext - Plataforma para nutricionistas',
            'description' => 'Conoce NutriNext: tecnología pensada para consultas nutricionales, con agenda, pacientes, historiales y comunicación con tus pacientes.',
            'keywords'    => 'nutrinext, nosotros, plataforma nutrición, software consulta nutricional, Chile',
            'canonical'   => seo_canonical_url('nosotros'),
        ]), [
            'empresa'     => $empresa,
            'testimonios' => $testimonios,
            'estadisticas'=> $estadisticas,
        ]));
    }
}
