<?php

namespace App\Models;

use CodeIgniter\Model;

class BotonPagoPlantilla extends Model
{
    protected $table = 'botones_pago_plantilla';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'empresa_id', 'titulo', 'descripcion', 'monto', 'moneda', 'activo', 'orden'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'empresa_id' => 'required|integer|greater_than[0]',
        'titulo' => 'required|string|max_length[255]',
        'monto' => 'required|decimal|greater_than[0]',
        'moneda' => 'required|string|max_length[10]',
        'activo' => 'required|in_list[A,I]'
    ];

    protected $validationMessages = [
        'empresa_id' => [
            'required' => 'La empresa es obligatoria.'
        ],
        'titulo' => [
            'required' => 'El título es obligatorio.'
        ],
        'monto' => [
            'required' => 'El monto es obligatorio.',
            'greater_than' => 'El monto debe ser mayor a 0.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener plantillas activas de una empresa
     */
    public function getPlantillasActivas($empresaId)
    {
        return $this->where('empresa_id', $empresaId)
            ->where('activo', 'A')
            ->orderBy('orden', 'ASC')
            ->orderBy('titulo', 'ASC')
            ->findAll();
    }

    /**
     * Obtener plantilla por ID
     */
    public function getPlantilla($id, $empresaId = null)
    {
        $builder = $this->where('id', $id);
        
        if ($empresaId) {
            $builder->where('empresa_id', $empresaId);
        }
        
        return $builder->first();
    }
}
