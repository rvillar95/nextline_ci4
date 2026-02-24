# 📋 Plan de Implementación: Sistema de Cálculo de Porciones y Plan Alimentario

## 🎯 Objetivo
Integrar el sistema de cálculo de porciones, calorimetría y plan alimentario desde las planillas Excel al sistema CodeIgniter 4, asociando todo a `detalle_agenda`, `paciente` y `nutricionista`.

---

## 📊 Análisis de las Planillas

### 1. **CALORIMETRIA** (Planilla Cálculo según porciones.xlsx)
**Inputs:**
- Peso (kg)
- Talla (cm)
- Edad
- Sexo (implícito para elegir fórmula Hombres/Mujeres)
- Minutos/día por actividad (ACTIVIDADES DIARIAS y DEPORTES)

**Fórmulas:**
- **Hombres:** `TMB = 66 + (13.7 × peso) + (5 × talla) - (6.8 × edad)`
- **Mujeres:** `TMB = 655 + (9.6 × peso) + (1.8 × talla) - (4.7 × edad)`
- **Calorías por actividad:** `Calorías = METs × (minutos/60) × (TMB/24)`

**Outputs:**
- M.B. (Metabolismo Basal) Hombres/Mujeres
- Calorías por actividad (Hombres/Mujeres)
- Total minutos
- Cals habituales
- Cals entrenamiento
- **Requerimiento total** (TMB + actividades)

### 2. **REQUERIMIENTO** (Planilla Cálculo según porciones.xlsx)
**Inputs:**
- Requerimiento energético total (kcal)
- Distribución calórica (%):
  - Proteínas (%)
  - Grasas (%)
  - CHO (%)
- Porciones por grupo alimentario (Cereales, Verduras, Frutas, Lácteos, Proteínas, etc.)

**Cálculos:**
- Macros en gramos: `(Requerimiento × %) / (kcal por gramo)`
  - Proteínas: `/4`
  - Grasas: `/9`
  - CHO: `/4`
- Calorías/CHO/Lípidos/Proteínas por grupo: `Porciones × VLOOKUP(código, porciones, ...)`

**Outputs:**
- Macros objetivo (g)
- Totales del plan (kcal, CHO, lípidos, proteínas)
- Adecuación porcentual (plan vs objetivo)

### 3. **CONTADOR PORCIONES PARA PLANIFICAR** (CALCULO DE PORCIONES.xlsx)
**Inputs:**
- Porciones objetivo por grupo
- Distribución por comidas (desayuno, colación, almuerzo, cena, once, otros)

**Cálculos:**
- `Sobra = Porciones objetivo - Suma(porciones por comida)`

**Outputs:**
- Porciones asignadas por comida
- Sobra (feedback visual: verde=ok, rojo=exceso/déficit)

### 4. **TABLA PORCIONES** (Catálogo de intercambios)
**Estructura:**
```
codigo | nombre                    | kcal | cho | lipidos | proteinas
cer    | cereales                  | 140  | 30  | 1       | 3
ver    | verduras                  | 30   | 5   | 0       | 2
vdl    | verduras de libre consumo | 10   | 2.5 | 0       | 0
fru    | frutas                    | 65   | 15  | 0       | 1
lag    | lacteos altos en grasa    | 110  | 9   | 6       | 5
lmg    | lacteos medios en grasa    | 85   | 9   | 3       | 5
lbg    | lacteos bajos en grasa    | 70   | 10  | 0       | 7
pag    | proteinas altas en grasa   | 120  | 1   | 8       | 11
pbg    | proteinas bajas en grasa   | 65   | 1   | 2       | 11
ls     | legumbres secas           | 170  | 30  | 1       | 11
ayg    | aceite y grasas            | 180  | 0   | 20      | 0
rl     | alimentos ricos en grasas  | 175  | 5   | 15      | 5
azu    | azucares                  | 20   | 5   | 0       | 0
```

---

## 🗄️ Modelo de Datos Propuesto

### Tablas Nuevas

#### 1. `intercambio_porcion` (Catálogo de intercambios)
```sql
CREATE TABLE intercambio_porcion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(10) NOT NULL UNIQUE COMMENT 'cer, ver, fru, etc.',
    nombre VARCHAR(100) NOT NULL COMMENT 'Cereales, Verduras, etc.',
    kcal DECIMAL(8,2) NOT NULL COMMENT 'Calorías por intercambio',
    cho_g DECIMAL(8,2) NOT NULL COMMENT 'Carbohidratos (g) por intercambio',
    grasa_g DECIMAL(8,2) NOT NULL COMMENT 'Grasas (g) por intercambio',
    prot_g DECIMAL(8,2) NOT NULL COMMENT 'Proteínas (g) por intercambio',
    empresa_id INT NULL COMMENT 'NULL = global, o específico por empresa',
    activo CHAR(1) DEFAULT 'A',
    fcreacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_empresa (empresa_id),
    INDEX idx_codigo (codigo)
) COMMENT 'Catálogo de intercambios/porciones estándar';
```

#### 2. `calorimetria` (Cálculo de gasto calórico)
```sql
CREATE TABLE calorimetria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    detalle_agenda_id INT NOT NULL COMMENT 'FK a detalle_agenda',
    paciente_id INT NOT NULL,
    nutricionista_id INT NOT NULL COMMENT 'FK a usuario (nutricionista)',
    
    -- Inputs
    peso DECIMAL(6,2) NOT NULL COMMENT 'kg',
    talla DECIMAL(6,2) NOT NULL COMMENT 'cm',
    edad INT NOT NULL,
    sexo ENUM('M', 'F') NOT NULL,
    
    -- Cálculos TMB
    tmb_hombres DECIMAL(8,2) NULL,
    tmb_mujeres DECIMAL(8,2) NULL,
    tmb_usado DECIMAL(8,2) NOT NULL COMMENT 'TMB seleccionado según sexo',
    
    -- Totales actividades
    total_minutos INT DEFAULT 0,
    cals_habituales DECIMAL(8,2) DEFAULT 0,
    cals_entrenamiento DECIMAL(8,2) DEFAULT 0,
    
    -- Requerimiento final
    requerimiento_total DECIMAL(8,2) NOT NULL COMMENT 'TMB + actividades',
    
    fcreacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_detalle_agenda (detalle_agenda_id),
    INDEX idx_paciente (paciente_id),
    INDEX idx_nutricionista (nutricionista_id),
    FOREIGN KEY (detalle_agenda_id) REFERENCES detalle_agenda(id),
    FOREIGN KEY (paciente_id) REFERENCES paciente(id),
    FOREIGN KEY (nutricionista_id) REFERENCES usuario(id)
) COMMENT 'Cálculo de calorimetría por consulta';
```

#### 3. `calorimetria_actividad` (Detalle de actividades)
```sql
CREATE TABLE calorimetria_actividad (
    id INT PRIMARY KEY AUTO_INCREMENT,
    calorimetria_id INT NOT NULL,
    tipo ENUM('diaria', 'deporte') NOT NULL,
    actividad VARCHAR(100) NOT NULL COMMENT 'Durmiendo, Caminar rápido, etc.',
    mets DECIMAL(5,2) NOT NULL,
    minutos_dia INT NOT NULL DEFAULT 0,
    calorias_hombre DECIMAL(8,2) DEFAULT 0,
    calorias_mujer DECIMAL(8,2) DEFAULT 0,
    orden INT DEFAULT 0,
    INDEX idx_calorimetria (calorimetria_id),
    FOREIGN KEY (calorimetria_id) REFERENCES calorimetria(id) ON DELETE CASCADE
) COMMENT 'Actividades registradas en calorimetría';
```

#### 4. `plan_alimentario` (Plan nutricional)
```sql
CREATE TABLE plan_alimentario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    detalle_agenda_id INT NOT NULL,
    paciente_id INT NOT NULL,
    nutricionista_id INT NOT NULL,
    calorimetria_id INT NULL COMMENT 'FK opcional a calorimetria',
    
    -- Requerimiento objetivo
    requerimiento_kcal DECIMAL(8,2) NOT NULL,
    prot_porcentaje DECIMAL(5,2) NOT NULL COMMENT '%',
    grasa_porcentaje DECIMAL(5,2) NOT NULL COMMENT '%',
    cho_porcentaje DECIMAL(5,2) NOT NULL COMMENT '%',
    
    -- Macros objetivo (calculados)
    prot_gramos DECIMAL(8,2) NOT NULL,
    grasa_gramos DECIMAL(8,2) NOT NULL,
    cho_gramos DECIMAL(8,2) NOT NULL,
    
    -- Adecuación
    adecuacion_min DECIMAL(8,2) NULL COMMENT '90% del requerimiento',
    adecuacion_max DECIMAL(8,2) NULL COMMENT '110% del requerimiento',
    
    -- Totales del plan (calculados desde porciones)
    total_kcal DECIMAL(8,2) DEFAULT 0,
    total_cho DECIMAL(8,2) DEFAULT 0,
    total_grasa DECIMAL(8,2) DEFAULT 0,
    total_prot DECIMAL(8,2) DEFAULT 0,
    
    -- Adecuación porcentual (calculada)
    adecuacion_kcal_porc DECIMAL(5,2) NULL,
    adecuacion_cho_porc DECIMAL(5,2) NULL,
    adecuacion_grasa_porc DECIMAL(5,2) NULL,
    adecuacion_prot_porc DECIMAL(5,2) NULL,
    
    observaciones TEXT NULL,
    fcreacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_detalle_agenda (detalle_agenda_id),
    INDEX idx_paciente (paciente_id),
    INDEX idx_nutricionista (nutricionista_id),
    FOREIGN KEY (detalle_agenda_id) REFERENCES detalle_agenda(id),
    FOREIGN KEY (paciente_id) REFERENCES paciente(id),
    FOREIGN KEY (nutricionista_id) REFERENCES usuario(id)
) COMMENT 'Plan alimentario por consulta';
```

#### 5. `plan_alimentario_porcion` (Porciones por grupo)
```sql
CREATE TABLE plan_alimentario_porcion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_alimentario_id INT NOT NULL,
    intercambio_porcion_id INT NOT NULL,
    porciones DECIMAL(6,2) NOT NULL DEFAULT 0 COMMENT 'Cantidad de intercambios',
    
    -- Calculados (porciones × valores del intercambio)
    calorias DECIMAL(8,2) DEFAULT 0,
    cho DECIMAL(8,2) DEFAULT 0,
    grasa DECIMAL(8,2) DEFAULT 0,
    prot DECIMAL(8,2) DEFAULT 0,
    
    orden INT DEFAULT 0,
    INDEX idx_plan (plan_alimentario_id),
    INDEX idx_intercambio (intercambio_porcion_id),
    FOREIGN KEY (plan_alimentario_id) REFERENCES plan_alimentario(id) ON DELETE CASCADE,
    FOREIGN KEY (intercambio_porcion_id) REFERENCES intercambio_porcion(id)
) COMMENT 'Porciones asignadas por grupo en el plan';
```

#### 6. `plan_alimentario_comida` (Distribución por comidas)
```sql
CREATE TABLE plan_alimentario_comida (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_alimentario_id INT NOT NULL,
    comida ENUM('desayuno', 'colacion', 'almuerzo', 'cena', 'once', 'otros') NOT NULL,
    porcentaje_vct DECIMAL(5,2) NULL COMMENT '% del VCT',
    minuta TEXT NULL COMMENT 'Descripción de la preparación',
    
    -- Totales por comida (calculados)
    total_kcal DECIMAL(8,2) DEFAULT 0,
    total_cho DECIMAL(8,2) DEFAULT 0,
    total_grasa DECIMAL(8,2) DEFAULT 0,
    total_prot DECIMAL(8,2) DEFAULT 0,
    
    orden INT DEFAULT 0,
    INDEX idx_plan (plan_alimentario_id),
    FOREIGN KEY (plan_alimentario_id) REFERENCES plan_alimentario(id) ON DELETE CASCADE
) COMMENT 'Comidas del plan alimentario';
```

#### 7. `plan_alimentario_item` (Items por comida)
```sql
CREATE TABLE plan_alimentario_item (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_alimentario_comida_id INT NOT NULL,
    intercambio_porcion_id INT NOT NULL,
    ingrediente VARCHAR(200) NULL COMMENT 'Nombre del alimento/ingrediente',
    porciones DECIMAL(6,2) NOT NULL DEFAULT 0,
    medida_casera VARCHAR(100) NULL COMMENT '1/2 pan, 1 taza, etc.',
    gramaje VARCHAR(50) NULL COMMENT '50g, etc.',
    
    -- Calculados
    calorias DECIMAL(8,2) DEFAULT 0,
    cho DECIMAL(8,2) DEFAULT 0,
    grasa DECIMAL(8,2) DEFAULT 0,
    prot DECIMAL(8,2) DEFAULT 0,
    
    costo_promedio DECIMAL(10,2) NULL,
    cantidad_comprada VARCHAR(100) NULL,
    observacion TEXT NULL,
    orden INT DEFAULT 0,
    INDEX idx_comida (plan_alimentario_comida_id),
    INDEX idx_intercambio (intercambio_porcion_id),
    FOREIGN KEY (plan_alimentario_comida_id) REFERENCES plan_alimentario_comida(id) ON DELETE CASCADE,
    FOREIGN KEY (intercambio_porcion_id) REFERENCES intercambio_porcion(id)
) COMMENT 'Items específicos por comida';
```

---

## 🔄 Flujo de Trabajo Propuesto

### Paso 1: Calorimetría (en consulta)
1. Nutricionista abre `detalle_agenda` (consulta).
2. Click en "Calcular Calorimetría".
3. **Formulario:**
   - Inputs: Peso, Talla, Edad, Sexo
   - Tabla de ACTIVIDADES DIARIAS (pre-cargadas con METs):
     - Columna "Minutos/día" (input)
     - Columna "Calorías Hombre" (calculado)
     - Columna "Calorías Mujer" (calculado)
   - Tabla de DEPORTES (pre-cargadas con METs):
     - Misma estructura
4. **Cálculos automáticos:**
   - TMB según sexo
   - Calorías por actividad
   - Totales (minutos, cals habituales, entrenamiento)
   - Requerimiento total
5. **Guardar** → Crea registro en `calorimetria` + `calorimetria_actividad`.

### Paso 2: Plan Alimentario (en la misma consulta)
1. Desde la misma consulta, click en "Crear Plan Alimentario".
2. **Si hay calorimetría:** Pre-llena "Requerimiento energético" con `requerimiento_total`.
3. **Formulario:**
   - Requerimiento energético (kcal)
   - Distribución calórica (% Proteínas, Grasas, CHO)
   - **Tabla de porciones por grupo:**
     - Grupo (dropdown desde `intercambio_porcion`)
     - Porciones (input numérico)
     - Calorías/CHO/Grasa/Prot (calculados automáticamente)
4. **Cálculos automáticos:**
   - Macros objetivo (g)
   - Totales del plan
   - Adecuación porcentual
5. **Guardar** → Crea `plan_alimentario` + `plan_alimentario_porcion`.

### Paso 3: Distribución por Comidas (opcional, después)
1. Desde el plan guardado, click en "Distribuir por Comidas".
2. **Vista tipo "Contador de Porciones":**
   - Columna: Porciones objetivo (desde `plan_alimentario_porcion`)
   - Columnas: Desayuno, Colación, Almuerzo, Cena, Once, Otros
   - Fila por grupo alimentario
   - Input: Porciones asignadas por comida
   - Columna "Sobra" (calculada, con color: verde=ok, rojo=exceso/déficit)
3. **Detalle de cada comida:**
   - Click en comida → Abre modal/formulario
   - Lista de items (ingredientes)
   - Por cada item: Código intercambio, Porciones, Medida casera, Gramaje
   - Totales por comida (calculados)
4. **Guardar** → Actualiza `plan_alimentario_comida` + `plan_alimentario_item`.

---

## 🎨 Integración en el Sistema Actual

### Ubicación en la UI

#### Opción A: En `agenda/consulta` (Recomendada)
- Agregar pestañas/secciones:
  1. **"Calorimetría"** → Formulario de cálculo
  2. **"Plan Alimentario"** → Formulario de porciones
  3. **"Distribución por Comidas"** → Tabla contador + detalle comidas
- **Ventaja:** Todo en un solo lugar, asociado a la consulta.

#### Opción B: En `historial` (Alternativa)
- Agregar sección "Planes Alimentarios" que liste planes por paciente.
- **Ventaja:** Historial centralizado.

**Recomendación:** **Opción A** (en consulta), porque:
- El nutricionista trabaja durante la consulta
- Asociación directa con `detalle_agenda`
- Flujo natural: Calorimetría → Plan → Distribución

### Endpoints Propuestos

```
POST /dashboard/agenda/calcularCalorimetria
  - Input: detalle_agenda_id, peso, talla, edad, sexo, actividades[]
  - Output: calorimetria_id, tmb, requerimiento_total

GET /dashboard/agenda/calorimetria/{detalle_agenda_id}
  - Obtiene calorimetría de una consulta

POST /dashboard/agenda/crearPlanAlimentario
  - Input: detalle_agenda_id, requerimiento_kcal, distribucion_%, porciones[]
  - Output: plan_alimentario_id, totales, adecuacion_%

GET /dashboard/agenda/planAlimentario/{detalle_agenda_id}
  - Obtiene plan alimentario de una consulta

POST /dashboard/agenda/distribuirComidas
  - Input: plan_alimentario_id, comidas[] (con items[])
  - Output: plan_alimentario_comida_id[]

GET /dashboard/agenda/planAlimentario/{plan_id}/comidas
  - Obtiene distribución por comidas
```

---

## 🔧 Servicios PHP Propuestos

### `CalorimetriaService.php`
```php
class CalorimetriaService {
    public function calcularTMB($peso, $talla, $edad, $sexo): float
    public function calcularCaloriasActividad($mets, $minutos, $tmb): float
    public function calcularRequerimientoTotal($tmb, $actividades): float
    public function guardarCalorimetria($detalleAgendaId, $data): int
}
```

### `PlanAlimentarioService.php`
```php
class PlanAlimentarioService {
    public function calcularMacros($kcal, $prot_%, $grasa_%, $cho_%): array
    public function calcularPorciones($porciones, $intercambioId): array
    public function calcularTotalesPlan($porciones): array
    public function calcularAdecuacion($totales, $objetivos): array
    public function guardarPlan($detalleAgendaId, $data): int
    public function distribuirComidas($planId, $distribucion): bool
}
```

---

## 📝 Checklist de Implementación

### Fase 1: Base de Datos
- [ ] Crear migración SQL con todas las tablas
- [ ] Insertar datos iniciales de `intercambio_porcion` (13 registros)
- [ ] Insertar catálogo de actividades con METs (ACTIVIDADES DIARIAS + DEPORTES)

### Fase 2: Backend
- [ ] Crear modelos: `IntercambioPorcion`, `Calorimetria`, `CalorimetriaActividad`, `PlanAlimentario`, `PlanAlimentarioPorcion`, `PlanAlimentarioComida`, `PlanAlimentarioItem`
- [ ] Crear servicios: `CalorimetriaService`, `PlanAlimentarioService`
- [ ] Crear controlador: `PlanAlimentarioController` (o extender `AgendaController`)

### Fase 3: Frontend
- [ ] Vista: Calorimetría (formulario + tabla actividades)
- [ ] Vista: Plan Alimentario (formulario + tabla porciones)
- [ ] Vista: Distribución por Comidas (tabla contador + detalle comidas)
- [ ] JavaScript: Cálculos en tiempo real (TMB, macros, adecuación, sobra)

### Fase 4: Integración
- [ ] Agregar pestañas/secciones en `agenda/consulta.php`
- [ ] Rutas en `Routes.php`
- [ ] Permisos en módulos (crear módulo "Plan Alimentario")

### Fase 5: Testing
- [ ] Probar cálculos TMB (hombres/mujeres)
- [ ] Probar cálculos de actividades
- [ ] Probar cálculos de porciones y macros
- [ ] Probar distribución por comidas y "sobra"

---

## 📦 Estructura de Módulos y Add-ons

### **1 Módulo Único: "Plan Alimentario"**

**Propuesta:** Un solo módulo que incluye:
- Calorimetría (cálculo de TMB y gasto calórico)
- Plan Alimentario (porciones por grupo)
- Distribución por Comidas (asignación a desayuno/almuerzo/cena/etc.)

**Razones:**
- ✅ Todo está relacionado (flujo: Calorimetría → Plan → Distribución)
- ✅ Más simple de gestionar (un solo punto de acceso)
- ✅ Mejor UX (todo en un lugar durante la consulta)
- ✅ Puede ser add-on o incluido en paquete base según necesidad

### **Configuración como Add-on (Opcional)**

El módulo puede configurarse como:
- **Incluido en paquete base:** `es_addon = 'N'` → Se asigna a paquetes directamente
- **Add-on premium:** `es_addon = 'S'` → Se gestiona desde módulo "Add-ons" (ya existe)

**Ventaja de Add-on:**
- Permite que empresas lo contraten por separado
- Precio mensual configurable por empresa
- Fechas de inicio/fin controlables
- Estado (activo/suspendido/cancelado)

**SQL para crear el módulo:**
```sql
-- Crear módulo "Plan Alimentario"
INSERT INTO `modulo`
(`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `es_addon`, `precio_mensual`, `fcreacion`)
VALUES
(42, 'Plan Alimentario', 'Cálculo de calorimetría, plan alimentario y distribución por comidas', '/dashboard/plan-alimentario', 'A', 'S', 'N', 'S', 0.00, NOW())
ON DUPLICATE KEY UPDATE
    `nombre` = VALUES(`nombre`),
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `estado` = 'A',
    `es_addon` = VALUES(`es_addon`);

-- Crear rutas del módulo (modulo_detalle)
-- Calorimetría
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`)
VALUES
((SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario'), 'Calcular Calorimetría', '/calcular-calorimetria', 'editar', 'A', 'N', 1),
((SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario'), 'Ver Calorimetría', '/calorimetria/{id}', 'ver', 'A', 'N', 2),
-- Plan Alimentario
((SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario'), 'Crear Plan Alimentario', '/crear-plan', 'editar', 'A', 'N', 3),
((SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario'), 'Ver Plan Alimentario', '/plan/{id}', 'ver', 'A', 'N', 4),
-- Distribución por Comidas
((SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario'), 'Distribuir por Comidas', '/distribuir-comidas/{id}', 'editar', 'A', 'N', 5),
((SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario'), 'Ver Comidas', '/comidas/{id}', 'ver', 'A', 'N', 6);
```

### **Asignación a Paquetes o Add-ons**

**Opción A: Incluido en Paquete Base**
```sql
-- Asignar a un paquete específico
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`)
VALUES (1, (SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario'), 'S');
```

**Opción B: Como Add-on (Gestionado desde módulo Add-ons)**
- El Super Admin puede asignarlo desde `/dashboard/addon/lista`
- Se registra en `empresa_addon` con `tipo = 'modulo'`
- Precio mensual configurable por empresa

---

## ⚠️ Consideraciones

1. **Compatibilidad:** Mantener `plan_alimentacion` (TEXT) en `detalle_agenda` para no romper datos existentes. Opcionalmente, generar texto desde el plan estructurado.

2. **Permisos:** 
   - El módulo "Plan Alimentario" puede ser add-on (`es_addon = 'S'`) o incluido en paquete base (`es_addon = 'N'`)
   - Si es add-on, se gestiona desde el módulo "Add-ons" existente
   - Si está en paquete base, se asigna directamente a paquetes

3. **Validaciones:**
   - Peso, talla, edad > 0
   - Distribución calórica = 100%
   - Porciones >= 0
   - Sobra debe ser >= 0 (o permitir exceso con advertencia)

4. **Performance:** Si hay muchos planes, considerar paginación en historial.

5. **Exportación:** Futuro: Exportar plan a PDF/Excel (similar a las planillas originales).

---

## 🚀 Próximos Pasos

1. **Aprobar este plan** (confirmar estructura de BD y flujo)
2. **Crear migración SQL** con todas las tablas
3. **Implementar Fase 1** (BD + datos iniciales)
4. **Implementar Fase 2** (Backend: modelos + servicios)
5. **Implementar Fase 3** (Frontend: vistas + JS)
6. **Integrar en consulta** (Fase 4)
7. **Testing y ajustes** (Fase 5)

---

¿Te parece bien este plan? ¿Algún ajuste antes de empezar a implementar?
