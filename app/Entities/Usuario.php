<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $attributes = [
        'id' => null,
        'nombre' => null,
        'apellido' => null,
        'correo' => null,
        'telefono' => null,
        'clave' => null,
        'perfil_id' => null,
        'estado' => 'A',
        'fcreacion' => null,
        'factualizacion' => null,
        'feliminacion' => null,
    ];

    // Puedes definir getters y setters para campos específicos si es necesario
    public function setClave(string $password)
    {
        $this->attributes['clave'] = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }
}