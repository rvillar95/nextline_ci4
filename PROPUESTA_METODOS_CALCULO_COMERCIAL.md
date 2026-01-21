# 💰 Propuesta Comercial: Métodos de Cálculo de Composición Corporal

## 🎯 Objetivo

Implementar un sistema de métodos de cálculo de composición corporal como **feature premium** que permita:
1. **Monetización**: Diferentes planes con diferentes métodos incluidos
2. **Escalabilidad**: Fácil agregar nuevos métodos en el futuro
3. **UX Profesional**: Interfaz clara que muestre el valor de cada método
4. **Control de Acceso**: Según el plan/paquete de la empresa

---

## 📊 Modelo de Negocio: Planes por Niveles

### **Plan Básico (Gratis/Incluido)**
- ✅ **Método 2 Componentes** (Masa Magra / Masa Adiposa)
- ✅ Cálculo básico de grasa corporal
- ✅ IMC y medidas básicas
- **Público**: Nutricionistas que recién empiezan o tienen pocos pacientes

### **Plan Intermedio (Premium)**
- ✅ Todo del Plan Básico
- ✅ **Método 4 Componentes** (Grasa / Músculo / Hueso / Residual)
- ✅ **Somatotipo** (Heath-Carter)
- ✅ Gráficos comparativos
- **Público**: Nutricionistas con práctica establecida
- **Precio sugerido**: $9.990 - $19.990/mes adicional

### **Plan Avanzado (Premium Plus)**
- ✅ Todo del Plan Intermedio
- ✅ **Método 5 Componentes** (Grasa / Músculo / Hueso / Residual / Piel)
- ✅ **Método 6 Componentes** (futuro)
- ✅ **Método 7 Componentes** (futuro)
- ✅ Reportes avanzados y comparativas históricas
- ✅ Exportación de datos
- **Público**: Clínicas, centros deportivos, nutricionistas especializados
- **Precio sugerido**: $19.990 - $39.990/mes adicional

---

## 🗄️ Estructura de Base de Datos

### ✅ **INTEGRACIÓN CON SISTEMA DE PAQUETES EXISTENTE**

En lugar de crear un sistema paralelo, **reutilizamos la infraestructura de paquetes** que ya existe:

### 1. Tabla `metodos_calculo` (Catálogo de métodos)

```sql
CREATE TABLE IF NOT EXISTS `metodos_calculo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL COMMENT 'Nombre del método',
  `slug` VARCHAR(50) NOT NULL COMMENT 'Slug único',
  `descripcion` TEXT COMMENT 'Descripción del método',
  `componentes` INT NOT NULL COMMENT 'Número de componentes (2, 3, 4, 5, 6, 7)',
  `formula` TEXT COMMENT 'Fórmula o referencia al método',
  `requiere_datos` JSON COMMENT 'Array de campos requeridos',
  `activo` ENUM('A', 'I') DEFAULT 'A',
  `orden` INT DEFAULT 0,
  `fcreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Datos iniciales:**
```sql
INSERT INTO `metodos_calculo` (`nombre`, `slug`, `descripcion`, `componentes`, `requiere_datos`) VALUES
('Masa Magra / Masa Adiposa', '2-componentes', 'Método de 2 componentes que separa masa magra y masa adiposa', 2, '["peso_actual", "altura_actual", "pliegue_tricipital", "pliegue_subescapular", "pliegue_suprailíaco"]'),
('Grasa / Músculo / Hueso / Residual', '4-componentes', 'Método de 4 componentes (Fisionutdep)', 4, '["peso_actual", "altura_actual", "altura_sentado", "diametro_humero", "diametro_femur", "circunferencia_brazo_contraido", "circunferencia_pantorrilla"]'),
('Grasa / Músculo / Hueso / Residual / Piel', '5-componentes', 'Método de 5 componentes (Francis Holway)', 5, '["peso_actual", "altura_actual", "altura_sentado", "diametro_biacromial", "diametro_bi_iliocristal", "diametro_humero", "diametro_femur", "pliegue_pectoral", "pliegue_axilar_medio"]'),
('Somatotipo (Heath-Carter)', 'somatotipo', 'Cálculo de somatotipo: endomorfo, mesomorfo, ectomorfo', 3, '["peso_actual", "altura_actual", "altura_sentado", "pliegue_tricipital", "pliegue_subescapular", "pliegue_suprailíaco", "diametro_humero", "diametro_femur", "circunferencia_brazo_contraido", "circunferencia_pantorrilla"]');
```

### 2. Tabla `paquete_metodo_calculo` (Similar a `paquete_modulo`)

**✅ REUTILIZA LA MISMA LÓGICA QUE `paquete_modulo`**

```sql
CREATE TABLE IF NOT EXISTS `paquete_metodo_calculo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `paquete_id` INT NOT NULL COMMENT 'ID del paquete',
  `metodo_calculo_id` INT NOT NULL COMMENT 'ID del método de cálculo',
  `incluido` CHAR(1) DEFAULT 'S' COMMENT 'S=Incluido, N=Bloqueado',
  `fcreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paquete_metodo` (`paquete_id`, `metodo_calculo_id`),
  KEY `paquete_id` (`paquete_id`),
  KEY `metodo_calculo_id` (`metodo_calculo_id`),
  CONSTRAINT `fk_paquete_metodo_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_paquete_metodo_metodo` FOREIGN KEY (`metodo_calculo_id`) REFERENCES `metodos_calculo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Asignación de métodos a paquetes:**
```sql
-- Paquete "NextLine Nutrición" (id=4) - Plan Básico
INSERT INTO `paquete_metodo_calculo` (`paquete_id`, `metodo_calculo_id`, `incluido`) VALUES
(4, 1, 'S'); -- Solo método 2 componentes

-- Crear nuevos paquetes para planes premium
-- Paquete "NextLine Nutrición Premium" (id=5) - Plan Intermedio
INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`) VALUES
('NextLine Nutrición Premium', 'nutricion-premium', 'Plan intermedio con métodos avanzados', 0, 19990, 'A', 5);

INSERT INTO `paquete_metodo_calculo` (`paquete_id`, `metodo_calculo_id`, `incluido`) VALUES
(5, 1, 'S'), -- Método 2 componentes
(5, 2, 'S'), -- Método 4 componentes
(5, 4, 'S'); -- Somatotipo

-- Paquete "NextLine Nutrición Avanzado" (id=6) - Plan Avanzado
INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `activo`, `orden`) VALUES
('NextLine Nutrición Avanzado', 'nutricion-avanzado', 'Plan avanzado con todos los métodos', 0, 39990, 'A', 6);

INSERT INTO `paquete_metodo_calculo` (`paquete_id`, `metodo_calculo_id`, `incluido`) VALUES
(6, 1, 'S'), -- Método 2 componentes
(6, 2, 'S'), -- Método 4 componentes
(6, 3, 'S'), -- Método 5 componentes
(6, 4, 'S'); -- Somatotipo
```

### ✅ **VENTAJAS DE ESTA INTEGRACIÓN:**

1. **Reutiliza infraestructura existente**: Misma lógica que `paquete_modulo`
2. **Consistente**: Mismo patrón de control de acceso
3. **Escalable**: Fácil agregar nuevos métodos o paquetes
4. **Comercial**: Los paquetes ya tienen precios (`precio_mensual`)
5. **Gestión unificada**: Super Admin gestiona métodos igual que módulos

---

## 🎨 Interfaz de Usuario (UX/UI)

### **Vista de Resultados de Composición Corporal**

```
┌─────────────────────────────────────────────────────────────┐
│  📊 Composición Corporal - Resultados                        │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  [Método 2 Componentes] ✅ ACTIVO                            │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ Masa Adiposa: 25.5 kg (35.2%)                      │    │
│  │ Masa Magra: 46.8 kg (64.8%)                         │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  [Método 4 Componentes] 🔒 PREMIUM                           │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ ⚠️ Este método requiere plan Premium               │    │
│  │ [Ver Planes] [Solicitar Demo]                      │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  [Método 5 Componentes] 🔒 PREMIUM PLUS                     │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ ⚠️ Este método requiere plan Premium Plus           │    │
│  │ [Ver Planes] [Solicitar Demo]                      │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  [Somatotipo] 🔒 PREMIUM                                      │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ ⚠️ Este método requiere plan Premium               │    │
│  │ [Ver Planes] [Solicitar Demo]                      │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

### **Características de la UI:**

1. **Badges de Estado:**
   - ✅ **ACTIVO**: Método disponible y calculado
   - 🔒 **PREMIUM**: Requiere upgrade
   - ⚠️ **DATOS FALTANTES**: Método disponible pero faltan datos

2. **Botones de Acción:**
   - **"Ver Planes"**: Lleva a página de planes/precios
   - **"Solicitar Demo"**: Permite ver resultados sin guardar
   - **"Upgrade"**: Proceso de contratación

3. **Comparativa Visual:**
   - Mostrar todos los métodos disponibles
   - Resaltar cuáles están activos
   - Mostrar preview de métodos bloqueados (con marca de agua)

---

## 🔧 Implementación Técnica

### 1. Modelo `MetodoCalculo.php`

```php
class MetodoCalculo extends Model
{
    protected $table = 'metodos_calculo';
    
    /**
     * Obtener métodos disponibles para una empresa según su PAQUETE
     * ✅ REUTILIZA LA MISMA LÓGICA QUE ModuloDetalle::getMenu()
     */
    public function getMetodosDisponibles($empresaId)
    {
        $db = \Config\Database::connect();
        
        // Obtener paquete_id de la empresa (igual que en ModuloDetalle)
        $empresaData = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRowArray();
        
        if (!$empresaData || empty($empresaData['paquete_id'])) {
            return [];
        }
        
        $paqueteId = $empresaData['paquete_id'];
        
        // Obtener métodos del paquete (igual que paquete_modulo)
        $sql = "SELECT 
                    mc.id,
                    mc.nombre,
                    mc.slug,
                    mc.descripcion,
                    mc.componentes,
                    pmc.incluido
                FROM metodos_calculo mc
                INNER JOIN paquete_metodo_calculo pmc ON pmc.metodo_calculo_id = mc.id
                WHERE pmc.paquete_id = :paquete_id:
                  AND pmc.incluido = 'S'
                  AND mc.activo = 'A'
                ORDER BY mc.orden ASC";
        
        return $db->query($sql, ['paquete_id' => $paqueteId])->getResult();
    }
    
    /**
     * Verificar si empresa tiene acceso a un método según su PAQUETE
     */
    public function tieneAcceso($empresaId, $metodoSlug)
    {
        $db = \Config\Database::connect();
        
        // Obtener paquete_id de la empresa
        $empresaData = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRowArray();
        
        if (!$empresaData || empty($empresaData['paquete_id'])) {
            return false;
        }
        
        $paqueteId = $empresaData['paquete_id'];
        
        // Verificar si el método está en el paquete
        $result = $db->table('metodos_calculo mc')
            ->join('paquete_metodo_calculo pmc', 'pmc.metodo_calculo_id = mc.id')
            ->where('mc.slug', $metodoSlug)
            ->where('pmc.paquete_id', $paqueteId)
            ->where('pmc.incluido', 'S')
            ->where('mc.activo', 'A')
            ->countAllResults();
        
        return $result > 0;
    }
}
```

### 2. Servicio de Cálculo `ComposicionCorporalService.php`

```php
class ComposicionCorporalService
{
    /**
     * Calcular composición según método
     */
    public function calcular($metodoSlug, $datos, $paciente)
    {
        switch ($metodoSlug) {
            case '2-componentes':
                return $this->calcular2Componentes($datos, $paciente);
            case '4-componentes':
                return $this->calcular4Componentes($datos, $paciente);
            case '5-componentes':
                return $this->calcular5Componentes($datos, $paciente);
            case 'somatotipo':
                return $this->calcularSomatotipo($datos, $paciente);
            default:
                throw new \Exception('Método no válido');
        }
    }
    
    /**
     * Método 2 Componentes (Durnin-Womersley simplificado)
     */
    private function calcular2Componentes($datos, $paciente)
    {
        // Implementar fórmula
        // Retornar: ['masa_adiposa' => X, 'masa_magra' => Y, ...]
    }
    
    /**
     * Método 4 Componentes (Fisionutdep)
     */
    private function calcular4Componentes($datos, $paciente)
    {
        // Implementar fórmula
        // Retornar: ['grasa' => X, 'musculo' => Y, 'hueso' => Z, 'residual' => W]
    }
    
    // ... más métodos
}
```

### 3. Controlador `ComposicionCorporalController.php`

```php
class ComposicionCorporalController extends BaseController
{
    public function calcular()
    {
        $metodoSlug = $this->request->getPost('metodo');
        $historialId = $this->request->getPost('historial_id');
        
        // Verificar acceso según PAQUETE (igual que módulos)
        $empresaId = session()->get('usuario')['empresa_id'];
        $metodoModel = new MetodoCalculo();
        
        if (!$metodoModel->tieneAcceso($empresaId, $metodoSlug)) {
            // Obtener información del paquete actual y sugerir upgrade
            $db = \Config\Database::connect();
            $empresa = $db->table('empresa e')
                ->select('e.paquete_id, p.nombre as paquete_nombre')
                ->join('paquetes p', 'p.id = e.paquete_id')
                ->where('e.id', $empresaId)
                ->get()
                ->getRow();
            
            return $this->response->setJSON([
                'error' => 'Método no disponible en tu paquete actual',
                'requiere_upgrade' => true,
                'paquete_actual' => $empresa->paquete_nombre ?? 'Sin paquete',
                'paquete_id_actual' => $empresa->paquete_id ?? null
            ])->setStatusCode(403);
        }
        
        // Obtener datos del historial
        $historial = (new HistorialClinico())->find($historialId);
        $paciente = (new Paciente())->find($historial->paciente_id);
        
        // Calcular
        $service = new ComposicionCorporalService();
        $resultado = $service->calcular($metodoSlug, $historial, $paciente);
        
        return $this->response->setJSON([
            'success' => true,
            'resultado' => $resultado,
            'metodo' => $metodoSlug
        ]);
    }
}
```

---

## 📈 Estrategia de Monetización

### **Opción 1: Incluido en Paquete Base**
- Método 2 componentes incluido en todos los planes
- Métodos premium como add-on opcional

### **Opción 2: Plan Separado**
- Crear "Plan Métodos de Cálculo" como módulo adicional
- Precios:
  - Básico: Gratis (solo 2 componentes)
  - Intermedio: $9.990/mes
  - Avanzado: $19.990/mes

### **Opción 3: Pago por Uso**
- Método 2 componentes: Gratis (ilimitado)
- Método 4 componentes: $500 por cálculo
- Método 5 componentes: $1.000 por cálculo
- Paquetes de cálculos: 10 cálculos = $4.000 (ahorro 20%)

### **⭐ Recomendación: Opción 2 (Plan Separado)**
- Más simple de implementar
- Predecible para el cliente
- Fácil de escalar
- Permite promociones y descuentos

---

## 🎛️ Gestión de Métodos desde Módulo de Paquetes

### **Integración con `PaqueteController`**

Agregar gestión de métodos de cálculo en la vista de "Gestionar Módulos" del paquete:

```php
// En PaqueteController::gestionarModulos()
public function gestionarModulos($id)
{
    // ... código existente ...
    
    // Agregar métodos de cálculo disponibles
    $metodoCalculoModel = new MetodoCalculo();
    $data['metodos_calculo'] = $metodoCalculoModel->where('activo', 'A')->findAll();
    
    // Agregar métodos asignados al paquete
    $db = \Config\Database::connect();
    $metodosAsignados = $db->table('paquete_metodo_calculo')
        ->where('paquete_id', $id)
        ->where('incluido', 'S')
        ->get()
        ->getResultArray();
    
    $data['metodos_asignados'] = array_column($metodosAsignados, 'metodo_calculo_id');
    
    return view('Modulos/paquete/gestionar_modulos', $data);
}
```

### **Vista: Agregar sección de Métodos de Cálculo**

En `app/Views/Modulos/paquete/gestionar_modulos.php`, agregar después de la sección de módulos:

```html
<!-- Sección de Métodos de Cálculo -->
<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-calculator me-2"></i> Métodos de Cálculo de Composición Corporal</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Selecciona qué métodos de cálculo están incluidos en este paquete:</p>
        
        <div class="row">
            <?php foreach ($metodos_calculo as $metodo): ?>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           name="metodos_calculo[]" 
                           value="<?= $metodo->id ?>"
                           id="metodo_<?= $metodo->id ?>"
                           <?= in_array($metodo->id, $metodos_asignados ?? []) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="metodo_<?= $metodo->id ?>">
                        <strong><?= esc($metodo->nombre) ?></strong>
                        <br>
                        <small class="text-muted"><?= esc($metodo->descripcion) ?></small>
                        <br>
                        <span class="badge bg-info"><?= $metodo->componentes ?> componentes</span>
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
```

### **Guardar Métodos de Cálculo**

```php
// En PaqueteController::guardarModulos()
public function guardarModulos()
{
    // ... código existente para módulos ...
    
    // Guardar métodos de cálculo
    $paquete_id = $this->request->getPost('paquete_id');
    $metodos_calculo = $this->request->getPost('metodos_calculo') ?? [];
    
    $db = \Config\Database::connect();
    
    // Eliminar métodos actuales del paquete
    $db->table('paquete_metodo_calculo')
        ->where('paquete_id', $paquete_id)
        ->delete();
    
    // Insertar nuevos métodos
    if (!empty($metodos_calculo)) {
        $dataInsert = [];
        foreach ($metodos_calculo as $metodo_id) {
            $dataInsert[] = [
                'paquete_id' => $paquete_id,
                'metodo_calculo_id' => $metodo_id,
                'incluido' => 'S',
                'fcreacion' => date('Y-m-d H:i:s')
            ];
        }
        $db->table('paquete_metodo_calculo')->insertBatch($dataInsert);
    }
    
    // ... resto del código ...
}
```

---

## 🚀 Plan de Implementación

### **Fase 1: Base de Datos y Modelos** (1-2 días)
- [ ] Crear tabla `metodos_calculo`
- [ ] Crear tabla `empresa_metodos_calculo`
- [ ] Modificar `empresa_configuraciones`
- [ ] Crear modelo `MetodoCalculo.php`
- [ ] Crear servicio `ComposicionCorporalService.php`

### **Fase 2: Implementar Método 2 Componentes** (1 día)
- [ ] Extraer fórmula del Excel
- [ ] Implementar cálculo
- [ ] Crear vista de resultados
- [ ] Integrar en formulario de consulta

### **Fase 3: Sistema de Control de Acceso** (1 día)
- [ ] Verificar acceso según plan
- [ ] Mostrar métodos bloqueados con UI atractiva
- [ ] Crear página de planes/precios

### **Fase 4: Implementar Métodos Premium** (2-3 días)
- [ ] Método 4 Componentes
- [ ] Método 5 Componentes
- [ ] Somatotipo

### **Fase 5: UI/UX y Polishing** (1-2 días)
- [ ] Diseñar cards de métodos
- [ ] Agregar gráficos comparativos
- [ ] Mejorar mensajes de upgrade
- [ ] Testing completo

---

## 💡 Ideas Adicionales

1. **Demo Gratuita:**
   - Permitir 3 cálculos premium gratis al mes
   - Después mostrar mensaje de upgrade

2. **Comparativa de Métodos:**
   - Mostrar resultados de todos los métodos lado a lado
   - Resaltar diferencias y similitudes

3. **Historial de Cálculos:**
   - Guardar resultados de cada método
   - Permitir comparar evolución

4. **Exportación:**
   - PDF con resultados de composición corporal
   - Excel con datos completos
   - Solo para planes premium

5. **API para Integraciones:**
   - Permitir que otros sistemas consuman los cálculos
   - Solo para planes avanzados

---

## 📊 Métricas de Éxito

- **Conversión**: % de empresas que contratan plan premium
- **Uso**: Número de cálculos premium realizados
- **Retención**: % de empresas que renuevan plan premium
- **Upsell**: % de empresas que suben de plan básico a premium

---

## ✅ Ventajas de esta Propuesta

1. **Escalable**: Fácil agregar nuevos métodos
2. **Comercial**: Modelo claro de monetización
3. **Técnico**: Reutiliza estructura existente (paquetes, configuraciones)
4. **UX**: Interfaz clara que muestra valor
5. **Flexible**: Permite diferentes estrategias de pricing
