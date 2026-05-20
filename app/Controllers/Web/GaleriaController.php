<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Galeria;
use App\Models\GaleriaCategoria;

class GaleriaController extends BaseController
{
    protected $galeriaModel;
    protected $galeriaCategoriaModel;

    public function __construct()
    {
        $this->galeriaModel = new Galeria();
        $this->galeriaCategoriaModel = new GaleriaCategoria();
    }

    /**
     * Mostrar la galería principal con filtros por categoría
     */
    public function index()
    {
        // Obtener categoría del parámetro URL
        $categoriaSlug = $this->request->getGet('categoria');
        
        // Obtener todas las categorías activas
        $categorias = $this->galeriaCategoriaModel->getCategoriasActivas();
        
        // Obtener galerías según filtro
        if ($categoriaSlug) {
            // Buscar la categoría por slug
            $categoria = $this->galeriaCategoriaModel->where('slug', $categoriaSlug)
                                                   ->where('estado', 'A')
                                                   ->first();
            
            if ($categoria) {
                $galerias = $this->galeriaModel->getGaleriaPorCategoria($categoria->nombre);
                $categoriaActual = $categoria;
            } else {
                // Si no se encuentra la categoría, mostrar todas
                $galerias = $this->galeriaModel->getGaleriaPorCategoria();
                $categoriaActual = null;
            }
        } else {
            // Mostrar todas las galerías
            $galerias = $this->galeriaModel->getGaleriaPorCategoria();
            $categoriaActual = null;
        }

        $tituloPagina = $categoriaActual ? $categoriaActual->nombre : 'Galería';
        $seoTitle = $categoriaActual
            ? ($categoriaActual->meta_titulo ?: $categoriaActual->nombre . ' | NutriNext')
            : 'Galería | NutriNext';
        $seoDesc = $categoriaActual
            ? ($categoriaActual->meta_descripcion ?: 'Galería de ' . $categoriaActual->nombre . ' en NutriNext.')
            : 'Galería de imágenes y recursos visuales en NutriNext.';

        return view('Web/galeria', array_merge(seo_page([
            'title'       => $seoTitle,
            'description' => mb_substr(strip_tags($seoDesc), 0, 160),
            'keywords'    => ($categoriaActual ? ($categoriaActual->meta_keywords ?? null) : null) ?? 'galería, nutrinext, nutrición',
            'canonical'   => seo_canonical_url('galeria'),
        ]), [
            'galerias'         => $galerias,
            'categorias'       => $categorias,
            'categoria_actual' => $categoriaActual,
            'titulo_pagina'    => $tituloPagina,
            'meta_titulo'      => $seoTitle,
            'meta_descripcion' => $seoDesc,
            'meta_keywords'    => ($categoriaActual ? ($categoriaActual->meta_keywords ?? null) : null) ?? 'galería, nutrinext',
        ]));
    }

    /**
     * Mostrar detalle de una imagen específica
     */
    public function detalle($id)
    {
        // Obtener la galería con información de categoría
        $galeria = $this->galeriaModel->getGaleriaConCategoria($id);
        
        if (!$galeria) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Imagen no encontrada');
        }

        // Obtener galerías relacionadas de la misma categoría
        $galeriasRelacionadas = [];
        if ($galeria->categoria_id) {
            $galeriasRelacionadas = $this->galeriaModel->getGaleriaPorCategoria($galeria->categoria_nombre);
            // Excluir la imagen actual
            $galeriasRelacionadas = array_filter($galeriasRelacionadas, function($item) use ($id) {
                return $item->id != $id;
            });
            // Limitar a 6 imágenes relacionadas
            $galeriasRelacionadas = array_slice($galeriasRelacionadas, 0, 6);
        }

        return view('Web/galeria_detalle', array_merge(seo_page([
            'title'       => $galeria->nombre . ' | Galería NutriNext',
            'description' => mb_substr(strip_tags($galeria->descripcion ?: 'Imagen de la galería NutriNext: ' . $galeria->nombre), 0, 160),
            'keywords'    => strtolower($galeria->nombre) . ', galería, nutrinext',
            'canonical'   => seo_canonical_url('galeria/detalle/' . $id),
        ]), [
            'galeria'              => $galeria,
            'galerias_relacionadas'=> $galeriasRelacionadas,
            'titulo_pagina'        => $galeria->nombre,
            'meta_titulo'          => $galeria->nombre . ' | Galería NutriNext',
            'meta_descripcion'     => $galeria->descripcion,
            'meta_keywords'        => strtolower($galeria->nombre) . ', galería, nutrinext',
        ]));
    }

    /**
     * Mostrar galerías por categoría específica
     */
    public function categoria($slug)
    {
        // Buscar la categoría por slug
        $categoria = $this->galeriaCategoriaModel->where('slug', $slug)
                                               ->where('estado', 'A')
                                               ->first();
        
        if (!$categoria) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Categoría no encontrada');
        }

        // Obtener galerías de esta categoría
        $galerias = $this->galeriaModel->getGaleriaPorCategoria($categoria->nombre);
        
        // Obtener todas las categorías para el filtro
        $categorias = $this->galeriaCategoriaModel->getCategoriasActivas();

        return view('Web/galeria', array_merge(seo_page([
            'title'       => ($categoria->meta_titulo ?: $categoria->nombre) . ' | Galería NutriNext',
            'description' => mb_substr(strip_tags($categoria->meta_descripcion ?: 'Galería: ' . $categoria->nombre), 0, 160),
            'keywords'    => $categoria->meta_keywords ?: 'galería, ' . strtolower($categoria->nombre) . ', nutrinext',
            'canonical'   => seo_canonical_url('galeria/categoria/' . $categoria->slug),
        ]), [
            'galerias'         => $galerias,
            'categorias'       => $categorias,
            'categoria_actual' => $categoria,
            'titulo_pagina'    => $categoria->nombre,
            'meta_titulo'      => $categoria->meta_titulo,
            'meta_descripcion' => $categoria->meta_descripcion,
            'meta_keywords'    => $categoria->meta_keywords,
        ]));
    }
}