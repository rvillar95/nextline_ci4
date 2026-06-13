<?php

declare(strict_types=1);

namespace App\Services\Abopech;

use App\Models\Abopech\AbogadoModel;
use App\Models\Abopech\AbogadoEstudioModel;
use App\Models\Abopech\ComunaModel;
use App\Models\Abopech\RegionModel;
use App\Models\Abopech\TribunalModel;
use App\Models\Abopech\TipoEstudioModel;

final class PerfilAbogadoService
{
    public function getPerfilCompleto(int $abogadoId): ?array
    {
        $abogado = (new AbogadoModel())->find($abogadoId);
        if (!$abogado) {
            return null;
        }

        $db = db_connect('abopech');

        $regiones = $db->table('rj_abogado_region ar')
            ->select('r.*')
            ->join('rj_region r', 'r.id = ar.region_id')
            ->where('ar.abogado_id', $abogadoId)
            ->get()->getResultArray();

        $comunas = $db->table('rj_abogado_comuna ac')
            ->select('c.*')
            ->join('rj_comuna c', 'c.id = ac.comuna_id')
            ->where('ac.abogado_id', $abogadoId)
            ->get()->getResultArray();

        $tribunales = $db->table('rj_abogado_tribunal at')
            ->select('t.*')
            ->join('rj_tribunal t', 't.id = at.tribunal_id')
            ->where('at.abogado_id', $abogadoId)
            ->where('at.estado', 'activo')
            ->get()->getResultArray();

        $estudios = (new AbogadoEstudioModel())->listByAbogado($abogadoId);

        return [
            'abogado'    => $abogado,
            'regiones'   => $regiones,
            'comunas'    => $comunas,
            'tribunales' => $tribunales,
            'estudios'   => $estudios,
        ];
    }

    public function syncRelaciones(int $abogadoId, array $regionIds, array $comunaIds, array $tribunalIds): void
    {
        $db = db_connect('abopech');

        $db->table('rj_abogado_region')->where('abogado_id', $abogadoId)->delete();
        foreach (array_unique(array_map('intval', $regionIds)) as $rid) {
            if ($rid > 0) {
                $db->table('rj_abogado_region')->insert(['abogado_id' => $abogadoId, 'region_id' => $rid]);
            }
        }

        $db->table('rj_abogado_comuna')->where('abogado_id', $abogadoId)->delete();
        foreach (array_unique(array_map('intval', $comunaIds)) as $cid) {
            if ($cid > 0) {
                $db->table('rj_abogado_comuna')->insert(['abogado_id' => $abogadoId, 'comuna_id' => $cid]);
            }
        }

        $db->table('rj_abogado_tribunal')->where('abogado_id', $abogadoId)->delete();
        foreach (array_unique(array_map('intval', $tribunalIds)) as $tid) {
            if ($tid > 0) {
                $db->table('rj_abogado_tribunal')->insert([
                    'abogado_id'  => $abogadoId,
                    'tribunal_id' => $tid,
                    'estado'      => 'activo',
                ]);
            }
        }
    }
}
