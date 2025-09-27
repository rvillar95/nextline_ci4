<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ServicioCategoria;
use App\Models\Servicio;

class ServicioCategoriaController extends BaseController
{
    public function index()
    {
        $categoriaModel = new ServicioCategoria();
        $servicioModel = new Servicio();
        
        // Obtener todas las categorías activas con conteo de servicios
        $categorias = $categoriaModel->where('estado', 'A')
                                   ->orderBy('orden', 'ASC')
                                   ->findAll();
        
        // Obtener servicios por categoría
        $serviciosPorCategoria = [];
        foreach ($categorias as $categoria) {
            $servicios = $servicioModel->where('categoria_id', $categoria->id)
                                     ->where('estado', 'A')
                                     ->orderBy('fcreacion', 'DESC')
                                     ->findAll();
            $serviciosPorCategoria[$categoria->id] = $servicios;
        }
        
        $data = [
            'title' => 'Categorías de Servicios - NextLine Constructor',
            'description' => 'Explora nuestros servicios organizados por categorías. Construcción residencial, comercial e industrial.',
            'keywords' => 'categorías servicios, construcción residencial, construcción comercial, servicios construcción',
            'categorias' => $categorias,
            'servicios_por_categoria' => $serviciosPorCategoria
        ];
        
        return view('Web/servicio_categorias', $data);
    }
    
    public function detalle($slug)
    {
        $categoriaModel = new ServicioCategoria();
        $servicioModel = new Servicio();
        
        // Obtener categoría por slug
        $categoria = $categoriaModel->where('slug', $slug)
                                  ->where('estado', 'A')
                                  ->first();
        
        if (!$categoria) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Categoría no encontrada');
        }
        
        // Obtener servicios de esta categoría
        $servicios = $servicioModel->where('categoria_id', $categoria->id)
                                 ->where('estado', 'A')
                                 ->orderBy('destacado', 'DESC')
                                 ->orderBy('fcreacion', 'DESC')
                                 ->findAll();
        
        // Obtener otras categorías para navegación
        $otrasCategorias = $categoriaModel->where('estado', 'A')
                                        ->where('id !=', $categoria->id)
                                        ->orderBy('orden', 'ASC')
                                        ->limit(6)
                                        ->findAll();
        
        $data = [
            'title' => $categoria->meta_titulo ?: $categoria->nombre . ' - NextLine Constructor',
            'description' => $categoria->meta_descripcion ?: 'Servicios de ' . $categoria->nombre . ' en NextLine Constructor.',
            'keywords' => $categoria->meta_keywords ?: 'servicios ' . strtolower($categoria->nombre) . ', construcción, NextLine',
            'categoria' => $categoria,
            'servicios' => $servicios,
            'otras_categorias' => $otrasCategorias
        ];
        
        return view('Web/servicio_categoria_detalle', $data);
    }
}
