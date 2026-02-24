# Solución: Error "Unexpected token '<'" - Servidor devuelve HTML en lugar de JSON

## Problema Identificado

El servidor está devolviendo HTML completo (la página de `consulta.php` con comentarios `<!-- DEBUG-VIEW START ... -->`) en lugar de JSON cuando se hacen peticiones AJAX a:
- `/dashboard/plan-alimentario/actividades`
- `/dashboard/plan-alimentario/intercambios`

## Causa Raíz

El problema es que las rutas en `modulo_detalle` están definidas como **rutas relativas** (ej: `/calcular-calorimetria`), pero el `SessionFilter` necesita **rutas absolutas** (ej: `/dashboard/plan-alimentario/calcular-calorimetria`) para reconocerlas y permitir el acceso.

Cuando el `SessionFilter` no reconoce la ruta, deniega el acceso y redirige, lo que causa que se devuelva HTML en lugar de JSON.

## Solución Aplicada

### 1. Actualizar SQL para rutas absolutas

El archivo `crear_sistema_plan_alimentario.sql` ha sido actualizado para que todas las rutas en `modulo_detalle` sean **absolutas**:

```sql
-- ANTES (rutas relativas - NO funcionan con SessionFilter)
('/calcular-calorimetria', ...)
('/calorimetria', ...)

-- DESPUÉS (rutas absolutas - funcionan correctamente)
('/dashboard/plan-alimentario/calcular-calorimetria', ...)
('/dashboard/plan-alimentario/calorimetria', ...)
('/dashboard/plan-alimentario/actividades', ...)
('/dashboard/plan-alimentario/intercambios', ...)
```

### 2. Mejoras en el Controlador

- Agregado `ob_clean()` para limpiar cualquier output buffer
- Agregado manejo de `\Throwable` además de `\Exception`
- Mejorado logging de errores

## Pasos para Resolver

### Paso 1: Actualizar las rutas en la base de datos

Ejecuta este SQL para actualizar las rutas existentes:

```sql
-- Actualizar rutas a formato absoluto
UPDATE `modulo_detalle` 
SET `ruta` = CONCAT('/dashboard/plan-alimentario', `ruta`)
WHERE `modulo_id` = (SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario' LIMIT 1)
  AND `ruta` NOT LIKE '/dashboard/%';

-- Agregar rutas que faltan (actividades e intercambios)
SET @modulo_plan_id = (SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario' LIMIT 1);

INSERT INTO `modulo_detalle`
(`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`)
VALUES
(@modulo_plan_id, 'Obtener Actividades', '/dashboard/plan-alimentario/actividades', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(@modulo_plan_id, 'Obtener Intercambios', '/dashboard/plan-alimentario/intercambios', 'ver', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE
    `estado` = 'A',
    `factualizacion` = NOW();
```

### Paso 2: Asignar permisos al perfil

Asegúrate de que el perfil "Nutricionista" tenga permisos para el módulo "Plan Alimentario":

```sql
-- Verificar que el módulo esté asignado al perfil
-- (Ajusta el perfil_id según tu sistema, típicamente 2 para Nutricionista)
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `incluido`)
SELECT 2, @modulo_plan_id, 'S'
FROM `perfil` p
WHERE p.id = 2
ON DUPLICATE KEY UPDATE `incluido` = 'S';
```

### Paso 3: Limpiar caché de permisos (si aplica)

Si usas caché de permisos, invalídalo:

```sql
-- Si usas caché, actualizar versión
UPDATE `cache` SET `value` = CAST(CAST(`value` AS UNSIGNED) + 1 AS CHAR) 
WHERE `key` = 'perm_version';
```

### Paso 4: Verificar en la base de datos

Ejecuta estas consultas para verificar:

```sql
-- Verificar módulo
SELECT * FROM `modulo` WHERE `ruta` = '/dashboard/plan-alimentario';

-- Verificar rutas del módulo (deben ser absolutas)
SELECT `id`, `descripcion`, `ruta`, `accion`, `estado` 
FROM `modulo_detalle` 
WHERE `modulo_id` = (SELECT id FROM modulo WHERE ruta = '/dashboard/plan-alimentario' LIMIT 1);

-- Verificar permisos del perfil
SELECT pm.*, m.nombre as modulo_nombre
FROM `perfil_modulo` pm
JOIN `modulo` m ON m.id = pm.modulo_id
WHERE pm.perfil_id = 2 AND m.ruta = '/dashboard/plan-alimentario';
```

## Verificación

Después de ejecutar el SQL:

1. **Recarga la página** en el navegador
2. **Abre la consola** (F12) y verifica que no haya errores
3. **Revisa la pestaña Network**:
   - Busca las peticiones a `actividades` e `intercambios`
   - Verifica que el **Status Code** sea `200`
   - Verifica que el **Content-Type** sea `application/json`
   - Verifica que la **Response** sea JSON válido (no HTML)

## Si el problema persiste

1. **Revisa los logs** de CodeIgniter en `writable/logs/`
2. **Verifica que las tablas tengan datos**:
   ```sql
   SELECT COUNT(*) FROM actividad_met WHERE activo = 'A';
   SELECT COUNT(*) FROM intercambio_porcion WHERE activo = 'A';
   ```
3. **Verifica que el SessionFilter esté permitiendo la ruta**:
   - Revisa los logs para ver si aparece "DENEGADA"
   - Verifica que la ruta esté en el formato correcto
