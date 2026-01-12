# ⚙️ Módulo de Configuraciones del Sistema

## 🎯 Objetivo

Permitir a cada nutricionista personalizar el comportamiento del sistema según sus preferencias, incluyendo:
- Envío de emails
- Envío de WhatsApp
- Creación de eventos en calendario
- Agregar pacientes como invitados
- Recordatorios automáticos

## 📋 Configuraciones Disponibles

### 1. Notificaciones

#### Enviar correo electrónico
- **Descripción:** Enviar email al paciente cuando se agenda una cita
- **Valor por defecto:** Activado (1)
- **Cuándo se usa:** En `AgendaController::agendar()`

#### Enviar WhatsApp
- **Descripción:** Enviar mensaje de WhatsApp cuando el paciente confirma la cita desde el email
- **Valor por defecto:** Activado (1)
- **Cuándo se usa:** En `AgendaController::confirmarDesdeEmail()` y `AgendaController::confirmarCita()`

#### Enviar recordatorios por WhatsApp
- **Descripción:** Enviar recordatorios automáticos de citas próximas por WhatsApp
- **Valor por defecto:** Activado (1)
- **Cuándo se usa:** En `EnviarRecordatoriosWhatsApp` (comando CLI)

#### Horas antes del recordatorio
- **Descripción:** Número de horas antes de la cita para enviar el recordatorio
- **Valor por defecto:** 24 horas
- **Rango:** 1-168 horas (1-7 días)

### 2. Integración con Calendario

#### Crear evento en calendario
- **Descripción:** Crear automáticamente un evento en el calendario (Google Calendar/Outlook) cuando el paciente confirma la cita desde el email
- **Valor por defecto:** Activado (1)
- **Cuándo se usa:** En `AgendaController::confirmarDesdeEmail()` y `AgendaController::confirmarCita()`
- **Nota:** Requiere que el nutricionista haya autorizado la conexión con su calendario

#### Agregar paciente como invitado
- **Descripción:** Agregar el email del paciente como invitado al evento del calendario (solo si el paciente tiene email)
- **Valor por defecto:** Activado (1)
- **Cuándo se usa:** En `AgendaController::crearEventoCalendario()`
- **Nota:** Solo funciona si "Crear evento en calendario" está activado

## 🗄️ Estructura de Base de Datos

### Tabla: `usuario_configuraciones`

```sql
CREATE TABLE `usuario_configuraciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `enviar_whatsapp` tinyint(1) DEFAULT 1,
  `enviar_email` tinyint(1) DEFAULT 1,
  `crear_evento_calendario` tinyint(1) DEFAULT 1,
  `agregar_paciente_como_invitado` tinyint(1) DEFAULT 1,
  `enviar_recordatorios_whatsapp` tinyint(1) DEFAULT 1,
  `horas_antes_recordatorio` int DEFAULT 24,
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_config` (`usuario_id`)
);
```

## 🔧 Instalación

### Paso 1: Ejecutar Script SQL

Ejecuta el script `crear_tabla_configuraciones_sistema.sql` en tu base de datos:

```sql
-- Esto creará:
-- 1. Tabla usuario_configuraciones
-- 2. Configuraciones por defecto para usuarios existentes
```

### Paso 2: Agregar Módulo al Sistema

Ejecuta el script `agregar_modulo_configuracion.sql`:

```sql
-- Esto agregará:
-- 1. Módulo "Configuraciones" al sistema
-- 2. Rutas del módulo
-- 3. Permisos para el perfil de Nutricionista
```

## 📍 Ubicación en el Sistema

- **Ruta:** `/dashboard/configuracion`
- **Controlador:** `app/Controllers/Dashboard/ConfiguracionController.php`
- **Vista:** `app/Views/Modulos/configuracion/index.php`
- **Modelo:** `app/Models/UsuarioConfiguracion.php`

## 🔄 Integración con Código Existente

### AgendaController

El código ahora verifica las configuraciones antes de ejecutar acciones:

```php
// Obtener configuraciones
$configuracionModel = new \App\Models\UsuarioConfiguracion();
$configuracion = $configuracionModel->obtenerConfiguracion($usuario_id);

// Verificar si está habilitado
if ($configuracion['enviar_email'] ?? 1) {
    // Enviar email
}

if ($configuracion['enviar_whatsapp'] ?? 1) {
    // Enviar WhatsApp
}

if ($configuracion['crear_evento_calendario'] ?? 1) {
    // Crear evento en calendario
}
```

## 🎨 Interfaz de Usuario

### Características

1. **Switches (Toggle) para cada opción**
   - Fácil de activar/desactivar
   - Descripción clara de cada opción

2. **Campos dependientes**
   - "Horas antes del recordatorio" solo se muestra si "Enviar recordatorios por WhatsApp" está activado
   - "Agregar paciente como invitado" solo se muestra si "Crear evento en calendario" está activado

3. **Alertas informativas**
   - Explican el comportamiento de cada opción
   - Advertencias sobre limitaciones

4. **Botón de restaurar valores por defecto**
   - Restaura todas las configuraciones a sus valores predeterminados

## ⚠️ Consideraciones Importantes

### Sobre el Calendario

- **El evento se crea en el calendario del nutricionista**, no del paciente
- **Las notificaciones del calendario llegan al correo del nutricionista** (organizador del evento)
- **El paciente solo recibe una invitación** si está configurado como invitado y tiene email
- **No se puede agendar directamente en el calendario del paciente** porque no tienen token OAuth2

### Valores por Defecto

Si un usuario no tiene configuraciones guardadas, el sistema usa valores por defecto (todo activado) mediante el método `obtenerConfiguracion()` del modelo.

## 🔗 Archivos Relacionados

- `app/Controllers/Dashboard/ConfiguracionController.php` - Controlador del módulo
- `app/Models/UsuarioConfiguracion.php` - Modelo de configuraciones
- `app/Views/Modulos/configuracion/index.php` - Vista del módulo
- `app/Controllers/Dashboard/AgendaController.php` - Integración con agenda
- `crear_tabla_configuraciones_sistema.sql` - Script SQL para crear tabla
- `agregar_modulo_configuracion.sql` - Script SQL para agregar módulo

## 📝 Notas Adicionales

- Las configuraciones son **por usuario** (cada nutricionista tiene sus propias configuraciones)
- Las configuraciones se aplican **inmediatamente** después de guardar
- Si una configuración está desactivada, el sistema **no ejecuta la acción** pero **no falla** el proceso principal
- Los logs registran cuando una acción se omite por configuración
