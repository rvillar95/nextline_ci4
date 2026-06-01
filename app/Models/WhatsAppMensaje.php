<?php

namespace App\Models;

use CodeIgniter\Model;

class WhatsAppMensaje extends Model
{
    protected $table = 'whatsapp_mensajes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'paciente_id', 'nutricionista_id', 'agenda_id', 'tipo_mensaje', 'direccion',
        'numero_destino', 'numero_origen', 'mensaje', 'mensaje_id_api', 'estado_envio', 'leido_nutricionista',
        'fecha_envio', 'fecha_entrega', 'fecha_lectura', 'error_mensaje', 'metadata'
    ];

    protected $useTimestamps = false; // Deshabilitar timestamps automáticos, manejar fcreacion manualmente
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'tipo_mensaje' => 'required|in_list[agendamiento,recordatorio,confirmacion,cancelacion,documento,otro]',
        'direccion' => 'required|in_list[enviado,recibido]',
        'numero_destino' => 'required|string|max_length[32]',
        'mensaje' => 'required|string',
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Formato consistente para guardar teléfonos en historial (+56…).
     */
    public static function normalizarNumeroAlmacen(?string $numero): string
    {
        $numero = trim((string) $numero);
        if ($numero === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $numero);
        if ($digits === '') {
            return mb_substr($numero, 0, 32);
        }

        if (strlen($digits) >= 11 && str_starts_with($digits, '56')) {
            return '+' . substr($digits, 0, 15);
        }
        if (strlen($digits) === 9) {
            return '+56' . $digits;
        }

        return '+' . substr($digits, 0, 15);
    }

    /**
     * Registrar envío de mensaje. Devuelve id insertado o false si falla validación/BD.
     *
     * @return int|false
     */
    public function registrarEnvio(array $data): int|false
    {
        $data['direccion'] = 'enviado';
        if (empty($data['estado_envio'])) {
            $data['estado_envio'] = 'pendiente';
        }
        $data['fecha_envio'] = $data['fecha_envio'] ?? date('Y-m-d H:i:s');
        $data['fcreacion'] = $data['fcreacion'] ?? date('Y-m-d H:i:s');

        if (!empty($data['numero_destino'])) {
            $data['numero_destino'] = self::normalizarNumeroAlmacen($data['numero_destino']);
        }
        if (!empty($data['numero_origen'])) {
            $origen = (string) $data['numero_origen'];
            $soloDigitos = preg_replace('/\D+/', '', $origen);
            $data['numero_origen'] = (strlen($soloDigitos) >= 9 && strlen($soloDigitos) <= 15)
                ? self::normalizarNumeroAlmacen($origen)
                : mb_substr($origen, 0, 32);
        }

        $id = $this->insert($data);
        if ($id === false) {
            log_message('error', 'WhatsAppMensaje::registrarEnvio falló: ' . json_encode($this->errors()) . ' paciente_id=' . ($data['paciente_id'] ?? ''));
        }

        return $id;
    }

    /**
     * Actualizar estado del mensaje
     */
    public function actualizarEstado($id, $estado, $fecha = null)
    {
        $data = ['estado_envio' => $estado];
        
        if ($fecha) {
            switch ($estado) {
                case 'enviado':
                    $data['fecha_envio'] = $fecha;
                    break;
                case 'entregado':
                    $data['fecha_entrega'] = $fecha;
                    break;
                case 'leido':
                    $data['fecha_lectura'] = $fecha;
                    break;
            }
        }

        return $this->update($id, $data);
    }

    /**
     * Obtener mensajes por paciente
     */
    public function getMensajesPorPaciente($pacienteId, $limit = 50)
    {
        return $this->where('paciente_id', $pacienteId)
            ->orderBy('fecha_envio', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public const MENSAJES_POR_LOTE = 30;

    /**
     * Registro antiguo sin cuerpo real (solo "Cancelación (plantilla X)" etc.).
     */
    public static function esSoloPlaceholder(?string $mensaje): bool
    {
        $mensaje = trim((string) $mensaje);
        if ($mensaje === '') {
            return true;
        }

        if (!preg_match('/\(plantilla\s+/iu', $mensaje)) {
            return (bool) preg_match('/^(Confirmación|Cancelación|Recordatorio)\s*$/iu', $mensaje);
        }

        $limpio = preg_replace('/\s*\(plantilla[^)]*\)/iu', '', $mensaje);
        $limpio = trim((string) $limpio);

        return $limpio === '' || (bool) preg_match('/^(Confirmación|Cancelación|Recordatorio)\s*$/iu', $limpio);
    }

    /**
     * Texto para mostrar en bandeja (incluye registros antiguos tipo "Recordatorio (plantilla…)").
     */
    public static function textoVisible(?string $mensaje, ?string $tipoMensaje = null, ?object $fila = null): string
    {
        $mensaje = trim((string) $mensaje);

        if ($mensaje !== '' && !self::esSoloPlaceholder($mensaje)) {
            if (!preg_match('/\(plantilla\s+/iu', $mensaje)) {
                return $mensaje;
            }
            $limpio = preg_replace('/\s*\(plantilla[^)]*\)/iu', '', $mensaje);
            $limpio = trim((string) $limpio);
            if ($limpio !== '' && !preg_match('/^(Confirmación|Cancelación|Recordatorio)\s*$/iu', $limpio)) {
                return $limpio;
            }
        }

        if ($fila !== null) {
            $model = new self();
            $reconstruido = $model->reconstruirMensajeDesdeCita($fila);
            if ($reconstruido !== '') {
                return $reconstruido;
            }
        }

        return match ($tipoMensaje) {
            'recordatorio' => '🔔 Recordatorio de cita',
            'confirmacion' => '✅ Confirmación de cita',
            'cancelacion' => '❌ Cancelación de cita',
            'agendamiento' => '📅 Mensaje de agenda',
            default => $mensaje !== '' ? preg_replace('/\s*\(plantilla[^)]*\)/iu', '', $mensaje) : '',
        };
    }

    /**
     * Reconstruye el texto enviado cuando en BD solo quedó el marcador de plantilla.
     */
    public function reconstruirMensajeDesdeCita(object $w): string
    {
        $pacienteId = (int) ($w->paciente_id ?? 0);
        if ($pacienteId <= 0) {
            return '';
        }

        $db = \Config\Database::connect();
        $builder = $db->table('detalle_agenda da')
            ->select('da.hora_inicio, COALESCE(da.fecha, a.fecha) AS fecha, p.nombre, p.apellido, u.nombre AS nutricionista_nombre, da.motivo')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('usuario u', 'u.id = COALESCE(da.usuario_id, a.usuario_id)', 'left', false)
            ->where('da.paciente_id', $pacienteId);

        $agendaId = (int) ($w->agenda_id ?? 0);
        if ($agendaId > 0) {
            $builder->where('da.agenda_id', $agendaId);
        }

        $cita = $builder->orderBy('da.id', 'DESC')->limit(1)->get()->getRow();
        if (!$cita) {
            return '';
        }

        $nombre = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
        $fecha = trim((string) ($cita->fecha ?? ''));
        $hora = !empty($cita->hora_inicio) ? date('H:i', strtotime($cita->hora_inicio)) : '';
        $nutri = trim((string) ($cita->nutricionista_nombre ?? 'Nutricionista'));

        return match ($w->tipo_mensaje ?? '') {
            'recordatorio' => "🔔 Recordatorio de Cita\n\n"
                . "Hola {$nombre},\n\n"
                . "Te recordamos tu cita con {$nutri}:\n\n"
                . "📅 Fecha: {$fecha}\n"
                . "🕐 Hora: {$hora}\n\n"
                . "¡Nos vemos pronto!",
            'confirmacion' => "¡Hola {$nombre}!\n\n"
                . "Tu cita con {$nutri} ha sido agendada:\n\n"
                . "📅 Fecha: {$fecha}\n"
                . "🕐 Hora: {$hora}\n\n"
                . "¡Te esperamos!",
            'cancelacion' => "Hola {$nombre}\n\n"
                . "Te informamos que tu cita con {$nutri} ha sido cancelada:\n\n"
                . "📅 Fecha: {$fecha}\n"
                . "🕐 Hora: {$hora}\n\n"
                . "¡Gracias por tu comprensión!",
            default => '',
        };
    }

    /**
     * Hilo paginado estilo WhatsApp: últimos N mensajes; before_id carga más antiguos.
     *
     * @return array{mensajes: array, has_more_older: bool, oldest_id: int|null}
     */
    public function getHiloPaginado(int $pacienteId, int $nutricionistaId, int $limit = self::MENSAJES_POR_LOTE, ?int $beforeId = null): array
    {
        $limit = max(5, min(50, $limit));

        $builder = $this->where('paciente_id', $pacienteId)
            ->groupStart()
                ->where('nutricionista_id', $nutricionistaId)
                ->orWhere('nutricionista_id', null)
            ->groupEnd();

        if ($beforeId !== null && $beforeId > 0) {
            $builder->where('id <', $beforeId);
        }

        $rows = $builder
            ->orderBy('id', 'DESC')
            ->limit($limit + 1)
            ->findAll();

        $hasMore = count($rows) > $limit;
        if ($hasMore) {
            array_pop($rows);
        }

        $rows = array_reverse($rows);
        $mensajes = [];
        $oldestId = null;

        foreach ($rows as $r) {
            if ($oldestId === null) {
                $oldestId = (int) $r->id;
            }
            $mensajes[] = $this->mapearMensajeHilo($r);
        }

        return [
            'mensajes' => $mensajes,
            'has_more_older' => $hasMore,
            'oldest_id' => $oldestId,
        ];
    }

    protected function mapearMensajeHilo(object $r): array
    {
        $estado = $r->estado_envio ?? '';
        if ($r->direccion === 'enviado' && in_array($estado, ['sent', 'pendiente'], true)) {
            $estado = 'enviado';
        }

        return [
            'id' => (int) $r->id,
            'direccion' => $r->direccion,
            'mensaje' => self::textoVisible($r->mensaje ?? '', $r->tipo_mensaje ?? null, $r),
            'tipo_mensaje' => $r->tipo_mensaje,
            'estado_envio' => $estado,
            'error_mensaje' => $r->error_mensaje ?? null,
            'fecha' => $r->fecha_envio ?? $r->fcreacion ?? null,
        ];
    }

    /**
     * @deprecated Usar getHiloPaginado
     */
    public function getHiloPorPaciente(int $pacienteId, int $nutricionistaId, int $limit = 200): array
    {
        return $this->getHiloPaginado($pacienteId, $nutricionistaId, min($limit, 50))['mensajes'];
    }

    /**
     * Resumen de conversaciones por paciente del nutricionista.
     */
    public function getConversacionesResumen(int $nutricionistaId): array
    {
        $db = \Config\Database::connect();
        $sql = "
            SELECT
                p.id AS paciente_id,
                CONCAT(p.nombre, ' ', p.apellido) AS nombre_completo,
                p.telefono,
                p.rut_dni,
                SUBSTRING_INDEX(
                    GROUP_CONCAT(w.tipo_mensaje ORDER BY COALESCE(w.fecha_envio, w.fcreacion) DESC SEPARATOR '|||'),
                    '|||',
                    1
                ) AS ultimo_tipo_mensaje,
                SUBSTRING_INDEX(
                    GROUP_CONCAT(w.mensaje ORDER BY COALESCE(w.fecha_envio, w.fcreacion) DESC SEPARATOR '|||'),
                    '|||',
                    1
                ) AS ultimo_mensaje,
                MAX(COALESCE(w.fecha_envio, w.fcreacion)) AS ultima_fecha,
                SUM(CASE WHEN w.direccion = 'recibido' THEN 1 ELSE 0 END) AS total_recibidos,
                SUM(CASE WHEN w.direccion = 'recibido' AND w.leido_nutricionista = 'N' THEN 1 ELSE 0 END) AS no_leidos
            FROM whatsapp_mensajes w
            INNER JOIN pacientes p ON p.id = w.paciente_id
            WHERE p.nutricionista_id = ?
              AND p.estado = 'A'
              AND (w.nutricionista_id = ? OR w.nutricionista_id IS NULL)
            GROUP BY p.id, p.nombre, p.apellido, p.telefono, p.rut_dni
            ORDER BY no_leidos DESC, ultima_fecha DESC
        ";

        $rows = $db->query($sql, [$nutricionistaId, $nutricionistaId])->getResultArray();
        foreach ($rows as &$row) {
            $preview = self::textoVisible(
                $row['ultimo_mensaje'] ?? '',
                $row['ultimo_tipo_mensaje'] ?? null,
                null
            );
            if ($preview === '') {
                $preview = trim((string) ($row['ultimo_mensaje'] ?? ''));
            }
            $row['ultimo_mensaje'] = $preview;
            unset($row['ultimo_tipo_mensaje']);
            if (strlen($row['ultimo_mensaje']) > 80) {
                $row['ultimo_mensaje'] = mb_substr($row['ultimo_mensaje'], 0, 77) . '…';
            }
        }

        return $rows;
    }

    /**
     * Marcar como leídos los mensajes recibidos del paciente (al abrir la conversación).
     */
    public function marcarConversacionLeida(int $pacienteId, int $nutricionistaId): void
    {
        // CI4 Model::update($id, $data): no pasar solo el array (se interpreta como id).
        \Config\Database::connect()->table($this->table)
            ->where('paciente_id', $pacienteId)
            ->where('direccion', 'recibido')
            ->where('leido_nutricionista', 'N')
            ->groupStart()
                ->where('nutricionista_id', $nutricionistaId)
                ->orWhere('nutricionista_id', null)
            ->groupEnd()
            ->update(['leido_nutricionista' => 'S']);
    }

    /**
     * Mensajes nuevos después de un id (sincronización en tiempo real).
     */
    public function getMensajesDespuesDe(int $pacienteId, int $nutricionistaId, int $afterId): array
    {
        if ($afterId <= 0) {
            return [];
        }

        $rows = $this->where('paciente_id', $pacienteId)
            ->where('id >', $afterId)
            ->groupStart()
                ->where('nutricionista_id', $nutricionistaId)
                ->orWhere('nutricionista_id', null)
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->limit(50)
            ->findAll();

        $mensajes = [];
        foreach ($rows as $r) {
            $mensajes[] = $this->mapearMensajeHilo($r);
        }

        return $mensajes;
    }

    public function getUltimoIdHilo(int $pacienteId, int $nutricionistaId): int
    {
        $row = $this->where('paciente_id', $pacienteId)
            ->groupStart()
                ->where('nutricionista_id', $nutricionistaId)
                ->orWhere('nutricionista_id', null)
            ->groupEnd()
            ->orderBy('id', 'DESC')
            ->first();

        return (int) ($row->id ?? 0);
    }

    /**
     * Ventana de 24 h de WhatsApp: hubo mensaje recibido del paciente en las últimas 24 horas.
     */
    public function tieneVentana24Horas(int $pacienteId): bool
    {
        $desde = date('Y-m-d H:i:s', strtotime('-24 hours'));

        return $this->where('paciente_id', $pacienteId)
            ->where('direccion', 'recibido')
            ->groupStart()
                ->where('fecha_envio >=', $desde)
                ->orWhere('fcreacion >=', $desde)
            ->groupEnd()
            ->countAllResults() > 0;
    }

    /**
     * Registrar mensaje entrante con paciente y nutricionista.
     */
    public function registrarRecibido(array $data): int|false
    {
        if (!empty($data['mensaje_id_api'])) {
            $duplicado = $this->where('mensaje_id_api', $data['mensaje_id_api'])->first();
            if ($duplicado) {
                return (int) $duplicado->id;
            }
        }

        $data['direccion'] = 'recibido';
        $data['estado_envio'] = 'recibido';
        $data['leido_nutricionista'] = 'N';
        $data['tipo_mensaje'] = $data['tipo_mensaje'] ?? 'otro';
        $data['fecha_envio'] = $data['fecha_envio'] ?? date('Y-m-d H:i:s');
        $data['fcreacion'] = $data['fcreacion'] ?? date('Y-m-d H:i:s');

        if (!empty($data['numero_origen'])) {
            $data['numero_origen'] = self::normalizarNumeroAlmacen($data['numero_origen']);
        }

        if (empty($data['numero_destino'])) {
            $data['numero_destino'] = $data['numero_origen'] ?? 'nutrinext';
        }
        $destino = (string) $data['numero_destino'];
        if (!str_starts_with($destino, 'wa:') && !str_starts_with($destino, 'nutrinext')) {
            $data['numero_destino'] = self::normalizarNumeroAlmacen($destino);
        }

        $id = $this->insert($data);
        if ($id === false) {
            log_message('error', 'WhatsAppMensaje::registrarRecibido falló: ' . json_encode($this->errors()));
        }

        return $id;
    }

    /**
     * Obtener mensajes fallidos
     */
    public function getMensajesFallidos($nutricionistaId = null)
    {
        $builder = $this->where('estado_envio', 'error');

        if ($nutricionistaId) {
            $builder->where('nutricionista_id', $nutricionistaId);
        }

        return $builder->orderBy('fcreacion', 'DESC')
            ->findAll();
    }
}
