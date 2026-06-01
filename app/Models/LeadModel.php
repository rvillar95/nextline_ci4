<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class LeadModel extends Model
{
    protected $table         = 'lead_contacto';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nombre', 'correo', 'telefono', 'mensaje', 'plan_interes', 'servicio_id',
        'estado_id', 'utm_source', 'utm_medium', 'utm_campaign', 'fcreacion', 'factualizacion',
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'nombre'       => 'required|string|min_length[2]|max_length[100]',
        'correo'       => 'required|valid_email|max_length[150]',
        'telefono'     => 'required|string|min_length[9]|max_length[50]',
        'mensaje'      => 'required|string|min_length[10]|max_length[2000]',
        'plan_interes' => 'required|string|max_length[120]',
        'servicio_id'  => 'permit_empty|is_natural',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required'   => 'El campo nombre es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 2 caracteres.',
            'max_length' => 'El nombre no puede exceder 100 caracteres.',
        ],
        'correo' => [
            'required'    => 'El campo correo es obligatorio.',
            'valid_email' => 'Ingrese un correo electrónico válido.',
        ],
        'telefono' => [
            'required'   => 'El campo teléfono es obligatorio.',
            'min_length' => 'El teléfono debe tener al menos 9 caracteres.',
        ],
        'mensaje' => [
            'required'   => 'El campo mensaje es obligatorio.',
            'min_length' => 'El mensaje debe tener al menos 10 caracteres.',
        ],
        'plan_interes' => [
            'required'   => 'Seleccione un plan o servicio de interés.',
            'max_length' => 'El plan seleccionado no es válido.',
        ],
    ];

    public function getValidationRules(array $options = []): array
    {
        return $this->validationRules;
    }

    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }
}
