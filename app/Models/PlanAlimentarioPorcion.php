<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanAlimentarioPorcion extends Model
{
    protected $table = 'plan_alimentario_porcion';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'plan_alimentario_id', 'intercambio_porcion_id', 'porciones',
        'calorias', 'cho', 'grasa', 'prot', 'orden'
    ];

    protected $useTimestamps = false;

    /**
     * Obtener porciones por plan
     */
    public function getPorPlan($planAlimentarioId)
    {
        $porciones = $this->where('plan_alimentario_id', $planAlimentarioId)
            ->orderBy('orden', 'ASC')
            ->findAll();

        // Cargar información del intercambio
        $intercambioModel = new IntercambioPorcion();
        foreach ($porciones as $porcion) {
            $porcion->intercambio = $intercambioModel->find($porcion->intercambio_porcion_id);
        }

        return $porciones;
    }
}
