# Propuesta: Simplificar Estructura de Agenda

## Problemas Identificados

1. ✅ **`agenda` no tiene FK del nutricionista**: No sabemos a qué nutricionista pertenece cada día
2. ✅ **`detalle_agenda` no tiene fecha**: Hay que hacer JOIN con `agenda` para saber la fecha
3. ✅ **`agenda_paciente` parece redundante**: Si solo un paciente puede ocupar un bloque, ¿por qué una tabla separada?

## Solución Propuesta: 2 Tablas en lugar de 3

### Nueva Estructura

#### 1. **`agenda`** - Día Completo
```sql
- id
- fecha
- hora_inicio (del día laboral)
- hora_fin (del día laboral)
- almuerzo_inicio
- almuerzo_fin
- usuario_id (NUEVO: FK al nutricionista)
- tipo_id (opcional)
```

#### 2. **`detalle_agenda`** - Bloques de Tiempo (con paciente cuando está ocupado)
```sql
- id
- agenda_id (FK a agenda)
- fecha (NUEVO: fecha del bloque, duplicada para facilitar consultas)
- usuario_id (nutricionista)
- hora_inicio
- hora_fin
- estado (1=Disponible, 2=Ocupado)
- modalidad_id (Presencial/Online)
- orden

-- Campos de paciente (NUEVOS, solo se llenan cuando está ocupado):
- paciente_id (NULL si está disponible, FK a pacientes si está ocupado)
- tipo_consulta (primera_vez, control, seguimiento, emergencia)
- motivo
- estado_cita (agendada, confirmada, en_proceso, completada, cancelada, no_asistio)
- fecha_confirmacion
- fecha_cancelacion
- motivo_cancelacion
- recordatorio_enviado
- fecha_recordatorio
- observaciones
```

#### 3. **`agenda_paciente`** - ELIMINAR
Ya no es necesaria. Los datos se guardan directamente en `detalle_agenda`.

---

## Ventajas de la Nueva Estructura

1. ✅ **Más simple**: 2 tablas en lugar de 3
2. ✅ **Más eficiente**: No necesitas JOIN con `agenda_paciente` para saber si un bloque está ocupado
3. ✅ **Fecha directa**: `detalle_agenda` tiene `fecha`, no necesitas JOIN
4. ✅ **FK del nutricionista**: `agenda` tiene `usuario_id`
5. ✅ **Lógica más clara**: Si `paciente_id` es NULL = disponible, si tiene valor = ocupado

---

## Ejemplo de Uso

### Antes (3 tablas):
```sql
-- Para saber si un bloque está ocupado:
SELECT da.*, ap.paciente_id, ap.estado_cita
FROM detalle_agenda da
LEFT JOIN agenda_paciente ap ON ap.detalle_agenda_id = da.id
WHERE da.id = 123;
```

### Después (2 tablas):
```sql
-- Para saber si un bloque está ocupado:
SELECT da.*, da.paciente_id, da.estado_cita
FROM detalle_agenda da
WHERE da.id = 123;
```

---

## Cambios en el Código

### 1. **AgendaController::agendar()**
```php
// ANTES:
$agendaPaciente = new AgendaPaciente();
$agendaPaciente->agendarCita($detalleAgendaId, $pacienteId, $data);

// DESPUÉS:
$detalleAgenda = new DetalleAgenda();
$detalleAgenda->update($detalleAgendaId, [
    'paciente_id' => $pacienteId,
    'tipo_consulta' => $data['tipo_consulta'],
    'estado_cita' => 'agendada',
    'estado' => 2, // Ocupado
    // ... otros campos
]);
```

### 2. **AgendaController::getEventos()**
```php
// ANTES:
->join('agenda_paciente ap', 'ap.detalle_agenda_id = da.id', 'left')

// DESPUÉS:
// Ya no necesitas JOIN, los datos están en detalle_agenda
->select('da.*, p.nombre, p.apellido')
->join('pacientes p', 'p.id = da.paciente_id', 'left')
```

### 3. **Modelo DetalleAgenda**
```php
protected $allowedFields = [
    'agenda_id', 'fecha', 'usuario_id', 'hora_inicio', 'hora_fin',
    'estado', 'modalidad_id', 'orden',
    // Campos de paciente:
    'paciente_id', 'tipo_consulta', 'motivo', 'estado_cita',
    'fecha_confirmacion', 'fecha_cancelacion', 'motivo_cancelacion',
    'recordatorio_enviado', 'fecha_recordatorio', 'observaciones'
];
```

---

## Migración de Datos

El script `simplificar_estructura_agenda.sql` hace lo siguiente:

1. ✅ Agrega `usuario_id` a `agenda`
2. ✅ Agrega `fecha` a `detalle_agenda`
3. ✅ Agrega campos de paciente a `detalle_agenda`
4. ✅ Migra datos de `agenda_paciente` a `detalle_agenda`
5. ✅ Actualiza `estado` en `detalle_agenda` (1=Disponible, 2=Ocupado)
6. ⚠️ Elimina `agenda_paciente` (comentado, descomentar después de verificar)

---

## ¿Cuándo usar esta estructura?

✅ **Usa esta estructura si**:
- Solo un paciente puede ocupar un bloque a la vez
- No necesitas historial de múltiples pacientes en el mismo bloque
- Quieres simplicidad y eficiencia

❌ **NO uses esta estructura si**:
- Necesitas que múltiples pacientes puedan estar en el mismo bloque (ej: consultas grupales)
- Necesitas historial completo de quién ocupó cada bloque en el pasado

---

## Conclusión

Para nutricionistas, esta estructura simplificada es **perfecta** porque:
- Un bloque = una cita = un paciente
- No hay consultas grupales
- Simplifica el código y las consultas
- Mejora el rendimiento

¿Procedemos con esta simplificación?
