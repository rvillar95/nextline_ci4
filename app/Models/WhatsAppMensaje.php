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
