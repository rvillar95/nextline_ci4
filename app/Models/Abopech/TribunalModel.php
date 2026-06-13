<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class TribunalModel extends BaseAbopechModel
{
    protected $table         = 'rj_tribunal';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'nombre', 'descripcion', 'region_id', 'comuna_id', 'tipo_codigo', 'estado', 'codigo_externo',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';

    public function listActivos(?string $tipoCodigo = null): array
    {
        $b = $this->where('estado', 'activo');
        if ($tipoCodigo !== null && $tipoCodigo !== '') {
            $b->where('tipo_codigo', $tipoCodigo);
        }

        return $b->orderBy('nombre', 'ASC')->findAll();
    }
}
