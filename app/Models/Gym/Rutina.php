<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class Rutina extends Model
{
    protected $table = 'gym_rutina';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'empresa_id',
        'creado_por_usuario_id',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    public function getAllByEmpresa(int $empresaId): array
    {
        return $this->select([
                'gym_rutina.*',
                "CONCAT(u.nombre, ' ', u.apellido) AS creado_por",
            ])
            ->join('usuario u', 'u.id = gym_rutina.creado_por_usuario_id', 'left')
            ->where('gym_rutina.empresa_id', $empresaId)
            ->orderBy('gym_rutina.id', 'DESC')
            ->findAll();
    }
}

