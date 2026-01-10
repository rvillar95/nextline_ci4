# Solución: Menú Vacío en Dashboard

## 🔍 Diagnóstico

Si el menú está vacío, puede ser por varias razones:

### 1. El perfil no tiene módulos asignados
### 2. Los módulos no tienen `mostrar = 'S'`
### 3. El usuario no tiene el perfil asignado
### 4. Los módulos no están activos

## ✅ Pasos para Solucionar

### Paso 1: Ejecutar Diagnóstico
```sql
-- Ejecutar el archivo: diagnostico_menu_vacio.sql
-- Esto mostrará qué está mal
```

### Paso 2: Verificar y Corregir

#### A. Si el perfil no existe o no tiene módulos:
```sql
-- Ejecutar el script corregido
SOURCE crear_perfil_nutricionista.sql;
```

#### B. Si los módulos no tienen `mostrar = 'S'`:
```sql
-- Actualizar los módulos para que se muestren
UPDATE modulo 
SET mostrar = 'S' 
WHERE id IN (2, 1, 33, 34, 35, 36, 37) 
AND estado = 'A';
```

#### C. Si el usuario no tiene el perfil asignado:
```sql
-- Ver usuarios
SELECT id, nombre, apellido, correo, perfil_id FROM usuario;

-- Asignar perfil al usuario (reemplazar [ID_USUARIO])
UPDATE usuario 
SET perfil_id = 9 
WHERE id = [ID_USUARIO];
```

#### D. Si los permisos no están activos:
```sql
-- Activar todos los permisos del perfil
UPDATE perfil_modulo 
SET estado = 'A' 
WHERE perfil_id = 9;
```

### Paso 3: Verificar que los módulos tienen detalles (submenús)

El menú necesita que los módulos tengan `modulo_detalle` con `mostrar = 'S'`:

```sql
-- Verificar detalles de módulos
SELECT 
    md.modulo_id,
    m.nombre as modulo,
    COUNT(md.id) as total_detalles,
    SUM(CASE WHEN md.mostrar = 'S' THEN 1 ELSE 0 END) as detalles_visibles
FROM modulo_detalle md
JOIN modulo m ON m.id = md.modulo_id
WHERE md.modulo_id IN (2, 1, 33, 34, 35, 36, 37)
AND md.estado = 'A'
GROUP BY md.modulo_id, m.nombre;
```

## 🔧 Script de Corrección Completo

```sql
-- 1. Asegurar que los módulos tienen mostrar = 'S'
UPDATE modulo 
SET mostrar = 'S', estado = 'A'
WHERE id IN (2, 1, 33, 34, 35, 36, 37);

-- 2. Asegurar que los permisos están activos
UPDATE perfil_modulo 
SET estado = 'A'
WHERE perfil_id = 9;

-- 3. Verificar que los detalles de módulos tienen mostrar = 'S' para los principales
UPDATE modulo_detalle
SET mostrar = 'S'
WHERE modulo_id IN (2, 1, 33, 34, 35, 36, 37)
AND orden IN (1, 2)  -- Primeros dos items de cada módulo
AND estado = 'A';
```

## 📋 Checklist de Verificación

- [ ] El perfil 9 existe y está activo
- [ ] El perfil 9 tiene módulos asignados en `perfil_modulo`
- [ ] Los módulos (2, 1, 33, 34, 35, 36, 37) tienen `mostrar = 'S'`
- [ ] Los módulos tienen `estado = 'A'`
- [ ] Los permisos en `perfil_modulo` tienen `estado = 'A'`
- [ ] El usuario tiene `perfil_id = 9`
- [ ] Los módulos tienen al menos un `modulo_detalle` con `mostrar = 'S'`

## 🚨 Problema Común: Módulo 2 (Inicio)

El módulo 2 (Inicio) puede no tener `modulo_detalle` o no tener ninguno con `mostrar = 'S'`. 

**Solución:**
```sql
-- Verificar si el módulo 2 tiene detalles
SELECT * FROM modulo_detalle WHERE modulo_id = 2;

-- Si no tiene, el módulo se mostrará como un enlace directo (sin submenú)
-- Esto está bien, el módulo 2 funciona así
```

## 💡 Nota Importante

El método `getMenu()` en `ModuloDetalle.php` filtra por:
- `mo.mostrar = 'S'` - El módulo debe mostrarse
- `mo.estado = 'A'` - El módulo debe estar activo
- `pe.estado = 'A'` - El permiso debe estar activo

Si alguno de estos no se cumple, el módulo NO aparecerá en el menú.
