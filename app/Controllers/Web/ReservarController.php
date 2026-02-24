<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Paciente;
use App\Models\EmpresaConfiguracion;
use Config\Services;

/**
 * Vista pública para que pacientes reserven hora (sin login).
 * Flujo: ver nutricionistas y horarios disponibles → elegir slot → datos (RUT, nombre, correo, teléfono) → reserva creada (estado reservada).
 */
class ReservarController extends BaseController
{
    /**
     * Página pública: listar nutricionistas y disponibilidad.
     * GET /reservar?e=empresa_id (e opcional; si no se envía se usa la primera empresa)
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $empresaId = (int) $this->request->getGet('e');
        if ($empresaId <= 0) {
            $row = $db->table('empresa')->select('id')->limit(1)->get()->getRow();
            $empresaId = $row ? (int) $row->id : 0;
        }
        $data = [
            'empresa_id' => $empresaId,
            'nutricionistas' => [],
            'csrf_token' => csrf_hash(),
        ];
        // Nutricionistas: perfil_id = 9 y con al menos un slot libre en detalle_agenda
        $data['nutricionistas'] = $db->table('usuario u')
            ->select('u.id, u.nombre, u.apellido, u.foto')
            ->join('detalle_agenda da', 'da.usuario_id = u.id')
            ->where('u.perfil_id', 9)
            ->where('u.estado', 'A')
            ->where('da.paciente_id', null)
            ->where('da.estado', 1)
            ->groupBy('u.id')
            ->orderBy('u.nombre')
            ->get()
            ->getResult();
        return view('Web/reservar', $data);
    }

    /**
     * Disponibilidad pública (slots sin paciente).
     * Filtro solo por usuario seleccionado y una fecha (=).
     * GET /reservar/disponibilidad?nutricionista_id=2&fecha=2026-02-24
     */
    public function disponibilidad()
    {
        $this->response->setContentType('application/json');
        $nutricionistaId = $this->request->getGet('nutricionista_id');
        $fecha = $this->request->getGet('fecha');
        if (!$fecha) {
            $fecha = date('Y-m-d');
        }
        $db = \Config\Database::connect();
        $builder = $db->table('detalle_agenda da')
            ->select('da.id, da.hora_inicio, da.hora_fin, da.fecha, a.fecha as fecha_agenda, u.nombre as nutricionista_nombre, u.apellido as nutricionista_apellido')
            ->join('agenda a', 'a.id = da.agenda_id')
            ->join('usuario u', 'u.id = da.usuario_id')
            ->where('da.paciente_id', null)
            ->where('da.estado', 1)
            ->where("STR_TO_DATE(COALESCE(da.fecha, a.fecha), '%d-%m-%Y') = ", $fecha);
        if ($nutricionistaId !== null && $nutricionistaId !== '') {
            $builder->where('da.usuario_id', (int) $nutricionistaId);
        }
        $slots = $builder->orderBy('da.hora_inicio')->get()->getResultArray();
        return $this->response->setJSON(['success' => true, 'slots' => $slots]);
    }

    /**
     * Buscar paciente por RUT para autocompletar (solo nombre, teléfono; correo editable).
     * GET /reservar/paciente-por-rut?rut=12345678-9
     */
    public function pacientePorRut()
    {
        $this->response->setContentType('application/json');
        $rut = $this->request->getGet('rut');
        $rut = $rut ? preg_replace('/[^0-9kK]/', '', trim($rut)) : '';
        if (strlen($rut) < 8) {
            return $this->response->setJSON(['found' => false]);
        }
        $db = \Config\Database::connect();
        $paciente = $db->table('pacientes')
            ->select('id, nombre, apellido, telefono, email')
            ->where('estado', 'A')
            ->where("REPLACE(REPLACE(REPLACE(IFNULL(rut_dni,''),'.',''),'-',''),' ','') = ", $rut)
            ->get()
            ->getRow();
        if (!$paciente) {
            return $this->response->setJSON(['found' => false]);
        }
        return $this->response->setJSON([
            'found' => true,
            'nombre' => $paciente->nombre ?? '',
            'apellido' => $paciente->apellido ?? '',
            'telefono' => $paciente->telefono ?? '',
            'email' => $paciente->email ?? '',
        ]);
    }

    /**
     * Crear reserva (paciente reserva desde link público).
     * POST detalle_agenda_id, rut_dni, nombre, apellido, email, telefono
     */
    public function reservar()
    {
        $this->response->setContentType('application/json');
        $detalleAgendaId = (int) $this->request->getPost('detalle_agenda_id');
        $rutDni = trim((string) $this->request->getPost('rut_dni'));
        $nombre = trim((string) $this->request->getPost('nombre'));
        $apellido = trim((string) $this->request->getPost('apellido'));
        $email = trim((string) $this->request->getPost('email'));
        $telefono = trim((string) $this->request->getPost('telefono'));

        if (!$detalleAgendaId || !$rutDni || !$nombre || !$apellido || !$email) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Faltan datos requeridos: RUT, nombre, apellido y correo.',
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $slot = $db->table('detalle_agenda da')
            ->select('da.id, da.usuario_id, da.fecha, da.hora_inicio, da.hora_fin, a.fecha as fecha_agenda')
            ->join('agenda a', 'a.id = da.agenda_id')
            ->where('da.id', $detalleAgendaId)
            ->where('da.paciente_id', null)
            ->where('da.estado', 1)
            ->get()
            ->getRow();

        if (!$slot) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'El horario ya no está disponible. Elija otro.',
            ])->setStatusCode(400);
        }

        $rutNormalized = preg_replace('/[^0-9kK]/', '', $rutDni);
        $pacienteModel = new Paciente();
        $existente = $db->table('pacientes')
            ->where('estado', 'A')
            ->where("REPLACE(REPLACE(REPLACE(IFNULL(rut_dni,''),'.',''),'-',''),' ','') = ", $rutNormalized)
            ->get()
            ->getRow();

        if ($existente) {
            $pacienteId = $existente->id;
            $pacienteModel->update($pacienteId, ['email' => $email]);
        } else {
            $pacienteId = $pacienteModel->insert([
                'nutricionista_id' => $slot->usuario_id,
                'tipo_paciente' => 'particular',
                'nombre' => $nombre,
                'apellido' => $apellido,
                'rut_dni' => $rutDni,
                'email' => $email,
                'telefono' => $telefono ?: null,
                'estado' => 'A',
            ]);
        }

        if (!$pacienteId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al guardar los datos. Intente de nuevo.',
            ])->setStatusCode(500);
        }

        $db->table('detalle_agenda')
            ->where('id', $detalleAgendaId)
            ->update([
                'paciente_id' => $pacienteId,
                'estado_cita' => 'reservada',
                'estado' => 2,
                'origen_agendamiento' => 'paciente',
            ]);

        $this->enviarEmailReservaRecibida($detalleAgendaId, $pacienteId, $slot);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Reserva recibida. Te hemos enviado un correo. El nutricionista revisará tu solicitud y te avisará cuando esté confirmada.',
        ]);
    }

    /**
     * Enviar email "Reserva recibida - pendiente de aprobación".
     */
    private function enviarEmailReservaRecibida(int $detalleAgendaId, int $pacienteId, object $slot): void
    {
        $db = \Config\Database::connect();
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
            'nombrePaciente' => $nombrePaciente,
            'fecha' => $fecha,
            'hora' => $hora,
            'nombreNutricionista' => $nombreNutricionista,
            'mensaje' => $mensajeHtml,
        ]);
        $email = Services::email();
        $email->setFrom(env('email.fromEmail', 'noreply@example.com'), env('email.fromName', 'Agenda'));
        $email->setTo($cita->email);
        $email->setSubject('Reserva recibida - ' . $fecha . ' a las ' . $hora);
        $email->setMessage($html);
        $email->send();
    }
}
