<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialClinico extends Model
{
    protected $table = 'historial_clinico';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'paciente_id', 'nutricionista_id', 'agenda_id', 'detalle_agenda_id', 'tipo_registro', 'fecha_consulta', 'hora_consulta',
        'peso_actual', 'altura_actual', 'imc_actual', 'circunferencia_cintura', 'circunferencia_cadera',
        'grasa_corporal', 'masa_muscular', 
        'pliegue_tricipital', 'pliegue_bicipital', 'pliegue_subescapular', 'pliegue_suprailíaco',
        'pliegue_abdominal', 'pliegue_muslo_anterior', 'pliegue_pantorrilla_medial',
        'suma_pliegues', 'grasa_corporal_calculada',
        'motivo_consulta', 'anamnesis', 'diagnostico',
        'plan_tratamiento', 'recomendaciones', 'observaciones', 'proxima_cita', 'estado'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';
    protected $deletedField = 'feliminacion';

    protected $validationRules = [
        'paciente_id' => 'required|integer|greater_than[0]',
        'tipo_registro' => 'required|in_list[consulta,seguimiento,control,emergencia]',
        'fecha_consulta' => 'required|valid_date',
        'estado' => 'required|in_list[A,I]'
    ];

    protected $validationMessages = [
        'paciente_id' => [
            'required' => 'El paciente es obligatorio.'
        ],
        'fecha_consulta' => [
            'required' => 'La fecha de consulta es obligatoria.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Obtener historial completo con relaciones
     */
    public function getHistorialCompleto($id)
    {
        $historial = $this->find($id);
        if (!$historial) {
            return null;
        }

        // Cargar paciente
        $pacienteModel = new Paciente();
        $historial->paciente = $pacienteModel->find($historial->paciente_id);

        // Cargar nutricionista
        if ($historial->nutricionista_id) {
            $usuarioModel = new Usuario();
            $historial->nutricionista = $usuarioModel->find($historial->nutricionista_id);
        }

        return $historial;
    }

    /**
     * Obtener historial por paciente
     */
    public function getHistorialPorPaciente($pacienteId, $limit = null)
    {
        $builder = $this->where('paciente_id', $pacienteId)
            ->where('estado', 'A')
            ->orderBy('fecha_consulta', 'DESC')
            ->orderBy('hora_consulta', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Calcular IMC automáticamente
     */
    public function calcularIMC($peso, $altura)
    {
        if ($peso > 0 && $altura > 0) {
            $alturaMetros = $altura / 100;
            return round($peso / ($alturaMetros * $alturaMetros), 2);
        }
        return null;
    }

    /**
     * Obtener última consulta de un paciente
     */
    public function getUltimaConsulta($pacienteId)
    {
        return $this->where('paciente_id', $pacienteId)
            ->where('estado', 'A')
            ->orderBy('fecha_consulta', 'DESC')
            ->orderBy('hora_consulta', 'DESC')
            ->first();
    }

    /**
     * Obtener evolución de peso de un paciente
     */
    public function getEvolucionPeso($pacienteId, $fechaInicio = null, $fechaFin = null)
    {
        $builder = $this->select('fecha_consulta, peso_actual, imc_actual')
            ->where('paciente_id', $pacienteId)
            ->where('estado', 'A')
            ->where('peso_actual IS NOT NULL')
            ->orderBy('fecha_consulta', 'ASC');

        if ($fechaInicio) {
            $builder->where('fecha_consulta >=', $fechaInicio);
        }
        if ($fechaFin) {
            $builder->where('fecha_consulta <=', $fechaFin);
        }

        return $builder->findAll();
    }
}
