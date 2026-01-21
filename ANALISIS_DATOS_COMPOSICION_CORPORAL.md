# 📊 Análisis de Datos para Composición Corporal - Métodos 2, 3, 4, 5, 6, 7 Componentes

## 🎯 Objetivo
Identificar todos los datos necesarios para implementar métodos de composición corporal de 2, 3, 4, 5, 6 y 7 componentes, y determinar qué falta agregar a la ficha clínica actual.

---

## 📋 DATOS ACTUALES EN EL SISTEMA

### ✅ Medidas Básicas
- `peso_actual` (kg) ✅
- `altura_actual` (cm) ✅
- `imc_actual` ✅ (calculado)

### ✅ Circunferencias/Perímetros
- `circunferencia_cintura` (cm) ✅
- `circunferencia_cadera` (cm) ✅

### ✅ Pliegues Cutáneos (7 pliegues)
- `pliegue_tricipital` (mm) ✅
- `pliegue_bicipital` (mm) ✅
- `pliegue_subescapular` (mm) ✅
- `pliegue_suprailíaco` (mm) ✅
- `pliegue_abdominal` (mm) ✅
- `pliegue_muslo_anterior` (mm) ✅
- `pliegue_pantorrilla_medial` (mm) ✅
- `suma_pliegues` (mm) ✅ (calculado)

### ✅ Composición Corporal Básica
- `grasa_corporal` (%) ✅ (manual)
- `grasa_corporal_calculada` (%) ✅ (calculado)
- `masa_muscular` (kg) ✅ (manual)

### ✅ Datos del Paciente (en tabla `pacientes`)
- `fecha_nacimiento` ✅ (para calcular edad)
- `genero` ✅ (M/F/O)

---

## ❌ DATOS FALTANTES PARA MÉTODOS COMPLETOS

### 🔴 CRÍTICOS (Necesarios para múltiples métodos)

#### 1. Altura Sentado
- **Campo:** `altura_sentado` (cm)
- **Uso:** Métodos 4, 5, 6, 7 componentes, somatotipo
- **Descripción:** Altura del paciente sentado, desde el asiento hasta la parte superior de la cabeza

#### 2. Perímetros/Circunferencias Adicionales
- **Campos:**
  - `circunferencia_brazo_relajado` (cm) - Brazo relajado
  - `circunferencia_brazo_contraido` (cm) - Brazo contraído/flexionado
  - `circunferencia_muslo_medio` (cm) - Muslo en su punto medio
  - `circunferencia_pantorrilla` (cm) - Pantorrilla en su punto máximo
  - `circunferencia_cuello` (cm) - Cuello
  - `circunferencia_torax` (cm) - Tórax/pecho
- **Uso:** Métodos 4, 5, 6, 7 componentes, somatotipo, cálculo de masa muscular

#### 3. Diámetros Óseos (NO TENEMOS NINGUNO)
- **Campos:**
  - `diametro_biacromial` (cm) - Ancho de hombros (distancia entre acromiones)
  - `diametro_bi_iliocristal` (cm) - Ancho de cadera (distancia entre crestas ilíacas)
  - `diametro_humero` (cm) - Diámetro biepicondilar del húmero (codo)
  - `diametro_femur` (cm) - Diámetro biepicondilar del fémur (rodilla)
  - `diametro_muneca` (cm) - Diámetro de muñeca
  - `diametro_tobillo` (cm) - Diámetro de tobillo
- **Uso:** Métodos 4, 5, 6, 7 componentes (especialmente para masa ósea), somatotipo

#### 4. Pliegues Cutáneos Adicionales
- **Campos:**
  - `pliegue_pectoral` (mm) - Pliegue pectoral
  - `pliegue_axilar_medio` (mm) - Pliegue axilar medio
  - `pliegue_muslo_medial` (mm) - Pliegue medial del muslo
- **Uso:** Métodos 5, 6, 7 componentes, fórmulas específicas (Jackson-Pollock 9 sitios, etc.)

---

## 📐 REQUERIMIENTOS POR MÉTODO

### 🔵 Método 2 Componentes (Masa Magra / Masa Adiposa)
**Datos necesarios:**
- ✅ Peso
- ✅ Altura
- ✅ Pliegues cutáneos (mínimo 3-4)
- ✅ Género
- ✅ Edad (calculada desde fecha_nacimiento)

**Estado:** ✅ **COMPLETO** - Tenemos todos los datos necesarios

---

### 🔵 Método 3 Componentes (Grasa / Masa Magra / Masa Ósea)
**Datos necesarios:**
- ✅ Peso
- ✅ Altura
- ✅ Pliegues cutáneos
- ❌ Diámetros óseos (húmero, fémur) - **FALTA**
- ✅ Género
- ✅ Edad

**Estado:** ⚠️ **PARCIAL** - Falta diámetros óseos

---

### 🔵 Método 4 Componentes (Grasa / Músculo / Hueso / Residual)
**Datos necesarios:**
- ✅ Peso
- ✅ Altura
- ❌ Altura sentado - **FALTA**
- ✅ Pliegues cutáneos (7 sitios)
- ❌ Diámetros óseos (húmero, fémur) - **FALTA**
- ✅ Perímetros (cintura, cadera)
- ❌ Perímetros adicionales (brazo, muslo) - **FALTA**
- ✅ Género
- ✅ Edad

**Estado:** ⚠️ **PARCIAL** - Faltan: altura sentado, diámetros óseos, perímetros adicionales

---

### 🔵 Método 5 Componentes (Grasa / Músculo / Hueso / Residual / Piel)
**Datos necesarios:**
- ✅ Peso
- ✅ Altura
- ❌ Altura sentado - **FALTA**
- ✅ Pliegues cutáneos (7 sitios)
- ❌ Pliegues adicionales (pectoral, axilar) - **FALTA** (opcional pero recomendado)
- ❌ Diámetros óseos (húmero, fémur, biacromial, bi-iliocristal) - **FALTA**
- ✅ Perímetros (cintura, cadera)
- ❌ Perímetros adicionales (brazo, muslo, pantorrilla) - **FALTA**
- ✅ Género
- ✅ Edad

**Estado:** ⚠️ **PARCIAL** - Faltan: altura sentado, diámetros óseos, perímetros adicionales, pliegues adicionales

---

### 🔵 Método 6 Componentes
**Datos necesarios:**
- Similar a 5 componentes + mediciones adicionales específicas
- ❌ Todos los datos del método 5 - **FALTA**
- ❌ Mediciones adicionales según método específico - **FALTA**

**Estado:** ❌ **INCOMPLETO**

---

### 🔵 Método 7 Componentes
**Datos necesarios:**
- Similar a 5-6 componentes + mediciones muy específicas
- ❌ Todos los datos del método 5-6 - **FALTA**
- ❌ Mediciones adicionales muy específicas - **FALTA**

**Estado:** ❌ **INCOMPLETO**

---

### 🔵 Somatotipo (Heath-Carter)
**Datos necesarios:**
- ✅ Peso
- ✅ Altura
- ❌ Altura sentado - **FALTA**
- ✅ Pliegues cutáneos (mínimo 3: tricipital, subescapular, suprailíaco)
- ❌ Diámetros óseos (húmero, fémur) - **FALTA**
- ❌ Perímetros (brazo contraído, pantorrilla) - **FALTA**
- ✅ Género
- ✅ Edad

**Estado:** ⚠️ **PARCIAL** - Faltan: altura sentado, diámetros óseos, perímetros adicionales

---

## 📊 RESUMEN DE CAMPOS A AGREGAR

### Prioridad ALTA (Necesarios para métodos 2, 4, 5 componentes y somatotipo)

#### 1. Altura Sentado
```sql
`altura_sentado` DECIMAL(5,2) NULL COMMENT 'Altura sentado en cm'
```

#### 2. Perímetros Adicionales (6 campos)
```sql
`circunferencia_brazo_relajado` DECIMAL(5,2) NULL COMMENT 'Brazo relajado en cm',
`circunferencia_brazo_contraido` DECIMAL(5,2) NULL COMMENT 'Brazo contraído en cm',
`circunferencia_muslo_medio` DECIMAL(5,2) NULL COMMENT 'Muslo medio en cm',
`circunferencia_pantorrilla` DECIMAL(5,2) NULL COMMENT 'Pantorrilla en cm',
`circunferencia_cuello` DECIMAL(5,2) NULL COMMENT 'Cuello en cm',
`circunferencia_torax` DECIMAL(5,2) NULL COMMENT 'Tórax en cm'
```

#### 3. Diámetros Óseos (6 campos)
```sql
`diametro_biacromial` DECIMAL(5,2) NULL COMMENT 'Diámetro biacromial en cm',
`diametro_bi_iliocristal` DECIMAL(5,2) NULL COMMENT 'Diámetro bi-iliocristal en cm',
`diametro_humero` DECIMAL(5,2) NULL COMMENT 'Diámetro biepicondilar húmero en cm',
`diametro_femur` DECIMAL(5,2) NULL COMMENT 'Diámetro biepicondilar fémur en cm',
`diametro_muneca` DECIMAL(5,2) NULL COMMENT 'Diámetro muñeca en cm',
`diametro_tobillo` DECIMAL(5,2) NULL COMMENT 'Diámetro tobillo en cm'
```

### Prioridad MEDIA (Para métodos más avanzados)

#### 4. Pliegues Adicionales (3 campos)
```sql
`pliegue_pectoral` DECIMAL(5,2) NULL COMMENT 'Pliegue pectoral en mm',
`pliegue_axilar_medio` DECIMAL(5,2) NULL COMMENT 'Pliegue axilar medio en mm',
`pliegue_muslo_medial` DECIMAL(5,2) NULL COMMENT 'Pliegue medial muslo en mm'
```

---

## 🗄️ ESTRUCTURA PROPUESTA PARA BASE DE DATOS

### Opción 1: Agregar todos los campos a `historial_clinico`
**Ventajas:**
- Simple, todo en una tabla
- Fácil de consultar

**Desventajas:**
- Tabla muy ancha (muchas columnas)
- Muchos campos NULL si no se usan todos

### Opción 2: Crear tabla separada `mediciones_antropometricas`
**Ventajas:**
- Tabla `historial_clinico` más limpia
- Permite versionar mediciones
- Más flexible para agregar nuevos campos

**Desventajas:**
- Requiere JOIN para consultas
- Más complejo

### ⭐ RECOMENDACIÓN: Opción 1 (Agregar a `historial_clinico`)
**Razón:** Ya tenemos muchos campos de mediciones en `historial_clinico`, mantener todo junto es más simple y eficiente para este caso de uso.

---

## 📝 CAMPOS CALCULADOS ADICIONALES (Opcionales)

Estos se pueden calcular automáticamente:

```sql
-- Para somatotipo
`indice_ectomorfia` DECIMAL(5,2) NULL COMMENT 'Índice de ectomorfia',
`indice_mesomorfia` DECIMAL(5,2) NULL COMMENT 'Índice de mesomorfia',
`indice_endomorfia` DECIMAL(5,2) NULL COMMENT 'Índice de endomorfia',

-- Para composición corporal
`masa_osea` DECIMAL(5,2) NULL COMMENT 'Masa ósea en kg',
`masa_residual` DECIMAL(5,2) NULL COMMENT 'Masa residual en kg',
`masa_piel` DECIMAL(5,2) NULL COMMENT 'Masa de la piel en kg',
`masa_magra` DECIMAL(5,2) NULL COMMENT 'Masa magra total en kg',
`masa_adiposa` DECIMAL(5,2) NULL COMMENT 'Masa adiposa total en kg',
`porcentaje_masa_muscular` DECIMAL(5,2) NULL COMMENT 'Porcentaje de masa muscular',
`porcentaje_masa_osea` DECIMAL(5,2) NULL COMMENT 'Porcentaje de masa ósea',
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Fase 1: Base de Datos
- [ ] Agregar campo `altura_sentado`
- [ ] Agregar 6 campos de perímetros adicionales
- [ ] Agregar 6 campos de diámetros óseos
- [ ] Agregar 3 campos de pliegues adicionales (opcional)
- [ ] Actualizar modelo `HistorialClinico` con nuevos campos en `allowedFields`

### Fase 2: Formularios
- [ ] Agregar campos en `app/Views/Modulos/agenda/consulta.php`
- [ ] Agregar campos en `app/Views/Modulos/historial/registro.php`
- [ ] Agregar campos en `app/Views/Modulos/historial/editar.php`
- [ ] Organizar campos por secciones (Pliegues, Perímetros, Diámetros)

### Fase 3: Controladores
- [ ] Actualizar `AgendaController::guardarMediciones()` para guardar nuevos campos
- [ ] Actualizar `HistorialController::registrar()` y `update()` para nuevos campos

### Fase 4: Validaciones
- [ ] Agregar validaciones de rangos razonables para cada campo
- [ ] Agregar ayuda contextual en formularios

---

## 📚 REFERENCIAS

- **Método 2 Componentes:** Durnin-Womersley, Jackson-Pollock
- **Método 4 Componentes:** De Rose, Matiegka
- **Método 5 Componentes:** Francis Holway (Fractionation of Body Mass)
- **Somatotipo:** Heath-Carter

---

## 🎯 PRÓXIMOS PASOS

1. ✅ **Este documento** - Identificar qué falta
2. ⏭️ Crear script SQL para agregar campos faltantes
3. ⏭️ Actualizar modelos y controladores
4. ⏭️ Actualizar formularios con nuevos campos
5. ⏭️ Implementar métodos de cálculo (2, 4, 5 componentes)
6. ⏭️ Sistema de control de acceso por plan (qué métodos puede ver cada nutricionista)
