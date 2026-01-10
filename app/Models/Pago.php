<?php

namespace App\Models;

use CodeIgniter\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'empresa_id', 'paquete_id', 'tipo_pago', 'monto', 'moneda', 'metodo_pago',
        'estado_pago', 'fecha_pago', 'fecha_vencimiento', 'periodo_inicio', 'periodo_fin',
        'referencia', 'comprobante_ruta', 'observaciones'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'empresa_id' => 'required|integer|greater_than[0]',
        'tipo_pago' => 'required|in_list[setup,mensual,anual,extra]',
        'monto' => 'required|decimal|greater_than[0]',
        'estado_pago' => 'required|in_list[pendiente,procesando,completado,fallido,reembolsado]'
    ];

    protected $validationMessages = [
        'empresa_id' => [
            'required' => 'La empresa es obligatoria.'
        ],
        'monto' => [
            'required' => 'El monto es obligatorio.',
            'greater_than' => 'El monto debe ser mayor a 0.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener pago completo con relaciones
     */
    public function getPagoCompleto($id)
    {
        $pago = $this->find($id);
        if (!$pago) {
            return null;
        }

        // Cargar empresa
        $empresaModel = new Empresa();
        $pago->empresa = $empresaModel->find($pago->empresa_id);

        // Cargar paquete
        if ($pago->paquete_id) {
            // Asumiendo que existe un modelo Paquete
            // $paqueteModel = new Paquete();
            // $pago->paquete = $paqueteModel->find($pago->paquete_id);
        }

        return $pago;
    }

    /**
     * Obtener pagos por empresa
     */
    public function getPagosPorEmpresa($empresaId, $estado = null)
    {
        $builder = $this->where('empresa_id', $empresaId);

        if ($estado) {
            $builder->where('estado_pago', $estado);
        }

        return $builder->orderBy('fecha_pago', 'DESC')
            ->findAll();
    }

    /**
     * Obtener pagos pendientes
     */
    public function getPagosPendientes($empresaId = null)
    {
        $builder = $this->where('estado_pago', 'pendiente')
            ->where('fecha_vencimiento >=', date('Y-m-d'));

        if ($empresaId) {
            $builder->where('empresa_id', $empresaId);
        }

        return $builder->orderBy('fecha_vencimiento', 'ASC')
            ->findAll();
    }

    /**
     * Procesar pago
     */
    public function procesarPago($id, $referencia = null, $comprobante = null)
    {
        $data = [
            'estado_pago' => 'completado',
            'fecha_pago' => date('Y-m-d')
        ];

        if ($referencia) {
            $data['referencia'] = $referencia;
        }
        if ($comprobante) {
            $data['comprobante_ruta'] = $comprobante;
        }

        return $this->update($id, $data);
    }
}
