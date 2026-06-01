<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class ProgramaRutina extends Model
{
    protected $table = 'gym_programa_rutina';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'programa_id',
        'rutina_id',
        'orden',
        'dia_semana',
    ];

    protected $useTimestamps = false;

    public function getDetallePrograma(int $programaId): array
    {
        $db = \Config\Database::connect();
        return $db->table('gym_programa_rutina pr')
            ->select([
                'pr.*',
                'r.nombre AS rutina_nombre',
            ])
            ->join('gym_rutina r', 'r.id = pr.rutina_id')
            ->where('pr.programa_id', $programaId)
            ->orderBy('pr.orden', 'ASC')
            ->get()
            ->getResult('array');
    }
}

