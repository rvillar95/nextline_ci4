# 💰 Propuesta: Modelo Comercial Híbrido - Planes + Add-ons

## 🎯 Objetivo

Crear un sistema flexible que permita:
1. **Planes Predefinidos** (3 tipos básicos) - Simple y claro
2. **Add-ons Individuales** (módulos/métodos premium) - Flexibilidad para necesidades específicas

---

## 📊 Modelo Híbrido: Planes Base + Add-ons

### **Filosofía:**
- **Planes Base**: Incluyen todo lo esencial, precio fijo mensual
- **Add-ons**: Módulos/métodos premium que se pueden agregar individualmente por costo adicional

---

## 🗄️ Estructura de Base de Datos

### 1. Agregar campo `precio_mensual` a `modulo` y `metodos_calculo`

```sql
-- Agregar precio mensual a módulos (para add-ons)
ALTER TABLE `modulo`
ADD COLUMN `precio_mensual` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Precio mensual si es add-on premium' AFTER `descripcion`,
ADD COLUMN `es_addon` ENUM('S', 'N') DEFAULT 'N' COMMENT 'S=Es add-on premium, N=Incluido en plan base' AFTER `precio_mensual`;

-- Agregar precio mensual a métodos de cálculo (para add-ons)
ALTER TABLE `metodos_calculo`
ADD COLUMN `precio_mensual` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Precio mensual si es add-on premium' AFTER `descripcion`,
ADD COLUMN `es_addon` ENUM('S', 'N') DEFAULT 'N' COMMENT 'S=Es add-on premium, N=Incluido en plan base' AFTER `precio_mensual`;
```

### 2. Tabla `empresa_addon` (Add-ons contratados por empresa)

```sql
CREATE TABLE IF NOT EXISTS `empresa_addon` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `empresa_id` INT NOT NULL,
  `tipo` ENUM('modulo', 'metodo_calculo') NOT NULL COMMENT 'Tipo de add-on',
  `referencia_id` INT NOT NULL COMMENT 'ID del módulo o método de cálculo',
  `precio_mensual` DECIMAL(10,2) NOT NULL COMMENT 'Precio mensual del add-on',
  `fecha_inicio` DATE NOT NULL COMMENT 'Fecha de inicio del add-on',
  `fecha_fin` DATE NULL COMMENT 'Fecha de fin (NULL = activo indefinidamente)',
  `estado` ENUM('activo', 'suspendido', 'cancelado') DEFAULT 'activo',
  `fcreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_empresa_addon` (`empresa_id`, `tipo`, `referencia_id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_tipo_referencia` (`tipo`, `referencia_id`),
  CONSTRAINT `fk_empresa_addon_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3. Modificar `pagos` para soportar add-ons

```sql
-- Ya existe campo `tipo_pago` con valor 'extra' - perfecto para add-ons
-- Agregar referencia al add-on
ALTER TABLE `pagos`
ADD COLUMN `addon_id` INT NULL COMMENT 'ID del add-on si el pago es por add-on' AFTER `paquete_id`,
ADD KEY `idx_addon` (`addon_id`),
ADD CONSTRAINT `fk_pagos_addon` FOREIGN KEY (`addon_id`) REFERENCES `empresa_addon` (`id`) ON DELETE SET NULL;
```

---

## 📋 Planes Predefinidos (Base)

### **Plan 1: NextLine Nutrición Básico** (paquete_id = 4)
- **Precio**: $0/mes (o el precio base que definas)
- **Incluye**:
  - Todos los módulos básicos (Agenda, Pacientes, Documentos, Historial, etc.)
  - Método 2 Componentes (gratis)
- **NO incluye**: Métodos premium, módulos add-on

### **Plan 2: NextLine Nutrición Premium** (paquete_id = 5)
- **Precio**: $19.990/mes
- **Incluye**:
  - Todo del Plan Básico
  - Método 4 Componentes
  - Somatotipo
- **NO incluye**: Método 5 Componentes, módulos add-on

### **Plan 3: NextLine Nutrición Avanzado** (paquete_id = 6)
- **Precio**: $39.990/mes
- **Incluye**:
  - Todo del Plan Premium
  - Método 5 Componentes
  - Todos los métodos futuros (6, 7 componentes)
- **NO incluye**: Módulos add-on (se pueden agregar)

---

## 🎁 Add-ons Disponibles

### **Add-ons de Métodos de Cálculo:**

1. **Método 5 Componentes** (Add-on)
   - Precio: $9.990/mes
   - Disponible para: Plan Básico y Premium
   - Si tienes Plan Avanzado: Ya está incluido

2. **Método 6 Componentes** (Add-on futuro)
   - Precio: $14.990/mes
   - Disponible para: Todos los planes

### **Add-ons de Módulos (Ejemplos):**

1. **Portal del Paciente Premium**
   - Precio: $4.990/mes
   - Funcionalidades avanzadas del portal

2. **Reportes Avanzados**
   - Precio: $7.990/mes
   - Exportación PDF, gráficos avanzados, comparativas

3. **Integración con Dispositivos**
   - Precio: $12.990/mes
   - Sincronización con básculas inteligentes, etc.

---

## 🔧 Lógica de Control de Acceso

### **Verificar Acceso a Módulo/Método:**

```php
class AccesoService
{
    /**
     * Verificar si empresa tiene acceso a un módulo
     * Considera: Paquete base + Add-ons activos
     */
    public function tieneAccesoModulo($empresaId, $moduloId)
    {
        $db = \Config\Database::connect();
        
        // 1. Verificar si está en el paquete base
        $empresa = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRow();
        
        if ($empresa) {
            $enPaquete = $db->table('paquete_modulo')
                ->where('paquete_id', $empresa->paquete_id)
                ->where('modulo_id', $moduloId)
                ->where('incluido', 'S')
                ->countAllResults() > 0;
            
            if ($enPaquete) {
                return true; // Está en el plan base
            }
        }
        
        // 2. Verificar si es add-on activo
        $addonActivo = $db->table('empresa_addon')
            ->where('empresa_id', $empresaId)
            ->where('tipo', 'modulo')
            ->where('referencia_id', $moduloId)
            ->where('estado', 'activo')
            ->where('(fecha_fin IS NULL OR fecha_fin >= CURDATE())', null, false)
            ->countAllResults() > 0;
        
        return $addonActivo;
    }
    
    /**
     * Verificar si empresa tiene acceso a un método de cálculo
     */
    public function tieneAccesoMetodo($empresaId, $metodoId)
    {
        $db = \Config\Database::connect();
        
        // 1. Verificar si está en el paquete base
        $empresa = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRow();
        
        if ($empresa) {
            $enPaquete = $db->table('paquete_metodo_calculo')
                ->where('paquete_id', $empresa->paquete_id)
                ->where('metodo_calculo_id', $metodoId)
                ->where('incluido', 'S')
                ->countAllResults() > 0;
            
            if ($enPaquete) {
                return true; // Está en el plan base
            }
        }
        
        // 2. Verificar si es add-on activo
        $addonActivo = $db->table('empresa_addon')
            ->where('empresa_id', $empresaId)
            ->where('tipo', 'metodo_calculo')
            ->where('referencia_id', $metodoId)
            ->where('estado', 'activo')
            ->where('(fecha_fin IS NULL OR fecha_fin >= CURDATE())', null, false)
            ->countAllResults() > 0;
        
        return $addonActivo;
    }
    
    /**
     * Obtener todos los add-ons activos de una empresa
     */
    public function getAddonsActivos($empresaId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('empresa_addon')
            ->where('empresa_id', $empresaId)
            ->where('estado', 'activo')
            ->where('(fecha_fin IS NULL OR fecha_fin >= CURDATE())', null, false)
            ->get()
            ->getResult();
    }
    
    /**
     * Calcular precio total mensual de una empresa
     */
    public function calcularPrecioTotalMensual($empresaId)
    {
        $db = \Config\Database::connect();
        
        // Precio del paquete base
        $empresa = $db->table('empresa e')
            ->select('p.precio_mensual as precio_paquete')
            ->join('paquetes p', 'p.id = e.paquete_id')
            ->where('e.id', $empresaId)
            ->get()
            ->getRow();
        
        $precioBase = $empresa->precio_paquete ?? 0;
        
        // Sumar precio de add-ons activos
        $addons = $this->getAddonsActivos($empresaId);
        $precioAddons = 0;
        foreach ($addons as $addon) {
            $precioAddons += $addon->precio_mensual;
        }
        
        return [
            'precio_base' => $precioBase,
            'precio_addons' => $precioAddons,
            'total' => $precioBase + $precioAddons
        ];
    }
}
```

---

## 🎨 Interfaz de Usuario

### **Vista: "Mi Plan y Add-ons"**

```
┌─────────────────────────────────────────────────────────────┐
│  💳 Mi Plan y Suscripciones                                 │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  📦 Plan Base: NextLine Nutrición Básico                    │
│  💰 $0/mes                                                    │
│  ✅ Incluye: Agenda, Pacientes, Historial, Método 2 comp.   │
│                                                               │
│  ─────────────────────────────────────────────────────────  │
│                                                               │
│  🎁 Add-ons Activos:                                         │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ ✅ Método 5 Componentes                              │   │
│  │    $9.990/mes | Activo desde 15/01/2026             │   │
│  │    [Cancelar Add-on]                                 │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                               │
│  ─────────────────────────────────────────────────────────  │
│                                                               │
│  💰 Total Mensual: $9.990                                    │
│  (Plan Base: $0 + Add-ons: $9.990)                         │
│                                                               │
│  ─────────────────────────────────────────────────────────  │
│                                                               │
│  🛒 Agregar Add-ons Disponibles:                            │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🔒 Método 4 Componentes                              │   │
│  │    $9.990/mes                                        │   │
│  │    [Agregar Add-on]                                  │   │
│  └─────────────────────────────────────────────────────┘   │
│  ┌─────────────────────────────────────────────────────┐   │
│  │ 🔒 Somatotipo                                        │   │
│  │    $4.990/mes                                       │   │
│  │    [Agregar Add-on]                                  │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                               │
│  💡 Tip: Contrata el Plan Premium ($19.990/mes) y ahorra    │
│     $4.980/mes (incluye Método 4 + Somatotipo)             │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### **Vista: Métodos de Cálculo con Badges**

```
┌─────────────────────────────────────────────────────────────┐
│  📊 Métodos de Cálculo Disponibles                           │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ✅ Método 2 Componentes (Incluido en tu plan)              │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ Resultados calculados...                            │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  🔒 Método 4 Componentes                                     │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ ⚠️ No disponible en tu plan actual                   │    │
│  │ Opciones:                                            │    │
│  │ • [Agregar como Add-on] $9.990/mes                  │    │
│  │ • [Upgrade a Plan Premium] $19.990/mes              │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
│  ✅ Método 5 Componentes (Add-on activo)                    │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ Resultados calculados...                            │    │
│  └─────────────────────────────────────────────────────┘    │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 💼 Flujo de Contratación de Add-on

### **1. Usuario solicita Add-on**

```php
// En AddonController
public function solicitarAddon()
{
    $empresaId = session()->get('usuario')['empresa_id'];
    $tipo = $this->request->getPost('tipo'); // 'modulo' o 'metodo_calculo'
    $referenciaId = $this->request->getPost('referencia_id');
    
    // Obtener precio del add-on
    if ($tipo === 'modulo') {
        $item = (new Modulo())->find($referenciaId);
    } else {
        $item = (new MetodoCalculo())->find($referenciaId);
    }
    
    if (!$item || $item->es_addon !== 'S') {
        return $this->response->setJSON(['error' => 'Add-on no válido']);
    }
    
    // Verificar si ya lo tiene
    $db = \Config\Database::connect();
    $existe = $db->table('empresa_addon')
        ->where('empresa_id', $empresaId)
        ->where('tipo', $tipo)
        ->where('referencia_id', $referenciaId)
        ->where('estado', 'activo')
        ->countAllResults() > 0;
    
    if ($existe) {
        return $this->response->setJSON(['error' => 'Ya tienes este add-on activo']);
    }
    
    // Crear add-on (pendiente de pago)
    $addonId = $db->table('empresa_addon')->insert([
        'empresa_id' => $empresaId,
        'tipo' => $tipo,
        'referencia_id' => $referenciaId,
        'precio_mensual' => $item->precio_mensual,
        'fecha_inicio' => date('Y-m-d'),
        'estado' => 'activo', // O 'pendiente' si requiere aprobación
    ]);
    
    // Crear registro de pago
    $pagoId = $db->table('pagos')->insert([
        'empresa_id' => $empresaId,
        'addon_id' => $addonId,
        'tipo_pago' => 'extra',
        'monto' => $item->precio_mensual,
        'estado_pago' => 'pendiente',
        'fecha_vencimiento' => date('Y-m-d', strtotime('+1 month')),
    ]);
    
    return $this->response->setJSON([
        'success' => true,
        'message' => 'Add-on agregado. Pendiente de pago.',
        'addon_id' => $addonId,
        'pago_id' => $pagoId
    ]);
}
```

---

## ✅ Ventajas del Modelo Híbrido

### **1. Flexibilidad Comercial**
- ✅ Clientes pueden empezar con plan básico
- ✅ Agregar solo lo que necesitan
- ✅ No pagar por funciones que no usan

### **2. Simplicidad para la Mayoría**
- ✅ 3 planes predefinidos claros
- ✅ La mayoría elige un plan y listo
- ✅ Add-ons solo para casos específicos

### **3. Escalabilidad**
- ✅ Fácil agregar nuevos add-ons
- ✅ No requiere crear nuevos planes
- ✅ Precios individuales por add-on

### **4. Upsell Natural**
- ✅ Mostrar add-ons disponibles
- ✅ Sugerir upgrade cuando conviene
- ✅ "Ahorra $X contratando el plan completo"

### **5. Técnicamente Sólido**
- ✅ Reutiliza estructura existente
- ✅ Misma lógica de control de acceso
- ✅ Integrado con sistema de pagos

---

## 📊 Ejemplo de Casos de Uso

### **Caso 1: Nutricionista Básico**
- Plan: NextLine Nutrición Básico ($0/mes)
- Add-ons: Ninguno
- **Total: $0/mes**

### **Caso 2: Nutricionista con Necesidad Específica**
- Plan: NextLine Nutrición Básico ($0/mes)
- Add-ons: Método 5 Componentes ($9.990/mes)
- **Total: $9.990/mes**
- **Ahorro vs Plan Avanzado: $30.000/mes** (solo paga lo que necesita)

### **Caso 3: Clínica Completa**
- Plan: NextLine Nutrición Avanzado ($39.990/mes)
- Add-ons: Reportes Avanzados ($7.990/mes)
- **Total: $47.980/mes**
- Tiene todo incluido + funcionalidades extra

---

## 🎯 Recomendación Final

**✅ Modelo Híbrido es la mejor opción porque:**

1. **Ofrece ambos modelos**: Planes predefinidos + Add-ons
2. **Flexible**: Se adapta a diferentes necesidades
3. **Comercial**: Permite upselling y personalización
4. **Técnico**: Reutiliza infraestructura existente
5. **Escalable**: Fácil agregar nuevos add-ons sin crear planes

**Estrategia sugerida:**
- **80% de clientes**: Eligen un plan predefinido (simple)
- **20% de clientes**: Agregan add-ons específicos (flexible)
- **Resultado**: Satisfacción alta + ingresos optimizados
