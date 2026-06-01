<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class Programa extends Model
{
    protected $table = 'gym_programa';
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
        'duracion_semanas',
        'activo',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    public function getAllByEmpresa(int $empresaId): array
    {
        return $this->select([
                'gym_programa.*',
                "CONCAT(u.nombre, ' ', u.apellido) AS creado_por",
            ])
            ->join('usuario u', 'u.id = gym_programa.creado_por_usuario_id', 'left')
            ->where('gym_programa.empresa_id', $empresaId)
            ->orderBy('gym_programa.id', 'DESC')
            ->findAll();
    }
}

