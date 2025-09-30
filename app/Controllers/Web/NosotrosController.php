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
        
        $data = [
            'title' => 'Nosotros - MANSANCHEZ Constructor',
            'description' => 'Conoce más sobre MANSANCHEZ Constructor, nuestra historia, valores y compromiso con la excelencia en construcción.',
            'keywords' => 'mansanchez, constructor, nosotros, empresa, historia, valores, equipo, experiencia',
            'empresa' => $empresa,
            'testimonios' => $testimonios,
            'estadisticas' => $estadisticas
        ];
        
        return view('Web/nosotros', $data);
    }
}
