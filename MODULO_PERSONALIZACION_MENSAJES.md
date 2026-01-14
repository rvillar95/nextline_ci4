# 📝 Módulo de Personalización de Mensajes

## 🎯 Idea Propuesta

Crear un módulo que permita personalizar los mensajes y textos que se generan automáticamente en el sistema, especialmente para:

### 1. Eventos de Calendario (Google Calendar / Outlook)

Actualmente, los eventos del calendario se crean con textos fijos en `CalendarService.php`:

- **Título del evento:** `"Consulta: {nombre_paciente}"`
- **Descripción:** Incluye información del paciente, tipo de consulta, modalidad, motivo
- **Ubicación:** `"Consultorio"` o `"Online"` según modalidad
- **Recordatorios:** Configurados automáticamente (24h y 1h antes)

**Propuesta:** Permitir personalizar estos textos desde la interfaz del sistema.

### 2. Mensajes de WhatsApp

Actualmente en `WhatsAppService.php`:

#### 2.1. Mensaje de Confirmación de Cita (`enviarConfirmacionCita`)

**Ubicación:** `app/Libraries/WhatsAppService.php` línea 259-299

**Mensaje actual:**
```
¡Hola {nombre_paciente}!

Tu cita con {nutricionista} ha sido agendada:

📅 Fecha: {fecha}
🕐 Hora: {hora_inicio}
📋 Tipo: {tipo_consulta}

¡Te esperamos!
```

**Variables utilizadas:**
- `{nombre_paciente}` - Nombre completo del paciente
- `{nutricionista}` - Nombre del nutricionista
- `{fecha}` - Fecha de la cita (formato: DD-MM-YYYY)
- `{hora_inicio}` - Hora de inicio (formato: HH:MM)
- `{tipo_consulta}` - Tipo de consulta (Control, Primera vez, etc.)

#### 2.2. Mensaje de Recordatorio de Cita (`enviarRecordatorioCita`)

**Ubicación:** `app/Libraries/WhatsAppService.php` línea 350-395

**Mensaje actual:**
```
🔔 Recordatorio de Cita

Hola {nombre_paciente},

Te recordamos tu cita con {nutricionista}:

📅 Fecha: {fecha}
🕐 Hora: {hora_inicio}
📋 Tipo: {tipo_consulta}

¡Nos vemos pronto!
```

**Variables utilizadas:**
- `{nombre_paciente}` - Nombre completo del paciente
- `{nutricionista}` - Nombre del nutricionista
- `{fecha}` - Fecha de la cita
- `{hora_inicio}` - Hora de inicio
- `{tipo_consulta}` - Tipo de consulta

**Propuesta:** Editor de plantillas con variables dinámicas, soporte para emojis, y posibilidad de personalizar el tono del mensaje.

### 3. Emails

Actualmente en `AgendaController.php`:

- Emails de confirmación de citas
- Emails de cancelación
- Recordatorios por email

**Propuesta:** Editor de plantillas HTML con variables.

## 📋 Funcionalidades Propuestas

### Interfaz de Usuario

1. **Lista de Plantillas**
   - Eventos de calendario
   - Mensajes de WhatsApp
   - Emails
   - Notificaciones

2. **Editor de Plantillas**
   - Editor WYSIWYG para emails
   - Editor de texto simple para WhatsApp
   - Preview en tiempo real
   - Variables disponibles (lista desplegable)

3. **Variables Disponibles**

   **Para WhatsApp:**
   - `{nombre_paciente}` - Nombre completo del paciente
   - `{nutricionista}` - Nombre del nutricionista
   - `{fecha}` - Fecha de la cita (formato: DD-MM-YYYY)
   - `{hora_inicio}` - Hora de inicio (formato: HH:MM)
   - `{hora_fin}` - Hora de fin (formato: HH:MM)
   - `{tipo_consulta}` - Tipo de consulta (Control, Primera vez, etc.)
   - `{modalidad}` - Presencial/Online
   - `{motivo}` - Motivo de la consulta
   - `{horas_antes}` - Horas antes del recordatorio (solo para recordatorios)
   - `{telefono_paciente}` - Teléfono del paciente
   - `{email_paciente}` - Email del paciente

   **Para Calendario:**
   - `{nombre_paciente}` - Nombre completo del paciente
   - `{nombre_nutricionista}` - Nombre del nutricionista
   - `{email_nutricionista}` - Email del nutricionista (organizador del evento)
   - `{email_paciente}` - Email del paciente (invitado al evento)
   - `{fecha}` - Fecha de la cita
   - `{hora_inicio}` - Hora de inicio
   - `{hora_fin}` - Hora de fin
   - `{tipo_consulta}` - Tipo de consulta
   - `{modalidad}` - Presencial/Online
   - `{motivo}` - Motivo de la consulta
   - `{ubicacion}` - Ubicación de la consulta (Consultorio/Online)

   **Nota importante:** El evento se crea en el calendario del nutricionista (usando su token OAuth2), por lo que el nutricionista es automáticamente el organizador del evento. El email del nutricionista es el asociado a su cuenta de Google Calendar/Outlook que autorizó la conexión.

### Estructura de Base de Datos Propuesta

```sql
CREATE TABLE `plantillas_mensajes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` enum(
    'calendario_titulo',
    'calendario_descripcion',
    'whatsapp_confirmacion',
    'whatsapp_recordatorio',
    'whatsapp_cancelacion',
    'email_confirmacion',
    'email_cancelacion',
    'email_recordatorio'
  ) NOT NULL,
  `nombre` varchar(255) NOT NULL COMMENT 'Nombre descriptivo de la plantilla',
  `contenido` text NOT NULL COMMENT 'Contenido de la plantilla con variables {variable}',
  `variables_disponibles` text COMMENT 'JSON con lista de variables disponibles para este tipo',
  `usuario_id` int DEFAULT NULL COMMENT 'NULL = global, o ID de usuario específico',
  `activo` tinyint(1) DEFAULT 1 COMMENT '1 = activa, 0 = inactiva',
  `es_default` tinyint(1) DEFAULT 0 COMMENT '1 = plantilla por defecto del sistema',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tipo_usuario` (`tipo`, `usuario_id`),
  KEY `idx_activo` (`activo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar plantillas por defecto
INSERT INTO `plantillas_mensajes` (`tipo`, `nombre`, `contenido`, `variables_disponibles`, `es_default`, `activo`) VALUES
('whatsapp_confirmacion', 'Confirmación de Cita (Default)', 
'¡Hola {nombre_paciente}!

Tu cita con {nutricionista} ha sido agendada:

📅 Fecha: {fecha}
🕐 Hora: {hora_inicio}
📋 Tipo: {tipo_consulta}

¡Te esperamos!',
'["nombre_paciente", "nutricionista", "fecha", "hora_inicio", "tipo_consulta"]',
1, 1),

('whatsapp_recordatorio', 'Recordatorio de Cita (Default)',
'🔔 Recordatorio de Cita

Hola {nombre_paciente},

Te recordamos tu cita con {nutricionista}:

📅 Fecha: {fecha}
🕐 Hora: {hora_inicio}
📋 Tipo: {tipo_consulta}

¡Nos vemos pronto!',
'["nombre_paciente", "nutricionista", "fecha", "hora_inicio", "tipo_consulta", "horas_antes"]',
1, 1);
```

### Ubicación en el Sistema

- **Ruta:** `/dashboard/configuracion/mensajes` o `/dashboard/personalizacion/mensajes`
- **Módulo:** Nuevo módulo "Personalización" o dentro de "Configuración"
- **Permisos:** Solo administradores o nutricionistas con permiso especial

## 🔄 Integración con Código Existente

### CalendarService.php

```php
// Antes:
'summary' => 'Consulta: ' . ($citaData['nombre_paciente'] ?? 'Paciente'),

// Después:
'summary' => $this->procesarPlantilla('calendario_titulo', $citaData),
```

### WhatsAppService.php

#### Confirmación de Cita

```php
// Antes (línea 283-290):
$mensaje = "¡Hola {$nombrePaciente}!\n\n";
$mensaje .= "Tu cita con {$nutricionista} ha sido agendada:\n\n";
$mensaje .= "📅 Fecha: {$fecha}\n";
$mensaje .= "🕐 Hora: {$horaInicio}\n";
if ($cita->tipo_consulta) {
    $mensaje .= "📋 Tipo: {$cita->tipo_consulta}\n";
}
$mensaje .= "\n¡Te esperamos!";

// Después:
$mensaje = $this->procesarPlantilla('whatsapp_confirmacion', [
    'nombre_paciente' => $nombrePaciente,
    'nutricionista' => $nutricionista,
    'fecha' => $fecha,
    'hora_inicio' => $horaInicio,
    'tipo_consulta' => $cita->tipo_consulta ?? null
]);
```

#### Recordatorio de Cita

```php
// Antes (línea 378-386):
$mensaje = "🔔 Recordatorio de Cita\n\n";
$mensaje .= "Hola {$nombrePaciente},\n\n";
$mensaje .= "Te recordamos tu cita con {$nutricionista}:\n\n";
$mensaje .= "📅 Fecha: {$fecha}\n";
$mensaje .= "🕐 Hora: {$horaInicio}\n";
if ($cita->tipo_consulta) {
    $mensaje .= "📋 Tipo: {$cita->tipo_consulta}\n";
}
$mensaje .= "\n¡Nos vemos pronto!";

// Después:
$mensaje = $this->procesarPlantilla('whatsapp_recordatorio', [
    'nombre_paciente' => $nombrePaciente,
    'nutricionista' => $nutricionista,
    'fecha' => $fecha,
    'hora_inicio' => $horaInicio,
    'tipo_consulta' => $cita->tipo_consulta ?? null,
    'horas_antes' => $horasAntes
]);
```

## 📝 Ejemplo de Plantilla

### Título de Evento de Calendario

**Plantilla:**
```
{nombre_paciente} - {tipo_consulta} ({modalidad})
```

**Resultado:**
```
Rafael Mauricio Villar Bahamondes - Control (Presencial)
```

### Descripción de Evento de Calendario

**Plantilla:**
```
Consulta con {nombre_nutricionista}

Paciente: {nombre_paciente}
Tipo: {tipo_consulta}
Modalidad: {modalidad}
Motivo: {motivo}

Creado desde NextLine Agenda
```

### Mensaje de Confirmación de WhatsApp

**Plantilla:**
```
¡Hola {nombre_paciente}!

Tu cita con {nutricionista} ha sido agendada:

📅 Fecha: {fecha}
🕐 Hora: {hora_inicio}
📋 Tipo: {tipo_consulta}

¡Te esperamos!
```

**Resultado:**
```
¡Hola Rafael Mauricio Villar Bahamondes!

Tu cita con Rafael Villar ha sido agendada:

📅 Fecha: 12-01-2026
🕐 Hora: 12:30
📋 Tipo: Control

¡Te esperamos!
```

### Mensaje de Recordatorio de WhatsApp

**Plantilla:**
```
🔔 Recordatorio de Cita

Hola {nombre_paciente},

Te recordamos tu cita con {nutricionista}:

📅 Fecha: {fecha}
🕐 Hora: {hora_inicio}
📋 Tipo: {tipo_consulta}

¡Nos vemos pronto!
```

**Resultado:**
```
🔔 Recordatorio de Cita

Hola Rafael Mauricio Villar Bahamondes,

Te recordamos tu cita con Rafael Villar:

📅 Fecha: 12-01-2026
🕐 Hora: 12:30
📋 Tipo: Control

¡Nos vemos pronto!
```

## 🎨 Interfaz Propuesta

1. **Vista de Lista**
   - Tabla con todas las plantillas
   - Filtros por tipo
   - Indicador de plantilla activa vs. inactiva
   - Botones: Editar, Duplicar, Activar/Desactivar

2. **Vista de Edición**
   - **Para WhatsApp:** Editor de texto simple con contador de caracteres (límite 4096)
   - **Para Emails:** Editor WYSIWYG (TinyMCE) con soporte HTML
   - **Para Calendario:** Editor de texto simple
   - Panel lateral con variables disponibles (click para insertar)
   - Botón "Insertar Variable" con dropdown
   - Preview en tiempo real con datos de ejemplo
   - Validación de variables (mostrar advertencia si falta una variable)
   - Botones: Guardar, Cancelar, Preview, Restaurar Default

3. **Vista de Preview**
   - Muestra cómo se verá el mensaje final
   - Con datos de ejemplo o datos reales de una cita

## ⚠️ Consideraciones

1. **Compatibilidad hacia atrás:** Si no hay plantilla personalizada, usar los textos por defecto
2. **Validación:** Asegurar que las variables existan antes de procesar
3. **Seguridad:** Sanitizar el contenido de las plantillas
4. **Multi-usuario:** Permitir plantillas globales y por usuario
5. **Historial:** Guardar versiones anteriores de las plantillas

## 🔗 Archivos Relacionados

- `app/Libraries/CalendarService.php` - Generación de eventos
- `app/Libraries/WhatsAppService.php` - Envío de mensajes WhatsApp
- `app/Controllers/Dashboard/AgendaController.php` - Envío de emails

## 📅 Prioridad

**Baja** - Funcionalidad nice-to-have, no crítica para el funcionamiento del sistema.

---

**Nota:** Esta es una idea para implementar en el futuro. El sistema actual funciona correctamente con los textos fijos.
