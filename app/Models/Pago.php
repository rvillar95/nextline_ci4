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
        'empresa_id', 'paquete_id', 'detalle_agenda_id', 'tipo_pago', 'monto', 'moneda', 'metodo_pago',
        'estado_pago', 'fecha_pago', 'fecha_vencimiento', 'periodo_inicio', 'periodo_fin',
        'referencia', 'comprobante_ruta', 'observaciones',
        'mp_preference_id', 'mp_payment_id', 'mp_status'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'empresa_id' => 'required|integer|greater_than[0]',
        'tipo_pago' => 'required|in_list[setup,mensual,anual,extra,cita]',
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

        // Cargar información de la cita si está vinculada
        if (!empty($pago->detalle_agenda_id)) {
            $db = \Config\Database::connect();
            $cita = $db->table('detalle_agenda da')
                ->select('da.*, a.fecha, da.hora_inicio, da.hora_fin, p.nombre as paciente_nombre, p.apellido as paciente_apellido')
                ->join('agenda a', 'a.id = da.agenda_id', 'left')
                ->join('pacientes p', 'p.id = da.paciente_id', 'left')
                ->where('da.id', $pago->detalle_agenda_id)
                ->get()
                ->getRow();
            
            if ($cita) {
                $pago->cita = $cita;
            }
        }

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

    /**
     * Crear pago con preferencia de Mercado Pago
     * 
     * @param array $data Datos del pago
     * @param string $preferenceId ID de la preferencia de Mercado Pago
     * @return int|false ID del pago creado o false si falla
     */
    public function crearPagoConPreferencia(array $data, string $preferenceId)
    {
        $data['mp_preference_id'] = $preferenceId;
        $data['estado_pago'] = 'pendiente';
        $data['metodo_pago'] = 'mercadopago';
        
        return $this->insert($data);
    }

    /**
     * Actualizar pago desde notificación de Mercado Pago
     * 
     * @param int $pagoId ID del pago en nuestra BD
     * @param string $paymentId ID del pago en Mercado Pago
     * @param string $mpStatus Estado del pago en Mercado Pago
     * @return bool
     */
    public function actualizarDesdeMercadoPago(int $pagoId, string $paymentId, string $mpStatus): bool
    {
        $estadoInterno = $this->mapearEstadoMercadoPago($mpStatus);
        
        $data = [
            'mp_payment_id' => $paymentId,
            'mp_status' => $mpStatus,
            'estado_pago' => $estadoInterno,
            'referencia' => $paymentId
        ];

        // Si el pago fue aprobado, actualizar fecha de pago
        if ($mpStatus === 'approved') {
            $data['fecha_pago'] = date('Y-m-d');
        }

        $actualizado = $this->update($pagoId, $data);
        
        // Si el pago fue aprobado y tiene detalle_agenda_id, actualizar estado de la cita a 'agendada'
        if ($actualizado && $mpStatus === 'approved') {
            $pago = $this->find($pagoId);
            if ($pago && !empty($pago->detalle_agenda_id)) {
                $db = \Config\Database::connect();
                $db->table('detalle_agenda')
                    ->where('id', $pago->detalle_agenda_id)
                    ->update([
                        'estado_cita' => 'agendada'
                    ]);
                
                log_message('info', 'Estado de cita actualizado a "agendada" para detalle_agenda_id: ' . $pago->detalle_agenda_id);
                
                // Crear evento en Google Calendar cuando el pago se aprueba
                $this->crearEventoCalendarioAlPagar($pago->detalle_agenda_id, $pago->empresa_id);
            }
        }
        
        return $actualizado;
    }

    /**
     * Mapear estado de Mercado Pago a estado interno
     */
    protected function mapearEstadoMercadoPago(string $mpStatus): string
    {
        $map = [
            'pending' => 'pendiente',
            'approved' => 'completado',
            'rejected' => 'fallido',
            'cancelled' => 'fallido',
            'refunded' => 'reembolsado',
            'charged_back' => 'reembolsado',
            'in_process' => 'procesando',
            'in_mediation' => 'procesando'
        ];

        return $map[$mpStatus] ?? 'pendiente';
    }

    /**
     * Obtener pagos por preferencia de Mercado Pago
     */
    public function getPagoPorPreferencia(string $preferenceId)
    {
        return $this->where('mp_preference_id', $preferenceId)->first();
    }

    /**
     * Obtener pagos por cita (detalle_agenda_id)
     */
    public function getPagosPorCita(int $detalleAgendaId)
    {
        return $this->where('detalle_agenda_id', $detalleAgendaId)
            ->orderBy('fcreacion', 'DESC')
            ->findAll();
    }

    /**
     * Crear evento en Google Calendar cuando el pago se aprueba
     * 
     * @param int $detalleAgendaId ID del detalle de agenda
     * @param int $empresaId ID de la empresa
     * @return bool
     */
    protected function crearEventoCalendarioAlPagar(int $detalleAgendaId, int $empresaId): bool
    {
        try {
            // Verificar si el calendario está configurado
            if (empty(env('CALENDAR_PROVIDER'))) {
                log_message('debug', 'Calendar provider no configurado, omitiendo creación de evento');
                return false;
            }

            // Obtener configuración de la empresa para verificar si está habilitado crear eventos
            $configuracionModel = new \App\Models\EmpresaConfiguracion();
            $configuracion = $configuracionModel->obtenerConfiguracion($empresaId);
            
            if (!($configuracion['crear_evento_calendario'] ?? 1)) {
                log_message('info', 'Crear evento en calendario está deshabilitado para empresa ' . $empresaId);
                return false;
            }

            // Obtener información de la cita
            $db = \Config\Database::connect();
            $cita = $db->table('detalle_agenda da')
                ->select('da.*, a.fecha, da.usuario_id, p.nombre, p.apellido, p.email as email_paciente, 
                         u.nombre as nombre_nutricionista, u.correo as email_nutricionista, ma.nombre as modalidad')
                ->join('agenda a', 'a.id = da.agenda_id', 'left')
                ->join('pacientes p', 'p.id = da.paciente_id', 'left')
                ->join('usuario u', 'u.id = da.usuario_id', 'left')
                ->join('modalidad_agenda ma', 'ma.id = da.modalidad_id', 'left')
                ->where('da.id', $detalleAgendaId)
                ->get()
                ->getRow();
            
            if (!$cita || empty($cita->usuario_id)) {
                log_message('warning', 'No se pudo obtener información de la cita o usuario_id para crear evento en calendario');
                return false;
            }

            // Verificar si ya existe un evento en el calendario (para no duplicar)
            if (!empty($cita->calendar_event_id)) {
                log_message('info', 'La cita ya tiene un evento en el calendario (ID: ' . $cita->calendar_event_id . '), omitiendo creación');
                return true;
            }

            // Obtener configuración para saber si agregar paciente como invitado
            $agregarPacienteComoInvitado = $configuracion['agregar_paciente_como_invitado'] ?? 1;

            // Preparar datos para el evento
            $citaData = [
                'detalle_agenda_id' => $detalleAgendaId,
                'usuario_id' => $cita->usuario_id,
                'fecha' => $cita->fecha,
                'hora_inicio' => $cita->hora_inicio,
                'hora_fin' => $cita->hora_fin,
                'nombre_paciente' => trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? '')),
                'email_paciente' => ($agregarPacienteComoInvitado && !empty($cita->email_paciente)) ? $cita->email_paciente : null,
                'nombre_nutricionista' => $cita->nombre_nutricionista ?? 'Nutricionista',
                'email_nutricionista' => $cita->email_nutricionista ?? null,
                'tipo_consulta' => ucfirst(str_replace('_', ' ', $cita->tipo_consulta ?? 'control')),
                'modalidad' => $cita->modalidad ?? 'No definida',
                'motivo' => $cita->motivo ?? ''
            ];

            // Crear el evento usando CalendarService
            $calendarService = new \App\Libraries\CalendarService($cita->usuario_id);
            $resultado = $calendarService->crearEvento($detalleAgendaId, $citaData);
            
            if ($resultado['success'] ?? false) {
                log_message('info', 'Evento creado en calendario después del pago para cita ID: ' . $detalleAgendaId . 
                    ($agregarPacienteComoInvitado && !empty($cita->email_paciente) ? ' (con paciente como invitado)' : ' (sin paciente como invitado)'));
                return true;
            } else {
                log_message('warning', 'No se pudo crear evento en calendario después del pago: ' . ($resultado['message'] ?? 'Error desconocido'));
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al crear evento en calendario después del pago: ' . $e->getMessage());
            return false;
        }
    }
}
