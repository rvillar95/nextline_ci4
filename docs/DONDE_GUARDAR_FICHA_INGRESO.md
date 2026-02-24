# Dónde guardar los datos de la Ficha de Ingreso Adulto

## Criterio general

- **Paciente:** Datos de la **persona** que cambian poco o que querés ver en todas las consultas (nombre, rut, alergias conocidas, condiciones médicas de base, medicación habitual).
- **Historial clínico:** Datos de **cada consulta/evaluación**, ligados a `paciente_id` y a `detalle_agenda_id` (la cita). Pueden cambiar en cada control.

---

## Recomendación por bloque de la ficha

### 1. Datos personales (nombre, rut, edad, sexo, correo, ocupación, horarios)

- **Dónde:** En su mayoría ya están en **`pacientes`** (nombre, apellido, rut_dni, fecha_nacimiento, genero, telefono, email).
- **Agregar en `pacientes`:** `ocupacion` (VARCHAR) si querés registrarla aparte; si no, podés usar `observaciones`.

### 2. ANAMNESIS CLÍNICA (tabaco, alcohol, drogas, enfermedad de base/RCV, signos/síntomas, tránsito intestinal/Bristol/diuresis, medicamentos, suplementos, ingesta hídrica, actividad física, sueño)

- **Dónde:** **`historial_clinico`** (por consulta).
- **Cómo:** Agregar un campo **`anamnesis_clinica`** (TEXT o JSON). Todo lo que hoy va en el Excel en “Anamnesis clínica” se guarda ahí para esa fecha/consulta. Así podés tener una anamnesis clínica distinta en evaluación inicial y en cada control.
- El campo actual **`anamnesis`** puede seguir usándose para texto libre o resumen; `anamnesis_clinica` sería el bloque estructurado de la ficha.

### 3. EXAMENES BIOQUÍMICOS (nombre, valor, fecha/interpretación)

- **Dónde:** **Por consulta**, no por paciente global.
- **Cómo:** Tabla nueva **`historial_examen_bioquimico`**:
  - `id`, `historial_clinico_id`, `nombre` (ej. “Glicemia”), `valor`, `fecha_interpretacion` (o texto interpretación).
- Así cada registro de historial puede tener varios exámenes y en controles futuros podés cargar otros sin mezclar con la evaluación inicial.

### 4. ANAMNESIS ALIMENTARIA (relación familiar, apetito, dieta restrictiva, ansiedad, quién cocina, etc.)

- **Dónde:** **`historial_clinico`** (por consulta).
- **Cómo:** Agregar **`anamnesis_alimentaria`** (TEXT o JSON). Todo el bloque “Anamnesis alimentaria” de la ficha (preguntas abiertas) para esa consulta.

### 5. Tendencia de consumo (grupos de alimentos + preferencia + alergias/intolerancias)

- **Dónde:** Combinación:
  - **Resumen de alergias/intolerancias** que sean “de siempre”: en **`pacientes.alergias`** (texto libre), para que aparezca en todas las consultas.
  - **Detalle por consulta (preferencias + alergias por grupo):** en **`historial_clinico`**, en un campo **`tendencia_consumo`** (JSON), por ejemplo:  
    `[ {"grupo":"Lácteos","preferencia":"Alto consumo","alergia_intolerancia":"Intolerancia lactosa"}, ... ]`
- Así en cada control podés actualizar preferencias y alergias sin borrar lo anterior y sin duplicar todo en paciente.

### 6. Recordatorio 24 h (desayuno, colación, almuerzo, once, cena, etc.)

- **Dónde:** **`historial_clinico`** (por consulta).
- **Cómo:** Campo **`recordatorio_24h`** (TEXT o JSON). Ejemplo JSON:  
  `{"desayuno":{"hora":"08:00","contenido":"..."}, "colacion1":{...}, "almuerzo":{...}, ...}`  
  o un texto libre por comida. Es dato del día/consulta, no del paciente en general.

---

## Resumen de tablas

| Dato | Tabla | Campo / tabla nueva |
|------|--------|----------------------|
| Ocupación | `pacientes` | `ocupacion` (opcional) |
| Anamnesis clínica (tabaco, alcohol, medicamentos, sueño, etc.) | `historial_clinico` | `anamnesis_clinica` (TEXT o JSON) |
| Exámenes bioquímicos | Nueva tabla | `historial_examen_bioquimico` (historial_id, nombre, valor, fecha_interpretacion) |
| Anamnesis alimentaria (preguntas abiertas) | `historial_clinico` | `anamnesis_alimentaria` (TEXT o JSON) |
| Tendencia de consumo (por grupo) | `historial_clinico` | `tendencia_consumo` (JSON) |
| Alergias “de siempre” | `pacientes` | Ya existe `alergias` |
| Recordatorio 24 h | `historial_clinico` | `recordatorio_24h` (TEXT o JSON) |

---

## Relación con la agenda

- **`historial_clinico.detalle_agenda_id`** ya relaciona cada registro de historial con una cita (`detalle_agenda`).
- Al “Guardar y finalizar consulta” desde Agenda → Consulta, se crea o actualiza el `historial_clinico` de ese `detalle_agenda_id` y se pueden rellenar estos nuevos campos desde el formulario de la ficha.

Con esto, todo lo de la ficha queda guardado: lo estable del paciente en **paciente**, y lo que es de cada evaluación/consulta en **historial_clinico** (y exámenes en la tabla nueva).
