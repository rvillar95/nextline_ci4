# 🔐 Estructura: Métodos de Cálculo y Control de Permisos

## 🎯 Pregunta Clave

**¿Los métodos de cálculo deben ser módulos independientes o funcionalidades dentro de Historial Clínico?**

---

## 📊 Dos Opciones de Implementación

### **Opción A: Métodos como Funcionalidades (Más Simple)**

Los métodos son **funcionalidades dentro del módulo "Historial Clínico"**, controlados por `paquete_metodo_calculo`.

**Estructura:**
```
Módulo: Historial Clínico (id=36)
  └── Funcionalidades:
      ├── Ver Historiales
      ├── Registrar Historial
      ├── Editar Historial
      └── Métodos de Cálculo (controlados por paquete_metodo_calculo)
          ├── Método 2 Componentes
          ├── Método 4 Componentes
          └── Método 5 Componentes
```

**Control de Acceso:**
- Se verifica en el controlador antes de calcular
- No aparece en el menú como módulo separado
- Más simple, menos granular

---

### **Opción B: Métodos como Rutas en `modulo_detalle` (Más Granular)** ⭐ **RECOMENDADA**

Cada método tiene su propia ruta en `modulo_detalle`, permitiendo control granular de permisos.

**Estructura:**
```
Módulo: Historial Clínico (id=36)
  └── Rutas (modulo_detalle):
      ├── /lista (ver historiales)
      ├── /registro (crear historial)
      ├── /editar (editar historial)
      ├── /calcular-2-componentes (método 2 componentes) ← NUEVO
      ├── /calcular-4-componentes (método 4 componentes) ← NUEVO
      ├── /calcular-5-componentes (método 5 componentes) ← NUEVO
      └── /calcular-somatotipo (somatotipo) ← NUEVO
```

**Control de Acceso:**
- Cada método tiene su ruta en `modulo_detalle`
- Se controla igual que cualquier otra ruta (perfil_modulo)
- Aparece en permisos del perfil
- Más granular, más control

---

## ✅ **RECOMENDACIÓN: Opción B (Rutas en modulo_detalle)**

### **Ventajas:**

1. **✅ Control Granular**: Cada método puede tener permisos independientes
2. **✅ Consistente**: Mismo sistema que todas las rutas
3. **✅ Visible**: Aparece en gestión de permisos del perfil
4. **✅ Flexible**: Puedes dar permiso a un método sin dar permiso a otro
5. **✅ Integrado**: Funciona con el sistema de paquetes existente

### **Ejemplo de Uso:**

```
Perfil: Nutricionista Junior
  ✅ Puede ver historiales
  ✅ Puede crear historiales
  ✅ Puede calcular método 2 componentes
  ❌ NO puede calcular método 4 componentes (premium)
  ❌ NO puede calcular método 5 componentes (premium)
```

---

## 🗄️ Implementación: Opción B

### **1. Crear Rutas en `modulo_detalle` para cada método**

```sql
-- Obtener ID del módulo Historial Clínico
SET @modulo_historial_id = (SELECT id FROM modulo WHERE nombre = 'Historial Clínico' LIMIT 1);

-- Insertar rutas de métodos de cálculo
INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@modulo_historial_id, 'Calcular Composición 2 Componentes', '/calcular-2-componentes', 'ver', 'A', 'N', 100, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_historial_id, 'Calcular Composición 4 Componentes', '/calcular-4-componentes', 'ver', 'A', 'N', 101, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_historial_id, 'Calcular Composición 5 Componentes', '/calcular-5-componentes', 'ver', 'A', 'N', 102, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_historial_id, 'Calcular Somatotipo', '/calcular-somatotipo', 'ver', 'A', 'N', 103, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `descripcion` = VALUES(`descripcion`),
    `ruta` = VALUES(`ruta`),
    `accion` = VALUES(`accion`),
    `estado` = 'A',
    `factualizacion` = NOW();
```

### **2. Asignar rutas a paquetes (igual que módulos)**

```sql
-- Plan Básico (paquete_id = 4): Solo método 2 componentes
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT 4, md.id, 'S', NOW()
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_historial_id
  AND md.ruta = '/calcular-2-componentes'
ON DUPLICATE KEY UPDATE `incluido` = 'S';

-- Plan Premium (paquete_id = 5): Métodos 2, 4 componentes + somatotipo
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT 5, md.id, 'S', NOW()
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_historial_id
  AND md.ruta IN ('/calcular-2-componentes', '/calcular-4-componentes', '/calcular-somatotipo')
ON DUPLICATE KEY UPDATE `incluido` = 'S';

-- Plan Avanzado (paquete_id = 6): Todos los métodos
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`)
SELECT 6, md.id, 'S', NOW()
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_historial_id
  AND md.ruta LIKE '/calcular-%'
ON DUPLICATE KEY UPDATE `incluido` = 'S';
```

### **3. Asignar permisos a perfiles**

```sql
-- Perfil Nutricionista (id=9): Todos los métodos según su paquete
-- Esto se hace automáticamente cuando se asigna el módulo al perfil
-- O manualmente si quieres control granular:

INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`)
SELECT 9, md.id, 1, 0, 0, 0, 'A', md.orden, NOW()
FROM modulo_detalle md
WHERE md.modulo_id = @modulo_historial_id
  AND md.ruta LIKE '/calcular-%'
ON DUPLICATE KEY UPDATE `ver` = 1;
```

---

## 🔧 Controlador: Verificar Permisos

```php
// En HistorialController o ComposicionCorporalController
public function calcular2Componentes()
{
    $historialId = $this->request->getPost('historial_id');
    
    // Verificar acceso usando el mismo sistema que cualquier ruta
    $moduloDetalle = new ModuloDetalle();
    $usuario = session()->get('usuario');
    
    // Verificar si tiene permiso para esta ruta específica
    $tieneAcceso = $moduloDetalle->getAllowedByPerfil(
        $usuario['perfil_id'],
        '/calcular-2-componentes',
        'ver'
    );
    
    if (!$tieneAcceso) {
        return $this->response->setJSON([
            'error' => 'No tienes permiso para usar este método',
            'requiere_upgrade' => true
        ])->setStatusCode(403);
    }
    
    // Calcular...
}
```

---

## 🎨 Vista: Mostrar Métodos Disponibles

```php
// En la vista de composición corporal
<?php
$moduloDetalle = new ModuloDetalle();
$usuario = session()->get('usuario');

// Verificar qué métodos tiene acceso
$metodos = [
    '2-componentes' => $moduloDetalle->getAllowedByPerfil($usuario['perfil_id'], '/calcular-2-componentes', 'ver'),
    '4-componentes' => $moduloDetalle->getAllowedByPerfil($usuario['perfil_id'], '/calcular-4-componentes', 'ver'),
    '5-componentes' => $moduloDetalle->getAllowedByPerfil($usuario['perfil_id'], '/calcular-5-componentes', 'ver'),
    'somatotipo' => $moduloDetalle->getAllowedByPerfil($usuario['perfil_id'], '/calcular-somatotipo', 'ver'),
];
?>

<?php if ($metodos['2-componentes']): ?>
    <!-- Mostrar método 2 componentes -->
<?php endif; ?>

<?php if ($metodos['4-componentes']): ?>
    <!-- Mostrar método 4 componentes -->
<?php else: ?>
    <!-- Mostrar mensaje de upgrade -->
<?php endif; ?>
```

---

## ✅ **RESPUESTA A TU PREGUNTA:**

**SÍ, funcionará exactamente igual que los módulos:**

1. ✅ **Cada método tiene su ruta en `modulo_detalle`**
   - `/calcular-2-componentes`
   - `/calcular-4-componentes`
   - `/calcular-5-componentes`
   - `/calcular-somatotipo`

2. ✅ **Se asignan a paquetes igual que módulos**
   - `paquete_modulo` relaciona paquete → ruta del método

3. ✅ **Se dan permisos igual que módulos**
   - `perfil_modulo` controla qué métodos puede usar cada perfil

4. ✅ **Control de acceso igual que módulos**
   - `ModuloDetalle::getAllowedByPerfil()` verifica acceso
   - `SessionFilter` protege las rutas automáticamente

5. ✅ **Gestión desde módulo de Paquetes**
   - Super Admin puede asignar métodos a paquetes
   - Igual que asigna módulos

---

## 🎯 **Diferencia Clave:**

- **NO son módulos independientes** (no aparecen en el menú principal)
- **SÍ son rutas dentro del módulo Historial Clínico** (control granular)
- **SÍ se controlan igual que módulos** (paquetes + perfiles)

---

## 📝 **Resumen:**

```
Módulo: Historial Clínico
  ├── Ruta: /lista (ver historiales)
  ├── Ruta: /registro (crear historial)
  ├── Ruta: /editar (editar historial)
  ├── Ruta: /calcular-2-componentes ← Controlada por paquete/perfil
  ├── Ruta: /calcular-4-componentes ← Controlada por paquete/perfil
  ├── Ruta: /calcular-5-componentes ← Controlada por paquete/perfil
  └── Ruta: /calcular-somatotipo ← Controlada por paquete/perfil
```

**Todo funciona igual que los módulos, pero son rutas dentro del módulo Historial Clínico.**
