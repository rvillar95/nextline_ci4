# Mejoras Pendientes para Integración de Calendario

## Funcionalidades a Implementar

### 1. Agregar Ubicación/Location a Eventos de Calendario

**Descripción:**
Permitir que el nutricionista pueda especificar una ubicación (dirección, consultorio, etc.) al crear eventos en el calendario. Esta ubicación se mostrará en el evento de Google Calendar/Outlook.

**Implementación sugerida:**

1. **Base de datos:**
   - Agregar campo `ubicacion` o `location` a la tabla `agenda` o `detalle_agenda`
   - O crear una tabla de configuraciones de consultorios/ubicaciones por nutricionista

2. **Modelo:**
   - Actualizar modelo `Agenda` o `DetalleAgenda` para incluir el campo de ubicación

3. **Vista:**
   - Agregar campo de texto/select en el formulario de agenda para seleccionar/ingresar ubicación
   - Permitir que el nutricionista configure ubicaciones predeterminadas (ej: "Consultorio Principal", "Sucursal Norte", etc.)

4. **CalendarService:**
   - Modificar métodos `crearEventoGoogle()` y `crearEventoOutlook()` para incluir el campo `location` en el evento
   - Para Google Calendar: usar el campo `location` en el objeto del evento
   - Para Outlook: usar el campo `location` en el objeto del evento

**Ejemplo de código para Google Calendar:**
```php
$evento = [
    'summary' => 'Consulta: ' . ($citaData['nombre_paciente'] ?? 'Paciente'),
    'description' => $this->generarDescripcionEvento($citaData),
    'location' => $citaData['ubicacion'] ?? 'Consultorio', // <-- Agregar aquí
    'start' => [
        'dateTime' => $fechaISO['inicio'],
        'timeZone' => 'America/Santiago'
    ],
    'end' => [
        'dateTime' => $fechaISO['fin'],
        'timeZone' => 'America/Santiago'
    ]
];
```

**Ejemplo de código para Outlook:**
```php
$evento = [
    'subject' => 'Consulta: ' . ($citaData['nombre_paciente'] ?? 'Paciente'),
    'body' => [
        'contentType' => 'HTML',
        'content' => $this->generarDescripcionEvento($citaData)
    ],
    'location' => [
        'displayName' => $citaData['ubicacion'] ?? 'Consultorio' // <-- Agregar aquí
    ],
    'start' => [
        'dateTime' => $fechaISO['inicio'],
        'timeZone' => 'America/Santiago'
    ],
    'end' => [
        'dateTime' => $fechaISO['fin'],
        'timeZone' => 'America/Santiago'
    ]
];
```

**Prioridad:** Media
**Fecha de solicitud:** 2026-01-12
**Notas:** El usuario observó que Google Calendar muestra un campo de ubicación en los eventos creados. Sería útil permitir personalizar esto desde la aplicación.

---

## Otras Mejoras Sugeridas

### 2. Configuración de Ubicaciones Predeterminadas
- Permitir que el nutricionista configure múltiples ubicaciones (consultorios, sucursales)
- Asociar ubicaciones a tipos de agenda o modalidades
- Mostrar selector de ubicación en el formulario de agenda

### 3. Sincronización Bidireccional
- Permitir que cambios en Google Calendar se reflejen en la aplicación
- Sincronizar cancelaciones y modificaciones desde el calendario externo

### 4. Recordatorios Personalizables
- Permitir configurar múltiples recordatorios (email, popup, SMS)
- Configurar tiempos de recordatorio diferentes por tipo de cita
