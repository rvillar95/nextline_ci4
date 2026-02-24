# Procesos que envían WhatsApp

Resumen de todos los flujos de la aplicación que envían mensajes por WhatsApp. Dependen de la configuración por empresa/usuario (`enviar_whatsapp`, `enviar_recordatorios_whatsapp`, etc.).

---

## 1. Confirmación de cita ✅

**Qué hace:** Avisa al paciente que su cita quedó agendada/confirmada (con fecha, hora, nutricionista, tipo de consulta).

**Dónde se dispara:**
- **Aprobar reserva** (Agenda) — cuando el nutricionista aprueba una reserva hecha desde el link público.  
  `AgendaController::aprobarReserva()` → `enviarWhatsAppConfirmacion()` → `WhatsAppService::enviarConfirmacionCita()`
- **Confirmar cita** (desde la lista de citas en el dashboard).  
  `AgendaController::confirmarCita()` → `enviarWhatsAppConfirmacion()` → `enviarConfirmacionCita()`
- **Paciente confirma desde el email** (botón “Confirmar” del correo).  
  `AgendaController` (acción de confirmación desde email) → `enviarWhatsAppConfirmacion()` → `enviarConfirmacionCita()`
- **Crear cita desde WhatsApp** (cuando el paciente escribe y el bot crea la cita).  
  `WhatsAppService::procesarMensajeEntrante()` → `enviarConfirmacionCita()`

**Tipo de envío:** Si está configurada la plantilla (`WHATSAPP_PLANTILLA_CONFIRMACION`), se usa plantilla **confirmacion_cita** (header + body con parámetros con nombre). Si no, mensaje de texto libre.

**Config:** `enviar_whatsapp` (empresa/usuario). Ver `PLANTILLAS_WHATSAPP.md` para la plantilla.

---

## 2. Cancelación de cita

**Qué hace:** Avisa al paciente que su cita fue cancelada (fecha, hora, nutricionista y **motivo opcional** que escribe el nutricionista).

**Dónde se dispara:**
- **Cancelar cita desde la lista de citas** (modal con motivo opcional).  
  `AgendaController::cancelarCita()` → `enviarWhatsAppCancelacion($id, $pacienteId, $motivo)` → `WhatsAppService::enviarCancelacionCita($detalleAgendaId, $pacienteId, $motivo)`
- **Paciente cancela desde el email** (botón “Cancelar” del correo).  
  `AgendaController` (acción cancelar desde email) → `enviarCancelacionCita($detalleAgendaId, $pacienteId)` (sin motivo; el paciente cancela).

**Tipo de envío:** Mensaje de texto (no plantilla). El texto incluye motivo si el nutricionista lo escribió en el modal.

**Config:** `enviar_whatsapp`.

---

## 3. Recordatorio de cita

**Qué hace:** Recuerda al paciente su cita X horas antes (fecha, hora, nutricionista).

**Dónde se dispara:**
- **Comando/cron** `php spark enviarRecordatoriosWhatsApp`.  
  `EnviarRecordatoriosWhatsApp` → por cada cita candidata → `WhatsAppService::enviarRecordatorioCita($detalleAgendaId, $horasAntes)`.

Solo se envían recordatorios para citas en estado `confirmada`, con teléfono y que cumplan la ventana de “X horas antes” según `horas_antes_recordatorio` de la configuración.

**Tipo de envío:** Mensaje de texto fijo (no plantilla).

**Config:** `enviar_recordatorios_whatsapp`, `horas_antes_recordatorio` (por usuario/empresa).

---

## 4. Cancelar horas (módulo “Cancelar Horas”)

**Qué hace:** Cancelación masiva de bloques; se notifica por WhatsApp (y email) a los pacientes de las citas canceladas con un mensaje configurable.

**Dónde se dispara:**
- **CancelarHorasController** — al procesar el formulario de cancelación masiva.  
  Usa el mensaje de cancelación de la configuración (variables `[NOMBRE_PACIENTE]`, `[FECHA]`, `[HORA]`, `[NOMBRE_NUTRICIONISTA]`) y llama `WhatsAppService::enviarMensaje()` con ese texto.

**Tipo de envío:** Mensaje de texto (plantilla de configuración con variables).

**Config:** Mensaje de cancelación en configuración de la empresa; no hay “motivo extra” por cita en este flujo (el texto es el mismo para todas las cancelaciones de esa acción).

---

## 5. Respuestas desde el webhook (mensajes entrantes)

**Qué hace:** Responde a mensajes entrantes de WhatsApp (paciente escribe al número de la clínica).

**Dónde se dispara:**
- **WhatsAppWebhookController** recibe el webhook de Meta → `WhatsAppService::procesarMensajeEntrante()`.

**Envíos que se hacen desde ahí:**
- Mensaje de “no encontramos tu número” si el teléfono no está registrado.
- Respuesta al escribir “confirmar”/“confirm”: `procesarConfirmacionCita()` (marca cita confirmada y puede enviar mensaje).
- Respuesta al escribir “cancelar”/“cancel”: `procesarCancelacionCita()` (cancela la cita y puede enviar mensaje).
- Si el mensaje parece una solicitud de cita: intenta agendar y, si hay horario, crea la cita y llama `enviarConfirmacionCita()`.
- Si no hay horario o el mensaje no se reconoce: mensaje de ayuda o “no hay horario disponible”.

**Tipo de envío:** Mensajes de texto (no plantillas).

**Config:** Webhook configurado en Meta; no depende de `enviar_whatsapp` para estas respuestas (son parte del flujo del bot).

---

## Resumen

| Proceso              | Controlador / Origen              | Método principal                         | ¿Plantilla? | Motivo / extra        |
|----------------------|-----------------------------------|------------------------------------------|-------------|------------------------|
| Confirmación cita    | AgendaController, webhook        | `enviarConfirmacionCita`                 | Sí (confirmacion_cita) | —                      |
| Cancelación cita     | AgendaController (lista + email) | `enviarCancelacionCita`                  | No          | Sí (motivo nutricionista en lista) |
| Recordatorio cita    | Comando cron                     | `enviarRecordatorioCita`                 | No          | —                      |
| Cancelar horas       | CancelarHorasController          | `enviarMensaje` con mensaje config       | No          | Mensaje configurable   |
| Webhook (entrantes)  | WhatsAppWebhookController        | `procesarMensajeEntrante` → varios       | No          | —                      |

**Nota:** Para que el motivo del nutricionista aparezca en el WhatsApp de cancelación al cancelar desde la lista, debe pasarse el `motivo` del modal a `enviarWhatsAppCancelacion()` y a `enviarCancelacionCita()` (ver corrección en código).
