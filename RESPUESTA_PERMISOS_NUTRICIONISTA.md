# 🔐 Respuesta: Permisos del Usuario Nutricionista

## ❓ Tu Pregunta

**"Entre los usuarios que tengo, por ejemplo el usuario nutricionista, ¿con qué permisos estará? ¿Los del perfil o los del paquete?"**

## 🔍 Análisis del Código

Después de revisar el código, encontré que el sistema tiene **DOS mecanismos diferentes** que funcionan de manera **inconsistente**:

### 1. **Generación del Menú** (lo que el usuario VE)
- **Usa**: `perfil_modulo` (tabla de permisos por perfil)
- **Método**: `ModuloDetalle::getMenu($perfil_id)`
- **Código**:
  ```sql
  SELECT pe.modulo_id, mo.nombre, mo.ruta, pe.ver, pe.registrar, pe.editar, pe.eliminar 
  FROM perfil_modulo pe
  WHERE pe.perfil_id = :perfil:
  ```
- **Conclusión**: El menú se genera según el **PERFIL**, NO según el paquete

### 2. **Verificación de Acceso** (lo que el sistema PERMITE)
- **Usa**: `paquete_modulo` (tabla de módulos por paquete)
- **Función**: `fn_usuario_puede_ver_modulo(usuario_id, modulo_id)`
- **Código**:
  ```sql
  SELECT incluido 
  FROM paquete_modulo
  WHERE paquete_id = (SELECT paquete_id FROM empresa WHERE id = usuario.empresa_id)
    AND modulo_id = :modulo_id:
  ```
- **Conclusión**: La verificación de acceso usa el **PAQUETE**, NO el perfil

## ⚠️ Problema Detectado

**Hay una inconsistencia:**
- El menú muestra módulos según el **PERFIL**
- Pero el sistema verifica acceso según el **PAQUETE**

**Esto puede causar:**
- Usuario ve módulos en el menú que no puede acceder
- O usuario no ve módulos que sí puede acceder (si están en el paquete pero no en el perfil)

## ✅ Respuesta Directa

**Para un usuario Nutricionista (perfil_id = 9):**

1. **El menú se genera según `perfil_modulo`** (tabla de permisos del perfil)
   - Si el perfil 9 tiene módulos 33-39 asignados en `perfil_modulo`
   - El usuario verá esos módulos en el menú

2. **El acceso se verifica según `paquete_modulo`** (tabla de módulos del paquete)
   - Si la empresa tiene paquete 4 (NextLine Nutrición)
   - El sistema verificará si los módulos están en `paquete_modulo` para ese paquete

3. **Para que funcione correctamente:**
   - Los módulos en `perfil_modulo` (perfil 9) DEBEN estar también en `paquete_modulo` (paquete 4)
   - Si hay módulos en el perfil que NO están en el paquete → El usuario los verá pero no podrá acceder
   - Si hay módulos en el paquete que NO están en el perfil → El usuario no los verá en el menú

## 🎯 Ejemplo Práctico

### Escenario: Usuario Nutricionista

**Empresa:**
- `id`: 1
- `paquete_id`: 4 (NextLine Nutrición)
- Módulos en `paquete_modulo`: 33, 34, 35, 36, 37, 38, 39

**Usuario:**
- `id`: 10
- `perfil_id`: 9 (Nutricionista)
- `empresa_id`: 1

**Permisos en `perfil_modulo` (perfil 9):**
- Módulos: 33, 34, 35, 36, 37, 38, 39
- Todos con `ver=1, editar=1, eliminar=1`

**Resultado:**
- ✅ El usuario VERÁ los módulos 33-39 en el menú (porque están en `perfil_modulo`)
- ✅ El usuario PODRÁ ACCEDER a los módulos 33-39 (porque están en `paquete_modulo`)
- ✅ Todo funciona correctamente

**Si hubiera inconsistencia:**
- Si `perfil_modulo` tiene módulo 40 pero `paquete_modulo` NO lo tiene:
  - ❌ El usuario verá el módulo 40 en el menú
  - ❌ Pero NO podrá acceder (la función `fn_usuario_puede_ver_modulo` retornará 'N')

## 📝 Recomendación

**Para que funcione correctamente, asegúrate de que:**
1. Los módulos en `perfil_modulo` (perfil 9) estén también en `paquete_modulo` (paquete 4)
2. O mejor aún, modificar el sistema para que:
   - El menú se genere usando AMBOS (perfil Y paquete)
   - O que el menú se genere usando solo el paquete y luego se filtre por perfil

## 🔧 Verificación

Para verificar los permisos de un usuario Nutricionista:

```sql
-- Ver qué módulos tiene el perfil Nutricionista
SELECT 
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.ver,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.perfil_id = 9
  AND pm.estado = 'A'
ORDER BY pm.modulo_id;

-- Ver qué módulos tiene el paquete NextLine Nutrición
SELECT 
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.incluido
FROM paquete_modulo pm
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.paquete_id = 4
  AND pm.incluido = 'S'
ORDER BY pm.modulo_id;

-- Comparar ambos (deberían ser iguales)
SELECT 
    pm_perfil.modulo_id,
    m.nombre AS modulo_nombre,
    CASE WHEN pm_perfil.modulo_id IS NOT NULL THEN 'S' ELSE 'N' END AS en_perfil,
    CASE WHEN pm_paquete.modulo_id IS NOT NULL THEN 'S' ELSE 'N' END AS en_paquete
FROM modulo m
LEFT JOIN perfil_modulo pm_perfil ON pm_perfil.modulo_id = m.id AND pm_perfil.perfil_id = 9
LEFT JOIN paquete_modulo pm_paquete ON pm_paquete.modulo_id = m.id AND pm_paquete.paquete_id = 4 AND pm_paquete.incluido = 'S'
WHERE m.id IN (33, 34, 35, 36, 37, 38, 39)
ORDER BY m.id;
```
