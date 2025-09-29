<?php

namespace App\Models;

use CodeIgniter\Model;

class Region extends Model
{
    protected $table = 'regiones';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'codigo', 'nombre', 'numero', 'activo'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'codigo' => 'required|string|max_length[10]',
        'nombre' => 'required|string|max_length[100]',
        'numero' => 'required|integer|greater_than[0]|less_than[20]',
        'activo' => 'permit_empty|integer|in_list[0,1]'
    ];

    protected $validationMessages = [
        'codigo' => [
            'required' => 'El código de la región es obligatorio',
            'max_length' => 'El código no puede exceder 10 caracteres'
        ],
        'nombre' => [
            'required' => 'El nombre de la región es obligatorio',
            'max_length' => 'El nombre no puede exceder 100 caracteres'
        ],
        'numero' => [
            'required' => 'El número de la región es obligatorio',
            'greater_than' => 'El número debe ser mayor a 0',
            'less_than' => 'El número debe ser menor a 20'
        ]
    ];

    /**
     * Obtener todas las regiones activas ordenadas por número
     */
    public function getRegionesActivas()
    {
        return $this->where('activo', 1)
                   ->orderBy('numero', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener región por código
     */
    public function getRegionPorCodigo($codigo)
    {
        return $this->where('codigo', $codigo)
                   ->where('activo', 1)
                   ->first();
    }

    /**
     * Obtener región por número
     */
    public function getRegionPorNumero($numero)
    {
        return $this->where('numero', $numero)
                   ->where('activo', 1)
                   ->first();
    }

    /**
     * Obtener comunas de una región específica
     */
    public function getComunasDeRegion($regionId)
    {
        $comunaModel = new Comuna();
        return $comunaModel->where('region_id', $regionId)
                          ->where('activo', 1)
                          ->orderBy('nombre', 'ASC')
                          ->findAll();
    }

    /**
     * Obtener estadísticas de regiones
     */
    public function getEstadisticasRegiones()
    {
        $comunaModel = new Comuna();
        
        $regiones = $this->where('activo', 1)->findAll();
        $estadisticas = [];
        
        foreach ($regiones as $region) {
            $totalComunas = $comunaModel->where('region_id', $region->id)
                                       ->where('activo', 1)
                                       ->countAllResults();
            
            $estadisticas[] = [
                'region' => $region,
                'total_comunas' => $totalComunas
            ];
        }
        
        return $estadisticas;
    }
}
