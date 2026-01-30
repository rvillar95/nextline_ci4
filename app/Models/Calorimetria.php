<?php

namespace App\Models;

use CodeIgniter\Model;

class Calorimetria extends Model
{
    protected $table = 'calorimetria';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'detalle_agenda_id', 'paciente_id', 'nutricionista_id',
        'peso', 'talla', 'edad', 'sexo',
        'tmb_hombres', 'tmb_mujeres', 'tmb_usado',
        'total_minutos', 'cals_habituales', 'cals_entrenamiento',
        'requerimiento_total',
        'fcreacion'  // campo de timestamp; debe estar en allowedFields para el INSERT
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = null;

    /**
     * Obtener calorimetría por detalle_agenda_id
     */
    public function getPorDetalleAgenda($detalleAgendaId)
    {
        $calorimetria = $this->where('detalle_agenda_id', $detalleAgendaId)->first();
        
        if ($calorimetria) {
            // Cargar actividades
            $actividadModel = new CalorimetriaActividad();
            $calorimetria->actividades = $actividadModel->where('calorimetria_id', $calorimetria->id)
                ->orderBy('tipo', 'ASC')
                ->orderBy('orden', 'ASC')
                ->findAll();
        }
        
        return $calorimetria;
    }

    /**
     * Obtener calorimetría completa con relaciones
     */
    public function getCalorimetriaCompleta($id)
    {
        $calorimetria = $this->find($id);
        if (!$calorimetria) {
            return null;
        }

        // Cargar paciente
        $pacienteModel = new Paciente();
        $calorimetria->paciente = $pacienteModel->find($calorimetria->paciente_id);

        // Cargar nutricionista
        $usuarioModel = new Usuario();
        $calorimetria->nutricionista = $usuarioModel->find($calorimetria->nutricionista_id);

        // Cargar actividades
        $actividadModel = new CalorimetriaActividad();
        $calorimetria->actividades = $actividadModel->where('calorimetria_id', $id)
            ->orderBy('tipo', 'ASC')
            ->orderBy('orden', 'ASC')
            ->findAll();

        return $calorimetria;
    }
}
