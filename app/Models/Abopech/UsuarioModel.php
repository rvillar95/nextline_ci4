<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class UsuarioModel extends BaseAbopechModel
{
    protected $table            = 'rj_usuario';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'correo', 'clave', 'nombre', 'apellido', 'telefono', 'tipo_cuenta', 'estado',
        'consentimiento_contacto', 'terminos_aceptados_en',
        'oauth_google_sub', 'oauth_google_email', 'oauth_vinculado_en',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';

    public function findByCorreo(string $correo): ?array
    {
        return $this->where('correo', $correo)->where('estado', 'A')->first();
    }

    public function findByGoogleSub(string $sub): ?array
    {
        return $this->where('oauth_google_sub', $sub)->where('estado', 'A')->first();
    }
}
