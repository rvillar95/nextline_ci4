<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class ContactoModel extends BaseAbopechModel
{
    protected $table         = 'contacto';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'abogado_id', 'nombre', 'apellido', 'correo', 'telefono', 'consentimiento_datos',
        'canal_redireccion', 'destino_e164', 'estado_seguimiento', 'notas_internas',
        'ip_origen', 'user_agent',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';
}
