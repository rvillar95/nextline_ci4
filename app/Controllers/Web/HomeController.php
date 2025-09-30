<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Servicio;
use App\Models\Galeria;
use App\Models\Proyecto;
use App\Models\Testimonio;
use App\Models\Imagen;

class HomeController extends BaseController
{
    public function index()
    {
        $servicioModel = new Servicio();
        $galeriaModel = new Galeria();
        $proyectoModel = new Proyecto();
        $testimonioModel = new Testimonio();
        $imagenModel = new Imagen();
        
        // Obtener servicios destacados (activos)
        $servicios_destacados = $servicioModel->where('estado', 'A')
                                           ->orderBy('fcreacion', 'DESC')
                                           ->limit(3)
                                           ->findAll();
        
        // Obtener proyectos destacados de la tabla proyectos
        $proyectos_destacados = $proyectoModel->getProyectosPublicos(6, true);
        
        // Obtener imágenes portada para proyectos destacados
        if (!empty($proyectos_destacados)) {
            $proyectoIds = array_column($proyectos_destacados, 'id');
            $imagenesPortada = $imagenModel->getImagenesDestacadas('proyecto', $proyectoIds);
            $imagenesPorProyecto = [];
            foreach ($imagenesPortada as $imagen) {
                $imagenesPorProyecto[$imagen->entidad_id] = $imagen;
            }
            
            foreach ($proyectos_destacados as $proyecto) {
                $proyecto->imagen_portada = $imagenesPorProyecto[$proyecto->id] ?? null;
            }
        }
        
        // Obtener proyectos nuevos (no destacados)
        $proyectos_nuevos = $proyectoModel->getProyectosPublicos(6, false);
        
        // Obtener testimonios destacados
        $testimonios_destacados = $testimonioModel->getTestimoniosDestacados(3);
        
        $data = [
            'title' => 'MANSANCHEZ - Constructor Profesional',
            'description' => 'Constructor profesional con más de 15 años de experiencia en construcción residencial y comercial. Proyectos de calidad garantizada.',
            'keywords' => 'mansanchez, constructor, construcción, obras, proyectos, remodelación, chile',
            'servicios_destacados' => $servicios_destacados,
            'proyectos_destacados' => $proyectos_destacados,
            'proyectos_nuevos' => $proyectos_nuevos,
            'testimonios_destacados' => $testimonios_destacados
        ];
        
        return view('Web/home', $data);
    }
}
