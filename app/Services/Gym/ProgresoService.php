<?php

namespace App\Services\Gym;

class ProgresoService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Resumen últimos 30 días.
     */
    public function resumen30Dias(int $usuarioId): array
    {
        $desde = date('Y-m-d', strtotime('-29 days'));

        $sesiones = (int) $this->db->table('gym_entrenamiento')
            ->where('usuario_id', $usuarioId)
            ->where('estado', 'completado')
            ->where('DATE(COALESCE(finalizado_en, iniciado_en)) >=', $desde)
            ->countAllResults();

        $volumen = $this->volumenTotalDesde($usuarioId, $desde);

        return [
            'sesiones' => $sesiones,
            'volumen'  => round($volumen, 0),
            'desde'    => $desde,
        ];
    }

    /**
     * Volumen (kg × reps) por semana ISO, últimas N semanas (orden cronológico).
     *
     * @return list<array{semana: string, label: string, volumen: float}>
     */
    public function volumenSemanal(int $usuarioId, int $semanas = 8): array
    {
        $semanas = max(1, min(52, $semanas));
        $rows = $this->db->query("
            SELECT
                YEARWEEK(COALESCE(ge.finalizado_en, ge.iniciado_en), 1) AS yw,
                MIN(DATE(COALESCE(ge.finalizado_en, ge.iniciado_en))) AS semana_inicio,
                SUM(COALESCE(ges.peso_kg, 0) * COALESCE(ges.repeticiones, 0)) AS volumen
            FROM gym_entrenamiento_serie ges
            INNER JOIN gym_entrenamiento_ejercicio gee ON gee.id = ges.entrenamiento_ejercicio_id
            INNER JOIN gym_entrenamiento ge ON ge.id = gee.entrenamiento_id
            WHERE ge.usuario_id = ?
              AND ge.estado = 'completado'
              AND ges.peso_kg IS NOT NULL
              AND ges.repeticiones IS NOT NULL
              AND ges.peso_kg > 0
              AND ges.repeticiones > 0
            GROUP BY yw
            ORDER BY yw DESC
            LIMIT {$semanas}
        ", [$usuarioId])->getResult();

        $rows = array_reverse($rows);
        $out = [];
        foreach ($rows as $r) {
            $inicio = $r->semana_inicio ?? date('Y-m-d');
            $out[] = [
                'semana'  => (string) $r->yw,
                'label'   => date('d/m', strtotime($inicio)),
                'volumen' => round((float) $r->volumen, 0),
            ];
        }

        return $out;
    }

    /** Días consecutivos con al menos una sesión completada (hoy o ayer como inicio). */
    public function rachaDias(int $usuarioId): int
    {
        $fechas = $this->db->table('gym_entrenamiento')
            ->select('DATE(COALESCE(finalizado_en, iniciado_en)) AS d', false)
            ->where('usuario_id', $usuarioId)
            ->where('estado', 'completado')
            ->groupBy('d')
            ->orderBy('d', 'DESC')
            ->get()
            ->getResult();

        if ($fechas === []) {
            return 0;
        }

        $set = [];
        foreach ($fechas as $f) {
            $set[(string) $f->d] = true;
        }

        $hoy = date('Y-m-d');
        $cursor = isset($set[$hoy]) ? $hoy : date('Y-m-d', strtotime('-1 day'));
        if (! isset($set[$cursor])) {
            return 0;
        }

        $racha = 0;
        while (isset($set[$cursor])) {
            $racha++;
            $cursor = date('Y-m-d', strtotime($cursor . ' -1 day'));
        }

        return $racha;
    }

    /**
     * Ejercicios que el alumno ha registrado al menos una vez.
     */
    public function ejerciciosConHistorial(int $usuarioId): array
    {
        return $this->db->table('gym_entrenamiento_ejercicio gee')
            ->select('gee.ejercicio_id, gee.nombre_ejercicio, MAX(ge.iniciado_en) AS ultimo')
            ->join('gym_entrenamiento ge', 'ge.id = gee.entrenamiento_id')
            ->join('gym_entrenamiento_serie ges', 'ges.entrenamiento_ejercicio_id = gee.id')
            ->where('ge.usuario_id', $usuarioId)
            ->where('ge.estado', 'completado')
            ->where("(ges.completada = 'S' OR ges.peso_kg IS NOT NULL OR ges.repeticiones IS NOT NULL)", null, false)
            ->groupBy('gee.ejercicio_id, gee.nombre_ejercicio')
            ->orderBy('ultimo', 'DESC')
            ->get()
            ->getResult();
    }

    /**
     * Peso máximo por sesión para un ejercicio (para gráfico de línea).
     *
     * @return list<array{fecha: string, label: string, peso_max: float, reps: int|null}>
     */
    public function historialPesoEjercicio(int $usuarioId, int $ejercicioId, int $limit = 15): array
    {
        $limit = max(1, min(50, $limit));
        $rows = $this->db->query("
            SELECT
                ge.id AS entrenamiento_id,
                DATE(COALESCE(ge.finalizado_en, ge.iniciado_en)) AS fecha,
                MAX(ges.peso_kg) AS peso_max,
                MAX(ges.repeticiones) AS reps_max
            FROM gym_entrenamiento_serie ges
            INNER JOIN gym_entrenamiento_ejercicio gee ON gee.id = ges.entrenamiento_ejercicio_id
            INNER JOIN gym_entrenamiento ge ON ge.id = gee.entrenamiento_id
            WHERE ge.usuario_id = ?
              AND gee.ejercicio_id = ?
              AND ge.estado = 'completado'
              AND ges.peso_kg IS NOT NULL
              AND ges.peso_kg > 0
            GROUP BY ge.id, fecha
            ORDER BY fecha ASC
            LIMIT {$limit}
        ", [$usuarioId, $ejercicioId])->getResult();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'fecha'    => (string) $r->fecha,
                'label'    => date('d/m', strtotime($r->fecha)),
                'peso_max' => round((float) $r->peso_max, 1),
                'reps'     => $r->reps_max !== null ? (int) $r->reps_max : null,
            ];
        }

        return $out;
    }

    /** PR actual (mayor peso) por ejercicio. */
    public function recordPersonal(int $usuarioId, int $ejercicioId): ?array
    {
        if ($this->db->tableExists('gym_ejercicio_pr')) {
            $pr = $this->db->table('gym_ejercicio_pr')
                ->where('usuario_id', $usuarioId)
                ->where('ejercicio_id', $ejercicioId)
                ->orderBy('peso_kg', 'DESC')
                ->orderBy('logrado_en', 'DESC')
                ->get(1)
                ->getRow();

            if ($pr) {
                return [
                    'peso_kg'      => (float) $pr->peso_kg,
                    'repeticiones' => $pr->repeticiones !== null ? (int) $pr->repeticiones : null,
                    'fecha'        => $pr->logrado_en,
                ];
            }
        }

        $row = $this->db->table('gym_entrenamiento_serie ges')
            ->select('ges.peso_kg, ges.repeticiones, ge.iniciado_en')
            ->join('gym_entrenamiento_ejercicio gee', 'gee.id = ges.entrenamiento_ejercicio_id')
            ->join('gym_entrenamiento ge', 'ge.id = gee.entrenamiento_id')
            ->where('ge.usuario_id', $usuarioId)
            ->where('gee.ejercicio_id', $ejercicioId)
            ->where('ge.estado', 'completado')
            ->where('ges.peso_kg IS NOT NULL', null, false)
            ->orderBy('ges.peso_kg', 'DESC')
            ->orderBy('ge.iniciado_en', 'DESC')
            ->get(1)
            ->getRow();

        if (!$row) {
            return null;
        }

        return [
            'peso_kg'      => (float) $row->peso_kg,
            'repeticiones' => $row->repeticiones !== null ? (int) $row->repeticiones : null,
            'fecha'        => $row->iniciado_en,
        ];
    }

    protected function volumenTotalDesde(int $usuarioId, string $desdeYmd): float
    {
        $row = $this->db->query("
            SELECT SUM(COALESCE(ges.peso_kg, 0) * COALESCE(ges.repeticiones, 0)) AS vol
            FROM gym_entrenamiento_serie ges
            INNER JOIN gym_entrenamiento_ejercicio gee ON gee.id = ges.entrenamiento_ejercicio_id
            INNER JOIN gym_entrenamiento ge ON ge.id = gee.entrenamiento_id
            WHERE ge.usuario_id = ?
              AND ge.estado = 'completado'
              AND DATE(COALESCE(ge.finalizado_en, ge.iniciado_en)) >= ?
              AND ges.peso_kg IS NOT NULL AND ges.repeticiones IS NOT NULL
        ", [$usuarioId, $desdeYmd])->getRow();

        return (float) ($row->vol ?? 0);
    }

    /**
     * IDs de series que fueron récord personal en un entrenamiento.
     *
     * @return array<int, true>
     */
    public function prSerieIdsEnEntrenamiento(int $usuarioId, int $entrenamientoId): array
    {
        if (!$this->db->tableExists('gym_ejercicio_pr')) {
            return [];
        }

        $rows = $this->db->table('gym_ejercicio_pr pr')
            ->select('pr.entrenamiento_serie_id')
            ->join('gym_entrenamiento_serie ges', 'ges.id = pr.entrenamiento_serie_id')
            ->join('gym_entrenamiento_ejercicio gee', 'gee.id = ges.entrenamiento_ejercicio_id')
            ->where('pr.usuario_id', $usuarioId)
            ->where('gee.entrenamiento_id', $entrenamientoId)
            ->where('pr.entrenamiento_serie_id IS NOT NULL', null, false)
            ->get()
            ->getResult();

        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r->entrenamiento_serie_id] = true;
        }

        return $map;
    }

    /**
     * Por ejercicio en la sesión: series con mayor volumen (peso × reps).
     *
     * @return array<int, float> serie_id => volumen kg
     */
    public function seriesMejorVolumenEnEntrenamiento(int $entrenamientoId): array
    {
        $rows = $this->db->table('gym_entrenamiento_serie ges')
            ->select('ges.id, gee.ejercicio_id, ges.peso_kg, ges.repeticiones')
            ->join('gym_entrenamiento_ejercicio gee', 'gee.id = ges.entrenamiento_ejercicio_id')
            ->where('gee.entrenamiento_id', $entrenamientoId)
            ->where('ges.peso_kg IS NOT NULL', null, false)
            ->where('ges.peso_kg >', 0)
            ->where('ges.repeticiones IS NOT NULL', null, false)
            ->where('ges.repeticiones >', 0)
            ->get()
            ->getResult();

        $maxPorEjercicio = [];
        foreach ($rows as $r) {
            $vol = (float) $r->peso_kg * (int) $r->repeticiones;
            $ejId = (int) $r->ejercicio_id;
            if (!isset($maxPorEjercicio[$ejId]) || $vol > $maxPorEjercicio[$ejId]) {
                $maxPorEjercicio[$ejId] = $vol;
            }
        }

        $out = [];
        foreach ($rows as $r) {
            $vol = (float) $r->peso_kg * (int) $r->repeticiones;
            $ejId = (int) $r->ejercicio_id;
            if (isset($maxPorEjercicio[$ejId]) && $vol >= $maxPorEjercicio[$ejId]) {
                $out[(int) $r->id] = round($vol, 0);
            }
        }

        return $out;
    }

    /**
     * Últimos récords personales logrados.
     *
     * @return list<object>
     */
    public function ultimosPr(int $usuarioId, int $limit = 10): array
    {
        if (!$this->db->tableExists('gym_ejercicio_pr')) {
            return [];
        }

        $limit = max(1, min(50, $limit));

        return $this->db->table('gym_ejercicio_pr')
            ->where('usuario_id', $usuarioId)
            ->orderBy('logrado_en', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }
}
