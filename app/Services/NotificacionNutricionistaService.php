<?php

namespace App\Services;

use App\Models\Notificacion;
use Config\Services;

/**
 * Notificaciones in-app y correo al nutricionista (reserva web, confirmación/cancelación por email).
 */
class NotificacionNutricionistaService
{
    public function obtenerDatosCita(int $detalleAgendaId, ?int $pacienteId = null): ?object
    {
        $db = \Config\Database::connect();
        $builder = $db->table('detalle_agenda da')
            ->select('da.id, da.usuario_id, da.fecha, da.hora_inicio, da.hora_fin, da.estado_cita,
                a.fecha as fecha_agenda,
                p.nombre as paciente_nombre, p.apellido as paciente_apellido, p.email as paciente_email, p.telefono as paciente_telefono,
                u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido, u.correo as nutricionista_correo')
            ->join('agenda a', 'a.id = da.agenda_id', 'left')
            ->join('pacientes p', 'p.id = da.paciente_id', 'left')
            ->join('usuario u', 'u.id = da.usuario_id', 'left')
            ->where('da.id', $detalleAgendaId);

        if ($pacienteId !== null) {
            $builder->where('da.paciente_id', $pacienteId);
        }

        return $builder->get()->getRow();
    }

    public function notificarReservaWeb(
        int $detalleAgendaId,
        int $usuarioNutricionistaId,
        string $nombrePaciente,
        string $emailPaciente,
        string $telefonoPaciente,
        string $fecha,
        string $horaInicio,
        string $horaFin = ''
    ): void {
        $mensaje = $nombrePaciente . ' solicitó hora el ' . $fecha . ' a las ' . $horaInicio . '. Revise y confirme en el calendario.';
        $this->enviar(
            $usuarioNutricionistaId,
            $detalleAgendaId,
            'reserva_web',
            'Nueva reserva desde la web',
            $mensaje,
            base_url('dashboard/agenda/lista?destacar=' . $detalleAgendaId),
            'emails/reserva_web_nutricionista',
            'Nueva reserva web - ' . $nombrePaciente . ' - ' . $fecha . ' ' . $horaInicio,
            [
                'nombrePaciente'   => $nombrePaciente,
                'emailPaciente'    => $emailPaciente,
                'telefonoPaciente' => $telefonoPaciente,
                'fecha'            => $fecha,
                'horaInicio'       => $horaInicio,
                'horaFin'          => $horaFin,
                'enlaceLista'      => base_url('dashboard/agenda/lista?destacar=' . $detalleAgendaId),
            ]
        );
    }

    /**
     * Paciente confirmó la cita desde el enlace del correo.
     */
    public function notificarConfirmacionDesdeEmail(int $detalleAgendaId, ?int $pacienteId, bool $pendientePago): void
    {
        $cita = $this->obtenerDatosCita($detalleAgendaId, $pacienteId);
        if (!$cita || empty($cita->usuario_id)) {
            return;
        }

        $nombrePaciente = trim(($cita->paciente_nombre ?? '') . ' ' . ($cita->paciente_apellido ?? ''));
        $fecha = $cita->fecha ?? $cita->fecha_agenda ?? '';
        $horaInicio = !empty($cita->hora_inicio) ? date('H:i', strtotime($cita->hora_inicio)) : '';
        $horaFin = !empty($cita->hora_fin) ? date('H:i', strtotime($cita->hora_fin)) : '';

        if ($pendientePago) {
            $titulo = 'Paciente confirmó — pendiente de pago';
            $mensaje = $nombrePaciente . ' confirmó la cita del ' . $fecha . ' a las ' . $horaInicio
                . '. El paciente debe completar el pago; revise el estado en la agenda.';
        } else {
            $titulo = 'Cita confirmada por el paciente';
            $mensaje = $nombrePaciente . ' confirmó su asistencia para el ' . $fecha . ' a las ' . $horaInicio . '.';
        }

        $this->enviar(
            (int) $cita->usuario_id,
            $detalleAgendaId,
            'confirmacion_email',
            $titulo,
            $mensaje,
            base_url('dashboard/agenda/lista?destacar=' . $detalleAgendaId),
            'emails/confirmacion_email_nutricionista',
            $titulo . ' - ' . $nombrePaciente,
            [
                'nombrePaciente' => $nombrePaciente,
                'emailPaciente'  => $cita->paciente_email ?? '',
                'fecha'          => $fecha,
                'horaInicio'     => $horaInicio,
                'horaFin'        => $horaFin,
                'pendientePago'  => $pendientePago,
                'enlaceLista'    => base_url('dashboard/agenda/lista?destacar=' . $detalleAgendaId),
            ]
        );
    }

    /**
     * Paciente canceló la cita desde el enlace del correo (solo notificación in-app; el correo al nutri puede enviarse aparte).
     */
    public function notificarCancelacionDesdeEmail(object $cita): void
    {
        if (empty($cita->usuario_id)) {
            return;
        }

        $detalleAgendaId = (int) ($cita->id ?? 0);
        $nombrePaciente = trim(($cita->nombre ?? $cita->paciente_nombre ?? '') . ' ' . ($cita->apellido ?? $cita->paciente_apellido ?? ''));
        $fecha = $cita->fecha ?? '';
        $horaInicio = !empty($cita->hora_inicio) ? date('H:i', strtotime($cita->hora_inicio)) : '';

        $mensaje = $nombrePaciente . ' canceló la cita del ' . $fecha . ' a las ' . $horaInicio . '. El horario quedó disponible.';

        try {
            $notifModel = new Notificacion();
            $notifModel->crear(
                (int) $cita->usuario_id,
                'cancelacion_email',
                'Cita cancelada por el paciente',
                $mensaje,
                base_url('dashboard/agenda/calendario'),
                'detalle_agenda',
                $detalleAgendaId > 0 ? $detalleAgendaId : null
            );
        } catch (\Throwable $e) {
            log_message('error', 'Notificación cancelación email: ' . $e->getMessage());
        }
    }

    private function enviar(
        int $usuarioNutricionistaId,
        int $detalleAgendaId,
        string $tipo,
        string $titulo,
        string $mensaje,
        string $enlace,
        string $vistaEmail,
        string $asuntoEmail,
        array $datosVista
    ): void {
        $db = \Config\Database::connect();
        $nutri = $db->table('usuario')
            ->select('id, nombre, apellido, correo')
            ->where('id', $usuarioNutricionistaId)
            ->get()
            ->getRow();

        if (!$nutri) {
            return;
        }

        $nombreNutricionista = trim(($nutri->nombre ?? '') . ' ' . ($nutri->apellido ?? ''));
        $datosVista['nombreNutricionista'] = $nombreNutricionista;

        try {
            $notifModel = new Notificacion();
            $notifModel->crear(
                (int) $nutri->id,
                $tipo,
                $titulo,
                $mensaje,
                $enlace,
                'detalle_agenda',
                $detalleAgendaId
            );
        } catch (\Throwable $e) {
            log_message('error', 'Notificación in-app (' . $tipo . '): ' . $e->getMessage());
        }

        if (empty($nutri->correo)) {
            log_message('warning', 'Notificación ' . $tipo . ': nutricionista sin correo (usuario_id=' . $nutri->id . ')');
            return;
        }

        try {
            $html = view($vistaEmail, $datosVista);
            $email = Services::email();
            $emailConfig = config(\Config\Email::class);
            $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
            $email->setTo($nutri->correo);
            $email->setSubject($asuntoEmail);
            $email->setMessage($html);
            if (!$email->send()) {
                log_message('error', 'Email nutricionista (' . $tipo . '): ' . $email->printDebugger(['headers']));
            }
        } catch (\Throwable $e) {
            log_message('error', 'Email nutricionista (' . $tipo . '): ' . $e->getMessage());
        }
    }
}
