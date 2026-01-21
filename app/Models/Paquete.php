<?php

namespace App\Models;

use CodeIgniter\Model;

class Paquete extends Model
{
    protected $table = 'paquetes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'nombre',
        'slug',
        'descripcion',
        'precio_setup',
        'precio_mensual',
        'activo',
        'orden'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|min_length[3]|max_length[100]|is_unique[paquetes.slug,id,{id}]',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre del paquete es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 100 caracteres'
        ],
        'slug' => [
            'required' => 'El slug es obligatorio',
            'min_length' => 'El slug debe tener al menos 3 caracteres',
            'max_length' => 'El slug no puede exceder 100 caracteres',
            'is_unique' => 'El slug ya existe'
        ]
    ];

    /**
     * Obtener todos los paquetes activos
     */
    public function getPaquetesActivos()
    {
        return $this->where('activo', 'A')
            ->orderBy('orden', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
