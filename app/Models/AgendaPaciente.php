<?php

namespace App\Models;

use CodeIgniter\Model;

class AgendaPaciente extends Model
{
    protected $table = 'agenda_paciente';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'detalle_agenda_id', 'paciente_id', 'nutricionista_id', 'tipo_consulta', 'motivo',
        'estado_cita', 'fecha_confirmacion', 'fecha_cancelacion', 'motivo_cancelacion',
        'recordatorio_enviado', 'fecha_recordatorio', 'observaciones'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'detalle_agenda_id' => 'required|integer|greater_than[0]',
        'paciente_id' => 'required|integer|greater_than[0]',
        'estado_cita' => 'required|in_list[agendada,confirmada,en_proceso,completada,cancelada,no_asistio]'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Agendar cita
     */
    public function agendarCita($detalleAgendaId, $pacienteId, $data = [])
    {
        $data['detalle_agenda_id'] = $detalleAgendaId;
        $data['paciente_id'] = $pacienteId;
        $data['estado_cita'] = 'agendada';

        return $this->insert($data);
    }

    /**
     * Confirmar cita
     */
    public function confirmarCita($id)
    {
        return $this->update($id, [
            'estado_cita' => 'confirmada',
            'fecha_confirmacion' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Cancelar cita
     */
    public function cancelarCita($id, $motivo = null)
    {
        $data = [
            'estado_cita' => 'cancelada',
            'fecha_cancelacion' => date('Y-m-d H:i:s')
        ];

        if ($motivo) {
            $data['motivo_cancelacion'] = $motivo;
        }

        return $this->update($id, $data);
    }

    /**
     * Obtener citas por paciente
     */
    public function getCitasPorPaciente($pacienteId, $estado = null)
    {
        $builder = $this->where('paciente_id', $pacienteId);

        if ($estado) {
            $builder->where('estado_cita', $estado);
        }

        return $builder->orderBy('fcreacion', 'DESC')
            ->findAll();
    }

    /**
     * Obtener cita completa con relaciones
     */
    public function getCitaCompleta($id)
    {
        $cita = $this->find($id);
        if (!$cita) {
            return null;
        }

        // Cargar detalle de agenda
        // Asumiendo que existe un modelo DetalleAgenda
        // $detalleAgendaModel = new DetalleAgenda();
        // $cita->detalle_agenda = $detalleAgendaModel->find($cita->detalle_agenda_id);

        // Cargar paciente
        $pacienteModel = new Paciente();
        $cita->paciente = $pacienteModel->find($cita->paciente_id);

        return $cita;
    }
}
