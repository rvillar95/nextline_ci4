<?php

namespace App\Models;

use CodeIgniter\Model;

class CalorimetriaActividad extends Model
{
    protected $table = 'calorimetria_actividad';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'calorimetria_id', 'tipo', 'actividad', 'mets', 'minutos_dia',
        'calorias_hombre', 'calorias_mujer', 'orden'
    ];

    protected $useTimestamps = false;

    /**
     * Obtener actividades por calorimetria_id
     */
    public function getPorCalorimetria($calorimetriaId)
    {
        return $this->where('calorimetria_id', $calorimetriaId)
            ->orderBy('tipo', 'ASC')
            ->orderBy('orden', 'ASC')
            ->findAll();
    }
}
