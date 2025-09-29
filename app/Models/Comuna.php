<?php

namespace App\Models;

use CodeIgniter\Model;

class Comuna extends Model
{
    protected $table = 'comunas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'codigo', 'nombre', 'region_id', 'activo'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'codigo' => 'required|string|max_length[10]',
        'nombre' => 'required|string|max_length[100]',
        'region_id' => 'required|integer|greater_than[0]',
        'activo' => 'permit_empty|integer|in_list[0,1]'
    ];

    protected $validationMessages = [
        'codigo' => [
            'required' => 'El código de la comuna es obligatorio',
            'max_length' => 'El código no puede exceder 10 caracteres'
        ],
        'nombre' => [
            'required' => 'El nombre de la comuna es obligatorio',
            'max_length' => 'El nombre no puede exceder 100 caracteres'
        ],
        'region_id' => [
            'required' => 'La región es obligatoria',
            'greater_than' => 'La región debe ser válida'
        ]
    ];

    /**
     * Obtener todas las comunas activas ordenadas por nombre
     */
    public function getComunasActivas()
    {
        return $this->where('activo', 1)
                   ->orderBy('nombre', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener comunas de una región específica
     */
    public function getComunasPorRegion($regionId)
    {
        return $this->where('region_id', $regionId)
                   ->where('activo', 1)
                   ->orderBy('nombre', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener comuna por código
     */
    public function getComunaPorCodigo($codigo)
    {
        return $this->where('codigo', $codigo)
                   ->where('activo', 1)
                   ->first();
    }

    /**
     * Obtener comuna con información de región
     */
    public function getComunaConRegion($comunaId)
    {
        return $this->select('comunas.*, regiones.nombre as region_nombre, regiones.codigo as region_codigo')
                   ->join('regiones', 'regiones.id = comunas.region_id')
                   ->where('comunas.id', $comunaId)
                   ->where('comunas.activo', 1)
                   ->first();
    }

    /**
     * Buscar comunas por nombre (para autocompletado)
     */
    public function buscarComunas($termino, $limit = 10)
    {
        return $this->select('comunas.*, regiones.nombre as region_nombre')
                   ->join('regiones', 'regiones.id = comunas.region_id')
                   ->where('comunas.activo', 1)
                   ->where('comunas.nombre LIKE', "%$termino%")
                   ->orderBy('comunas.nombre', 'ASC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Obtener estadísticas de comunas por región
     */
    public function getEstadisticasComunas()
    {
        $regionModel = new Region();
        
        $regiones = $regionModel->where('activo', 1)->findAll();
        $estadisticas = [];
        
        foreach ($regiones as $region) {
            $totalComunas = $this->where('region_id', $region->id)
                                ->where('activo', 1)
                                ->countAllResults();
            
            $estadisticas[] = [
                'region' => $region,
                'total_comunas' => $totalComunas
            ];
        }
        
        return $estadisticas;
    }

    /**
     * Validar que una comuna pertenece a una región específica
     */
    public function validarComunaRegion($comunaId, $regionId)
    {
        $comuna = $this->where('id', $comunaId)
                      ->where('region_id', $regionId)
                      ->where('activo', 1)
                      ->first();
        
        return $comuna !== null;
    }
}
