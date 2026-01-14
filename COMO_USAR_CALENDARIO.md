# 📅 Cómo Usar la Integración con Calendario

## ✅ Paso 1: Conectar Calendario (Ya Completado)

Ya te logueaste con Google, por lo que la conexión está guardada. Los tokens están almacenados en la base de datos.

## 🎯 Paso 2: Agendar una Cita

Ahora cuando agendes una cita, automáticamente se creará un evento en tu calendario de Google.

### Cómo Funciona:

1. **Agenda una cita normalmente** desde el calendario de la aplicación
2. **El sistema automáticamente:**
   - Crea el evento en tu Google Calendar
   - Incluye toda la información de la cita
   - Agrega recordatorios (24 horas y 1 hora antes)
   - Si el paciente tiene email, lo agrega como "attendee"

### Información que se incluye en el evento:

- **Título:** "Consulta: [Nombre del Paciente]"
- **Fecha y hora:** La misma de la cita agendada
- **Descripción:**
  - Nombre del nutricionista
  - Nombre del paciente
  - Tipo de consulta
  - Modalidad (Presencial/Online)
  - Motivo (si hay)
- **Ubicación:** "Consultorio" o "Online" según la modalidad
- **Recordatorios:** 
  - Email 24 horas antes
  - Notificación 1 hora antes

## 🔄 Sincronización Automática

### Cuando se crea una cita:
- ✅ Se crea evento en Google Calendar automáticamente

### Cuando se confirma una cita:
- ✅ El evento se actualiza (si está implementado)

### Cuando se cancela una cita:
- ✅ El evento se elimina del calendario (si está implementado)

## 📱 Verificar en Google Calendar

1. Ve a [Google Calendar](https://calendar.google.com/)
2. Busca eventos con el título "Consulta: [Nombre del Paciente]"
3. Verifica que la fecha, hora y detalles sean correctos

## 🧪 Probar Ahora

1. **Ve al calendario de la aplicación**
2. **Agenda una nueva cita** con un paciente
3. **Revisa tu Google Calendar** - deberías ver el evento creado automáticamente

## ⚙️ Configuración Adicional

### Si quieres desconectar el calendario:

Actualmente no hay una interfaz para desconectar, pero puedes:
- Eliminar los tokens de la tabla `usuario_calendar_tokens`
- O volver a conectar (sobrescribirá los tokens anteriores)

### Si quieres cambiar de cuenta de Google:

1. Ve a `/dashboard/agenda/calendar/connect` nuevamente
2. Autoriza con la nueva cuenta
3. Los tokens anteriores se actualizarán

## 🐛 Troubleshooting

### Los eventos no se crean:

1. **Verifica que los tokens estén guardados:**
   ```sql
   SELECT * FROM usuario_calendar_tokens 
   WHERE usuario_id = [TU_ID];
   ```

2. **Revisa los logs:**
   - `writable/logs/log-[fecha].log`
   - Busca errores relacionados con "calendario"

3. **Verifica la configuración en `.env`:**
   - `CALENDAR_PROVIDER=google`
   - `GOOGLE_CALENDAR_CLIENT_ID` está configurado
   - `GOOGLE_CALENDAR_CLIENT_SECRET` está configurado

### El evento se crea pero con información incorrecta:

- Verifica que los datos de la cita estén completos en la base de datos
- Revisa que `detalle_agenda` tenga todos los campos necesarios

## 📝 Notas

- Los eventos se crean en tu calendario **principal** (primary calendar)
- Los recordatorios son automáticos según la configuración
- Si el paciente tiene email, se agrega como "attendee" (invitado)
- La zona horaria es "America/Santiago" (puedes cambiarla en `CalendarService.php`)
