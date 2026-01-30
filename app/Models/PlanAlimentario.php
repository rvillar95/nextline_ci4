<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanAlimentario extends Model
{
    protected $table = 'plan_alimentario';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'detalle_agenda_id', 'paciente_id', 'nutricionista_id', 'calorimetria_id',
        'requerimiento_kcal', 'prot_porcentaje', 'grasa_porcentaje', 'cho_porcentaje',
        'prot_gramos', 'grasa_gramos', 'cho_gramos',
        'adecuacion_min', 'adecuacion_max',
        'total_kcal', 'total_cho', 'total_grasa', 'total_prot',
        'adecuacion_kcal_porc', 'adecuacion_cho_porc', 'adecuacion_grasa_porc', 'adecuacion_prot_porc',
        'observaciones',
        'fcreacion'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = '';  // vacío: la tabla no tiene campo de actualización; null producía SQL inválido

    /**
     * Obtener plan por detalle_agenda_id
     */
    public function getPorDetalleAgenda($detalleAgendaId)
    {
        $plan = $this->where('detalle_agenda_id', $detalleAgendaId)->first();
        
        if ($plan) {
            // Cargar porciones
            $porcionModel = new PlanAlimentarioPorcion();
            $plan->porciones = $porcionModel->getPorPlan($plan->id);
        }
        
        return $plan;
    }

    /**
     * Obtener plan completo con relaciones
     */
    public function getPlanCompleto($id)
    {
        $plan = $this->find($id);
        if (!$plan) {
            return null;
        }

        // Cargar paciente
        $pacienteModel = new Paciente();
        $plan->paciente = $pacienteModel->find($plan->paciente_id);

        // Cargar nutricionista
        $usuarioModel = new Usuario();
        $plan->nutricionista = $usuarioModel->find($plan->nutricionista_id);

        // Cargar calorimetría si existe
        if ($plan->calorimetria_id) {
            $calorimetriaModel = new Calorimetria();
            $plan->calorimetria = $calorimetriaModel->find($plan->calorimetria_id);
        }

        // Cargar porciones
        $porcionModel = new PlanAlimentarioPorcion();
        $plan->porciones = $porcionModel->getPorPlan($id);

        // Cargar comidas
        $comidaModel = new PlanAlimentarioComida();
        $plan->comidas = $comidaModel->getPorPlan($id);

        return $plan;
    }
}
