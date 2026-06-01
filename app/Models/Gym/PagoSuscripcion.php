<?php

namespace App\Models\Gym;

use CodeIgniter\Model;

class PagoSuscripcion extends Model
{
    protected $table = 'gym_pago_suscripcion';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'empresa_id',
        'usuario_id',
        'plan_id',
        'monto',
        'moneda',
        'estado',
        'mp_preference_id',
        'mp_payment_id',
        'external_reference',
        'detalle',
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';
}

