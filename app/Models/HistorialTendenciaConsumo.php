<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialTendenciaConsumo extends Model
{
    protected $table = 'historial_tendencia_consumo';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'historial_clinico_id', 'grupo', 'preferencia', 'alergia_intolerancia'
    ];

    protected $useTimestamps = false;

    /** Grupos de alimentos fijos (slug => etiqueta) */
    public static function getGrupos()
    {
        return [
            'pan' => 'PAN',
            'cereales_almuerzo' => 'CEREALES ALMUERZO',
            'cereales_desayuno' => 'CEREALES DESAYUNO',
            'verduras' => 'VERDURAS',
            'frutas' => 'FRUTAS',
            'lacteos' => 'LÁCTEOS',
            'carnes_rojas' => 'CARNES ROJAS',
            'carnes_blancas' => 'CARNES BLANCAS',
            'pescado' => 'PESCADO',
            'mariscos' => 'MARISCOS',
            'huevo' => 'HUEVO',
            'embutidos' => 'EMBUTIDOS',
            'legumbres' => 'LEGUMBRES',
            'frutos_secos_palta' => 'FRUTOS SECOS / PALTA',
            'azucar' => 'AZÚCAR',
            'comida_chatarra' => 'COMIDA CHATARRA',
        ];
    }

    /**
     * Obtener tendencia de consumo por historial (índice por grupo)
     */
    public function getPorHistorial($historialClinicoId)
    {
        $rows = $this->where('historial_clinico_id', $historialClinicoId)
            ->orderBy('id', 'ASC')
            ->findAll();
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row->grupo] = $row;
        }
        return $indexed;
    }
}
