<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class TipoEstudioModel extends BaseAbopechModel
{
    protected $table         = 'rj_tipo_estudio';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nombre', 'orden', 'activo'];

    public function listActivos(): array
    {
        return $this->where('activo', 1)->orderBy('orden', 'ASC')->findAll();
    }
}
