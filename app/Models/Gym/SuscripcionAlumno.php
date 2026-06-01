<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class SuscripcionAlumno extends Model
{
    protected $table = 'gym_suscripcion_alumno';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'empresa_id',
        'usuario_id',
        'plan_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'fecha_proximo_pago',
        'renovacion_automatica',
        'mp_preapproval_id',
        'mp_ultimo_payment_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    public function getByUsuario(int $usuarioId)
    {
        return $this->where('usuario_id', $usuarioId)->first();
    }
}

