<?php

namespace App\Models;

use CodeIgniter\Model;

class Galeria extends Model
{
    protected $table      = 'galeria';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id','nombre','categoria_id','descripcion','portada','foto','estado'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
    protected $deletedField  = 'feliminacion';

    protected $validationRules = [
        'nombre' => 'required|string|max_length[100]',
        'categoria_id' => 'permit_empty|integer',
        'descripcion' => 'string|max_length[500]',
        'portada' => 'string|max_length[2000]',
        'estado' => 'required|in_list[A,I]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El campo nombre es obligatorio.',
            'string' => 'El campo nombre debe ser una cadena de texto.',
            'max_length' => 'El campo nombre no puede exceder de 100 caracteres.'
        ],
        'categoria_id' => [
            'integer' => 'El campo categoría debe ser un número entero.'
        ],
        'descripcion' => [
            'string' => 'El campo descripción debe ser una cadena de texto.',
            'max_length' => 'El campo descripción no puede exceder de 500 caracteres.'
        ],
        'portada' => [
            'string' => 'El campo portada debe ser una cadena de texto.',
            'max_length' => 'El campo portada no puede exceder de 2000 caracteres.'
        ],
        'estado' => [
            'required' => 'El campo estado es obligatorio.',
            'in_list' => 'El campo estado debe ser A o I.'
        ]
    ];

    public function getGaleriaAll()
    {
        // Verificar si la tabla galeria_categoria existe
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        
        if (in_array('galeria_categoria', $tables)) {
            return $this->select('galeria.*, galeria_categoria.nombre as categoria_nombre')
                       ->join('galeria_categoria', 'galeria_categoria.id = galeria.categoria_id', 'left')
                       ->orderBy('galeria.fcreacion', 'DESC')
                       ->findAll();
        } else {
            // Si no existe la tabla de categorías, devolver solo galería
            return $this->orderBy('fcreacion', 'DESC')->findAll();
        }
    }

    public function getGaleriaPorCategoria($categoria_id = null)
    {
        $builder = $this->select('galeria.*, galeria_categoria.nombre as categoria_nombre')
                       ->join('galeria_categoria', 'galeria_categoria.id = galeria.categoria_id', 'left')
                       ->where('galeria.estado', 'A');
        
        if ($categoria_id) {
            $builder->where('galeria.categoria_id', $categoria_id);
        }
        
        return $builder->orderBy('galeria.fcreacion', 'DESC')->findAll();
    }

    public function getGaleriaConCategoria($id)
    {
        return $this->select('galeria.*, galeria_categoria.nombre as categoria_nombre')
                   ->join('galeria_categoria', 'galeria_categoria.id = galeria.categoria_id', 'left')
                   ->where('galeria.id', $id)
                   ->first();
    }

}
