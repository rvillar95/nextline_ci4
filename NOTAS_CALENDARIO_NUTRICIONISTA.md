# 📅 Notas sobre Integración de Calendario - Nutricionista

## 🎯 Punto Clave

**Las notificaciones y recordatorios del calendario siempre llegarán al correo del nutricionista**, no al del paciente.

## 🔄 Flujo Completo

### 1. Autenticación del Nutricionista
- El nutricionista se loguea en la aplicación
- El nutricionista autoriza la conexión con su Google Calendar o Outlook Calendar
- Se guarda el token OAuth2 del nutricionista en `usuario_calendar_tokens`

### 2. Agendamiento de Cita
- Cuando el nutricionista agenda una cita para un paciente:
  - El evento se crea en **el calendario del nutricionista** (usando su token OAuth2)
  - El nutricionista es automáticamente el **organizador** del evento
  - El correo del nutricionista es el asociado a su cuenta de Google Calendar/Outlook

### 3. Notificaciones del Calendario
- **Recordatorios configurados:**
  - 24 horas antes (por email)
  - 1 hora antes (popup)
- **Destinatario:** El correo del nutricionista (organizador del evento)
- **Razón:** El evento pertenece al calendario del nutricionista

### 4. Invitado (Paciente)
- Si el paciente tiene email, se le agrega como **invitado** (attendee) al evento
- El paciente puede recibir una invitación al evento
- Pero las notificaciones principales del calendario van al organizador (nutricionista)

## 📧 Correos Involucrados

### Correo del Nutricionista
- **Uso:** Organizador del evento, recibe todas las notificaciones del calendario
- **Origen:** Cuenta de Google Calendar/Outlook que autorizó la conexión
- **Almacenado en:** Tabla `usuario` (campo `correo`)

### Correo del Paciente
- **Uso:** Invitado al evento (si tiene email)
- **Origen:** Tabla `pacientes` (campo `email`)
- **Función:** Puede recibir invitación, pero no las notificaciones principales del calendario

## 🔔 Notificaciones vs. Recordatorios

### Notificaciones del Calendario (Google Calendar/Outlook)
- **Destinatario:** Nutricionista (organizador)
- **Tipo:** Recordatorios automáticos del calendario (24h y 1h antes)
- **Origen:** Google Calendar API / Outlook Calendar API

### Recordatorios del Sistema (WhatsApp/Email)
- **Destinatario:** Paciente
- **Tipo:** Mensajes de WhatsApp y emails enviados por el sistema
- **Origen:** `WhatsAppService` y `AgendaController::enviarEmailConfirmacion()`

## ✅ Resumen

**Sí, es correcto:** La aplicación siempre tiene a un nutricionista logueado, y cuando agenda una cita:
- El evento se crea en **su calendario**
- Las notificaciones del calendario llegan a **su correo**
- El paciente es invitado (si tiene email), pero no recibe las notificaciones principales del calendario

## 🔗 Archivos Relacionados

- `app/Libraries/CalendarService.php` - Creación de eventos en calendario
- `app/Controllers/Dashboard/AgendaController.php` - Lógica de agendamiento
- `INTEGRACION_CALENDARIO.md` - Documentación completa de la integración
