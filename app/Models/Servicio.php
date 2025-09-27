<?php

namespace App\Models;

use CodeIgniter\Model;

class Servicio extends Model
{
    protected $table      = 'servicio';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id', 'nombre', 'categoria', 'categoria_id', 'icono', 'descripcionCorta', 'descripcionLarga', 
        'caracteristicas', 'beneficios', 'tiempo_estimado', 'garantia', 
        'precio_desde', 'precio_hasta', 'mostrar_precio', 'foto', 'estado', 'orden', 'destacado', 'slug'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'categoria' => 'permit_empty|string|max_length[100]',
        'icono' => 'permit_empty|string|max_length[100]',
        'descripcionCorta' => 'required|string|max_length[500]',
        'descripcionLarga' => 'required|string|max_length[2000]',
        'caracteristicas' => 'permit_empty|string',
        'beneficios' => 'permit_empty|string',
        'tiempo_estimado' => 'permit_empty|string|max_length[50]',
        'garantia' => 'permit_empty|string|max_length[100]',
        'precio_desde' => 'permit_empty|decimal',
        'precio_hasta' => 'permit_empty|decimal',
        'mostrar_precio' => 'permit_empty|in_list[S,N]',
        'estado' => 'required|in_list[A,I]',
        'orden' => 'permit_empty|integer|greater_than_equal_to[0]',
        'destacado' => 'permit_empty|in_list[S,N]',
        'slug' => 'permit_empty|string|max_length[150]|is_unique[servicio.slug,id,{id}]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El campo nombre es obligatorio.',
            'string' => 'El campo nombre debe ser una cadena de texto.',
            'max_length' => 'El campo nombre no puede exceder de 100 caracteres.'
        ],
        'categoria' => [
            'string' => 'El campo categoría debe ser una cadena de texto.',
            'max_length' => 'El campo categoría no puede exceder de 100 caracteres.'
        ],
        'icono' => [
            'string' => 'El campo icono debe ser una cadena de texto.',
            'max_length' => 'El campo icono no puede exceder de 100 caracteres.'
        ],
        'descripcionCorta' => [
            'required' => 'El campo descripción corta es obligatorio.',
            'string' => 'El campo descripción corta debe ser una cadena de texto.',
            'max_length' => 'El campo descripción corta no puede exceder de 500 caracteres.'
        ],
        'descripcionLarga' => [
            'required' => 'El campo descripción larga es obligatorio.',
            'string' => 'El campo descripción larga debe ser una cadena de texto.',
            'max_length' => 'El campo descripción larga no puede exceder de 2000 caracteres.'
        ],
        'caracteristicas' => [
            'string' => 'El campo características debe ser una cadena de texto.'
        ],
        'beneficios' => [
            'string' => 'El campo beneficios debe ser una cadena de texto.'
        ],
        'tiempo_estimado' => [
            'string' => 'El campo tiempo estimado debe ser una cadena de texto.',
            'max_length' => 'El campo tiempo estimado no puede exceder de 50 caracteres.'
        ],
        'garantia' => [
            'string' => 'El campo garantía debe ser una cadena de texto.',
            'max_length' => 'El campo garantía no puede exceder de 100 caracteres.'
        ],
        'precio_desde' => [
            'decimal' => 'El campo precio desde debe ser un número decimal.'
        ],
        'precio_hasta' => [
            'decimal' => 'El campo precio hasta debe ser un número decimal.'
        ],
        'mostrar_precio' => [
            'in_list' => 'El campo mostrar precio debe ser S o N.'
        ],
        'estado' => [
            'required' => 'El campo estado es obligatorio.',
            'in_list' => 'El campo estado debe ser A o I.'
        ],
        'orden' => [
            'integer' => 'El campo orden debe ser un número entero.',
            'greater_than_equal_to' => 'El campo orden debe ser mayor o igual a 0.'
        ],
        'destacado' => [
            'in_list' => 'El campo destacado debe ser S o N.'
        ],
        'slug' => [
            'string' => 'El campo slug debe ser una cadena de texto.',
            'max_length' => 'El campo slug no puede exceder de 150 caracteres.',
            'is_unique' => 'El slug ya existe. Por favor, elige otro.'
        ]
    ];

    public function getServicioAll()
    {
        return $this->orderBy('orden', 'ASC')
                   ->orderBy('fcreacion', 'DESC')
                   ->findAll();
    }

    public function getServiciosDestacados($limit = 3)
    {
        return $this->where('estado', 'A')
                   ->where('destacado', 'S')
                   ->orderBy('orden', 'ASC')
                   ->limit($limit)
                   ->findAll();
    }

    public function getServicioPorSlug($slug)
    {
        return $this->select('servicio.*, servicio_categoria.nombre as categoria_nombre, servicio_categoria.icono as categoria_icono, servicio_categoria.color as categoria_color')
                    ->join('servicio_categoria', 'servicio_categoria.id = servicio.categoria_id', 'left')
                    ->where('servicio.slug', $slug)
                    ->where('servicio.estado', 'A')
                    ->first();
    }

    public function generarSlug($nombre)
    {
        $slug = strtolower(trim($nombre));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
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
     * Obtener servicios con información de categoría
     */
    public function getServiciosConCategoria($categoriaId = null)
    {
        $builder = $this->select('servicio.*, servicio_categoria.nombre as categoria_nombre, servicio_categoria.icono as categoria_icono, servicio_categoria.color as categoria_color')
                        ->join('servicio_categoria', 'servicio_categoria.id = servicio.categoria_id', 'left')
                        ->where('servicio.estado', 'A');
        
        if ($categoriaId) {
            $builder->where('servicio.categoria_id', $categoriaId);
        }
        
        return $builder->orderBy('servicio.orden', 'ASC')
                      ->orderBy('servicio.nombre', 'ASC')
                      ->findAll();
    }

    /**
     * Obtener servicios por categoría (usando nombre para compatibilidad)
     */
    public function getServiciosPorCategoria($categoriaNombre = null)
    {
        if ($categoriaNombre) {
            return $this->select('servicio.*, servicio_categoria.nombre as categoria_nombre, servicio_categoria.icono as categoria_icono, servicio_categoria.color as categoria_color')
                        ->join('servicio_categoria', 'servicio_categoria.id = servicio.categoria_id', 'left')
                        ->where('servicio.estado', 'A')
                        ->where('servicio_categoria.nombre', $categoriaNombre)
                        ->orderBy('servicio.orden', 'ASC')
                        ->orderBy('servicio.nombre', 'ASC')
                        ->findAll();
        }
        
        return $this->getServiciosConCategoria();
    }

}
