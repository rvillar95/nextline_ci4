<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialExamenBioquimico extends Model
{
    protected $table = 'historial_examen_bioquimico';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'historial_clinico_id', 'nombre', 'valor', 'fecha_interpretacion'
    ];

    protected $useTimestamps = false;

    /**
     * Obtener exámenes por historial
     */
    public function getPorHistorial($historialClinicoId)
    {
        return $this->where('historial_clinico_id', $historialClinicoId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
