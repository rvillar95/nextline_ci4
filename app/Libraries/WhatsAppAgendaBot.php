<?php

namespace App\Libraries;

use App\Models\WhatsAppAgendaSesion;
use App\Services\ReservaPublicaService;

/**
 * Bot conversacional para agendar cita por WhatsApp (misma lógica que /reservar).
 */
class WhatsAppAgendaBot
{
    protected WhatsAppService $whatsapp;
    protected ReservaPublicaService $reserva;
    protected WhatsAppAgendaSesion $sesionModel;
    protected int $empresaId;

    private const TRIGGERS = ['agendar', 'reservar', 'reserva', 'hora', 'cita', 'menu', '1'];
    private const CANCELAR = ['cancelar', 'salir', '0', 'volver'];

    public function __construct(WhatsAppService $whatsapp, ?int $empresaId = null)
    {
        $this->whatsapp = $whatsapp;
        $this->reserva = new ReservaPublicaService();
        $this->sesionModel = new WhatsAppAgendaSesion();
        $this->empresaId = $this->reserva->resolverEmpresaId($empresaId);
    }

    /**
     * @param object|null $paciente Fila paciente si existe
     */
    public function debeAtender(string $texto, ?array $sesion): bool
    {
        if ($sesion && ($sesion['paso'] ?? '') !== 'menu' && ($sesion['paso'] ?? '') !== '') {
            return true;
        }
        $norm = $this->normalizarComando($texto);
        foreach (self::TRIGGERS as $t) {
            if ($norm === $t || str_starts_with($norm, $t . ' ')) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param object|null $paciente
     */
    public function procesar(string $telefono, string $texto, $paciente = null): array
    {
        $texto = trim($texto);
        $norm = $this->normalizarComando($texto);
        $sesion = $this->sesionModel->obtenerPorTelefono($telefono);
        $datos = $this->sesionModel->datosDecodificados($sesion);
        $paso = $sesion['paso'] ?? 'menu';

        $pasoActivo = $sesion && ($sesion['paso'] ?? 'menu') !== 'menu' && ($sesion['paso'] ?? '') !== '';

        if ($this->esCancelar($norm) && $pasoActivo) {
            $this->sesionModel->eliminarPorTelefono($telefono);
            $this->responder($telefono, "Agendamiento cancelado. Cuando quieras reservar, escribe *agendar*.", $paciente);
            return ['success' => true, 'bot' => true, 'cancelado' => true];
        }

        if (!$this->debeAtender($texto, $sesion)) {
            return ['success' => true, 'bot' => false];
        }

        if ($paso === 'menu' || $paso === '' || in_array($norm, self::TRIGGERS, true)) {
            return $this->iniciarFlujo($telefono, $paciente);
        }

        switch ($paso) {
            case 'fecha':
                return $this->pasoFecha($telefono, $texto, $datos, $paciente);
            case 'nutricionista':
                return $this->pasoNutricionista($telefono, $texto, $datos, $paciente);
            case 'hora':
                return $this->pasoHora($telefono, $texto, $datos, $paciente);
            case 'rut':
                return $this->pasoRut($telefono, $texto, $datos, $paciente);
            case 'nombre':
                return $this->pasoNombre($telefono, $texto, $datos, $paciente);
            case 'apellido':
                return $this->pasoApellido($telefono, $texto, $datos, $paciente);
            case 'email':
                return $this->pasoEmail($telefono, $texto, $datos, $paciente);
            case 'confirmar':
                return $this->pasoConfirmar($telefono, $texto, $datos, $paciente);
            default:
                return $this->iniciarFlujo($telefono, $paciente);
        }
    }

    protected function iniciarFlujo(string $telefono, $paciente): array
    {
        $datos = ['telefono_wa' => $telefono];
        if ($paciente) {
            $datos['rut_dni'] = $paciente->rut_dni ?? '';
            $datos['nombre'] = $paciente->nombre ?? '';
            $datos['apellido'] = $paciente->apellido ?? '';
            $datos['email'] = $paciente->email ?? '';
        }
        $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'fecha', $datos);

        $msg = "📅 *Agendar hora*\n\n";
        $msg .= "Indica la *fecha* de tu consulta en formato:\n";
        $msg .= "• AAAA-MM-DD (ej: " . date('Y-m-d', strtotime('+3 days')) . ")\n";
        $msg .= "• o DD/MM/AAAA\n\n";
        $msg .= "Escribe *cancelar* para salir.";

        $this->responder($telefono, $msg, $paciente);
        return ['success' => true, 'bot' => true, 'paso' => 'fecha'];
    }

    protected function pasoFecha(string $telefono, string $texto, array $datos, $paciente): array
    {
        $fecha = $this->reserva->parseFechaEntrada($texto);
        if (!$fecha) {
            $this->responder($telefono, "No reconocí la fecha. Usa AAAA-MM-DD o DD/MM/AAAA (fecha futura).", $paciente);
            return ['success' => true, 'bot' => true];
        }

        $nuts = $this->reserva->nutricionistasConSlotsEnFecha($this->empresaId, $fecha);
        if ($nuts === []) {
            $this->responder($telefono, "No hay profesionales con horarios disponibles el " . $this->reserva->formatearFechaHumana($fecha) . ". Prueba otra fecha.", $paciente);
            return ['success' => true, 'bot' => true];
        }

        $datos['fecha'] = $fecha;
        $datos['nutricionistas'] = array_map(static function (array $n): array {
            return [
                'id'      => (int) $n['id'],
                'nombre'  => trim(($n['nombre'] ?? '') . ' ' . ($n['apellido'] ?? '')),
            ];
        }, $nuts);

        $lines = [];
        $lines[] = "Profesionales disponibles el *" . $this->reserva->formatearFechaHumana($fecha) . "*:\n";
        foreach ($datos['nutricionistas'] as $i => $n) {
            $lines[] = ($i + 1) . '. ' . $n['nombre'];
        }
        $lines[] = "\nResponde con el *número* del profesional.";

        $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'nutricionista', $datos);
        $this->responder($telefono, implode("\n", $lines), $paciente);
        return ['success' => true, 'bot' => true, 'paso' => 'nutricionista'];
    }

    protected function pasoNutricionista(string $telefono, string $texto, array $datos, $paciente): array
    {
        $idx = (int) preg_replace('/\D/', '', $texto);
        $lista = $datos['nutricionistas'] ?? [];
        if ($idx < 1 || $idx > count($lista)) {
            $this->responder($telefono, "Elige un número de la lista (1-" . count($lista) . ").", $paciente);
            return ['success' => true, 'bot' => true];
        }

        $elegido = $lista[$idx - 1];
        $datos['nutricionista_id'] = $elegido['id'];
        $datos['nutricionista_nombre'] = $elegido['nombre'];

        $slots = $this->reserva->disponibilidad($elegido['id'], $datos['fecha']);
        if ($slots === []) {
            $this->responder($telefono, "Ese profesional ya no tiene cupos ese día. Escribe otra *fecha* o *cancelar*.", $paciente);
            $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'fecha', $datos);
            return ['success' => true, 'bot' => true];
        }

        $datos['slots'] = array_map(static function (array $s): array {
            $hi = $s['hora_inicio'] ?? '';
            if (is_string($hi) && strlen($hi) >= 5) {
                $hi = substr($hi, 0, 5);
            }
            return ['id' => (int) $s['id'], 'hora' => $hi];
        }, $slots);

        $lines = ["Horarios con *" . $datos['nutricionista_nombre'] . "*:\n"];
        foreach ($datos['slots'] as $i => $s) {
            $lines[] = ($i + 1) . '. ' . $s['hora'];
        }
        $lines[] = "\nResponde con el *número* del horario.";

        $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'hora', $datos);
        $this->responder($telefono, implode("\n", $lines), $paciente);
        return ['success' => true, 'bot' => true, 'paso' => 'hora'];
    }

    protected function pasoHora(string $telefono, string $texto, array $datos, $paciente): array
    {
        $idx = (int) preg_replace('/\D/', '', $texto);
        $lista = $datos['slots'] ?? [];
        if ($idx < 1 || $idx > count($lista)) {
            $this->responder($telefono, "Elige un número de horario (1-" . count($lista) . ").", $paciente);
            return ['success' => true, 'bot' => true];
        }

        $slot = $lista[$idx - 1];
        $datos['detalle_agenda_id'] = $slot['id'];
        $datos['hora'] = $slot['hora'];

        if (!empty($datos['rut_dni']) && !empty($datos['email'])) {
            return $this->mostrarConfirmacion($telefono, $datos, $paciente);
        }

        $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'rut', $datos);
        $this->responder($telefono, "Indica tu *RUT* (ej: 12345678-9):", $paciente);
        return ['success' => true, 'bot' => true, 'paso' => 'rut'];
    }

    protected function pasoRut(string $telefono, string $texto, array $datos, $paciente): array
    {
        $rut = trim($texto);
        if (strlen(preg_replace('/[^0-9kK]/', '', $rut)) < 8) {
            $this->responder($telefono, "RUT inválido. Inténtalo de nuevo.", $paciente);
            return ['success' => true, 'bot' => true];
        }
        $datos['rut_dni'] = $rut;

        $existente = $this->reserva->pacientePorRut($rut);
        if ($existente) {
            $datos['nombre'] = $existente['nombre'];
            $datos['apellido'] = $existente['apellido'];
            $datos['email'] = $existente['email'];
            if (empty($datos['telefono_wa']) && !empty($existente['telefono'])) {
                $datos['telefono_wa'] = $existente['telefono'];
            }
            return $this->mostrarConfirmacion($telefono, $datos, $paciente);
        }

        $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'nombre', $datos);
        $this->responder($telefono, "¿Cuál es tu *nombre*?", $paciente);
        return ['success' => true, 'bot' => true, 'paso' => 'nombre'];
    }

    protected function pasoNombre(string $telefono, string $texto, array $datos, $paciente): array
    {
        if (strlen(trim($texto)) < 2) {
            $this->responder($telefono, "Escribe tu nombre.", $paciente);
            return ['success' => true, 'bot' => true];
        }
        $datos['nombre'] = trim($texto);
        $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'apellido', $datos);
        $this->responder($telefono, "¿Tu *apellido*?", $paciente);
        return ['success' => true, 'bot' => true, 'paso' => 'apellido'];
    }

    protected function pasoApellido(string $telefono, string $texto, array $datos, $paciente): array
    {
        if (strlen(trim($texto)) < 2) {
            $this->responder($telefono, "Escribe tu apellido.", $paciente);
            return ['success' => true, 'bot' => true];
        }
        $datos['apellido'] = trim($texto);
        $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'email', $datos);
        $this->responder($telefono, "¿Tu *correo electrónico*?", $paciente);
        return ['success' => true, 'bot' => true, 'paso' => 'email'];
    }

    protected function pasoEmail(string $telefono, string $texto, array $datos, $paciente): array
    {
        $email = trim($texto);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->responder($telefono, "Correo no válido. Inténtalo de nuevo.", $paciente);
            return ['success' => true, 'bot' => true];
        }
        $datos['email'] = $email;
        return $this->mostrarConfirmacion($telefono, $datos, $paciente);
    }

    protected function mostrarConfirmacion(string $telefono, array $datos, $paciente): array
    {
        $this->sesionModel->guardarSesion($telefono, $this->empresaId, 'confirmar', $datos);

        $msg = "✅ *Confirma tu reserva*\n\n";
        $msg .= "📅 " . $this->reserva->formatearFechaHumana($datos['fecha']) . "\n";
        $msg .= "🕐 " . ($datos['hora'] ?? '') . "\n";
        $msg .= "👤 " . ($datos['nutricionista_nombre'] ?? '') . "\n";
        $msg .= "🆔 RUT: " . ($datos['rut_dni'] ?? '') . "\n";
        $msg .= "📧 " . ($datos['email'] ?? '') . "\n\n";
        $msg .= "Responde *si* para confirmar o *no* para cancelar.";

        $this->responder($telefono, $msg, $paciente);
        return ['success' => true, 'bot' => true, 'paso' => 'confirmar'];
    }

    protected function pasoConfirmar(string $telefono, string $texto, array $datos, $paciente): array
    {
        $norm = $this->normalizarComando($texto);
        if (in_array($norm, ['no', 'n'], true)) {
            $this->sesionModel->eliminarPorTelefono($telefono);
            $this->responder($telefono, "Reserva no realizada. Escribe *agendar* cuando quieras intentar de nuevo.", $paciente);
            return ['success' => true, 'bot' => true];
        }
        if (!in_array($norm, ['si', 'sí', 's', 'confirmar', 'ok', 'yes'], true)) {
            $this->responder($telefono, "Responde *si* o *no*.", $paciente);
            return ['success' => true, 'bot' => true];
        }

        $resultado = $this->reserva->crearReserva(
            (int) ($datos['detalle_agenda_id'] ?? 0),
            (string) ($datos['rut_dni'] ?? ''),
            (string) ($datos['nombre'] ?? ''),
            (string) ($datos['apellido'] ?? ''),
            (string) ($datos['email'] ?? ''),
            $telefono,
            'whatsapp'
        );

        $this->sesionModel->eliminarPorTelefono($telefono);

        if (!$resultado['success']) {
            $this->responder($telefono, "❌ " . ($resultado['error'] ?? 'No se pudo completar la reserva.') . "\n\nEscribe *agendar* para intentar de nuevo.", $paciente);
            return ['success' => true, 'bot' => true, 'error' => $resultado['error'] ?? ''];
        }

        $msg = "🎉 *Reserva recibida*\n\n";
        $msg .= $this->reserva->formatearFechaHumana($datos['fecha']) . " a las " . ($datos['hora'] ?? $resultado['hora_inicio'] ?? '') . "\n";
        $msg .= "con " . ($datos['nutricionista_nombre'] ?? '') . ".\n\n";
        $msg .= "El nutricionista revisará tu solicitud y te avisará cuando esté confirmada.";

        $nutId = isset($datos['nutricionista_id']) ? (int) $datos['nutricionista_id'] : null;
        $pacienteId = $resultado['paciente_id'] ?? ($paciente->id ?? null);
        $this->responder($telefono, $msg, $paciente, $pacienteId, $nutId);

        return [
            'success'           => true,
            'bot'               => true,
            'reserva_creada'    => true,
            'detalle_agenda_id' => $resultado['detalle_agenda_id'] ?? null,
        ];
    }

    protected function responder(string $telefono, string $mensaje, $paciente = null, ?int $pacienteId = null, ?int $nutricionistaId = null): void
    {
        $pid = $pacienteId ?? ($paciente->id ?? null);
        $nid = $nutricionistaId ?? ($paciente->nutricionista_id ?? null);
        $this->whatsapp->enviarMensaje($telefono, $mensaje, $pid, null, $nid);
    }

    protected function normalizarComando(string $texto): string
    {
        $t = mb_strtolower(trim($texto), 'UTF-8');
        $t = preg_replace('/[^\p{L}\p{N}\s]/u', '', $t) ?? $t;
        return trim($t);
    }

    protected function esCancelar(string $norm): bool
    {
        return in_array($norm, self::CANCELAR, true);
    }
}
