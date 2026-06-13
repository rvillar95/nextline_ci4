<?php

declare(strict_types=1);

namespace App\Models\Abopech;

final class DocumentoGcsModel extends BaseAbopechModel
{
    protected $table         = 'rj_documento_gcs';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'abogado_id', 'nombre', 'descripcion', 'bucket', 'object_key', 'content_type',
        'tamano_bytes', 'sha256_hex', 'gcs_generation', 'subido_por_usuario_id',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'fcreacion';
    protected $updatedField  = null;
}
