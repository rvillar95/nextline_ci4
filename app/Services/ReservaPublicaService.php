<?php

namespace App\Services;

use App\Models\EmpresaConfiguracion;
use App\Models\Paciente;
use App\Models\Usuario;
use Config\Database;
use Config\Services;

/**
 * Lógica compartida de reserva pública (web /reservar y bot WhatsApp).
 */
class ReservaPublicaService
{
    public function resolverEmpresaId(?int $empresaId = null): int
    {
        if ($empresaId !== null && $empresaId > 0) {
            return $empresaId;
        }
        $envId = (int) env('WHATSAPP_EMPRESA_ID', 0);
        if ($envId > 0) {
            return $envId;
        }
        $db = Database::connect();
        $row = $db->table('empresa')->select('id')->limit(1)->get()->getRow();
        return $row ? (int) $row->id : 0;
    }

    /**
     * Nutricionistas activos con al menos un cupo futuro (misma lógica que /reservar).
     */
    public function listarNutricionistasConCupos(?int $empresaId = null): array
    {
        $empresaId = $this->resolverEmpresaId($empresaId);
        $db = Database::connect();
        $hoy = date('Y-m-d');
        $horaAhora = date('H:i:s');

        $builder = $db->table('usuario u')
            ->select('u.id, u.nombre, u.apellido, u.foto')
            ->join('detalle_agenda da', 'da.usuario_id = u.id')
            ->join('agenda a', 'a.id = da.agenda_id')
            ->where('u.perfil_id', Usuario::PERFIL_NUTRICIONISTA)
            ->where('u.estado', 'A')
            ->where('da.paciente_id', null)
            ->where('da.estado', 1)
            ->groupStart()
                ->where("STR_TO_DATE(COALESCE(da.fecha, a.fecha), '%d-%m-%Y') > ", $hoy)
                ->orGroupStart()
                    ->where("STR_TO_DATE(COALESCE(da.fecha, a.fecha), '%d-%m-%Y') = ", $hoy)
                    ->where('da.hora_inicio >=', $horaAhora)
                ->groupEnd()
            ->groupEnd()
            ->groupBy('u.id')
            ->orderBy('u.nombre');

        if ($empresaId > 0) {
            $builder->where('u.empresa_id', $empresaId);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Nutricionistas con slots libres en una fecha concreta (Y-m-d).
     */
    public function nutricionistasConSlotsEnFecha(int $empresaId, string $fecha): array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return [];
        }
        $hoy = date('Y-m-d');
        if ($fecha < $hoy) {
            return [];
        }

        $db = Database::connect();
        $builder = $db->table('usuario u')
            ->select('u.id, u.nombre, u.apellido')
            ->join('detalle_agenda da', 'da.usuario_id = u.id')
            ->join('agenda a', 'a.id = da.agenda_id')
            ->where('u.perfil_id', Usuario::PERFIL_NUTRICIONISTA)
            ->where('u.estado', 'A')
            ->where('u.empresa_id', $empresaId)
            ->where('da.paciente_id', null)
            ->where('da.estado', 1)
            ->where("STR_TO_DATE(COALESCE(da.fecha, a.fecha), '%d-%m-%Y') = ", $fecha)
            ->groupBy('u.id')
            ->orderBy('u.nombre', 'ASC');

        $rows = $builder->get()->getResultArray();
        if ($fecha !== $hoy) {
            return $rows;
        }

        $horaAhora = date('H:i:s');
        $conCupos = [];
        foreach ($rows as $nut) {
            $slots = $this->disponibilidad((int) $nut['id'], $fecha);
            if ($slots !== []) {
                $conCupos[] = $nut;
            }
        }
        return $conCupos;
    }

    /**
     * Slots libres para nutricionista y fecha (Y-m-d).
     */
    public function disponibilidad(?int $nutricionistaId, string $fecha): array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha = date('Y-m-d');
        }
        $hoy = date('Y-m-d');
        if ($fecha < $hoy) {
            return [];
        }

        $db = Database::connect();
        $builder = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, da.hora_fin, da.fecha, a.fecha as fecha_agenda, u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido')
            ->join('agenda a', 'a.id = da.agenda_id')
            ->join('usuario u', 'u.id = da.usuario_id')
            ->where('da.paciente_id', null)
            ->where('da.estado', 1)
            ->where("STR_TO_DATE(COALESCE(da.fecha, a.fecha), '%d-%m-%Y') = ", $fecha);

        if ($nutricionistaId !== null && $nutricionistaId > 0) {
            $builder->where('da.usuario_id', $nutricionistaId);
        }

        $slots = $builder->orderBy('da.hora_inicio')->get()->getResultArray();

        if ($fecha === $hoy) {
            $horaAhora = date('H:i:s');
            $slots = array_values(array_filter($slots, static function (array $s) use ($horaAhora): bool {
                $hi = $s['hora_inicio'] ?? '';
                if (is_string($hi) && strlen($hi) === 5) {
                    $hi .= ':00';
                }
                return $hi >= $horaAhora;
            }));
        }

        return $slots;
    }

    public function pacientePorRut(string $rut): ?array
    {
        $rutNormalized = preg_replace('/[^0-9kK]/', '', trim($rut));
        if (strlen($rutNormalized) < 8) {
            return null;
        }
        $db = Database::connect();
        $paciente = $db->table('pacientes')
            ->select('id, nombre, apellido, telefono, email')
            ->where('estado', 'A')
            ->where("REPLACE(REPLACE(REPLACE(IFNULL(rut_dni,''),'.',''),'-',''),' ','') = ", $rutNormalized)
            ->get()
            ->getRow();
        if (!$paciente) {
            return null;
        }
        return [
            'id'       => (int) $paciente->id,
            'nombre'   => $paciente->nombre ?? '',
            'apellido' => $paciente->apellido ?? '',
            'telefono' => $paciente->telefono ?? '',
            'email'    => $paciente->email ?? '',
        ];
    }

    /**
     * Crear reserva (estado reservada, origen paciente). Retorna ['success' => bool, ...].
     */
    public function crearReserva(
        int $detalleAgendaId,
        string $rutDni,
        string $nombre,
        string $apellido,
        string $email,
        ?string $telefono = null,
        string $origenAgendamiento = 'paciente'
    ): array {
        $rutDni = trim($rutDni);
        $nombre = trim($nombre);
        $apellido = trim($apellido);
        $email = trim($email);
        $telefono = $telefono !== null ? trim($telefono) : null;

        if (!$detalleAgendaId || !$rutDni || !$nombre || !$apellido || !$email) {
            return [
                'success' => false,
                'error'   => 'Faltan datos requeridos: RUT, nombre, apellido y correo.',
            ];
        }

        $db = Database::connect();
        $slot = $db->table('detalle_agenda da')
            ->select('da.id, da.usuario_id, da.fecha, da.hora_inicio, da.hora_fin, a.fecha as fecha_agenda')
            ->join('agenda a', 'a.id = da.agenda_id')
            ->where('da.id', $detalleAgendaId)
            ->where('da.paciente_id', null)
            ->where('da.estado', 1)
            ->get()
            ->getRow();

        if (!$slot) {
            return [
                'success' => false,
                'error'   => 'El horario ya no está disponible. Elija otro.',
            ];
        }

        if ($this->slotEsPasado($slot)) {
            return [
                'success' => false,
                'error'   => 'No puede reservar un horario en el pasado. Elija una fecha y hora futuras.',
            ];
        }

        $rutNormalized = preg_replace('/[^0-9kK]/', '', $rutDni);
        $pacienteModel = new Paciente();
        $existente = $db->table('pacientes')
            ->where('estado', 'A')
            ->where("REPLACE(REPLACE(REPLACE(IFNULL(rut_dni,''),'.',''),'-',''),' ','') = ", $rutNormalized)
            ->get()
            ->getRow();

        if ($existente) {
            $pacienteId = (int) $existente->id;
            $update = ['email' => $email];
            if ($telefono) {
                $update['telefono'] = $telefono;
            }
            $pacienteModel->update($pacienteId, $update);
        } else {
            $pacienteId = $pacienteModel->insert([
                'nutricionista_id' => $slot->usuario_id,
                'tipo_paciente'    => 'particular',
                'nombre'           => $nombre,
                'apellido'         => $apellido,
                'rut_dni'          => $rutDni,
                'email'            => $email,
                'telefono'         => $telefono ?: null,
                'estado'           => 'A',
            ]);
        }

        if (!$pacienteId) {
            return [
                'success' => false,
                'error'   => 'Error al guardar los datos. Intente de nuevo.',
            ];
        }

        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update([
                'paciente_id'         => $pacienteId,
                'estado_cita'         => 'reservada',
                'estado'              => 2,
                'origen_agendamiento' => $origenAgendamiento,
            ]);

        $this->enviarEmailReservaRecibida($detalleAgendaId, $pacienteId, $slot);

        $fecha = $slot->fecha ?? $slot->fecha_agenda ?? '';
        $horaInicio = date('H:i', strtotime($slot->hora_inicio ?? '00:00'));
        $horaFin = !empty($slot->hora_fin) ? date('H:i', strtotime($slot->hora_fin)) : '';

        (new NotificacionNutricionistaService())->notificarReservaWeb(
            $detalleAgendaId,
            (int) $slot->usuario_id,
            trim($nombre . ' ' . $apellido),
            $email,
            $telefono ?? '',
            $fecha,
            $horaInicio,
            $horaFin
        );

        return [
            'success'           => true,
            'paciente_id'       => (int) $pacienteId,
            'detalle_agenda_id' => $detalleAgendaId,
            'fecha'             => $fecha,
            'hora_inicio'       => $horaInicio,
            'hora_fin'          => $horaFin,
            'message'           => 'Reserva recibida. Te hemos enviado un correo. El nutricionista revisará tu solicitud y te avisará cuando esté confirmada.',
        ];
    }

    public function parseFechaEntrada(string $texto): ?string
    {
        $texto = trim($texto);
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $texto, $m)) {
            return $this->validarFechaFutura($m[1] . '-' . $m[2] . '-' . $m[3]);
        }
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $texto, $m)) {
            return $this->validarFechaFutura(sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]));
        }
        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2})$/', $texto, $m)) {
            $anio = (int) $m[3];
            $anio += $anio < 100 ? 2000 : 0;
            return $this->validarFechaFutura(sprintf('%04d-%02d-%02d', $anio, (int) $m[2], (int) $m[1]));
        }
        return null;
    }

    private function validarFechaFutura(string $ymd): ?string
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $ymd);
        if (!$dt || $dt->format('Y-m-d') !== $ymd) {
            return null;
        }
        $hoy = date('Y-m-d');
        if ($ymd < $hoy) {
            return null;
        }
        return $ymd;
    }

    public function formatearFechaHumana(string $ymd): string
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $ymd);
        if (!$dt) {
            return $ymd;
        }
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        return (int) $dt->format('d') . ' de ' . $meses[(int) $dt->format('n') - 1] . ' de ' . $dt->format('Y');
    }

    private function parseFechaAgenda(?string $fechaDa, ?string $fechaAgenda): ?\DateTime
    {
        $fecha = $fechaDa ?: $fechaAgenda;
        if (!$fecha) {
            return null;
        }
        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fecha, $m)) {
            return new \DateTime($m[3] . '-' . $m[2] . '-' . $m[1]);
        }
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $fecha)) {
            return new \DateTime(substr($fecha, 0, 10));
        }
        return null;
    }

    public function slotEsPasado(object $slot): bool
    {
        $dt = $this->parseFechaAgenda($slot->fecha ?? null, $slot->fecha_agenda ?? null);
        if (!$dt) {
            return true;
        }
        $hora = $slot->hora_inicio ?? '00:00:00';
        if (is_string($hora) && strlen($hora) === 5) {
            $hora .= ':00';
        }
        $fechaHora = new \DateTime($dt->format('Y-m-d') . ' ' . $hora);
        return $fechaHora < new \DateTime();
    }

    private function enviarEmailReservaRecibida(int $detalleAgendaId, int $pacienteId, object $slot): void
    {
        $db = Database::connect();
        $cita = $db->table('detalle_agenda da')
            ->select('da.fecha, da.hora_inicio, da.hora_fin, p.nombre, p.apellido, p.email, u.nombre as nut_nombre, u.apellido as nut_apellido')
            ->join('pacientes p', 'p.id = da.paciente_id')
            ->join('usuario u', 'u.id = da.usuario_id')
            ->where('da.id', $detalleAgendaId)
            ->get()
            ->getRow();
        if (!$cita || empty($cita->email)) {
            return;
        }
        $fecha = $cita->fecha ?? $slot->fecha ?? $slot->fecha_agenda ?? '';
        $hora = date('H:i', strtotime($cita->hora_inicio ?? $slot->hora_inicio));
        $nombrePaciente = trim(($cita->nombre ?? '') . ' ' . ($cita->apellido ?? ''));
        $nombreNutricionista = trim(($cita->nut_nombre ?? '') . ' ' . ($cita->nut_apellido ?? ''));

        $configModel = new EmpresaConfiguracion();
        $usuario = $db->table('usuario')->select('empresa_id')->where('id', $slot->usuario_id)->get()->getRow();
        $empresaId = $usuario->empresa_id ?? null;
        $config = $empresaId ? $configModel->obtenerConfiguracion($empresaId) : [];
        $mensaje = $config['mensaje_reserva_recibida'] ?? null;
        if (empty($mensaje)) {
            $mensaje = "Hola [NOMBRE_PACIENTE],\n\nHemos recibido tu solicitud de hora para el [FECHA] a las [HORA] con [NOMBRE_NUTRICIONISTA].\n\nEl nutricionista revisará tu reserva y te enviaremos un correo cuando esté confirmada.\n\nSaludos.";
        }
        $mensaje = str_replace('[NOMBRE_PACIENTE]', $nombrePaciente, $mensaje);
        $mensaje = str_replace('[FECHA]', $fecha, $mensaje);
        $mensaje = str_replace('[HORA]', $hora, $mensaje);
        $mensaje = str_replace('[NOMBRE_NUTRICIONISTA]', $nombreNutricionista, $mensaje);
        $mensajeHtml = nl2br(htmlspecialchars($mensaje));

        $html = view('emails/reserva_recibida', [
            'nombrePaciente'      => $nombrePaciente,
            'fecha'               => $fecha,
            'hora'                => $hora,
            'nombreNutricionista' => $nombreNutricionista,
            'mensaje'             => $mensajeHtml,
        ]);
        $email = Services::email();
        $email->setFrom(env('email.fromEmail', 'noreply@example.com'), env('email.fromName', 'Agenda'));
        $email->setTo($cita->email);
        $email->setSubject('Reserva recibida - ' . $fecha . ' a las ' . $hora);
        $email->setMessage($html);
        $email->send();
    }
}
