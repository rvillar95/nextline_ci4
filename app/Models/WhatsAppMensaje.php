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
        'numero_destino', 'numero_origen', 'mensaje', 'mensaje_id_api', 'estado_envio',
        'fecha_envio', 'fecha_entrega', 'fecha_lectura', 'error_mensaje', 'metadata'
    ];

    protected $useTimestamps = false; // Deshabilitar timestamps automáticos, manejar fcreacion manualmente
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'tipo_mensaje' => 'required|in_list[agendamiento,recordatorio,confirmacion,cancelacion,documento,otro]',
        'direccion' => 'required|in_list[enviado,recibido]',
        'numero_destino' => 'required|string|max_length[20]',
        'mensaje' => 'required|string'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Registrar envío de mensaje
     */
    public function registrarEnvio($data)
    {
        $data['direccion'] = 'enviado';
        $data['estado_envio'] = 'pendiente';
        $data['fecha_envio'] = date('Y-m-d H:i:s');
        $data['fcreacion'] = date('Y-m-d H:i:s'); // Agregar manualmente el timestamp de creación
        
        return $this->insert($data);
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

    /**
     * Hilo cronológico para la bandeja de mensajes (solo pacientes del nutricionista).
     */
    public function getHiloPorPaciente(int $pacienteId, int $nutricionistaId, int $limit = 200): array
    {
        $rows = $this->where('paciente_id', $pacienteId)
            ->groupStart()
                ->where('nutricionista_id', $nutricionistaId)
                ->orWhere('nutricionista_id', null)
            ->groupEnd()
            ->orderBy('fecha_envio', 'ASC')
            ->orderBy('fcreacion', 'ASC')
            ->limit($limit)
            ->findAll();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => (int) $r->id,
                'direccion' => $r->direccion,
                'mensaje' => $r->mensaje,
                'tipo_mensaje' => $r->tipo_mensaje,
                'estado_envio' => $r->estado_envio,
                'fecha' => $r->fecha_envio ?? $r->fcreacion ?? null,
            ];
        }

        return $out;
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
                    GROUP_CONCAT(w.mensaje ORDER BY COALESCE(w.fecha_envio, w.fcreacion) DESC SEPARATOR '|||'),
                    '|||',
                    1
                ) AS ultimo_mensaje,
                MAX(COALESCE(w.fecha_envio, w.fcreacion)) AS ultima_fecha,
                SUM(CASE WHEN w.direccion = 'recibido' THEN 1 ELSE 0 END) AS total_recibidos
            FROM whatsapp_mensajes w
            INNER JOIN pacientes p ON p.id = w.paciente_id
            WHERE p.nutricionista_id = ?
              AND p.estado = 'A'
              AND (w.nutricionista_id = ? OR w.nutricionista_id IS NULL)
            GROUP BY p.id, p.nombre, p.apellido, p.telefono, p.rut_dni
            ORDER BY ultima_fecha DESC
        ";

        return $db->query($sql, [$nutricionistaId, $nutricionistaId])->getResultArray();
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
        $data['direccion'] = 'recibido';
        $data['estado_envio'] = 'recibido';
        $data['tipo_mensaje'] = $data['tipo_mensaje'] ?? 'otro';
        $data['fecha_envio'] = $data['fecha_envio'] ?? date('Y-m-d H:i:s');
        $data['fcreacion'] = $data['fcreacion'] ?? date('Y-m-d H:i:s');

        return $this->insert($data);
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
