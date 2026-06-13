<?php

declare(strict_types=1);

namespace App\Services\Abopech;

final class AbogadoBusquedaService
{
    /**
     * @return list<array<string, mixed>>
     */
    public function buscar(?int $regionId = null, ?int $comunaId = null, ?int $tribunalId = null, int $limit = 50): array
    {
        $db = db_connect('abopech');
        $builder = $db->table('rj_abogado a')
            ->select('a.*, u.telefono AS usuario_telefono, u.correo AS usuario_correo')
            ->join('rj_usuario u', 'u.id = a.usuario_id')
            ->where('a.estado_perfil', 'aprobado')
            ->where('u.estado', 'A');

        if ($regionId !== null && $regionId > 0) {
            $builder->join('rj_abogado_region ar', 'ar.abogado_id = a.id')
                ->where('ar.region_id', $regionId);
        }

        if ($comunaId !== null && $comunaId > 0) {
            $builder->join('rj_abogado_comuna ac', 'ac.abogado_id = a.id')
                ->where('ac.comuna_id', $comunaId);
        }

        if ($tribunalId !== null && $tribunalId > 0) {
            $builder->join('rj_abogado_tribunal at', 'at.abogado_id = a.id')
                ->where('at.tribunal_id', $tribunalId)
                ->where('at.estado', 'activo');
        }

        return $builder->groupBy('a.id')->orderBy('a.apellidos', 'ASC')->limit($limit)->get()->getResultArray();
    }
}
