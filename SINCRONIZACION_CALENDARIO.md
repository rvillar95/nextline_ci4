# 🔄 Sincronización de Calendario con Google Calendar/Outlook

## 🎯 Funcionalidad

El sistema sincroniza automáticamente las citas confirmadas con el calendario del nutricionista (Google Calendar o Outlook).

## ⏰ Cuándo se Crea el Evento

**El evento se crea en el calendario solo cuando la cita es confirmada**, no cuando se agenda inicialmente.

### Flujo Completo:

1. **Nutricionista agenda una cita** → Estado: `pendiente`
   - Se envía email al paciente con botones de confirmar/cancelar
   - ❌ **NO se crea evento en el calendario aún**

2. **Paciente confirma desde el email** → Estado: `confirmada`
   - Se envía WhatsApp de confirmación al paciente
   - ✅ **Se crea evento en el calendario del nutricionista**

3. **Alternativa: Nutricionista confirma manualmente** → Estado: `confirmada`
   - Se envía WhatsApp de confirmación al paciente
   - ✅ **Se crea evento en el calendario del nutricionista**

## 📅 Sincronización

### Evento Creado Incluye:
- **Título:** "Consulta: {nombre_paciente}"
- **Fecha y hora:** De la cita agendada
- **Descripción:** Información completa del paciente, tipo de consulta, modalidad, motivo
- **Ubicación:** "Consultorio" o "Online" según modalidad
- **Invitado:** Paciente (si tiene email)
- **Recordatorios:**
  - 24 horas antes (por email al nutricionista)
  - 1 hora antes (popup en el calendario)

### Notificaciones del Calendario

**Las notificaciones del calendario siempre llegan al correo del nutricionista** porque:
- El evento se crea en su calendario (usando su token OAuth2)
- El nutricionista es automáticamente el **organizador** del evento
- El correo del nutricionista es el asociado a su cuenta de Google Calendar/Outlook

## 🔄 Sincronización Bidireccional (Futuro)

Actualmente la sincronización es **unidireccional** (de la aplicación al calendario). 

**Posibles mejoras futuras:**
- Sincronización bidireccional (si se modifica en Google Calendar, actualizar en la aplicación)
- Sincronización de eventos existentes en el calendario
- Detección de conflictos de horarios

## 📝 Código Relacionado

### Creación del Evento

**Ubicación:** `app/Controllers/Dashboard/AgendaController.php`

1. **Cuando el paciente confirma desde el email:**
   ```php
   // Línea ~1337-1348
   confirmarDesdeEmail() {
       // ... actualizar estado a 'confirmada'
       // ... enviar WhatsApp
       // ✅ Crear evento en calendario
       $this->crearEventoCalendario($detalleAgendaId, $usuarioId);
   }
   ```

2. **Cuando el nutricionista confirma manualmente:**
   ```php
   // Línea ~528-543
   confirmarCita() {
       // ... actualizar estado a 'confirmada'
       // ... enviar WhatsApp
       // ✅ Crear evento en calendario
       $this->crearEventoCalendario($id, $usuario_id);
   }
   ```

### Método de Creación

**Ubicación:** `app/Controllers/Dashboard/AgendaController.php::crearEventoCalendario()`

**Librería:** `app/Libraries/CalendarService.php`

## ⚠️ Consideraciones

1. **Solo citas confirmadas:** El evento solo se crea cuando `estado_cita = 'confirmada'`
2. **No duplicados:** Si el evento ya existe (tiene `calendar_event_id`), no se crea otro
3. **Manejo de errores:** Si falla la creación del evento, no falla la confirmación de la cita
4. **Token válido:** Requiere que el nutricionista haya autorizado la conexión con su calendario

## 🔗 Archivos Relacionados

- `app/Controllers/Dashboard/AgendaController.php` - Lógica de confirmación y creación de eventos
- `app/Libraries/CalendarService.php` - Servicio de integración con calendarios
- `INTEGRACION_CALENDARIO.md` - Documentación completa de la integración
- `NOTAS_CALENDARIO_NUTRICIONISTA.md` - Notas sobre notificaciones
