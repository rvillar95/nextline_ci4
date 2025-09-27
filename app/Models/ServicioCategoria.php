<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicioCategoria extends Model
{
    protected $table      = 'servicio_categoria';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['nombre', 'slug', 'descripcion', 'icono', 'color', 'estado', 'orden', 'meta_titulo', 'meta_descripcion', 'meta_keywords'];

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
                    ->findAll();
    }

    public function getCategoriaConServicios()
    {
        return $this->select('servicio_categoria.*, COUNT(servicio.id) as total_servicios')
                    ->join('servicio', 'servicio.categoria_id = servicio_categoria.id AND servicio.estado = "A"', 'left')
                    ->where('servicio_categoria.estado', 'A')
                    ->groupBy('servicio_categoria.id')
                    ->orderBy('servicio_categoria.orden', 'ASC')
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
            $seo['meta_descripcion'] = $descripcion . ' Servicios profesionales de construcción en Linares, Maule, Chile.';
        } else {
            $seo['meta_descripcion'] = 'Servicios de ' . strtolower($nombre) . ' profesionales en Linares, Maule, Chile. Constructor con más de 15 años de experiencia.';
        }
        
        // Meta Keywords: Palabras clave basadas en el nombre y términos de construcción
        $keywords = [];
        $keywords[] = strtolower($nombre);
        $keywords[] = 'construcción';
        $keywords[] = 'constructor';
        $keywords[] = 'Linares';
        $keywords[] = 'Maule';
        $keywords[] = 'Chile';
        $keywords[] = 'servicios';
        $keywords[] = 'profesional';
        
        // Agregar palabras específicas según el tipo de categoría
        $nombreLower = strtolower($nombre);
        if (strpos($nombreLower, 'residencial') !== false) {
            $keywords[] = 'casas';
            $keywords[] = 'hogar';
            $keywords[] = 'familia';
        }
        if (strpos($nombreLower, 'comercial') !== false) {
            $keywords[] = 'edificios';
            $keywords[] = 'oficinas';
            $keywords[] = 'locales';
        }
        if (strpos($nombreLower, 'remodelación') !== false || strpos($nombreLower, 'remodelacion') !== false) {
            $keywords[] = 'renovación';
            $keywords[] = 'mejoras';
            $keywords[] = 'reforma';
        }
        if (strpos($nombreLower, 'ampliación') !== false || strpos($nombreLower, 'ampliacion') !== false) {
            $keywords[] = 'extensión';
            $keywords[] = 'agregar';
            $keywords[] = 'crecer';
        }
        
        $seo['meta_keywords'] = implode(', ', array_unique($keywords));
        
        return $seo;
    }
}