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
        
        return view('Web/servicio_categorias', array_merge(seo_page([
            'title'       => 'Categorías de servicios | NutriNext',
            'description' => 'Explora las funcionalidades de NutriNext organizadas por categoría para tu consulta nutricional.',
            'keywords'    => 'categorías nutrinext, módulos nutricionista, software nutrición',
            'canonical'   => seo_canonical_url('servicios-categorias'),
        ]), [
            'categorias'              => $categorias,
            'servicios_por_categoria' => $serviciosPorCategoria,
        ]));
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
        
        return view('Web/servicio_categoria_detalle', array_merge(seo_page([
            'title'       => ($categoria->meta_titulo ?: $categoria->nombre) . ' | NutriNext',
            'description' => mb_substr(strip_tags($categoria->meta_descripcion ?: 'Servicios de ' . $categoria->nombre . ' en NutriNext.'), 0, 160),
            'keywords'    => $categoria->meta_keywords ?: 'nutrinext, ' . strtolower($categoria->nombre),
            'canonical'   => seo_canonical_url('servicios-categorias/' . $categoria->slug),
        ]), [
            'categoria'        => $categoria,
            'servicios'        => $servicios,
            'otras_categorias' => $otrasCategorias,
        ]));
    }
}
