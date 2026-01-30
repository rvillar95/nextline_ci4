<?php

namespace App\Models;

use CodeIgniter\Model;

class IntercambioPorcion extends Model
{
    protected $table = 'intercambio_porcion';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'codigo', 'nombre', 'kcal', 'cho_g', 'grasa_g', 'prot_g', 'empresa_id', 'activo'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = null;

    /**
     * Obtener intercambio por código
     */
    public function getPorCodigo($codigo, $empresaId = null)
    {
        $builder = $this->where('codigo', $codigo)
            ->where('activo', 'A');
        
        if ($empresaId) {
            $builder->where('(empresa_id IS NULL OR empresa_id = ' . (int)$empresaId . ')', null, false);
        } else {
            $builder->where('empresa_id IS NULL');
        }
        
        return $builder->first();
    }

    /**
     * Obtener todos los intercambios activos
     */
    public function getIntercambiosActivos($empresaId = null)
    {
        $builder = $this->where('activo', 'A')
            ->orderBy('nombre', 'ASC');
        
        if ($empresaId) {
            $builder->where('(empresa_id IS NULL OR empresa_id = ' . (int)$empresaId . ')', null, false);
        } else {
            $builder->where('empresa_id IS NULL');
        }
        
        return $builder->findAll();
    }
}
