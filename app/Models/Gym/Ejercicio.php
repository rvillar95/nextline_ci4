<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class Ejercicio extends Model
{
    protected $table = 'gym_ejercicio';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'empresa_id',
        'nombre',
        'grupo_muscular_principal_id',
        'grupo_muscular_secundario_id',
        'tipo_base_id',
        'instrucciones',
        'activo',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    public function getAllByEmpresa(int $empresaId): array
    {
        return $this->select([
                'gym_ejercicio.*',
                'gmp.nombre AS grupo_muscular_principal',
                'gms.nombre AS grupo_muscular_secundario',
                'tbe.nombre AS tipo_base_nombre',
            ])
            ->join('gym_grupo_muscular gmp', 'gmp.id = gym_ejercicio.grupo_muscular_principal_id')
            ->join('gym_grupo_muscular gms', 'gms.id = gym_ejercicio.grupo_muscular_secundario_id', 'left')
            ->join('gym_tipo_base_ejercicio tbe', 'tbe.id = gym_ejercicio.tipo_base_id')
            ->where('gym_ejercicio.empresa_id', $empresaId)
            ->orderBy('gym_ejercicio.id', 'DESC')
            ->findAll();
    }
}

