<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\GaleriaCategoria;
use App\Models\Galeria;

class GaleriaCategoriaController extends BaseController
{
    public function index()
    {
        $categoriaModel = new GaleriaCategoria();
        $galeriaModel = new Galeria();
        
        // Obtener todas las categorías activas con conteo de galerías
        $categorias = $categoriaModel->where('estado', 'A')
                                   ->orderBy('orden', 'ASC')
                                   ->findAll();
        
        // Obtener galerías por categoría
        $galeriasPorCategoria = [];
        foreach ($categorias as $categoria) {
            $galerias = $galeriaModel->where('categoria_id', $categoria->id)
                                    ->where('estado', 'A')
                                    ->orderBy('fcreacion', 'DESC')
                                    ->limit(6)
                                    ->findAll();
            $galeriasPorCategoria[$categoria->id] = $galerias;
        }
        
        $data = [
            'title' => 'Categorías de Galería - NextLine Constructor',
            'description' => 'Explora nuestras obras organizadas por categorías. Casas, edificios, ampliaciones y más.',
            'keywords' => 'categorías galería, obras construcción, casas, edificios, ampliaciones',
            'categorias' => $categorias,
            'galerias_por_categoria' => $galeriasPorCategoria
        ];
        
        return view('Web/galeria_categorias', $data);
    }
    
    public function detalle($slug)
    {
        $categoriaModel = new GaleriaCategoria();
        $galeriaModel = new Galeria();
        
        // Obtener categoría por slug
        $categoria = $categoriaModel->where('slug', $slug)
                                  ->where('estado', 'A')
                                  ->first();
        
        if (!$categoria) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Categoría no encontrada');
        }
        
        // Obtener galerías de esta categoría
        $galerias = $galeriaModel->where('categoria_id', $categoria->id)
                                ->where('estado', 'A')
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
            'description' => $categoria->meta_descripcion ?: 'Galería de ' . $categoria->nombre . ' en NextLine Constructor.',
            'keywords' => $categoria->meta_keywords ?: 'galería ' . strtolower($categoria->nombre) . ', obras construcción, NextLine',
            'categoria' => $categoria,
            'galerias' => $galerias,
            'otras_categorias' => $otrasCategorias
        ];
        
        return view('Web/galeria_categoria_detalle', $data);
    }
}
