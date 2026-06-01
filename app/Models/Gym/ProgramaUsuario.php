<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class ProgramaUsuario extends Model
{
    protected $table = 'gym_programa_usuario';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'programa_id',
        'usuario_id',
        'asignado_por_usuario_id',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    public function getAsignacionesByEmpresa(int $empresaId): array
    {
        $db = \Config\Database::connect();
        return $db->table('gym_programa_usuario gpu')
            ->select([
                'gpu.*',
                'p.nombre AS programa_nombre',
                "CONCAT(a.nombre, ' ', a.apellido) AS alumno_nombre",
                'a.correo AS alumno_correo',
            ])
            ->join('gym_programa p', 'p.id = gpu.programa_id')
            ->join('usuario a', 'a.id = gpu.usuario_id')
            ->where('p.empresa_id', $empresaId)
            ->orderBy('gpu.id', 'DESC')
            ->get()
            ->getResult('object');
    }
}

