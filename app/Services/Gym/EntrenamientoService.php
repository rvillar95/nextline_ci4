<?php

namespace App\Services\Gym;

class EntrenamientoService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function sesionEnCurso(int $usuarioId): ?object
    {
        return $this->db->table('gym_entrenamiento')
            ->where('usuario_id', $usuarioId)
            ->where('estado', 'en_curso')
            ->orderBy('id', 'DESC')
            ->get(1)
            ->getRow();
    }

    /**
     * Valida que la rutina pertenece a un programa activo del alumno.
     *
     * @return object|null fila con rutina + programa + asignación
     */
    public function rutinaAsignada(int $usuarioId, int $empresaId, int $rutinaId): ?object
    {
        return $this->db->table('gym_programa_rutina pr')
            ->select('pr.rutina_id, pr.programa_id, pr.dia_semana, pr.orden, r.nombre as rutina_nombre, r.descripcion as rutina_descripcion, p.nombre as programa_nombre, gpu.id as programa_usuario_id, gpu.estado as asignacion_estado')
            ->join('gym_rutina r', 'r.id = pr.rutina_id')
            ->join('gym_programa p', 'p.id = pr.programa_id')
            ->join('gym_programa_usuario gpu', 'gpu.programa_id = p.id')
            ->where('gpu.usuario_id', $usuarioId)
            ->where('gpu.estado', 'activa')
            ->where('p.empresa_id', $empresaId)
            ->where('pr.rutina_id', $rutinaId)
            ->where('r.activo', 1)
            ->get(1)
            ->getRow();
    }

    public function rutinaDelDia(int $usuarioId, int $empresaId): ?object
    {
        $dia = (int) date('N');
        if ($dia === 7) {
            $dia = 7;
        }

        $row = $this->db->table('gym_programa_rutina pr')
            ->select('pr.rutina_id, pr.programa_id, pr.dia_semana, r.nombre as rutina_nombre, p.nombre as programa_nombre, gpu.id as programa_usuario_id')
            ->join('gym_rutina r', 'r.id = pr.rutina_id')
            ->join('gym_programa p', 'p.id = pr.programa_id')
            ->join('gym_programa_usuario gpu', 'gpu.programa_id = p.id')
            ->where('gpu.usuario_id', $usuarioId)
            ->where('gpu.estado', 'activa')
            ->where('p.empresa_id', $empresaId)
            ->where('pr.dia_semana', $dia)
            ->orderBy('pr.orden', 'ASC')
            ->get(1)
            ->getRow();

        if ($row) {
            return $row;
        }

        return $this->db->table('gym_programa_rutina pr')
            ->select('pr.rutina_id, pr.programa_id, pr.dia_semana, r.nombre as rutina_nombre, p.nombre as programa_nombre, gpu.id as programa_usuario_id')
            ->join('gym_rutina r', 'r.id = pr.rutina_id')
            ->join('gym_programa p', 'p.id = pr.programa_id')
            ->join('gym_programa_usuario gpu', 'gpu.programa_id = p.id')
            ->where('gpu.usuario_id', $usuarioId)
            ->where('gpu.estado', 'activa')
            ->where('p.empresa_id', $empresaId)
            ->orderBy('pr.orden', 'ASC')
            ->get(1)
            ->getRow();
    }

    /**
     * @return array{ok: bool, entrenamiento_id?: int, error?: string, conflicto_id?: int}
     */
    public function iniciar(int $usuarioId, int $empresaId, int $rutinaId, bool $abandonarAnterior = false): array
    {
        $asign = $this->rutinaAsignada($usuarioId, $empresaId, $rutinaId);
        if (!$asign) {
            return ['ok' => false, 'error' => 'Esta rutina no está en tus programas asignados.'];
        }

        $enCurso = $this->sesionEnCurso($usuarioId);
        if ($enCurso) {
            if ((int) $enCurso->rutina_id === $rutinaId) {
                return ['ok' => true, 'entrenamiento_id' => (int) $enCurso->id, 'reanudado' => true];
            }
            if (!$abandonarAnterior) {
                return ['ok' => false, 'error' => 'conflicto', 'conflicto_id' => (int) $enCurso->id];
            }
            $this->abandonar((int) $enCurso->id, $usuarioId);
        }

        $ahora = date('Y-m-d H:i:s');
        $this->db->table('gym_entrenamiento')->insert([
            'usuario_id'           => $usuarioId,
            'empresa_id'           => $empresaId,
            'rutina_id'            => $rutinaId,
            'programa_id'          => (int) $asign->programa_id,
            'programa_usuario_id'  => (int) $asign->programa_usuario_id,
            'estado'               => 'en_curso',
            'iniciado_en'          => $ahora,
            'fcreacion'            => $ahora,
        ]);
        $entrenamientoId = (int) $this->db->insertID();

        $items = $this->db->table('gym_rutina_ejercicio re')
            ->select('re.*, e.nombre as ejercicio_nombre, e.video_url')
            ->join('gym_ejercicio e', 'e.id = re.ejercicio_id')
            ->where('re.rutina_id', $rutinaId)
            ->orderBy('re.orden', 'ASC')
            ->get()
            ->getResult();

        foreach ($items as $it) {
            $seriesPlan = max(1, (int) ($it->series ?? 1));
            $notasEj = trim((string) ($it->notas ?? ''));
            $this->db->table('gym_entrenamiento_ejercicio')->insert([
                'entrenamiento_id'    => $entrenamientoId,
                'ejercicio_id'        => (int) $it->ejercicio_id,
                'orden'               => (int) $it->orden,
                'nombre_ejercicio'    => (string) ($it->ejercicio_nombre ?? 'Ejercicio'),
                'series_planificadas' => $seriesPlan,
                'reps_planificadas'   => $it->repeticiones ?? null,
                'descanso_seg'        => $it->descanso_seg ?? null,
                'notas_plan'          => $notasEj !== '' ? $notasEj : null,
                'notas_coach'         => $notasEj !== '' ? $notasEj : null,
                'video_url'           => $it->video_url ?? null,
            ]);
            $eeId = (int) $this->db->insertID();
            for ($s = 1; $s <= $seriesPlan; $s++) {
                $this->db->table('gym_entrenamiento_serie')->insert([
                    'entrenamiento_ejercicio_id' => $eeId,
                    'numero_serie'               => $s,
                    'completada'                 => 'N',
                    'fcreacion'                  => $ahora,
                ]);
            }
        }

        return ['ok' => true, 'entrenamiento_id' => $entrenamientoId];
    }

    public function abandonar(int $entrenamientoId, int $usuarioId): bool
    {
        return $this->db->table('gym_entrenamiento')
            ->where('id', $entrenamientoId)
            ->where('usuario_id', $usuarioId)
            ->where('estado', 'en_curso')
            ->update([
                'estado'        => 'abandonado',
                'finalizado_en' => date('Y-m-d H:i:s'),
            ]);
    }

    public function finalizar(int $entrenamientoId, int $usuarioId, ?string $notas = null): bool
    {
        return $this->db->table('gym_entrenamiento')
            ->where('id', $entrenamientoId)
            ->where('usuario_id', $usuarioId)
            ->where('estado', 'en_curso')
            ->update([
                'estado'        => 'completado',
                'finalizado_en' => date('Y-m-d H:i:s'),
                'notas'         => $notas,
            ]);
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public function guardarSerie(int $serieId, int $usuarioId, array $data): array
    {
        $serie = $this->db->table('gym_entrenamiento_serie ges')
            ->select('ges.*, gee.entrenamiento_id, gee.ejercicio_id, gee.nombre_ejercicio, ge.usuario_id, ge.estado')
            ->join('gym_entrenamiento_ejercicio gee', 'gee.id = ges.entrenamiento_ejercicio_id')
            ->join('gym_entrenamiento ge', 'ge.id = gee.entrenamiento_id')
            ->where('ges.id', $serieId)
            ->get(1)
            ->getRow();

        if (!$serie || (int) $serie->usuario_id !== $usuarioId) {
            return ['ok' => false, 'error' => 'Serie no encontrada'];
        }
        if ($serie->estado !== 'en_curso') {
            return ['ok' => false, 'error' => 'La sesión ya finalizó'];
        }

        $update = ['factualizacion' => date('Y-m-d H:i:s')];

        $pesoFinal = $serie->peso_kg !== null && $serie->peso_kg !== '' ? (float) $serie->peso_kg : null;
        $pesoAnteriorSerie = $pesoFinal;
        $repsFinal = $serie->repeticiones !== null ? (int) $serie->repeticiones : null;

        if (array_key_exists('peso_kg', $data)) {
            $pesoFinal = $data['peso_kg'] !== '' && $data['peso_kg'] !== null ? (float) $data['peso_kg'] : null;
            $update['peso_kg'] = $pesoFinal;
        }
        if (array_key_exists('repeticiones', $data)) {
            $repsFinal = $data['repeticiones'] !== '' && $data['repeticiones'] !== null ? (int) $data['repeticiones'] : null;
            $update['repeticiones'] = $repsFinal;
        }

        $marcada = null;
        if (array_key_exists('completada', $data)) {
            $marcada = ($data['completada'] === true || $data['completada'] === 'S' || $data['completada'] === '1');
        }

        // Serie registrada si tiene peso/reps o el alumno marcó el check
        if ($marcada === true || $pesoFinal !== null || $repsFinal !== null) {
            $update['completada'] = 'S';
        } elseif ($marcada === false && $pesoFinal === null && $repsFinal === null) {
            $update['completada'] = 'N';
        }

        if (array_key_exists('notas', $data)) {
            $update['notas'] = $data['notas'];
        }

        $this->db->table('gym_entrenamiento_serie')->where('id', $serieId)->update($update);

        $result = ['ok' => true, 'pr' => false];

        if ($pesoFinal !== null && $pesoFinal > 0
            && ($pesoAnteriorSerie === null || $pesoFinal > $pesoAnteriorSerie)) {
            $maxHistorico = $this->maxPesoEjercicio($usuarioId, (int) $serie->ejercicio_id, $serieId);
            if ($maxHistorico === null || $pesoFinal > $maxHistorico) {
                $this->registrarPr(
                    $usuarioId,
                    (int) $serie->ejercicio_id,
                    (string) ($serie->nombre_ejercicio ?? ''),
                    $pesoFinal,
                    $repsFinal,
                    $serieId
                );
                $result['pr'] = true;
                $result['peso_nuevo'] = $pesoFinal;
                $result['peso_anterior'] = $maxHistorico;
                $result['ejercicio_nombre'] = $serie->nombre_ejercicio ?? '';
                $result['repeticiones'] = $repsFinal;
            }
        }

        return $result;
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public function guardarSensacionEjercicio(int $entrenamientoEjercicioId, int $usuarioId, ?string $sensacion): array
    {
        if (!$this->db->fieldExists('sensacion', 'gym_entrenamiento_ejercicio')) {
            return ['ok' => false, 'error' => 'Función no disponible'];
        }

        $row = $this->db->table('gym_entrenamiento_ejercicio gee')
            ->select('gee.id, ge.usuario_id, ge.estado')
            ->join('gym_entrenamiento ge', 'ge.id = gee.entrenamiento_id')
            ->where('gee.id', $entrenamientoEjercicioId)
            ->get(1)
            ->getRow();

        if (!$row || (int) $row->usuario_id !== $usuarioId) {
            return ['ok' => false, 'error' => 'Ejercicio no encontrado'];
        }
        if ($row->estado !== 'en_curso') {
            return ['ok' => false, 'error' => 'La sesión ya finalizó'];
        }

        $txt = trim((string) $sensacion);
        if (mb_strlen($txt) > 500) {
            $txt = mb_substr($txt, 0, 500);
        }

        $this->db->table('gym_entrenamiento_ejercicio')
            ->where('id', $entrenamientoEjercicioId)
            ->update(['sensacion' => $txt !== '' ? $txt : null]);

        return ['ok' => true];
    }

    /** Mayor peso registrado para un ejercicio (incluye sesión en curso). */
    public function maxPesoEjercicio(int $usuarioId, int $ejercicioId, ?int $excluirSerieId = null): ?float
    {
        $b = $this->db->table('gym_entrenamiento_serie ges')
            ->selectMax('ges.peso_kg', 'max_peso')
            ->join('gym_entrenamiento_ejercicio gee', 'gee.id = ges.entrenamiento_ejercicio_id')
            ->join('gym_entrenamiento ge', 'ge.id = gee.entrenamiento_id')
            ->where('ge.usuario_id', $usuarioId)
            ->where('gee.ejercicio_id', $ejercicioId)
            ->where('ges.peso_kg IS NOT NULL', null, false)
            ->where('ges.peso_kg >', 0);

        if ($excluirSerieId) {
            $b->where('ges.id !=', $excluirSerieId);
        }

        $row = $b->get()->getRow();

        return $row && $row->max_peso !== null ? (float) $row->max_peso : null;
    }

    protected function registrarPr(
        int $usuarioId,
        int $ejercicioId,
        string $nombreEjercicio,
        float $pesoKg,
        ?int $repeticiones,
        int $serieId
    ): void {
        if (!$this->db->tableExists('gym_ejercicio_pr')) {
            return;
        }

        $this->db->table('gym_ejercicio_pr')->insert([
            'usuario_id'             => $usuarioId,
            'ejercicio_id'           => $ejercicioId,
            'nombre_ejercicio'       => $nombreEjercicio !== '' ? $nombreEjercicio : null,
            'peso_kg'                => $pesoKg,
            'repeticiones'           => $repeticiones,
            'entrenamiento_serie_id' => $serieId,
            'logrado_en'             => date('Y-m-d H:i:s'),
        ]);
    }

    /** Serie con datos guardados (peso, reps o marcada completada). */
    public static function serieTieneRegistro(object $serie): bool
    {
        if (($serie->completada ?? 'N') === 'S') {
            return true;
        }

        return $serie->peso_kg !== null && $serie->peso_kg !== ''
            || $serie->repeticiones !== null && $serie->repeticiones !== '';
    }

    public function obtenerSesionCompleta(int $entrenamientoId, int $usuarioId): ?array
    {
        $ent = $this->db->table('gym_entrenamiento ge')
            ->select('ge.*, r.nombre as rutina_nombre, p.nombre as programa_nombre')
            ->join('gym_rutina r', 'r.id = ge.rutina_id', 'left')
            ->join('gym_programa p', 'p.id = ge.programa_id', 'left')
            ->where('ge.id', $entrenamientoId)
            ->where('ge.usuario_id', $usuarioId)
            ->get(1)
            ->getRow();

        if (!$ent) {
            return null;
        }

        $ejercicios = $this->db->table('gym_entrenamiento_ejercicio gee')
            ->where('entrenamiento_id', $entrenamientoId)
            ->orderBy('orden', 'ASC')
            ->get()
            ->getResult();

        $lista = [];
        foreach ($ejercicios as $ej) {
            $series = $this->db->table('gym_entrenamiento_serie')
                ->where('entrenamiento_ejercicio_id', (int) $ej->id)
                ->orderBy('numero_serie', 'ASC')
                ->get()
                ->getResult();
            $lista[] = ['ejercicio' => $ej, 'series' => $series];
        }

        return ['entrenamiento' => $ent, 'ejercicios' => $lista];
    }

    /** Notas del coach para la asignación activa (programa → alumno). */
    public function notasCoachPrograma(?int $programaUsuarioId): ?string
    {
        if (!$programaUsuarioId || $programaUsuarioId <= 0) {
            return null;
        }
        if (!$this->db->fieldExists('notas_coach', 'gym_programa_usuario')) {
            return null;
        }

        $row = $this->db->table('gym_programa_usuario')
            ->select('notas_coach')
            ->where('id', $programaUsuarioId)
            ->get(1)
            ->getRow();

        $txt = trim((string) ($row->notas_coach ?? ''));

        return $txt !== '' ? $txt : null;
    }

    public function historialAlumno(int $usuarioId, int $limit = 30): array
    {
        return $this->db->table('gym_entrenamiento ge')
            ->select('ge.*, r.nombre as rutina_nombre')
            ->join('gym_rutina r', 'r.id = ge.rutina_id', 'left')
            ->where('ge.usuario_id', $usuarioId)
            ->whereIn('ge.estado', ['completado', 'abandonado'])
            ->orderBy('ge.iniciado_en', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    public function historialCoach(int $alumnoUsuarioId, int $empresaId, int $limit = 50): array
    {
        return $this->db->table('gym_entrenamiento ge')
            ->select('ge.*, r.nombre as rutina_nombre')
            ->join('gym_rutina r', 'r.id = ge.rutina_id', 'left')
            ->join('usuario u', 'u.id = ge.usuario_id')
            ->where('ge.usuario_id', $alumnoUsuarioId)
            ->where('ge.empresa_id', $empresaId)
            ->orderBy('ge.iniciado_en', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }

    /**
     * Adherencia últimos 7 días: sesiones completadas vs días con rutina planificada.
     */
    public function adherenciaSemanal(int $usuarioId, int $empresaId): array
    {
        $desde = date('Y-m-d', strtotime('-6 days'));
        $completadas = (int) $this->db->table('gym_entrenamiento')
            ->where('usuario_id', $usuarioId)
            ->where('estado', 'completado')
            ->where('DATE(finalizado_en) >=', $desde)
            ->countAllResults();

        $asignaciones = $this->db->table('gym_programa_usuario gpu')
            ->join('gym_programa p', 'p.id = gpu.programa_id')
            ->where('gpu.usuario_id', $usuarioId)
            ->where('gpu.estado', 'activa')
            ->where('p.empresa_id', $empresaId)
            ->countAllResults();

        $planificadas = max(1, $asignaciones * 3);

        return [
            'completadas'  => $completadas,
            'planificadas' => $planificadas,
            'porcentaje'   => min(100, (int) round(($completadas / $planificadas) * 100)),
        ];
    }

    /**
     * Calendario día a día (heatmap): entrenó sí/no.
     *
     * @return list<array{fecha: string, label: string, entreno: bool}>
     */
    public function adherenciaDetalle(int $usuarioId, int $empresaId, int $dias = 28): array
    {
        $dias = max(7, min(90, $dias));
        $hasta = date('Y-m-d');
        $desde = date('Y-m-d', strtotime('-' . ($dias - 1) . ' days'));

        $rows = $this->db->table('gym_entrenamiento')
            ->select('DATE(COALESCE(finalizado_en, iniciado_en)) AS d', false)
            ->where('usuario_id', $usuarioId)
            ->where('empresa_id', $empresaId)
            ->where('estado', 'completado')
            ->where('DATE(COALESCE(finalizado_en, iniciado_en)) >=', $desde)
            ->where('DATE(COALESCE(finalizado_en, iniciado_en)) <=', $hasta)
            ->groupBy('d')
            ->get()
            ->getResult();

        $set = [];
        foreach ($rows as $r) {
            $set[(string) $r->d] = true;
        }

        $out = [];
        $cursor = $desde;
        while ($cursor <= $hasta) {
            $out[] = [
                'fecha'   => $cursor,
                'label'   => date('d/m', strtotime($cursor)),
                'entreno' => isset($set[$cursor]),
            ];
            $cursor = date('Y-m-d', strtotime($cursor . ' +1 day'));
        }

        return $out;
    }

    /** Adherencia % últimos N días (sesiones vs meta ~3/semana). */
    public function adherenciaPorcentaje(int $usuarioId, int $empresaId, int $dias = 28): int
    {
        $dias = max(7, min(90, $dias));
        $desde = date('Y-m-d', strtotime('-' . ($dias - 1) . ' days'));

        $completadas = (int) $this->db->table('gym_entrenamiento')
            ->where('usuario_id', $usuarioId)
            ->where('empresa_id', $empresaId)
            ->where('estado', 'completado')
            ->where('DATE(COALESCE(finalizado_en, iniciado_en)) >=', $desde)
            ->countAllResults();

        $semanas = max(1, (int) ceil($dias / 7));
        $meta = $semanas * 3;

        return min(100, (int) round(($completadas / $meta) * 100));
    }

    public function ultimoEntrenamientoEn(int $usuarioId, int $empresaId): ?string
    {
        $row = $this->db->table('gym_entrenamiento')
            ->select('COALESCE(finalizado_en, iniciado_en) AS t', false)
            ->where('usuario_id', $usuarioId)
            ->where('empresa_id', $empresaId)
            ->where('estado', 'completado')
            ->orderBy('t', 'DESC')
            ->get(1)
            ->getRow();

        return $row ? (string) $row->t : null;
    }

    /**
     * Alumnos activos sin sesión completada en los últimos N días.
     *
     * @return list<object>
     */
    public function alumnosSinEntrenar(int $empresaId, int $perfilAlumnoId, int $dias = 7): array
    {
        $desde = date('Y-m-d', strtotime('-' . max(1, $dias) . ' days'));

        $alumnos = $this->db->table('usuario u')
            ->select('u.id, u.nombre, u.apellido, u.correo')
            ->where('u.empresa_id', $empresaId)
            ->where('u.perfil_id', $perfilAlumnoId)
            ->where('u.estado', 'A')
            ->get()
            ->getResult();

        if ($alumnos === []) {
            return [];
        }

        $ids = array_map(static fn ($a) => (int) $a->id, $alumnos);
        $activos = $this->db->table('gym_entrenamiento')
            ->select('usuario_id')
            ->whereIn('usuario_id', $ids)
            ->where('empresa_id', $empresaId)
            ->where('estado', 'completado')
            ->where('DATE(COALESCE(finalizado_en, iniciado_en)) >=', $desde)
            ->groupBy('usuario_id')
            ->get()
            ->getResult();

        $conEntreno = [];
        foreach ($activos as $a) {
            $conEntreno[(int) $a->usuario_id] = true;
        }

        $out = [];
        foreach ($alumnos as $a) {
            if (!isset($conEntreno[(int) $a->id])) {
                $a->ultimo_entreno = $this->ultimoEntrenamientoEn((int) $a->id, $empresaId);
                $out[] = $a;
            }
        }

        return $out;
    }

    public function ultimoPesoEjercicio(int $usuarioId, int $ejercicioId, ?int $excluirEntrenamientoId = null): ?float
    {
        $b = $this->db->table('gym_entrenamiento_serie ges')
            ->select('ges.peso_kg')
            ->join('gym_entrenamiento_ejercicio gee', 'gee.id = ges.entrenamiento_ejercicio_id')
            ->join('gym_entrenamiento ge', 'ge.id = gee.entrenamiento_id')
            ->where('ge.usuario_id', $usuarioId)
            ->where('gee.ejercicio_id', $ejercicioId)
            ->where('ges.completada', 'S')
            ->where('ges.peso_kg IS NOT NULL', null, false)
            ->orderBy('ge.iniciado_en', 'DESC')
            ->orderBy('ges.numero_serie', 'DESC');

        if ($excluirEntrenamientoId) {
            $b->where('ge.id !=', $excluirEntrenamientoId);
        }

        $row = $b->get(1)->getRow();

        return $row && $row->peso_kg !== null ? (float) $row->peso_kg : null;
    }

    /** Sesión validada para el coach (misma empresa). */
    public function obtenerSesionCoach(int $entrenamientoId, int $alumnoUsuarioId, int $empresaId): ?array
    {
        $data = $this->obtenerSesionCompleta($entrenamientoId, $alumnoUsuarioId);
        if (!$data) {
            return null;
        }
        if ((int) ($data['entrenamiento']->empresa_id ?? 0) !== $empresaId) {
            return null;
        }

        return $data;
    }

    /** ID de la sesión completada anterior de la misma rutina. */
    public function sesionAnteriorMismaRutina(int $entrenamientoId, int $alumnoUsuarioId, int $empresaId): ?int
    {
        $curr = $this->db->table('gym_entrenamiento')
            ->where('id', $entrenamientoId)
            ->where('usuario_id', $alumnoUsuarioId)
            ->where('empresa_id', $empresaId)
            ->get(1)
            ->getRow();

        if (!$curr) {
            return null;
        }

        $prev = $this->db->table('gym_entrenamiento')
            ->where('usuario_id', $alumnoUsuarioId)
            ->where('empresa_id', $empresaId)
            ->where('rutina_id', (int) $curr->rutina_id)
            ->where('estado', 'completado')
            ->where('id !=', $entrenamientoId)
            ->where('iniciado_en <', $curr->iniciado_en)
            ->orderBy('iniciado_en', 'DESC')
            ->get(1)
            ->getRow();

        return $prev ? (int) $prev->id : null;
    }

    /**
     * Resumen por ejercicio para comparación.
     *
     * @return array{nombre: string, ejercicio_id: int, series: list<array>, mejor_peso: ?float, mejor_reps: ?int, volumen: int}
     */
    public function resumenEjercicioSesion(object $ej, array $series): array
    {
        $hechas = array_values(array_filter($series, [self::class, 'serieTieneRegistro']));
        $bestPeso = null;
        $bestReps = null;
        $volumen = 0;
        $lineas = [];

        foreach ($hechas as $s) {
            $peso = $s->peso_kg !== null && $s->peso_kg !== '' ? (float) $s->peso_kg : null;
            $reps = $s->repeticiones !== null && $s->repeticiones !== '' ? (int) $s->repeticiones : null;
            $lineas[] = [
                'numero' => (int) $s->numero_serie,
                'peso'   => $peso,
                'reps'   => $reps,
            ];
            if ($peso !== null && ($bestPeso === null || $peso > $bestPeso)) {
                $bestPeso = $peso;
                $bestReps = $reps;
            }
            if ($peso !== null && $reps !== null && $peso > 0 && $reps > 0) {
                $volumen += (int) round($peso * $reps);
            }
        }

        return [
            'nombre'       => (string) ($ej->nombre_ejercicio ?? 'Ejercicio'),
            'ejercicio_id' => (int) ($ej->ejercicio_id ?? 0),
            'series'       => $lineas,
            'mejor_peso'   => $bestPeso,
            'mejor_reps'   => $bestReps,
            'volumen'      => $volumen,
        ];
    }

    /**
     * Compara dos sesiones del alumno (idealmente misma rutina).
     *
     * @return array{ok: bool, error?: string, misma_rutina?: bool, rutina_nombre?: string, sesion_a?: object, sesion_b?: object, filas?: list<array>}
     */
    public function compararSesiones(int $idA, int $idB, int $alumnoUsuarioId, int $empresaId): array
    {
        if ($idA === $idB) {
            return ['ok' => false, 'error' => 'Selecciona dos sesiones distintas.'];
        }

        $sa = $this->obtenerSesionCoach($idA, $alumnoUsuarioId, $empresaId);
        $sb = $this->obtenerSesionCoach($idB, $alumnoUsuarioId, $empresaId);
        if (!$sa || !$sb) {
            return ['ok' => false, 'error' => 'Una o ambas sesiones no existen.'];
        }

        $entA = $sa['entrenamiento'];
        $entB = $sb['entrenamiento'];
        $mismaRutina = (int) ($entA->rutina_id ?? 0) === (int) ($entB->rutina_id ?? 0);

        $mapA = [];
        foreach ($sa['ejercicios'] as $bloque) {
            $res = $this->resumenEjercicioSesion($bloque['ejercicio'], $bloque['series']);
            $mapA[$res['ejercicio_id']] = $res;
        }

        $mapB = [];
        foreach ($sb['ejercicios'] as $bloque) {
            $res = $this->resumenEjercicioSesion($bloque['ejercicio'], $bloque['series']);
            $mapB[$res['ejercicio_id']] = $res;
        }

        $ids = array_unique(array_merge(array_keys($mapA), array_keys($mapB)));
        $filas = [];
        foreach ($ids as $ejId) {
            $a = $mapA[$ejId] ?? null;
            $b = $mapB[$ejId] ?? null;
            $nombre = $a['nombre'] ?? $b['nombre'] ?? 'Ejercicio';
            $deltaPeso = null;
            $deltaVol = null;
            if ($a && $b && $a['mejor_peso'] !== null && $b['mejor_peso'] !== null) {
                $deltaPeso = round($b['mejor_peso'] - $a['mejor_peso'], 1);
            }
            if ($a && $b) {
                $deltaVol = $b['volumen'] - $a['volumen'];
            }
            $filas[] = [
                'ejercicio_id' => (int) $ejId,
                'nombre'       => $nombre,
                'a'            => $a,
                'b'            => $b,
                'delta_peso'   => $deltaPeso,
                'delta_vol'    => $deltaVol,
            ];
        }

        usort($filas, static fn ($x, $y) => strcmp($x['nombre'], $y['nombre']));

        return [
            'ok'            => true,
            'misma_rutina'  => $mismaRutina,
            'rutina_nombre' => (string) ($entA->rutina_nombre ?? $entB->rutina_nombre ?? ''),
            'sesion_a'      => $entA,
            'sesion_b'      => $entB,
            'filas'         => $filas,
        ];
    }
}
