<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanAlimentarioItem extends Model
{
    protected $table = 'plan_alimentario_item';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'plan_alimentario_comida_id', 'intercambio_porcion_id', 'ingrediente',
        'porciones', 'medida_casera', 'gramaje',
        'calorias', 'cho', 'grasa', 'prot',
        'costo_promedio', 'cantidad_comprada', 'observacion', 'orden'
    ];

    protected $useTimestamps = false;

    /**
     * Obtener items por comida
     */
    public function getPorComida($planAlimentarioComidaId)
    {
        $items = $this->where('plan_alimentario_comida_id', $planAlimentarioComidaId)
            ->orderBy('orden', 'ASC')
            ->findAll();

        // Cargar información del intercambio
        $intercambioModel = new IntercambioPorcion();
        foreach ($items as $item) {
            $item->intercambio = $intercambioModel->find($item->intercambio_porcion_id);
        }

        return $items;
    }
}
