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
        'nombre','correo','telefono','mensaje','servicio_id',
        'estado_id','utm_source','utm_medium','utm_campaign','factualizacion'
    ];
    protected $useTimestamps = false;

    protected $validationRules = [
        'nombre'  => 'required|string|min_length[2]|max_length[100]',
        'correo'  => 'required|valid_email|max_length[150]',
        'telefono'=> 'permit_empty|chilean_phone|max_length[50]',
        'mensaje' => 'permit_empty|max_length[2000]',
        'servicio_id' => 'required|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El campo nombre es obligatorio.',
            'string' => 'El campo nombre debe ser una cadena de texto.',
            'min_length' => 'El campo nombre debe tener al menos 2 caracteres.',
            'max_length' => 'El campo nombre no puede exceder de 100 caracteres.'
        ],
        'correo' => [
            'required' => 'El campo correo es obligatorio.',
            'valid_email' => 'El campo correo debe contener una dirección de correo válida.',
            'max_length' => 'El campo correo no puede exceder de 150 caracteres.'
        ],
        'telefono' => [
            'max_length' => 'El campo teléfono no puede exceder de 50 caracteres.',
            'chilean_phone' => 'El campo teléfono debe ser un número chileno válido (ej: +56912345678 o 912345678).'
        ],
        'mensaje' => [
            'max_length' => 'El campo mensaje no puede exceder de 2000 caracteres.'
        ],
        'servicio_id' => [
            'required' => 'El campo servicio de interés es obligatorio.',
            'is_natural_no_zero' => 'El campo servicio debe ser un número válido.'
        ]
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
