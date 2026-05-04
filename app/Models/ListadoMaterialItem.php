<?php

namespace App\Models;

use CodeIgniter\Model;

class ListadoMaterialItem extends Model
{
    protected $table            = 'listado_material_item';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'listado_material_id',
        'nombre_material',
        'descripcion',
        'unidad_medida',
        'cantidad',
        'orden'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Obtiene items por listado_material_id
     */
    public function getItemsByListado($listadoMaterialId)
    {
        return $this->where('listado_material_id', $listadoMaterialId)
                    ->orderBy('orden', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Elimina todos los items de un listado
     */
    public function deleteByListado($listadoMaterialId)
    {
        return $this->where('listado_material_id', $listadoMaterialId)->delete();
    }
}

