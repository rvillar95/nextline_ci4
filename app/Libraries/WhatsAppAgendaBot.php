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

    private const ID_AGENDAR = 'agendar';
    private const ID_CANCELAR = 'btn_cancelar';
    private const ID_CONFIRM_SI = 'confirm_si';
    private const ID_CONFIRM_NO = 'confirm_no';
    private const ID_MAS_HORARIOS = 'btn_mas_horarios';
    private const ID_RUT_CONFIRM = 'rut_confirm_si';
    private const ID_RUT_OTRO = 'rut_otro';
    private const ID_RUT_TEL_SI = 'rut_tel_si';
    private const ID_RUT_TEL_NO = 'rut_tel_no';
    private const MAX_FILAS_LISTA_WA = 10;
    /** Días sugeridos al elegir fecha (lista WA permite hasta 10; botones solo 3). */
    private const DIAS_SUGERIDOS = 5;
    private const OPCION_AGENDAR = '1';
    private const PALABRAS_AGENDAR = ['agendar', 'reservar', 'reserva', 'hora', 'cita'];
    private const CANCELAR = ['cancelar', 'salir', '0', 'volver'];

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
        $this->reserva = new ReservaPublicaService();
        $this->sesionModel = new WhatsAppAgendaSesion();
    }

    public static function tieneFlujoAgendaActivo(?array $sesion): bool
    {
        if (!$sesion) {
            return false;
        }
        $paso = $sesion['paso'] ?? 'menu';
        return $paso !== 'menu' && $paso !== '';
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
        $pasoActivo = self::tieneFlujoAgendaActivo($sesion);

        if ($this->esCancelar($texto, $norm) && $pasoActivo) {
            $this->sesionModel->eliminarPorTelefono($telefono);
            $this->responder($telefono, "Agendamiento cancelado.", $paciente);
            return $this->mostrarMenu($telefono, $paciente);
        }

        if (!$pasoActivo) {
            if ($this->esOpcionAgendar($texto, $norm)) {
                return $this->iniciarFlujo($telefono, $paciente);
            }
            return $this->mostrarMenu($telefono, $paciente);
        }

        switch ($paso) {
            case 'rut':
                return $this->pasoRut($telefono, $texto, $datos, $paciente);
            case 'rut_telefono':
                return $this->pasoRutTelefono($telefono, $texto, $datos, $paciente);
            case 'nombre':
                return $this->pasoNombre($telefono, $texto, $datos, $paciente);
            case 'apellido':
                return $this->pasoApellido($telefono, $texto, $datos, $paciente);
            case 'email':
                return $this->pasoEmail($telefono, $texto, $datos, $paciente);
            case 'fecha':
                return $this->pasoFecha($telefono, $texto, $datos, $paciente);
            case 'nutricionista':
                return $this->pasoNutricionista($telefono, $texto, $datos, $paciente);
            case 'hora':
                return $this->pasoHora($telefono, $texto, $datos, $paciente);
            case 'confirmar':
                return $this->pasoConfirmar($telefono, $texto, $datos, $paciente);
            default:
                return $this->mostrarMenu($telefono, $paciente);
        }
    }

    protected function mostrarMenu(string $telefono, $paciente): array
    {
        $datos = ['telefono_wa' => $telefono];
        if ($paciente) {
            $datos['nombre_paciente'] = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
        }
        $this->sesionModel->guardarSesion($telefono, 0, 'menu', $datos);

        $saludo = !empty($datos['nombre_paciente'])
            ? 'Hola ' . trim($datos['nombre_paciente']) . '!'
            : 'Hola!';

        $cuerpo = $saludo . "\n\n👋 Bienvenido a *NutriNext*.\n¿Qué deseas hacer? Toca un botón:";

        $this->responderBotones($telefono, $cuerpo, [
            ['id' => self::ID_AGENDAR, 'title' => '📅 Agendar hora'],
        ], $paciente, $cuerpo . "\n\nResponde *1* o escribe *agendar* para reservar.");

        return ['success' => true, 'bot' => true, 'paso' => 'menu'];
    }

    protected function esOpcionAgendar(string $texto, string $norm): bool
    {
        if ($texto === self::ID_AGENDAR || $norm === self::ID_AGENDAR) {
            return true;
        }
        if ($norm === self::OPCION_AGENDAR) {
            return true;
        }
        foreach (self::PALABRAS_AGENDAR as $palabra) {
            if ($norm === $palabra || str_starts_with($norm, $palabra . ' ')) {
                return true;
            }
        }
        return false;
    }

    protected function iniciarFlujo(string $telefono, $paciente): array
    {
        $datos = $this->datosBaseSesion($telefono, $paciente);
        $this->guardarPaso($telefono, 'rut', $datos);

        $rutPendiente = trim((string) ($datos['rut_pendiente_confirmar'] ?? ''));
        if ($rutPendiente !== '' && strlen(RutChile::limpiar($rutPendiente)) >= 8) {
            return $this->preguntarConfirmacionRut($telefono, $datos, $paciente);
        }

        $this->pedirRutTexto($telefono, $paciente);

        return ['success' => true, 'bot' => true, 'paso' => 'rut'];
    }

    protected function preguntarConfirmacionRut(string $telefono, array $datos, $paciente): array
    {
        $this->guardarPaso($telefono, 'rut', $datos);

        $rutFmt = RutChile::formatear((string) ($datos['rut_pendiente_confirmar'] ?? ''));
        $nombreSaludo = trim((string) ($datos['nombre_saludo'] ?? ''));
        $saludo = $nombreSaludo !== '' ? "Hola {$nombreSaludo}!\n\n" : '';

        $cuerpo = $saludo . "📅 Tu WhatsApp está vinculado a este RUT en nuestra agenda.\n¿Confirmas que es el tuyo?\n*{$rutFmt}*\n\nToca un botón:";
        $fallback = $cuerpo . "\n\nResponde *si* para confirmar o elige *Otro RUT*.";

        $this->responderBotones($telefono, $cuerpo, [
            ['id' => self::ID_RUT_CONFIRM, 'title' => '✅ Sí, es mi RUT'],
            ['id' => self::ID_RUT_OTRO, 'title' => '🔄 Otro RUT'],
        ], $paciente, $fallback);

        return ['success' => true, 'bot' => true, 'paso' => 'rut'];
    }

    protected function pedirRutTexto(string $telefono, $paciente): void
    {
        $this->responder(
            $telefono,
            "📅 *Agendar hora*\n\nIndica tu *RUT* (ej: 12345678-9).\n"
            . "Validamos el dígito verificador y si ya eres paciente.\n"
            . "Escribe *cancelar* para salir.",
            $paciente
        );
    }

    protected function datosBaseSesion(string $telefono, $paciente): array
    {
        $datos = ['telefono_wa' => $telefono];

        // Solo sugerir RUT por teléfono; nombre/correo se cargan tras validar el RUT en BD.
        if ($paciente) {
            $rutTel = trim((string) ($paciente->rut_dni ?? ''));
            if ($rutTel !== '' && strlen(RutChile::limpiar($rutTel)) >= 8 && RutChile::validar($rutTel)) {
                $datos['rut_pendiente_confirmar'] = RutChile::formatear($rutTel);
            }
            $nombreSaludo = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
            if ($nombreSaludo !== '') {
                $datos['nombre_saludo'] = $nombreSaludo;
            }
        }

        return $datos;
    }

    /**
     * Quita datos personales que no deben arrastrarse desde otro RUT o solo el teléfono WA.
     */
    protected function limpiarDatosIdentidad(array $datos): array
    {
        unset(
            $datos['nombre'],
            $datos['apellido'],
            $datos['email'],
            $datos['paciente_bd_id'],
            $datos['nombre_saludo']
        );

        return $datos;
    }

    protected function pasoFecha(string $telefono, string $texto, array $datos, $paciente): array
    {
        $fecha = $this->parseIdFecha($texto) ?? $this->reserva->parseFechaEntrada($texto);
        if (!$fecha) {
            $this->responder($telefono, 'No reconocí la fecha. Elige de la lista o escribe DD/MM/AAAA.', $paciente);
            $this->mostrarSelectorFechas($telefono, $paciente, 'Elige un día disponible:', false);
            return ['success' => true, 'bot' => true];
        }

        return $this->mostrarNutricionistas($telefono, $fecha, $datos, $paciente);
    }

    protected function mostrarSelectorFechas(string $telefono, $paciente, string $prefijo = '', bool $esInicio = true): void
    {
        $filas = $this->filasProximasFechas(self::DIAS_SUGERIDOS);
        if ($esInicio) {
            $cuerpo = "📅 *Agendar hora*\n\nToca *Ver días* y elige una fecha (próximos " . self::DIAS_SUGERIDOS . " días).\n";
            $cuerpo .= "También puedes escribir otra fecha (DD/MM/AAAA).\nEscribe *cancelar* para salir.";
        } else {
            $cuerpo = trim($prefijo) !== '' ? trim($prefijo) . "\n\n" : '';
            $cuerpo .= 'Toca *Ver días* y elige otra fecha.';
        }
        $fallback = $cuerpo . "\n\n";
        foreach ($filas as $i => $f) {
            $fallback .= ($i + 1) . '. ' . ($f['title'] ?? '') . ' — ' . ($f['description'] ?? '') . "\n";
        }
        $fallback .= "\nResponde con el número o escribe la fecha.";

        $this->responderLista($telefono, $cuerpo, 'Ver días', $filas, $paciente, $fallback, 'Fechas');
    }

    protected function mostrarNutricionistas(string $telefono, string $fecha, array $datos, $paciente): array
    {
        $nuts = $this->reserva->nutricionistasConSlotsEnFecha(null, $fecha);
        if ($nuts === []) {
            $this->responder($telefono, 'No hay profesionales con cupos el ' . $this->reserva->formatearFechaHumana($fecha) . '. Prueba otro día.', $paciente);
            $this->mostrarSelectorFechas($telefono, $paciente, 'Elige otra fecha:', false);
            return ['success' => true, 'bot' => true, 'paso' => 'fecha'];
        }

        $datos['fecha'] = $fecha;
        $datos['nutricionistas'] = array_map(static function (array $n): array {
            return [
                'id'         => (int) $n['id'],
                'nombre'     => trim(($n['nombre'] ?? '') . ' ' . ($n['apellido'] ?? '')),
                'empresa_id' => (int) ($n['empresa_id'] ?? 0),
            ];
        }, $nuts);

        $this->sesionModel->guardarSesion($telefono, 0, 'nutricionista', $datos);

        $cuerpo = 'Profesionales disponibles el *' . $this->reserva->formatearFechaHumana($fecha) . "*:\nToca *Ver profesionales* y elige uno.";
        $filas = [];
        foreach ($datos['nutricionistas'] as $i => $n) {
            $filas[] = [
                'id' => 'nut_' . $n['id'],
                'title' => $n['nombre'],
                'description' => 'Nutricionista',
            ];
        }

        $fallback = $cuerpo . "\n\n";
        foreach ($datos['nutricionistas'] as $i => $n) {
            $fallback .= ($i + 1) . '. ' . $n['nombre'] . "\n";
        }
        $fallback .= "\nResponde con el número.";

        $this->responderLista($telefono, $cuerpo, 'Ver profesionales', $filas, $paciente, $fallback, 'Nutricionistas');

        return ['success' => true, 'bot' => true, 'paso' => 'nutricionista'];
    }

    protected function pasoNutricionista(string $telefono, string $texto, array $datos, $paciente): array
    {
        $elegido = $this->resolverOpcionLista($texto, $datos['nutricionistas'] ?? [], 'nut_', 'id');
        if (!$elegido) {
            $this->responder($telefono, 'Elige un profesional de la lista o responde con su número.', $paciente);
            return $this->mostrarNutricionistas($telefono, $datos['fecha'], $datos, $paciente);
        }
        $datos['nutricionista_id'] = $elegido['id'];
        $datos['nutricionista_nombre'] = $elegido['nombre'];
        $datos['empresa_id'] = (int) ($elegido['empresa_id'] ?? 0);
        if ($datos['empresa_id'] <= 0) {
            $datos['empresa_id'] = $this->reserva->empresaIdDelNutricionista($elegido['id']);
        }

        $slots = $this->reserva->disponibilidad($elegido['id'], $datos['fecha']);
        if ($slots === []) {
            $this->responder($telefono, "Ese profesional ya no tiene cupos ese día. Escribe otra *fecha* o *cancelar*.", $paciente);
            unset($datos['empresa_id'], $datos['nutricionista_id'], $datos['nutricionista_nombre']);
            $this->sesionModel->guardarSesion($telefono, 0, 'fecha', $datos);
            return ['success' => true, 'bot' => true];
        }

        $datos['slots_offset'] = 0;
        $datos['slots'] = array_map(static function (array $s): array {
            $hi = $s['hora_inicio'] ?? '';
            if (is_string($hi) && strlen($hi) >= 5) {
                $hi = substr($hi, 0, 5);
            }
            $hf = $s['hora_fin'] ?? '';
            if (is_string($hf) && strlen($hf) >= 5) {
                $hf = substr($hf, 0, 5);
            }
            return ['id' => (int) $s['id'], 'hora' => $hi, 'hora_fin' => $hf];
        }, $slots);

        $this->sesionModel->guardarSesion($telefono, (int) $datos['empresa_id'], 'hora', $datos);

        return $this->mostrarHorarios($telefono, $datos, $paciente);
    }

    protected function mostrarHorarios(string $telefono, array $datos, $paciente): array
    {
        $todos = $datos['slots'] ?? [];
        $offset = (int) ($datos['slots_offset'] ?? 0);
        $restantes = max(0, count($todos) - $offset);
        $hayMasPaginas = $restantes > self::MAX_FILAS_LISTA_WA;
        $cuposEnPagina = $hayMasPaginas ? self::MAX_FILAS_LISTA_WA - 1 : min(self::MAX_FILAS_LISTA_WA, $restantes);
        $pagina = array_slice($todos, $offset, $cuposEnPagina);

        $cuerpo = 'Horarios con *' . ($datos['nutricionista_nombre'] ?? '') . "*";
        if ($offset > 0) {
            $cuerpo .= ' (página ' . (int) (floor($offset / (self::MAX_FILAS_LISTA_WA - 1)) + 1) . ')';
        }
        $cuerpo .= ":\nToca *Ver horarios* y elige en la lista.";

        $filas = $this->filasDesdeSlots($pagina);
        if ($hayMasPaginas) {
            $filas[] = [
                'id' => self::ID_MAS_HORARIOS,
                'title' => '➡ Más horarios',
                'description' => 'Ver siguientes cupos',
            ];
        }

        $fallback = $this->textoFallbackHorariosPagina($cuerpo, $pagina, $hayMasPaginas, count($todos) - $offset - $cuposEnPagina);

        $this->responderLista($telefono, $cuerpo, 'Ver horarios', $filas, $paciente, $fallback, 'Horarios');
        $this->guardarPaso($telefono, 'hora', $datos);

        return ['success' => true, 'bot' => true, 'paso' => 'hora'];
    }

    /**
     * @param array<int, array{id: int, hora: string, hora_fin?: string}> $slots
     * @return array<int, array{id: string, title: string, description: string}>
     */
    protected function filasDesdeSlots(array $slots): array
    {
        $filas = [];
        $contadorPorHorario = [];
        foreach ($slots as $s) {
            $hora = $s['hora'] ?? '';
            $hf = $s['hora_fin'] ?? '';
            $clave = $hora . '|' . $hf;
            $contadorPorHorario[$clave] = ($contadorPorHorario[$clave] ?? 0) + 1;
            $n = $contadorPorHorario[$clave];

            $title = $hora;
            if ($hf !== '' && $hf !== $hora) {
                $title = $hora . '-' . $hf;
            }
            if ($n > 1) {
                $sufijo = ' (' . $n . ')';
                $title = mb_substr($title, 0, 24 - mb_strlen($sufijo)) . $sufijo;
            } else {
                $title = mb_substr($title, 0, 24);
            }

            $desc = $n > 1 ? 'Otro cupo mismo horario' : 'Turno disponible';

            $filas[] = [
                'id' => 'slot_' . $s['id'],
                'title' => $title,
                'description' => mb_substr($desc, 0, 72),
            ];
        }
        return $filas;
    }

    /**
     * @param array<int, array{id: int, hora: string, hora_fin?: string}> $pagina
     */
    protected function textoFallbackHorariosPagina(string $cuerpo, array $pagina, bool $hayMas, int $restantesSinVer): string
    {
        $texto = $cuerpo . "\n\n";
        foreach ($pagina as $i => $s) {
            $linea = ($i + 1) . '. ' . ($s['hora'] ?? '');
            if (!empty($s['hora_fin']) && ($s['hora_fin'] ?? '') !== ($s['hora'] ?? '')) {
                $linea .= ' - ' . $s['hora_fin'];
            }
            $texto .= $linea . "\n";
        }
        if ($hayMas) {
            $texto .= "\n➡ Escribe *más* o elige *Más horarios* en la lista (quedan {$restantesSinVer} cupos).";
        } else {
            $texto .= "\nResponde con el número del horario.";
        }
        return $texto;
    }

    protected function pasoHora(string $telefono, string $texto, array $datos, $paciente): array
    {
        if ($texto === self::ID_MAS_HORARIOS || $this->normalizarComando($texto) === 'mas') {
            $offset = (int) ($datos['slots_offset'] ?? 0);
            $datos['slots_offset'] = $offset + (self::MAX_FILAS_LISTA_WA - 1);
            return $this->mostrarHorarios($telefono, $datos, $paciente);
        }

        $slot = $this->resolverOpcionLista($texto, $datos['slots'] ?? [], 'slot_', 'id');
        if (!$slot) {
            $this->responder($telefono, 'Elige un horario de la lista o responde con su número.', $paciente);
            return $this->mostrarHorarios($telefono, $datos, $paciente);
        }
        $datos['detalle_agenda_id'] = $slot['id'];
        $datos['hora'] = $slot['hora'];

        if (!$this->datosIdentidadCompletos($datos)) {
            $this->responder($telefono, 'Faltan tus datos. Indica tu *RUT* para continuar.', $paciente);
            $this->guardarPaso($telefono, 'rut', $datos);
            return ['success' => true, 'bot' => true, 'paso' => 'rut'];
        }

        return $this->mostrarConfirmacion($telefono, $datos, $paciente);
    }

    protected function pasoRut(string $telefono, string $texto, array $datos, $paciente): array
    {
        if ($texto === self::ID_RUT_OTRO) {
            $datos = $this->limpiarDatosIdentidad($datos);
            unset($datos['rut_dni'], $datos['rut_pendiente_confirmar']);
            $this->guardarPaso($telefono, 'rut', $datos);
            $this->pedirRutTexto($telefono, $paciente);
            return ['success' => true, 'bot' => true, 'paso' => 'rut'];
        }

        $norm = $this->normalizarComando($texto);
        $rutPendiente = trim((string) ($datos['rut_pendiente_confirmar'] ?? ''));

        if ($texto === self::ID_RUT_CONFIRM
            || ($rutPendiente !== '' && in_array($norm, ['si', 'sí', 's', 'ok', 'yes'], true))) {
            if ($rutPendiente === '') {
                $this->responder($telefono, 'No tenemos un RUT para confirmar. Escríbelo, por favor.', $paciente);
                return ['success' => true, 'bot' => true];
            }
            if (!RutChile::validar($rutPendiente)) {
                $this->responder($telefono, 'El RUT registrado no es válido. Escríbelo de nuevo (ej: 12345678-9).', $paciente);
                $datos = $this->limpiarDatosIdentidad($datos);
                unset($datos['rut_pendiente_confirmar'], $datos['rut_dni']);
                $this->guardarPaso($telefono, 'rut', $datos);
                $this->pedirRutTexto($telefono, $paciente);
                return ['success' => true, 'bot' => true];
            }

            $datos = $this->limpiarDatosIdentidad($datos);
            $datos['rut_dni'] = RutChile::formatear($rutPendiente);
            unset($datos['rut_pendiente_confirmar']);

            return $this->continuarTrasValidarRut($telefono, $datos, $paciente);
        }

        $rut = trim($texto);
        if (strlen(RutChile::limpiar($rut)) < 8) {
            $this->responder($telefono, 'RUT incompleto. Ejemplo: 12345678-9', $paciente);
            return ['success' => true, 'bot' => true];
        }
        if (!RutChile::validar($rut)) {
            $this->responder(
                $telefono,
                'El dígito verificador del RUT no coincide. Revísalo e inténtalo de nuevo (ej: 12345678-9).',
                $paciente
            );
            return ['success' => true, 'bot' => true];
        }

        $datos = $this->limpiarDatosIdentidad($datos);
        unset($datos['rut_pendiente_confirmar']);
        $datos['rut_dni'] = RutChile::formatear($rut);

        return $this->continuarTrasValidarRut($telefono, $datos, $paciente);
    }

    /**
     * Tras ingresar RUT: valida coherencia con el WhatsApp antes de seguir el flujo.
     */
    protected function continuarTrasValidarRut(string $telefono, array $datos, $paciente): array
    {
        $rut = (string) ($datos['rut_dni'] ?? '');
        $conflicto = $this->reserva->evaluarConflictoTelefonoRut($telefono, $rut);

        if ($conflicto !== null) {
            if (($conflicto['tipo'] ?? '') === 'bloquear') {
                return $this->rechazarTelefonoIncorrecto($telefono, $datos, $paciente, $conflicto);
            }

            $datos['rut_conflicto_vinculado'] = $conflicto['rut_vinculado_fmt'] ?? '';
            $datos['rut_conflicto_nombre'] = $conflicto['nombre_vinculado'] ?? '';
            $this->guardarPaso($telefono, 'rut_telefono', $datos);

            return $this->preguntarRutCorrespondeTelefono($telefono, $datos, $paciente);
        }

        return $this->continuarTrasIdentificarRut($telefono, $datos, $paciente);
    }

    protected function preguntarRutCorrespondeTelefono(string $telefono, array $datos, $paciente): array
    {
        $rutIngresado = (string) ($datos['rut_dni'] ?? '');
        $rutVinculado = (string) ($datos['rut_conflicto_vinculado'] ?? '');
        $nombreVinc = trim((string) ($datos['rut_conflicto_nombre'] ?? ''));

        $cuerpo = "⚠️ *Verificación de identidad*\n\n";
        $cuerpo .= "Este WhatsApp está registrado en la agenda";
        if ($nombreVinc !== '') {
            $cuerpo .= " a nombre de *{$nombreVinc}*";
        }
        $cuerpo .= " con el RUT *{$rutVinculado}*.\n\n";
        $cuerpo .= "Usted ingresó el RUT *{$rutIngresado}*.\n\n";
        $cuerpo .= "¿El RUT *{$rutIngresado}* corresponde a *este mismo* número de WhatsApp?\n\nToca un botón:";

        $fallback = $cuerpo . "\n\nResponde *si* o *no*.";

        $this->responderBotones($telefono, $cuerpo, [
            ['id' => self::ID_RUT_TEL_SI, 'title' => '✅ Sí, es mi RUT'],
            ['id' => self::ID_RUT_TEL_NO, 'title' => '❌ No, otro número'],
        ], $paciente, $fallback);

        return ['success' => true, 'bot' => true, 'paso' => 'rut_telefono'];
    }

    protected function pasoRutTelefono(string $telefono, string $texto, array $datos, $paciente): array
    {
        $norm = $this->normalizarComando($texto);
        $esSi = $texto === self::ID_RUT_TEL_SI
            || in_array($norm, ['si', 'sí', 's', 'ok', 'yes'], true);
        $esNo = $texto === self::ID_RUT_TEL_NO
            || in_array($norm, ['no', 'n'], true);

        if (!$esSi && !$esNo) {
            return $this->preguntarRutCorrespondeTelefono($telefono, $datos, $paciente);
        }

        unset($datos['rut_conflicto_vinculado'], $datos['rut_conflicto_nombre']);

        if ($esNo) {
            $existente = $this->reserva->pacientePorRut((string) ($datos['rut_dni'] ?? ''));

            return $this->rechazarTelefonoIncorrecto(
                $telefono,
                $datos,
                $paciente,
                [
                    'tipo'         => 'bloquear',
                    'telefono_rut' => $existente['telefono'] ?? '',
                    'nombre_rut'   => $existente
                        ? trim(($existente['nombre'] ?? '') . ' ' . ($existente['apellido'] ?? ''))
                        : '',
                ]
            );
        }

        // Afirma que el RUT ingresado es de este WhatsApp: solo si coincide con ficha del RUT o no hay otro RUT en este número.
        $rut = (string) ($datos['rut_dni'] ?? '');
        $existente = $this->reserva->pacientePorRut($rut);
        if ($existente && !\App\Models\Paciente::telefonosCoinciden($existente['telefono'] ?? '', $telefono)) {
            return $this->rechazarTelefonoIncorrecto($telefono, $datos, $paciente, [
                'tipo'         => 'bloquear',
                'telefono_rut' => $existente['telefono'],
                'nombre_rut'   => trim(($existente['nombre'] ?? '') . ' ' . ($existente['apellido'] ?? '')),
            ]);
        }

        $conflicto = $this->reserva->evaluarConflictoTelefonoRut($telefono, $rut);
        if ($conflicto !== null) {
            return $this->rechazarTelefonoIncorrecto($telefono, $datos, $paciente, [
                'tipo'                => 'bloquear',
                'telefono_rut'        => '',
                'mensaje_personalizado' => 'Este WhatsApp ya está vinculado al RUT *'
                    . ($conflicto['rut_vinculado_fmt'] ?? '') . '*. '
                    . 'No puede registrar otro RUT desde este número. '
                    . 'Escriba desde el teléfono registrado para el RUT que desea agendar, '
                    . 'o use el RUT asociado a este WhatsApp.',
            ]);
        }

        $this->guardarPaso($telefono, 'rut', $datos);

        return $this->continuarTrasIdentificarRut($telefono, $datos, $paciente);
    }

    protected function rechazarTelefonoIncorrecto(
        string $telefono,
        array $datos,
        $paciente,
        array $info
    ): array {
        $this->sesionModel->eliminarPorTelefono($telefono);

        if (!empty($info['mensaje_personalizado'])) {
            $msg = "🚫 " . $info['mensaje_personalizado'];
        } else {
            $rutFmt = (string) ($datos['rut_dni'] ?? '');
            $telRut = trim((string) ($info['telefono_rut'] ?? ''));
            $nombreRut = trim((string) ($info['nombre_rut'] ?? ''));

            $msg = "🚫 *No puede agendar con este WhatsApp*\n\n";
            $msg .= "El RUT *{$rutFmt}*";
            if ($nombreRut !== '') {
                $msg .= " ({$nombreRut})";
            }
            $msg .= " no corresponde al teléfono desde el que escribe.\n\n";
            if ($telRut !== '' && !\App\Models\Paciente::telefonosCoinciden($telRut, $telefono)) {
                $msg .= "Para agendar con ese RUT, escriba desde el WhatsApp registrado en su ficha:\n*{$telRut}*\n\n";
            } elseif ($telRut !== '') {
                $msg .= "En la agenda este número ya está asociado a otra persona (otro RUT). "
                    . "No puede crear otra ficha con el mismo WhatsApp.\n\n";
            } else {
                $msg .= "Para agendar con ese RUT, use el teléfono que tenga registrado en su ficha con el nutricionista.\n\n";
            }
            $msg .= "Si necesita corregir sus datos, contacte a su nutricionista.";
        }

        $msg .= "\n\nEscribe *agendar* cuando quieras intentar de nuevo con el número correcto.";

        $this->responder($telefono, $msg, $paciente);

        return $this->mostrarMenu($telefono, $paciente);
    }

    protected function continuarTrasIdentificarRut(string $telefono, array $datos, $paciente): array
    {
        $rut = (string) ($datos['rut_dni'] ?? '');
        $existente = $this->reserva->pacientePorRut($rut);

        if ($existente) {
            $datos['paciente_bd_id'] = $existente['id'];
            $datos['nombre'] = $existente['nombre'];
            $datos['apellido'] = $existente['apellido'];
            $datos['email'] = trim((string) ($existente['email'] ?? ''));

            $nombre = trim($datos['nombre'] . ' ' . $datos['apellido']);

            if ($datos['email'] === '' || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $this->guardarPaso($telefono, 'email', $datos);
                $this->responder(
                    $telefono,
                    "✅ Te encontramos en el sistema como *{$nombre}*.\n\n"
                    . $this->textoPedirCorreo(),
                    $paciente
                );
                return ['success' => true, 'bot' => true, 'paso' => 'email'];
            }

            $this->guardarPaso($telefono, 'fecha', $datos);
            $this->mostrarSelectorFechas(
                $telefono,
                $paciente,
                "✅ Te encontramos en el sistema, {$nombre}.\nAhora elige el día de tu consulta:",
                false
            );
            return ['success' => true, 'bot' => true, 'paso' => 'fecha'];
        }

        $this->guardarPaso($telefono, 'nombre', $datos);
        $this->responder(
            $telefono,
            "📝 No encontramos el RUT *" . ($datos['rut_dni'] ?? '') . "* en la agenda.\n\n"
            . "Registraremos tus datos en *3 pasos* (nombre → apellido → correo).\n\n"
            . "━━ *Paso 1 de 3: Nombre(s)* ━━\n"
            . "Escribe solo tu(s) *nombre(s) de pila*, *sin* apellido.\n"
            . "• Un nombre: *María*\n"
            . "• Dos nombres: *María José*\n"
            . "❌ No escribas el apellido aquí.",
            $paciente
        );
        return ['success' => true, 'bot' => true, 'paso' => 'nombre'];
    }

    protected function textoPedirCorreo(): string
    {
        return "━━ *Correo electrónico* ━━\n"
            . "Escríbelo para enviarte la *confirmación* y *avisos* de tu cita "
            . "(recordatorios o cambios de horario).\n\n"
            . "🔒 Solo lo usamos para tu atención en NutriNext; *no* enviamos publicidad.\n"
            . "Ej: *nombre@correo.cl*";
    }

    protected function datosIdentidadCompletos(array $datos): bool
    {
        return trim((string) ($datos['rut_dni'] ?? '')) !== ''
            && trim((string) ($datos['nombre'] ?? '')) !== ''
            && trim((string) ($datos['apellido'] ?? '')) !== ''
            && trim((string) ($datos['email'] ?? '')) !== ''
            && filter_var(trim((string) ($datos['email'])), FILTER_VALIDATE_EMAIL);
    }

    protected function pasoNombre(string $telefono, string $texto, array $datos, $paciente): array
    {
        $nombre = trim($texto);
        if (strlen($nombre) < 2) {
            $this->responder(
                $telefono,
                "Escribe al menos tu primer *nombre* (solo nombres, sin apellido).\nEj: *Ana*",
                $paciente
            );
            return ['success' => true, 'bot' => true];
        }
        $datos['nombre'] = $nombre;
        $this->guardarPaso($telefono, 'apellido', $datos);
        $this->responder(
            $telefono,
            "✅ Nombre registrado: *{$nombre}*\n\n"
            . "━━ *Paso 2 de 3: Apellido(s)* ━━\n"
            . "Escribe solo tu(s) *apellido(s)*, *sin* repetir el nombre.\n"
            . "• Un apellido: *Pérez*\n"
            . "• Dos apellidos: *González Pérez*\n"
            . "❌ No escribas otra vez *{$nombre}*.",
            $paciente
        );
        return ['success' => true, 'bot' => true, 'paso' => 'apellido'];
    }

    protected function pasoApellido(string $telefono, string $texto, array $datos, $paciente): array
    {
        $apellido = trim($texto);
        if (strlen($apellido) < 2) {
            $this->responder(
                $telefono,
                "Escribe al menos un *apellido* (sin incluir tu nombre otra vez).\nEj: *Silva*",
                $paciente
            );
            return ['success' => true, 'bot' => true];
        }
        $datos['apellido'] = $apellido;
        $this->guardarPaso($telefono, 'email', $datos);
        $nombreCompleto = trim(($datos['nombre'] ?? '') . ' ' . $apellido);
        $this->responder(
            $telefono,
            "✅ Apellido registrado: *{$apellido}*\n"
            . "Te registramos como: *{$nombreCompleto}*\n\n"
            . "━━ *Paso 3 de 3* ━━\n"
            . $this->textoPedirCorreo(),
            $paciente
        );
        return ['success' => true, 'bot' => true, 'paso' => 'email'];
    }

    protected function pasoEmail(string $telefono, string $texto, array $datos, $paciente): array
    {
        $email = trim($texto);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->responder(
                $telefono,
                "Ese correo no parece válido.\nEscríbelo completo, por ejemplo: *nombre@correo.cl*\n\n"
                . $this->textoPedirCorreo(),
                $paciente
            );
            return ['success' => true, 'bot' => true];
        }
        $datos['email'] = $email;
        $this->guardarPaso($telefono, 'fecha', $datos);
        $nombreCompleto = trim(($datos['nombre'] ?? '') . ' ' . ($datos['apellido'] ?? ''));
        $this->mostrarSelectorFechas(
            $telefono,
            $paciente,
            "✅ Listo, *{$nombreCompleto}*.\nCorreo guardado para avisos de tu cita.\n\nElige el *día* de tu consulta:",
            false
        );
        return ['success' => true, 'bot' => true, 'paso' => 'fecha'];
    }

    protected function mostrarConfirmacion(string $telefono, array $datos, $paciente): array
    {
        $this->guardarPaso($telefono, 'confirmar', $datos);

        $msg = "✅ *Confirma tu reserva*\n\n";
        $msg .= "📅 " . $this->reserva->formatearFechaHumana($datos['fecha']) . "\n";
        $msg .= "🕐 " . ($datos['hora'] ?? '') . "\n";
        $msg .= "👤 " . ($datos['nutricionista_nombre'] ?? '') . "\n";
        $msg .= "👤 " . trim(($datos['nombre'] ?? '') . ' ' . ($datos['apellido'] ?? '')) . "\n";
        $msg .= "🆔 RUT: " . ($datos['rut_dni'] ?? '') . "\n";
        $msg .= "📱 WhatsApp: " . ($datos['telefono_wa'] ?? '') . "\n";
        $msg .= "📧 " . ($datos['email'] ?? '') . "\n\n";
        $this->responderBotones($telefono, $msg . "\n\n¿Confirmas la reserva?", [
            ['id' => self::ID_CONFIRM_SI, 'title' => '✅ Sí, confirmar'],
            ['id' => self::ID_CONFIRM_NO, 'title' => '❌ No, cancelar'],
        ], $paciente, $msg . "\n\nResponde *si* o *no*.");

        return ['success' => true, 'bot' => true, 'paso' => 'confirmar'];
    }

    protected function pasoConfirmar(string $telefono, string $texto, array $datos, $paciente): array
    {
        $norm = $this->normalizarComando($texto);
        if ($texto === self::ID_CONFIRM_NO || in_array($norm, ['no', 'n'], true)) {
            $this->sesionModel->eliminarPorTelefono($telefono);
            return $this->mostrarMenu($telefono, $paciente);
        }
        if ($texto !== self::ID_CONFIRM_SI && !in_array($norm, ['si', 'sí', 's', 'confirmar', 'ok', 'yes'], true)) {
            return $this->mostrarConfirmacion($telefono, $datos, $paciente);
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

    protected function guardarPaso(string $telefono, string $paso, array $datos): void
    {
        $empresaId = (int) ($datos['empresa_id'] ?? 0);
        $this->sesionModel->guardarSesion($telefono, $empresaId, $paso, $datos);
    }

    protected function responder(string $telefono, string $mensaje, $paciente = null, ?int $pacienteId = null, ?int $nutricionistaId = null): void
    {
        $pid = $pacienteId ?? ($paciente->id ?? null);
        $nid = $nutricionistaId ?? ($paciente->nutricionista_id ?? null);
        $this->whatsapp->enviarMensaje($telefono, $mensaje, $pid, null, $nid);
    }

    protected function responderBotones(string $telefono, string $cuerpo, array $botones, $paciente, string $fallbackTexto): void
    {
        $pid = $paciente->id ?? null;
        $nid = $paciente->nutricionista_id ?? null;
        $r = $this->whatsapp->enviarBotones($telefono, $cuerpo, $botones, $pid, $nid);
        if (empty($r['success'])) {
            $this->responder($telefono, $fallbackTexto, $paciente, $pid, $nid);
        }
    }

    protected function responderLista(
        string $telefono,
        string $cuerpo,
        string $botonLista,
        array $filas,
        $paciente,
        string $fallbackTexto,
        string $tituloSeccion = 'Opciones'
    ): void {
        $pid = $paciente->id ?? null;
        $nid = $paciente->nutricionista_id ?? null;
        $r = $this->whatsapp->enviarLista($telefono, $cuerpo, $botonLista, $filas, $pid, $nid, $tituloSeccion);
        if (empty($r['success'])) {
            $this->responder($telefono, $fallbackTexto, $paciente, $pid, $nid);
        }
    }

    /**
     * Próximos N días para lista interactiva (id f_YYYY-MM-DD).
     *
     * @return array<int, array{id: string, title: string, description: string}>
     */
    protected function filasProximasFechas(int $cantidad = 5): array
    {
        $diasSem = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        $filas = [];
        $cantidad = max(1, min($cantidad, self::MAX_FILAS_LISTA_WA));
        for ($i = 1; $i <= $cantidad; $i++) {
            $ymd = date('Y-m-d', strtotime("+{$i} day"));
            $w = (int) date('w', strtotime($ymd));
            $filas[] = [
                'id' => 'f_' . $ymd,
                'title' => $diasSem[$w] . ' ' . date('d/m', strtotime($ymd)),
                'description' => $this->reserva->formatearFechaHumana($ymd),
            ];
        }
        return $filas;
    }

    protected function parseIdFecha(string $texto): ?string
    {
        if (preg_match('/^f_(\d{4}-\d{2}-\d{2})$/', trim($texto), $m)) {
            return $this->reserva->parseFechaEntrada($m[1]);
        }
        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $lista
     */
    protected function resolverOpcionLista(string $texto, array $lista, string $prefijo, string $campoId): ?array
    {
        $texto = trim($texto);
        if (preg_match('/^' . preg_quote($prefijo, '/') . '(\d+)$/', $texto, $m)) {
            $id = (int) $m[1];
            foreach ($lista as $item) {
                if ((int) ($item[$campoId] ?? 0) === $id) {
                    return $item;
                }
            }
        }
        $idx = (int) preg_replace('/\D/', '', $texto);
        if ($idx >= 1 && $idx <= count($lista)) {
            return $lista[$idx - 1];
        }
        return null;
    }

    protected function normalizarComando(string $texto): string
    {
        $t = mb_strtolower(trim($texto), 'UTF-8');
        $t = preg_replace('/[^\p{L}\p{N}\s]/u', '', $t) ?? $t;
        return trim($t);
    }

    protected function esCancelar(string $texto, string $norm): bool
    {
        return $texto === self::ID_CANCELAR || in_array($norm, self::CANCELAR, true);
    }
}
