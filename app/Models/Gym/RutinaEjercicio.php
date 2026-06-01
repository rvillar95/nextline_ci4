<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class RutinaEjercicio extends Model
{
    protected $table = 'gym_rutina_ejercicio';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'rutina_id',
        'ejercicio_id',
        'orden',
        'series',
        'repeticiones',
        'descanso_seg',
        'notas',
    ];

    protected $useTimestamps = false;

    public function getDetalleRutina(int $rutinaId): array
    {
        $db = \Config\Database::connect();
        return $db->table('gym_rutina_ejercicio re')
            ->select([
                're.*',
                'e.nombre AS ejercicio_nombre',
                'gmp.nombre AS grupo_muscular_principal',
            ])
            ->join('gym_ejercicio e', 'e.id = re.ejercicio_id')
            ->join('gym_grupo_muscular gmp', 'gmp.id = e.grupo_muscular_principal_id')
            ->where('re.rutina_id', $rutinaId)
            ->orderBy('re.orden', 'ASC')
            ->get()
            ->getResult('array');
    }
}

