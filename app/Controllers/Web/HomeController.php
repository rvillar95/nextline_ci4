<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Servicio;
use App\Models\Galeria;
use App\Models\Proyecto;

class HomeController extends BaseController
{
    public function index()
    {
        $servicioModel = new Servicio();
        $galeriaModel = new Galeria();
        $proyectoModel = new Proyecto();
        
        // Obtener servicios destacados (activos)
        $servicios_destacados = $servicioModel->where('estado', 'A')
                                           ->orderBy('fcreacion', 'DESC')
                                           ->limit(3)
                                           ->findAll();
        
        // Obtener proyectos destacados de la galería
        $proyectos_destacados = $galeriaModel->where('estado', 'A')
                                           ->orderBy('fcreacion', 'DESC')
                                           ->limit(6)
                                           ->findAll();
        
        // Obtener proyectos destacados del módulo proyectos
        $proyectos_nuevos = $proyectoModel->getProyectosPublicos(6, true);
        
        $data = [
            'title' => 'NextLine Constructor - Construcción Profesional',
            'description' => 'Constructor profesional con más de 15 años de experiencia en construcción residencial y comercial. Proyectos de calidad garantizada.',
            'keywords' => 'constructor, construcción, obras, proyectos, remodelación, Linares, Maule, Chile',
            'servicios_destacados' => $servicios_destacados,
            'proyectos_destacados' => $proyectos_destacados,
            'proyectos_nuevos' => $proyectos_nuevos
        ];
        
        return view('Web/home', $data);
    }
}
