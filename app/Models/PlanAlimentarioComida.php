<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanAlimentarioComida extends Model
{
    protected $table = 'plan_alimentario_comida';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'plan_alimentario_id', 'comida', 'porcentaje_vct', 'minuta',
        'total_kcal', 'total_cho', 'total_grasa', 'total_prot', 'orden'
    ];

    protected $useTimestamps = false;

    /**
     * Obtener comidas por plan
     */
    public function getPorPlan($planAlimentarioId)
    {
        $comidas = $this->where('plan_alimentario_id', $planAlimentarioId)
            ->orderBy('orden', 'ASC')
            ->findAll();

        // Cargar items de cada comida
        $itemModel = new PlanAlimentarioItem();
        foreach ($comidas as $comida) {
            $comida->items = $itemModel->getPorComida($comida->id);
        }

        return $comidas;
    }

    /**
     * Obtener comida específica con items
     */
    public function getComidaCompleta($id)
    {
        $comida = $this->find($id);
        if ($comida) {
            $itemModel = new PlanAlimentarioItem();
            $comida->items = $itemModel->getPorComida($id);
        }
        return $comida;
    }
}
