<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaConfiguracion extends Model
{
    protected $table      = 'empresa_configuraciones';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'empresa_id',
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
        'empresa_id' => 'required|integer|is_natural_no_zero',
        'enviar_whatsapp' => 'permit_empty|in_list[0,1]',
        'enviar_email' => 'permit_empty|in_list[0,1]',
        'crear_evento_calendario' => 'permit_empty|in_list[0,1]',
        'agregar_paciente_como_invitado' => 'permit_empty|in_list[0,1]',
        'enviar_recordatorios_whatsapp' => 'permit_empty|in_list[0,1]',
        'horas_antes_recordatorio' => 'permit_empty|integer|greater_than_equal_to[1]|less_than_equal_to[168]'
    ];

    /**
     * Obtener configuración de una empresa
     * Si no existe, crear una con valores por defecto
     */
    public function obtenerConfiguracion($empresaId)
    {
        $config = $this->where('empresa_id', $empresaId)->first();
        
        if (!$config) {
            // Crear configuración por defecto
            $config = [
                'empresa_id' => $empresaId,
                'enviar_whatsapp' => 1,
                'enviar_email' => 1,
                'crear_evento_calendario' => 1,
                'agregar_paciente_como_invitado' => 1,
                'enviar_recordatorios_whatsapp' => 1,
                'horas_antes_recordatorio' => 24
            ];
            
            $this->insert($config);
            return $this->where('empresa_id', $empresaId)->first();
        }
        
        return $config;
    }

    /**
     * Obtener configuración por usuario (obtiene la configuración de su empresa)
     */
    public function obtenerConfiguracionPorUsuario($usuarioId)
    {
        $db = \Config\Database::connect();
        $usuario = $db->table('usuario')
            ->select('empresa_id')
            ->where('id', $usuarioId)
            ->get()
            ->getRowArray();
        
        if (!$usuario || !$usuario['empresa_id']) {
            // Si el usuario no tiene empresa, retornar valores por defecto
            return [
                'enviar_whatsapp' => 1,
                'enviar_email' => 1,
                'crear_evento_calendario' => 1,
                'agregar_paciente_como_invitado' => 1,
                'enviar_recordatorios_whatsapp' => 1,
                'horas_antes_recordatorio' => 24
            ];
        }
        
        return $this->obtenerConfiguracion($usuario['empresa_id']);
    }

    /**
     * Actualizar configuración de una empresa
     */
    public function actualizarConfiguracion($empresaId, $data)
    {
        $config = $this->where('empresa_id', $empresaId)->first();
        
        if ($config) {
            return $this->update($config['id'], $data);
        } else {
            // Si no existe, crear nueva
            $data['empresa_id'] = $empresaId;
            return $this->insert($data);
        }
    }

    /**
     * Verificar si una opción está habilitada para una empresa
     */
    public function estaHabilitado($empresaId, $opcion)
    {
        $config = $this->obtenerConfiguracion($empresaId);
        return isset($config[$opcion]) && $config[$opcion] == 1;
    }

    /**
     * Verificar si una opción está habilitada para un usuario (a través de su empresa)
     */
    public function estaHabilitadoPorUsuario($usuarioId, $opcion)
    {
        $config = $this->obtenerConfiguracionPorUsuario($usuarioId);
        return isset($config[$opcion]) && $config[$opcion] == 1;
    }
}
