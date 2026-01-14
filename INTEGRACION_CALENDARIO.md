# 📅 Integración con Calendarios (Google Calendar / Outlook)

Esta guía explica cómo configurar la integración de las citas agendadas con el calendario del nutricionista (Google Calendar o Microsoft Outlook).

## 🎯 Funcionalidad

Cuando un nutricionista agenda una cita, automáticamente se crea un evento en **su calendario personal** (Google Calendar o Outlook) con:
- Fecha y hora de la cita
- Nombre del paciente
- Tipo de consulta
- Modalidad (Presencial/Online)
- Recordatorios automáticos (24 horas y 1 hora antes)

### ⚠️ Importante: Notificaciones del Calendario

**Las notificaciones y recordatorios del calendario llegarán al correo del nutricionista**, ya que:
- El evento se crea en el calendario del nutricionista (usando su token OAuth2)
- El nutricionista es automáticamente el **organizador** del evento
- El correo del nutricionista es el asociado a su cuenta de Google Calendar/Outlook que autorizó la conexión
- El paciente puede ser agregado como **invitado** (attendee) si tiene email, pero las notificaciones principales van al organizador (nutricionista)

**Flujo:**
1. Nutricionista se loguea en la aplicación
2. Nutricionista autoriza la conexión con su Google Calendar/Outlook
3. Cuando agenda una cita, el evento se crea en **su calendario**
4. Las notificaciones y recordatorios del calendario llegan al **correo del nutricionista**
5. Si el paciente tiene email, se le agrega como invitado (puede recibir invitación, pero las notificaciones principales son del organizador)

## 📋 Requisitos Previos

1. **Cuenta de Google** (para Google Calendar) o **Cuenta de Microsoft** (para Outlook)
2. **Acceso a Google Cloud Console** o **Azure Portal**
3. **Base de datos actualizada** con las tablas necesarias

## 🔧 Configuración

### Paso 1: Ejecutar Script SQL

Ejecuta el script `crear_tabla_calendar_tokens.sql` en tu base de datos:

```sql
-- Esto creará:
-- 1. Tabla usuario_calendar_tokens (almacena tokens OAuth2)
-- 2. Columna calendar_event_id en detalle_agenda
```

### Paso 2: Configurar Google Calendar (Recomendado)

#### 2.1. Crear Proyecto en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la **Google Calendar API**:
   - Ve a "APIs & Services" > "Library"
   - Busca "Google Calendar API"
   - Haz clic en "Enable"

#### 2.2. Crear Credenciales OAuth 2.0

1. Ve a "APIs & Services" > "Credentials"
2. Haz clic en "Create Credentials" > "OAuth client ID"
3. Si es la primera vez, configura la pantalla de consentimiento:
   - Tipo de aplicación: Externa
   - Nombre de la app: NextLine Agenda
   - Email de soporte: tu email
   - Dominios autorizados: tu dominio (ej: `tudominio.com`)
   - Agrega tu email como usuario de prueba
4. Crea el OAuth client:
   - Tipo: "Web application"
   - Nombre: NextLine Calendar Integration
   - **Authorized redirect URIs**: 
     ```
     https://tudominio.com/dashboard/agenda/calendario/callback
     ```
     (Para desarrollo local: `http://localhost/dashboard/agenda/calendario/callback`)

#### 2.3. Obtener Credenciales

Después de crear el OAuth client, obtendrás:
- **Client ID**: `xxxxx.apps.googleusercontent.com`
- **Client Secret**: `xxxxx`

#### 2.4. Configurar en `.env`

Agrega estas variables a tu archivo `.env`:

```env
# Calendario
CALENDAR_PROVIDER=google

# Google Calendar
GOOGLE_CALENDAR_CLIENT_ID=tu-client-id.apps.googleusercontent.com
GOOGLE_CALENDAR_CLIENT_SECRET=tu-client-secret
# Opcional: Deshabilitar verificación SSL en desarrollo (solo para WAMP/XAMPP local)
GOOGLE_DISABLE_SSL_VERIFY=false
```

### Paso 3: Configurar Outlook Calendar (Opcional)

#### 3.1. Registrar Aplicación en Azure Portal

1. Ve a [Azure Portal](https://portal.azure.com/)
2. Ve a "Azure Active Directory" > "App registrations"
3. Haz clic en "New registration"
4. Configura:
   - Name: NextLine Agenda
   - Supported account types: "Accounts in any organizational directory and personal Microsoft accounts"
   - Redirect URI: `https://tudominio.com/dashboard/agenda/calendario/callback`
5. Después de crear, ve a "Certificates & secrets"
6. Crea un nuevo "Client secret"
7. Ve a "API permissions" y agrega:
   - `Calendars.ReadWrite` (Microsoft Graph)

#### 3.2. Configurar en `.env`

```env
# Calendario
CALENDAR_PROVIDER=outlook

# Outlook Calendar
OUTLOOK_CALENDAR_CLIENT_ID=tu-application-id
OUTLOOK_CALENDAR_CLIENT_SECRET=tu-client-secret
```

## 🚀 Uso

### Para el Nutricionista

1. **Primera vez**: Debe autorizar la conexión con su calendario:
   - Ve a Configuración > Integración con Calendario
   - Haz clic en "Conectar con Google Calendar" (o Outlook)
   - Será redirigido a Google/Microsoft para autorizar
   - Después de autorizar, volverá automáticamente

2. **Agendar citas**: 
   - Cuando agenda una cita, automáticamente se crea el evento en su calendario
   - El evento incluye toda la información de la cita
   - Se envían recordatorios automáticos

3. **Actualizaciones**:
   - Si se confirma/cancela una cita, el evento se actualiza automáticamente
   - Si se elimina una cita, el evento se elimina del calendario

## 🔄 Flujo de Autorización OAuth2

```
1. Nutricionista hace clic en "Conectar Calendario"
2. Sistema redirige a Google/Microsoft para autorizar
3. Usuario autoriza y Google/Microsoft redirige de vuelta con código
4. Sistema intercambia código por tokens (access_token + refresh_token)
5. Tokens se guardan encriptados en la base de datos
6. Sistema puede crear/actualizar eventos usando access_token
7. Cuando access_token expira, se usa refresh_token para obtener uno nuevo
```

## 📊 Estructura de Datos

### Tabla: `usuario_calendar_tokens`

Almacena los tokens OAuth2 de cada nutricionista:

```sql
- usuario_id: ID del nutricionista
- provider: 'google' o 'outlook'
- access_token: Token de acceso (válido por 1 hora)
- refresh_token: Token de refresco (válido indefinidamente)
- expires_at: Fecha de expiración del access_token
```

### Columna: `detalle_agenda.calendar_event_id`

Almacena el ID del evento en el calendario para poder actualizarlo/eliminarlo después.

## 🛠️ Funcionalidades Técnicas

### Crear Evento

Cuando se agenda una cita:
- Se obtiene el token del nutricionista
- Se crea evento en su calendario
- Se guarda el `event_id` en `detalle_agenda`

### Actualizar Evento

Cuando se confirma/cancela una cita:
- Se busca el `calendar_event_id`
- Se actualiza el evento en el calendario
- Se cambia el título/descripción según el estado

### Eliminar Evento

Cuando se elimina una cita:
- Se busca el `calendar_event_id`
- Se elimina el evento del calendario

## 🔒 Seguridad

- Los tokens se almacenan encriptados en la base de datos
- Los tokens tienen expiración automática
- Se usa refresh_token para renovar tokens sin re-autorizar
- Solo el nutricionista puede ver/modificar sus propios eventos

## ⚠️ Limitaciones

- **Google Calendar**: 
  - Límite de 1,000,000 requests por día (más que suficiente)
  - Los eventos se crean en el calendario "primary" del usuario

- **Outlook Calendar**:
  - Límite de 10,000 requests por 10 minutos
  - Los eventos se crean en el calendario principal del usuario

## 🐛 Troubleshooting

### Error: "No hay token de acceso"

**Solución**: El nutricionista debe autorizar la conexión primero.

### Error: "Token expirado"

**Solución**: El sistema intenta refrescar automáticamente. Si falla, el usuario debe re-autorizar.

### Error: "Calendar API no habilitada"

**Solución**: Verifica que hayas habilitado la API en Google Cloud Console / Azure Portal.

### Los eventos no se crean

**Verifica**:
1. Que el token esté guardado en `usuario_calendar_tokens`
2. Que las credenciales en `.env` sean correctas
3. Que el redirect URI coincida exactamente con el configurado en Google/Azure
4. Revisa los logs en `writable/logs/`

## 📝 Notas Adicionales

- Los recordatorios se configuran automáticamente (24h y 1h antes)
- El paciente puede ser agregado como "attendee" si tiene email
- Los eventos incluyen zona horaria (America/Santiago)
- Se puede cambiar la zona horaria en `CalendarService.php`

## 🔗 Enlaces Útiles

- [Google Calendar API Documentation](https://developers.google.com/calendar/api/v3/reference)
- [Microsoft Graph Calendar API](https://docs.microsoft.com/en-us/graph/api/resources/calendar)
- [OAuth 2.0 Playground](https://developers.google.com/oauthplayground/)
