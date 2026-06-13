<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class AbogadoModel extends BaseAbopechModel
{
    protected $table            = 'rj_abogado';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'usuario_id', 'rut', 'nombres', 'apellidos', 'habilidades', 'experiencia', 'estado_perfil',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';

    public function findByUsuarioId(int $usuarioId): ?array
    {
        return $this->where('usuario_id', $usuarioId)->first();
    }

    public function findPublico(int $id): ?array
    {
        return $this->where('id', $id)->where('estado_perfil', 'aprobado')->first();
    }
}
