<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class RegionModel extends BaseAbopechModel
{
    protected $table         = 'rj_region';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['codigo', 'numero', 'nombre', 'activo'];

    public function listActivas(): array
    {
        return $this->where('activo', 1)->orderBy('numero', 'ASC')->findAll();
    }
}
