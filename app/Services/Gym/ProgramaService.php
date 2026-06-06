<?php

namespace App\Services\Gym;

use App\Models\Gym\ProgramaUsuario;

class ProgramaService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * @return array{ok: bool, rutina_id?: int, error?: string}
     */
    public function duplicarRutina(int $rutinaId, int $empresaId, int $usuarioId, ?string $nombre = null): array
    {
        $rutina = $this->db->table('gym_rutina')
            ->where('id', $rutinaId)
            ->where('empresa_id', $empresaId)
            ->get(1)
            ->getRow();

        if (!$rutina) {
            return ['ok' => false, 'error' => 'Rutina no encontrada'];
        }

        $nombreFinal = $this->nombreCopia($nombre, (string) $rutina->nombre);

        $this->db->transStart();

        $this->db->table('gym_rutina')->insert([
            'empresa_id'            => $empresaId,
            'creado_por_usuario_id' => $usuarioId,
            'nombre'                => $nombreFinal,
            'descripcion'           => $rutina->descripcion,
            'activo'                => (int) ($rutina->activo ?? 1),
            'fcreacion'             => date('Y-m-d H:i:s'),
        ]);
        $nuevaRutinaId = (int) $this->db->insertID();

        $this->copiarEjerciciosRutina($rutinaId, $nuevaRutinaId);

        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return ['ok' => false, 'error' => 'Error al duplicar la rutina'];
        }

        return ['ok' => true, 'rutina_id' => $nuevaRutinaId];
    }

    /**
     * Copia programa y duplica cada rutina vinculada (independiente del original).
     *
     * @return array{ok: bool, programa_id?: int, error?: string}
     */
    public function duplicarPrograma(int $programaId, int $empresaId, int $usuarioId, ?string $nombre = null): array
    {
        $programa = $this->db->table('gym_programa')
            ->where('id', $programaId)
            ->where('empresa_id', $empresaId)
            ->get(1)
            ->getRow();

        if (!$programa) {
            return ['ok' => false, 'error' => 'Programa no encontrado'];
        }

        $links = $this->db->table('gym_programa_rutina')
            ->where('programa_id', $programaId)
            ->orderBy('orden', 'ASC')
            ->get()
            ->getResult();

        foreach ($links as $link) {
            $exists = $this->db->table('gym_rutina')
                ->where('id', (int) $link->rutina_id)
                ->where('empresa_id', $empresaId)
                ->countAllResults();
            if ($exists === 0) {
                return ['ok' => false, 'error' => 'Una rutina del programa ya no existe'];
            }
        }

        $nombreFinal = $this->nombreCopia($nombre, (string) $programa->nombre);

        $this->db->transStart();

        $this->db->table('gym_programa')->insert([
            'empresa_id'            => $empresaId,
            'creado_por_usuario_id' => $usuarioId,
            'nombre'                => $nombreFinal,
            'descripcion'           => $programa->descripcion,
            'duracion_semanas'      => $programa->duracion_semanas,
            'activo'                => (int) ($programa->activo ?? 1),
            'fcreacion'             => date('Y-m-d H:i:s'),
        ]);
        $nuevoProgramaId = (int) $this->db->insertID();

        $mapRutinas = [];
        foreach ($links as $link) {
            $origRutinaId = (int) $link->rutina_id;
            if (!isset($mapRutinas[$origRutinaId])) {
                $rutina = $this->db->table('gym_rutina')
                    ->where('id', $origRutinaId)
                    ->where('empresa_id', $empresaId)
                    ->get(1)
                    ->getRow();

                $this->db->table('gym_rutina')->insert([
                    'empresa_id'            => $empresaId,
                    'creado_por_usuario_id' => $usuarioId,
                    'nombre'                => $this->nombreCopia(null, (string) $rutina->nombre),
                    'descripcion'           => $rutina->descripcion,
                    'activo'                => (int) ($rutina->activo ?? 1),
                    'fcreacion'             => date('Y-m-d H:i:s'),
                ]);
                $nuevaRutinaId = (int) $this->db->insertID();
                $this->copiarEjerciciosRutina($origRutinaId, $nuevaRutinaId);
                $mapRutinas[$origRutinaId] = $nuevaRutinaId;
            }

            $this->db->table('gym_programa_rutina')->insert([
                'programa_id' => $nuevoProgramaId,
                'rutina_id'   => $mapRutinas[$origRutinaId],
                'orden'       => (int) $link->orden,
                'dia_semana'  => $link->dia_semana,
            ]);
        }

        $this->db->transComplete();
        if ($this->db->transStatus() === false) {
            return ['ok' => false, 'error' => 'Error al duplicar el programa'];
        }

        return ['ok' => true, 'programa_id' => $nuevoProgramaId];
    }

    /**
     * Duplica programa (con rutinas) y lo asigna a un alumno.
     *
     * @return array{ok: bool, programa_id?: int, asignacion_id?: int, error?: string}
     */
    public function duplicarProgramaYAsignar(
        int $programaId,
        int $empresaId,
        int $usuarioId,
        int $alumnoUsuarioId,
        ?string $nombre = null,
        ?string $fechaInicio = null
    ): array {
        $dup = $this->duplicarPrograma($programaId, $empresaId, $usuarioId, $nombre);
        if (!$dup['ok']) {
            return $dup;
        }

        $gpu = new ProgramaUsuario();
        $nuevoProgramaId = (int) $dup['programa_id'];

        $exist = $gpu->where('programa_id', $nuevoProgramaId)->where('usuario_id', $alumnoUsuarioId)->first();
        if ($exist) {
            $gpu->update($exist->id, [
                'estado'                    => 'activa',
                'fecha_inicio'              => $fechaInicio ?: date('Y-m-d'),
                'fecha_fin'                 => null,
                'asignado_por_usuario_id'   => $usuarioId,
            ]);
            $asignacionId = (int) $exist->id;
        } else {
            $asignacionId = (int) $gpu->insert([
                'programa_id'               => $nuevoProgramaId,
                'usuario_id'                => $alumnoUsuarioId,
                'asignado_por_usuario_id'   => $usuarioId,
                'fecha_inicio'              => $fechaInicio ?: date('Y-m-d'),
                'estado'                    => 'activa',
            ]);
            if ($asignacionId <= 0) {
                return ['ok' => false, 'error' => 'Programa duplicado pero no se pudo asignar'];
            }
        }

        return [
            'ok'             => true,
            'programa_id'    => $nuevoProgramaId,
            'asignacion_id'  => $asignacionId,
        ];
    }

    private function copiarEjerciciosRutina(int $origRutinaId, int $nuevaRutinaId): void
    {
        $items = $this->db->table('gym_rutina_ejercicio')
            ->where('rutina_id', $origRutinaId)
            ->orderBy('orden', 'ASC')
            ->get()
            ->getResult();

        foreach ($items as $it) {
            $this->db->table('gym_rutina_ejercicio')->insert([
                'rutina_id'     => $nuevaRutinaId,
                'ejercicio_id'  => (int) $it->ejercicio_id,
                'orden'         => (int) $it->orden,
                'series'        => $it->series,
                'repeticiones'  => $it->repeticiones,
                'descanso_seg'  => $it->descanso_seg,
                'notas'         => $it->notas,
            ]);
        }
    }

    private function nombreCopia(?string $nombre, string $original): string
    {
        $base = trim($nombre ?? '');
        if ($base === '') {
            $base = 'Copia de ' . trim($original);
        }

        return mb_substr($base, 0, 160);
    }
}
