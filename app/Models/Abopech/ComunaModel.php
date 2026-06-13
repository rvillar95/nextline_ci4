<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class ComunaModel extends BaseAbopechModel
{
    protected $table         = 'rj_comuna';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['region_id', 'codigo', 'nombre', 'activo'];

    public function listByRegion(int $regionId): array
    {
        return $this->where('region_id', $regionId)->where('activo', 1)->orderBy('nombre', 'ASC')->findAll();
    }
}
