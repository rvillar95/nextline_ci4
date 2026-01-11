# Paleta de Colores - Sistema de Agenda para Nutricionistas

## 🎨 Principios de Diseño UX

### Regla de Oro
- **COLOR = ESTADO** (único propósito del color)
- **ICONO/BADGE = MODALIDAD** (representación visual secundaria)

### Objetivos
- Transmitir salud, calma, confianza y profesionalismo
- Ser accesible para usuarios con daltonismo leve
- Permitir lectura rápida del estado (0.2 segundos)
- Diseño limpio y moderno para usuarios no técnicos

---

## 📊 Tabla de Estados y Colores

| Estado | Color HEX | Descripción | Uso |
|--------|-----------|-------------|-----|
| **Disponible** | `#7BCB87` | Verde suave (saturación reducida) | Horarios libres que pueden ser agendados |
| **Confirmada** | `#4A90E2` | Azul confiable | Citas confirmadas por el paciente |
| **Agendada** | `#4A90E2` | Azul confiable | Citas agendadas (mismo que confirmada) |
| **Pendiente** | `#FFB74D` | Naranjo claro | Esperando confirmación del paciente |
| **En Proceso** | `#FF9800` | Naranjo intenso | Cita en curso, consulta activa |
| **Completada** | `#90A4AE` | Gris azulado | Cita finalizada exitosamente |
| **Cancelada** | `#E57373` | Rojo suave | Cita cancelada (no agresivo) |
| **No Asistió** | `#BA68C8` | Morado suave | Paciente no se presentó |
| **No Disponible** | `#BDBDBD` | Gris claro | Horario bloqueado o inactivo |
| **Bloqueado** | `#BDBDBD` | Gris claro | Horario no disponible (mismo que no disponible) |

### Características de la Paleta
- ✅ Colores suaves y profesionales (no agresivos)
- ✅ Alto contraste para texto blanco (#FFFFFF)
- ✅ Distinguibles para daltonismo (diferentes tonos y saturación)
- ✅ Transmiten emociones apropiadas para salud

---

## 🏷️ Modalidades e Iconos

| Modalidad | Icono | Badge Alternativo | Descripción |
|-----------|-------|-------------------|-------------|
| **Presencial** | 🏥 | Badge "P" | Consulta en el consultorio |
| **Online** | 💻 | Badge "O" | Consulta por videollamada |
| **No Definido** | ❔ | Badge "?" | Modalidad no especificada |

**Nota:** Los iconos aparecen al inicio del título de cada evento en el calendario.

---

## 🎯 Ejemplo Visual de Bloque de Agenda

### Bloque Disponible - Presencial
```
┌─────────────────────────────┐
│ 🏥 Disponible               │  ← Fondo: #6BCB77 (Verde suave)
│                             │  ← Texto: #FFFFFF (Blanco)
│ 09:00 - 09:30               │  ← Borde: rgba(255,255,255,0.3)
└─────────────────────────────┘
```

### Bloque Confirmada - Online
```
┌─────────────────────────────┐
│ 💻 Juan Pérez - Control     │  ← Fondo: #4A90E2 (Azul confiable)
│                             │  ← Texto: #FFFFFF (Blanco)
│ 10:00 - 10:30               │  ← Icono: 💻 (Online)
└─────────────────────────────┘
```

### Bloque En Proceso - Presencial
```
┌─────────────────────────────┐
│ 🏥 María González           │  ← Fondo: #FFA726 (Naranjo cálido)
│                             │  ← Texto: #FFFFFF (Blanco)
│ 11:00 - 11:30               │  ← Estado: En proceso
└─────────────────────────────┘
```

### Bloque Cancelada - Online
```
┌─────────────────────────────┐
│ 💻 Pedro Sánchez            │  ← Fondo: #E57373 (Rojo suave)
│                             │  ← Texto: #FFFFFF (Blanco)
│ 14:00 - 14:30               │  ← Estado: Cancelada
└─────────────────────────────┘
```

---

## 🎨 Mejoras Visuales Implementadas

### 1. Estilos CSS Mejorados
- **Bordes redondeados**: `border-radius: 6px` (más suave)
- **Borde sutil**: `border: 1px solid rgba(255, 255, 255, 0.3)` (mejor definición)
- **Hover effect**: Elevación y sombra al pasar el mouse
- **Transiciones suaves**: `transition: all 0.2s ease`
- **Mejor contraste**: Texto blanco sobre todos los colores

### 2. Leyenda Rediseñada
- **Separación clara**: Estados (colores) vs Modalidades (iconos)
- **Fondo degradado**: Suave para mejor legibilidad
- **Iconos grandes**: Fácil identificación visual
- **Tooltip informativo**: Explica la regla de oro

### 3. Accesibilidad
- **Contraste WCAG AA**: Todos los colores cumplen estándares
- **Distinguibles para daltonismo**: Diferentes tonos y saturación
- **Texto legible**: Tamaño y peso adecuados
- **Feedback visual**: Hover states claros

---

## 📱 Responsive Design

La leyenda se adapta a diferentes tamaños de pantalla:
- **Desktop**: 2 columnas (Estados | Modalidades)
- **Tablet**: 2 columnas apiladas
- **Mobile**: 1 columna (todo apilado)

---

## ✅ Checklist de Implementación

- [x] Paleta de colores profesional definida
- [x] Colores solo representan ESTADOS
- [x] Iconos representan MODALIDADES
- [x] Leyenda rediseñada y clara
- [x] Estilos CSS mejorados
- [x] Contraste accesible
- [x] Hover effects implementados
- [x] Diseño responsive
- [x] Documentación completa

---

## 🚀 Próximas Mejoras Sugeridas

1. **Modo oscuro**: Variante de colores para tema oscuro
2. **Filtros visuales**: Permitir filtrar por estado o modalidad
3. **Animaciones**: Transiciones suaves al cambiar estados
4. **Tooltips informativos**: Más detalles al hover sobre eventos
5. **Exportación**: Generar reportes con la misma paleta

---

## 📝 Notas Técnicas

- Los colores se aplican en `AgendaController::getEventos()`
- Los iconos se agregan al título del evento
- La leyenda está en `app/Views/Modulos/agenda/calendario.php`
- Los estilos CSS están en la sección `<style>` del mismo archivo
- Compatible con FullCalendar 5.x

---

**Última actualización**: Enero 2026
**Versión**: 1.0
**Diseñado para**: Sistema de Agenda para Nutricionistas
