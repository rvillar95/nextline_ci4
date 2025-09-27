<?php

namespace App\Models;

use CodeIgniter\Model;

class GaleriaCategoria extends Model
{
    protected $table      = 'galeria_categoria';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id', 'nombre', 'slug', 'descripcion', 'icono', 'color', 'estado', 'orden', 'meta_titulo', 'meta_descripcion', 'meta_keywords'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    // Validación deshabilitada - se usa la del controlador
    protected $skipValidation = true;

    public function getCategoriasActivas()
    {
        return $this->where('estado', 'A')
                   ->orderBy('orden', 'ASC')
                   ->orderBy('nombre', 'ASC')
                   ->findAll();
    }

    public function getCategoriaPorId($id)
    {
        return $this->where('id', $id)
                   ->where('estado', 'A')
                   ->first();
    }

    public function getCategoriaConGalerias()
    {
        return $this->select('galeria_categoria.*, COUNT(galeria.id) as total_galerias')
                   ->join('galeria', 'galeria.categoria_id = galeria_categoria.id AND galeria.estado = "A"', 'left')
                   ->where('galeria_categoria.estado', 'A')
                   ->groupBy('galeria_categoria.id')
                   ->orderBy('galeria_categoria.orden', 'ASC')
                   ->orderBy('galeria_categoria.nombre', 'ASC')
                   ->findAll();
    }

    /**
     * Generar slug automáticamente
     */
    public function generarSlug($nombre)
    {
        // Convertir a minúsculas y trim
        $slug = strtolower(trim($nombre));
        
        // Reemplazar acentos y caracteres especiales del español
        $acentos = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c'
        ];
        
        $slug = strtr($slug, $acentos);
        
        // Reemplazar espacios y caracteres no alfanuméricos con guiones
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        
        // Eliminar guiones múltiples
        $slug = preg_replace('/-+/', '-', $slug);
        
        // Eliminar guiones al inicio y final
        $slug = trim($slug, '-');

        // Verificar si el slug ya existe
        $originalSlug = $slug;
        $counter = 1;
        while ($this->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Generar campos SEO automáticamente
     */
    public function generarSEO($nombre, $descripcion = '')
    {
        $seo = [];
        
        // Meta Título: Nombre + "NextLine Constructor"
        $seo['meta_titulo'] = $nombre . ' - NextLine Constructor';
        
        // Meta Descripción: Descripción + información adicional
        if (!empty($descripcion)) {
            $seo['meta_descripcion'] = $descripcion . ' Galería de obras de construcción en Linares, Maule, Chile.';
        } else {
            $seo['meta_descripcion'] = 'Galería de ' . strtolower($nombre) . ' construidos en Linares, Maule, Chile. Constructor con más de 15 años de experiencia.';
        }
        
        // Meta Keywords: Palabras clave basadas en el nombre y términos de construcción
        $keywords = [];
        $keywords[] = strtolower($nombre);
        $keywords[] = 'construcción';
        $keywords[] = 'constructor';
        $keywords[] = 'Linares';
        $keywords[] = 'Maule';
        $keywords[] = 'Chile';
        $keywords[] = 'galería';
        $keywords[] = 'obras';
        $keywords[] = 'proyectos';
        
        // Agregar palabras específicas según el tipo de categoría
        $nombreLower = strtolower($nombre);
        if (strpos($nombreLower, 'casa') !== false) {
            $keywords[] = 'hogar';
            $keywords[] = 'familia';
            $keywords[] = 'residencial';
        }
        if (strpos($nombreLower, 'edificio') !== false) {
            $keywords[] = 'comercial';
            $keywords[] = 'oficinas';
            $keywords[] = 'locales';
        }
        if (strpos($nombreLower, 'quincho') !== false) {
            $keywords[] = 'parrilla';
            $keywords[] = 'jardín';
            $keywords[] = 'recreación';
        }
        if (strpos($nombreLower, 'ampliación') !== false || strpos($nombreLower, 'ampliacion') !== false) {
            $keywords[] = 'extensión';
            $keywords[] = 'agregar';
            $keywords[] = 'crecer';
        }
        if (strpos($nombreLower, 'remodelación') !== false || strpos($nombreLower, 'remodelacion') !== false) {
            $keywords[] = 'renovación';
            $keywords[] = 'mejoras';
            $keywords[] = 'reforma';
        }
        
        $seo['meta_keywords'] = implode(', ', array_unique($keywords));
        
        return $seo;
    }
}
