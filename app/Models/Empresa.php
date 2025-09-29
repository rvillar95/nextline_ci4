<?php

namespace App\Models;

use CodeIgniter\Model;

class Empresa extends Model
{
    protected $table = 'empresa';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nombre',
        'nombre_comercial',
        'rut',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'logo_path',
        'descripcion',
        'mision',
        'vision',
        'valores',
        'estado'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'fmodificacion';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[255]',
        'email' => 'required|valid_email',
        'telefono' => 'required|min_length[8]|max_length[50]',
        'direccion' => 'required|min_length[10]|max_length[500]'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre de la empresa es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 255 caracteres'
        ],
        'email' => [
            'required' => 'El email es obligatorio',
            'valid_email' => 'Debe proporcionar un email válido'
        ],
        'telefono' => [
            'required' => 'El teléfono es obligatorio',
            'min_length' => 'El teléfono debe tener al menos 8 caracteres',
            'max_length' => 'El teléfono no puede exceder 50 caracteres'
        ],
        'direccion' => [
            'required' => 'La dirección es obligatoria',
            'min_length' => 'La dirección debe tener al menos 10 caracteres',
            'max_length' => 'La dirección no puede exceder 500 caracteres'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Obtener datos de la empresa activa
     */
    public function getEmpresaActiva()
    {
        return $this->where('estado', 'A')->first();
    }

    /**
     * Obtener datos de la empresa por ID
     */
    public function getEmpresaById($id)
    {
        return $this->find($id);
    }

    /**
     * Actualizar datos de la empresa
     */
    public function actualizarEmpresa($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Obtener logo de la empresa
     */
    public function getLogoPath()
    {
        $empresa = $this->getEmpresaActiva();
        return $empresa ? $empresa->logo_path : null;
    }

    /**
     * Obtener información completa de la empresa para PDF
     */
    public function getDatosParaPDF()
    {
        $empresa = $this->getEmpresaActiva();
        
        if (!$empresa) {
            return [
                'nombre' => 'MANSANCHEZ',
                'nombre_comercial' => 'MANSANCHEZ Construcciones',
                'direccion' => 'Santiago, Chile',
                'telefono' => '+56 9 1234 5678',
                'email' => 'info@mansanchez.cl',
                'sitio_web' => 'www.mansanchez.cl',
                'logo_path' => 'lib/images/logo-min.jpg',
                'descripcion' => 'Construcciones y Remodelaciones'
            ];
        }

        return [
            'nombre' => $empresa->nombre,
            'nombre_comercial' => $empresa->nombre_comercial ?? $empresa->nombre,
            'direccion' => $empresa->direccion,
            'telefono' => $empresa->telefono,
            'email' => $empresa->email,
            'sitio_web' => $empresa->sitio_web,
            'logo_path' => $empresa->logo_path,
            'descripcion' => $empresa->descripcion ?? 'Construcciones y Remodelaciones'
        ];
    }
}
