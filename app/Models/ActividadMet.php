<?php

namespace App\Models;

use CodeIgniter\Model;

class ActividadMet extends Model
{
    protected $table = 'actividad_met';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tipo', 'nombre', 'mets', 'descripcion', 'orden', 'activo'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = null;

    /**
     * Obtener actividades por tipo
     */
    public function getPorTipo($tipo)
    {
        return $this->where('tipo', $tipo)
            ->where('activo', 'A')
            ->orderBy('orden', 'ASC')
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtener todas las actividades activas
     */
    public function getActividadesActivas()
    {
        return $this->where('activo', 'A')
            ->orderBy('tipo', 'ASC')
            ->orderBy('orden', 'ASC')
            ->findAll();
    }
}
