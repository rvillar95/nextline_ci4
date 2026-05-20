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
        
        return view('Web/galeria_categorias', array_merge(seo_page([
            'title'       => 'Categorías de galería | NutriNext',
            'description' => 'Explora la galería de NutriNext organizada por categorías.',
            'keywords'    => 'galería nutrinext, categorías, nutrición',
            'canonical'   => seo_canonical_url('galeria-categorias'),
        ]), [
            'categorias'             => $categorias,
            'galerias_por_categoria' => $galeriasPorCategoria,
        ]));
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
        
        return view('Web/galeria_categoria_detalle', array_merge(seo_page([
            'title'       => ($categoria->meta_titulo ?: $categoria->nombre) . ' | Galería NutriNext',
            'description' => mb_substr(strip_tags($categoria->meta_descripcion ?: 'Galería: ' . $categoria->nombre), 0, 160),
            'keywords'    => $categoria->meta_keywords ?: 'galería, nutrinext, ' . strtolower($categoria->nombre),
            'canonical'   => seo_canonical_url('galeria-categorias/' . $categoria->slug),
        ]), [
            'categoria'        => $categoria,
            'galerias'         => $galerias,
            'otras_categorias' => $otrasCategorias,
        ]));
    }
}
