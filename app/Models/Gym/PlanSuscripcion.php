<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class PlanSuscripcion extends Model
{
    protected $table = 'gym_plan_suscripcion';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'empresa_id',
        'nombre',
        'descripcion',
        'monto_mensual',
        'moneda',
        'activo',
        'orden',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    public function getActivosByEmpresa(int $empresaId): array
    {
        return $this->where('empresa_id', $empresaId)
            ->where('activo', 1)
            ->orderBy('orden', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}

