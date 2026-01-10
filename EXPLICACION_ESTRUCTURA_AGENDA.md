# Explicación: Estructura de las 3 Tablas de Agenda

## ¿Por qué 3 tablas?

La estructura actual tiene **3 niveles** para manejar la agenda de manera flexible:

---

## 1. **`agenda`** - Nivel DÍA (Día Completo)

**Propósito**: Representa un **día completo** con su horario laboral general.

**Campos principales**:
- `id`: ID único del día
- `fecha`: Fecha del día (ej: 2026-01-15)
- `hora_inicio`: Hora de inicio del día laboral (ej: 09:00)
- `hora_fin`: Hora de fin del día laboral (ej: 18:00)
- `almuerzo_inicio`: Hora de inicio del almuerzo (ej: 13:00)
- `almuerzo_fin`: Hora de fin del almuerzo (ej: 14:00)
- `tipo_id`: Tipo de día (opcional, para nutricionistas no es crítico)

**Ejemplo**:
```
id: 1
fecha: 2026-01-15
hora_inicio: 09:00
hora_fin: 18:00
almuerzo_inicio: 13:00
almuerzo_fin: 14:00
```

**Significado**: "El día 15 de enero, trabajo de 9:00 AM a 6:00 PM, con almuerzo de 1:00 PM a 2:00 PM"

---

## 2. **`detalle_agenda`** - Nivel BLOQUE (Horarios Específicos)

**Propósito**: Representa **bloques de tiempo específicos** dentro de un día.

**Campos principales**:
- `id`: ID único del bloque
- `agenda_id`: Referencia al día (FK a `agenda.id`)
- `usuario_id`: ID del nutricionista
- `hora_inicio`: Hora de inicio del bloque (ej: 09:00)
- `hora_fin`: Hora de fin del bloque (ej: 09:30)
- `estado`: 1 = Disponible, 0 = No disponible
- `modalidad_id`: 1 = Presencial, 2 = Online, 3 = No definido
- `orden`: Orden del bloque en el día

**Ejemplo** (para el día 15 de enero):
```
Bloque 1: 09:00 - 09:30 (Disponible, Presencial)
Bloque 2: 09:30 - 10:00 (Disponible, Online)
Bloque 3: 10:00 - 10:30 (Disponible, Presencial)
Bloque 4: 10:30 - 11:00 (Disponible, Presencial)
... (continúa hasta 18:00, saltando el horario de almuerzo)
```

**Significado**: "Dentro del día 15 de enero, tengo bloques de 30 minutos disponibles para agendar"

---

## 3. **`agenda_paciente`** - Nivel CITA (Relación Paciente-Bloque)

**Propósito**: Relaciona un **paciente con un bloque de tiempo** cuando se agenda una cita.

**Campos principales**:
- `id`: ID único de la cita
- `detalle_agenda_id`: Referencia al bloque (FK a `detalle_agenda.id`)
- `paciente_id`: ID del paciente
- `nutricionista_id`: ID del nutricionista
- `tipo_consulta`: primera_vez, control, seguimiento, emergencia
- `estado_cita`: agendada, confirmada, en_proceso, completada, cancelada, no_asistio
- `motivo`: Motivo de la consulta
- `observaciones`: Notas adicionales

**Ejemplo**:
```
id: 1
detalle_agenda_id: 5 (bloque de 10:00-10:30 del día 15)
paciente_id: 3 (Juan Pérez)
tipo_consulta: control
estado_cita: agendada
```

**Significado**: "Juan Pérez tiene una cita de control el día 15 de enero de 10:00 a 10:30"

---

## Relación entre las 3 Tablas

```
agenda (1 día)
  └── detalle_agenda (múltiples bloques del día)
        └── agenda_paciente (citas agendadas en esos bloques)
```

**Ejemplo completo**:

### Día 15 de Enero (tabla `agenda`)
```
id: 1
fecha: 2026-01-15
hora_inicio: 09:00
hora_fin: 18:00
```

### Bloques del día (tabla `detalle_agenda`)
```
Bloque 1: agenda_id=1, 09:00-09:30, Disponible
Bloque 2: agenda_id=1, 09:30-10:00, Disponible
Bloque 3: agenda_id=1, 10:00-10:30, Disponible ← CITA AQUÍ
Bloque 4: agenda_id=1, 10:30-11:00, Disponible
...
```

### Cita agendada (tabla `agenda_paciente`)
```
detalle_agenda_id: 3 (bloque 10:00-10:30)
paciente_id: 5 (María González)
estado_cita: agendada
```

---

## ¿Por qué esta estructura?

### Ventajas:
1. **Flexibilidad**: Puedes tener diferentes horarios laborales por día
2. **Eficiencia**: No duplicas información (hora_inicio/hora_fin del día se guarda una vez)
3. **Escalabilidad**: Fácil agregar más información a cada nivel
4. **Historial**: Puedes ver qué días trabajaste y qué bloques tenías disponibles

### Desventajas:
1. **Complejidad**: 3 tablas pueden parecer excesivas
2. **Consultas**: Requiere JOINs para obtener información completa

---

## ¿Se puede simplificar?

### Opción 1: Mantener las 3 tablas (Actual)
- ✅ Flexible y escalable
- ✅ Permite diferentes horarios por día
- ❌ Más complejo

### Opción 2: Eliminar `agenda`, usar solo `detalle_agenda`
- ✅ Más simple (2 tablas)
- ❌ Perderías información del horario laboral del día
- ❌ Tendrías que repetir hora_inicio/hora_fin en cada bloque

### Opción 3: Todo en una tabla (No recomendado)
- ❌ Mucha duplicación de datos
- ❌ Difícil de mantener

---

## Recomendación

**Para nutricionistas, mantener las 3 tablas es lo mejor** porque:
- Permite flexibilidad en horarios
- Facilita reportes (días trabajados, horas disponibles, etc.)
- Permite agregar funcionalidades futuras (diferentes horarios por día de la semana)

**Lo que SÍ se puede simplificar**:
- `tipo_agenda` no es crítico para nutricionistas (se puede dejar NULL o un valor por defecto)
- `modalidad_agenda` es útil (presencial vs online)

---

## Resumen Visual

```
┌─────────────────────────────────────┐
│  agenda (DÍA)                       │
│  - fecha: 2026-01-15                │
│  - hora_inicio: 09:00               │
│  - hora_fin: 18:00                  │
└──────────────┬──────────────────────┘
               │
               │ (1 día tiene muchos bloques)
               │
               ▼
┌─────────────────────────────────────┐
│  detalle_agenda (BLOQUES)            │
│  - 09:00-09:30 (Disponible)         │
│  - 09:30-10:00 (Disponible)         │
│  - 10:00-10:30 (Disponible) ←       │
│  - 10:30-11:00 (Disponible)         │
└──────────────┬──────────────────────┘
               │
               │ (1 bloque puede tener 1 cita)
               │
               ▼
┌─────────────────────────────────────┐
│  agenda_paciente (CITA)               │
│  - paciente: Juan Pérez              │
│  - tipo: control                     │
│  - estado: agendada                  │
└──────────────────────────────────────┘
```

---

## Conclusión

Las 3 tablas tienen sentido porque cada una maneja un **nivel diferente** de información:
- **`agenda`**: Día completo
- **`detalle_agenda`**: Bloques de tiempo
- **`agenda_paciente`**: Citas agendadas

Es una estructura **normalizada** que evita duplicación y permite flexibilidad.
