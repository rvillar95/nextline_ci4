<?php

namespace App\Models;

use CodeIgniter\Model;

class Suscripcion extends Model
{
    protected $table = 'suscripciones';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'empresa_id', 'paquete_id', 'estado', 'fecha_inicio', 'fecha_fin',
        'fecha_proximo_pago', 'renovacion_automatica', 'monto_mensual', 'observaciones'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'empresa_id' => 'required|integer|greater_than[0]',
        'paquete_id' => 'required|integer|greater_than[0]',
        'estado' => 'required|in_list[activa,suspendida,cancelada,expirada]',
        'fecha_inicio' => 'required|valid_date',
        'monto_mensual' => 'required|decimal|greater_than[0]'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener suscripción activa de una empresa
     */
    public function getSuscripcionActiva($empresaId)
    {
        return $this->where('empresa_id', $empresaId)
            ->where('estado', 'activa')
            ->first();
    }

    /**
     * Verificar si una suscripción está activa
     */
    public function estaActiva($empresaId)
    {
        $suscripcion = $this->getSuscripcionActiva($empresaId);
        
        if (!$suscripcion) {
            return false;
        }

        // Verificar si no ha expirado
        if ($suscripcion->fecha_fin && $suscripcion->fecha_fin < date('Y-m-d')) {
            $this->update($suscripcion->id, ['estado' => 'expirada']);
            return false;
        }

        return true;
    }

    /**
     * Renovar suscripción
     */
    public function renovar($id, $nuevaFechaFin = null)
    {
        $suscripcion = $this->find($id);
        if (!$suscripcion) {
            return false;
        }

        $data = [
            'fecha_proximo_pago' => date('Y-m-d', strtotime('+1 month')),
            'estado' => 'activa'
        ];

        if ($nuevaFechaFin) {
            $data['fecha_fin'] = $nuevaFechaFin;
        } elseif ($suscripcion->fecha_fin) {
            // Extender un mes más
            $data['fecha_fin'] = date('Y-m-d', strtotime($suscripcion->fecha_fin . ' +1 month'));
        }

        return $this->update($id, $data);
    }
}
