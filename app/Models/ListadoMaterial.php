<?php

namespace App\Models;

use CodeIgniter\Model;

class ListadoMaterial extends Model
{
    protected $table            = 'listado_material';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'numero_listado',
        'titulo',
        'cliente_id',
        'proyecto_id',
        'fecha_listado',
        'estado',
        'observaciones'
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
    protected $beforeInsert   = ['generateNumeroListado'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Genera automáticamente el número de listado
     */
    protected function generateNumeroListado(array $data)
    {
        if (empty($data['data']['numero_listado'])) {
            $year = date('Y');
            $lastListado = $this->where('numero_listado LIKE', "LM-{$year}-%")
                                ->orderBy('id', 'DESC')
                                ->first();

            if ($lastListado) {
                preg_match('/LM-\d{4}-(\d+)/', $lastListado->numero_listado, $matches);
                $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
            } else {
                $nextNumber = 1;
            }

            $data['data']['numero_listado'] = sprintf('LM-%s-%04d', $year, $nextNumber);
        }

        return $data;
    }

    /**
     * Obtiene un listado con sus relaciones
     */
    public function getListadoConRelaciones($id)
    {
        return $this->select('listado_material.*, 
                              c.nombre_razon_social as cliente_nombre,
                              p.nombre as proyecto_nombre')
                    ->join('clientes c', 'c.id = listado_material.cliente_id', 'left')
                    ->join('proyectos p', 'p.id = listado_material.proyecto_id', 'left')
                    ->where('listado_material.id', $id)
                    ->first();
    }

    /**
     * Obtiene todos los listados con sus relaciones
     */
    public function getListadosConRelaciones()
    {
        return $this->select('listado_material.*, 
                              c.nombre_razon_social as cliente_nombre,
                              p.nombre as proyecto_nombre')
                    ->join('clientes c', 'c.id = listado_material.cliente_id', 'left')
                    ->join('proyectos p', 'p.id = listado_material.proyecto_id', 'left')
                    ->orderBy('listado_material.created_at', 'DESC')
                    ->findAll();
    }
}

