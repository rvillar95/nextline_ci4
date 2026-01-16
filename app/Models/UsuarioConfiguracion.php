<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioConfiguracion extends Model
{
    protected $table      = 'usuario_configuraciones';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'usuario_id',
        'enviar_whatsapp',
        'enviar_email',
        'crear_evento_calendario',
        'agregar_paciente_como_invitado',
        'enviar_recordatorios_whatsapp',
        'horas_antes_recordatorio',
        'mensaje_cancelacion_pendiente',
        'mensaje_cancelacion_confirmada',
        'mensaje_cancelacion_en_proceso'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';

    protected $validationRules = [
        'usuario_id' => 'required|integer|is_natural_no_zero',
        'enviar_whatsapp' => 'permit_empty|in_list[0,1]',
        'enviar_email' => 'permit_empty|in_list[0,1]',
        'crear_evento_calendario' => 'permit_empty|in_list[0,1]',
        'agregar_paciente_como_invitado' => 'permit_empty|in_list[0,1]',
        'enviar_recordatorios_whatsapp' => 'permit_empty|in_list[0,1]',
        'horas_antes_recordatorio' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[168]'
    ];

    /**
     * Obtener configuración de un usuario
     * Si no existe, crear una con valores por defecto
     */
    public function obtenerConfiguracion($usuarioId)
    {
        $config = $this->where('usuario_id', $usuarioId)->first();
        
        if (!$config) {
            // Crear configuración por defecto
            $config = [
                'usuario_id' => $usuarioId,
                'enviar_whatsapp' => 1,
                'enviar_email' => 1,
                'crear_evento_calendario' => 1,
                'agregar_paciente_como_invitado' => 1,
                'enviar_recordatorios_whatsapp' => 1,
                'horas_antes_recordatorio' => 24
            ];
            
            $this->insert($config);
            return $this->where('usuario_id', $usuarioId)->first();
        }
        
        return $config;
    }

    /**
     * Actualizar configuración de un usuario
     */
    public function actualizarConfiguracion($usuarioId, $data)
    {
        $config = $this->where('usuario_id', $usuarioId)->first();
        
        if ($config) {
            return $this->update($config['id'], $data);
        } else {
            // Si no existe, crear nueva
            $data['usuario_id'] = $usuarioId;
            return $this->insert($data);
        }
    }

    /**
     * Verificar si una opción está habilitada
     */
    public function estaHabilitado($usuarioId, $opcion)
    {
        $config = $this->obtenerConfiguracion($usuarioId);
        return isset($config[$opcion]) && $config[$opcion] == 1;
    }
}
