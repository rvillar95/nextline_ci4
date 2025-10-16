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
        'estado_id','utm_source','utm_medium','utm_campaign','fcreacion','factualizacion'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'nombre'  => 'required|string|min_length[2]|max_length[100]',
        'correo'  => 'required|valid_email|max_length[150]',
        'telefono'=> 'required|string|min_length[9]|max_length[50]',
        'mensaje' => 'required|string|min_length[10]|max_length[2000]',
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
            'required' => 'El campo teléfono es obligatorio.',
            'string' => 'El campo teléfono debe ser una cadena de texto.',
            'min_length' => 'El campo teléfono debe tener al menos 9 caracteres.',
            'max_length' => 'El campo teléfono no puede exceder de 50 caracteres.'
        ],
        'mensaje' => [
            'required' => 'El campo mensaje es obligatorio.',
            'string' => 'El campo mensaje debe ser una cadena de texto.',
            'min_length' => 'El campo mensaje debe tener al menos 10 caracteres.',
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
