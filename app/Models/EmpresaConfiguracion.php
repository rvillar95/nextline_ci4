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
        'mensaje_cancelacion_en_proceso',
        'mp_access_token',
        'mp_public_key',
        'mp_access_token_sandbox',
        'mp_public_key_sandbox',
        'mp_access_token_production',
        'mp_public_key_production',
        'mp_mode',
        'mp_habilitado'
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

    /**
     * Obtener credenciales de Mercado Pago de una empresa
     * Usa las credenciales según el modo configurado (sandbox o production)
     * 
     * @param int $empresaId
     * @return array|null ['access_token', 'public_key', 'mode', 'habilitado']
     */
    public function obtenerCredencialesMercadoPago($empresaId)
    {
        $config = $this->obtenerConfiguracion($empresaId);
        
        if (!$config) {
            return null;
        }

        // Normalizar el modo a minúsculas
        $mode = strtolower(trim($config['mp_mode'] ?? 'sandbox'));
        
        // Determinar qué credenciales usar según el modo
        $accessToken = null;
        $publicKey = null;
        
        if ($mode === 'production') {
            // Usar credenciales de producción
            $accessToken = $config['mp_access_token_production'] ?? null;
            $publicKey = $config['mp_public_key_production'] ?? null;
            
            // Fallback a campos antiguos si los nuevos no existen (compatibilidad)
            if (empty($accessToken)) {
                $accessToken = $config['mp_access_token'] ?? null;
            }
            if (empty($publicKey)) {
                $publicKey = $config['mp_public_key'] ?? null;
            }
        } else {
            // Usar credenciales de sandbox (por defecto)
            $accessToken = $config['mp_access_token_sandbox'] ?? null;
            $publicKey = $config['mp_public_key_sandbox'] ?? null;
            
            // Fallback a campos antiguos si los nuevos no existen (compatibilidad)
            if (empty($accessToken)) {
                $accessToken = $config['mp_access_token'] ?? null;
            }
            if (empty($publicKey)) {
                $publicKey = $config['mp_public_key'] ?? null;
            }
        }
        
        // Si no hay credenciales, retornar null
        if (empty($accessToken)) {
            return null;
        }
        
        return [
            'access_token' => $accessToken,
            'public_key' => $publicKey,
            'mode' => $mode,
            'habilitado' => isset($config['mp_habilitado']) && $config['mp_habilitado'] == 1
        ];
    }

    /**
     * Verificar si Mercado Pago está habilitado para una empresa
     */
    public function mercadoPagoHabilitado($empresaId)
    {
        $credenciales = $this->obtenerCredencialesMercadoPago($empresaId);
        return $credenciales && $credenciales['habilitado'] && !empty($credenciales['access_token']);
    }
}
