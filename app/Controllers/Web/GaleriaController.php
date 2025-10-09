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

        // Preparar datos para la vista
        $data = [
            'galerias' => $galerias,
            'categorias' => $categorias,
            'categoria_actual' => $categoriaActual,
            'titulo_pagina' => $categoriaActual ? $categoriaActual->nombre : 'Nuestra Galería',
            'meta_titulo' => $categoriaActual ? $categoriaActual->meta_titulo : 'Galería de Obras - NextLine Constructor',
            'meta_descripcion' => $categoriaActual ? $categoriaActual->meta_descripcion : 'Explora nuestra galería de obras construidas en Linares, Maule. Casas, edificios, quinchos y más proyectos de construcción.',
            'meta_keywords' => $categoriaActual ? $categoriaActual->meta_keywords : 'galería, obras, construcción, Linares, Maule, Chile, proyectos, casas, edificios'
        ];

        return view('Web/galeria', $data);
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

        // Preparar datos para la vista
        $data = [
            'galeria' => $galeria,
            'galerias_relacionadas' => $galeriasRelacionadas,
            'titulo_pagina' => $galeria->nombre,
            'meta_titulo' => $galeria->nombre . ' - Galería NextLine Constructor',
            'meta_descripcion' => $galeria->descripcion ?: 'Imagen de ' . $galeria->nombre . ' construida por NextLine Constructor en Linares, Maule.',
            'meta_keywords' => strtolower($galeria->nombre) . ', galería, construcción, Linares, Maule, Chile'
        ];

        return view('Web/galeria_detalle', $data);
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

        // Preparar datos para la vista
        $data = [
            'galerias' => $galerias,
            'categorias' => $categorias,
            'categoria_actual' => $categoria,
            'titulo_pagina' => $categoria->nombre,
            'meta_titulo' => $categoria->meta_titulo,
            'meta_descripcion' => $categoria->meta_descripcion,
            'meta_keywords' => $categoria->meta_keywords
        ];

        return view('Web/galeria', $data);
    }
}