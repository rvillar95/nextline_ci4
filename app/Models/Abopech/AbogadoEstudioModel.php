<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class AbogadoEstudioModel extends BaseAbopechModel
{
    protected $table         = 'rj_abogado_estudio';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'abogado_id', 'nombre', 'descripcion', 'universidad', 'anio_titulacion',
        'tipo_estudio_id', 'documento_id',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';

    public function listByAbogado(int $abogadoId): array
    {
        return $this->where('abogado_id', $abogadoId)->orderBy('anio_titulacion', 'DESC')->findAll();
    }
}
