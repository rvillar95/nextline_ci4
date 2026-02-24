# Solución de Errores: "Error al cargar intercambios" y "Error al cargar actividades"

## Problemas Identificados y Corregidos

### 1. **Ordenamiento por columna inexistente**
- **Problema**: El modelo `IntercambioPorcion` intentaba ordenar por `orden`, pero esta columna no existe en la tabla.
- **Solución**: Removido `->orderBy('orden', 'ASC')` del método `getIntercambiosActivos()`.

### 2. **Validación AJAX muy estricta**
- **Problema**: Los métodos requerían `isAJAX()` pero las peticiones desde jQuery podían no ser detectadas correctamente.
- **Solución**: Removida la validación `isAJAX()` y agregado mejor manejo de errores con try-catch.

### 3. **Falta de headers AJAX**
- **Problema**: Las peticiones AJAX desde jQuery no incluían el header `X-Requested-With`.
- **Solución**: Agregado header `X-Requested-With: XMLHttpRequest` en las peticiones AJAX.

### 4. **Manejo de errores mejorado**
- **Problema**: Los errores no mostraban información detallada.
- **Solución**: Agregado logging y mensajes de error más descriptivos.

## Verificaciones Necesarias

### 1. Verificar que las tablas tengan datos

Ejecuta estas consultas en tu base de datos:

```sql
-- Verificar intercambios
SELECT COUNT(*) as total FROM intercambio_porcion WHERE activo = 'A';
SELECT * FROM intercambio_porcion WHERE activo = 'A' LIMIT 5;

-- Verificar actividades
SELECT COUNT(*) as total FROM actividad_met WHERE activo = 'A';
SELECT * FROM actividad_met WHERE activo = 'A' LIMIT 5;
```

Si no hay datos, ejecuta nuevamente el script `crear_sistema_plan_alimentario.sql`, específicamente las secciones de INSERT.

### 2. Verificar logs de CodeIgniter

Revisa el archivo `writable/logs/log-YYYY-MM-DD.log` para ver errores específicos.

### 3. Verificar en el navegador

Abre la consola del navegador (F12) y revisa:
- La pestaña **Network** para ver las peticiones AJAX
- La pestaña **Console** para ver errores JavaScript

## Archivos Modificados

1. `app/Models/IntercambioPorcion.php` - Removido ordenamiento por `orden`
2. `app/Controllers/Dashboard/PlanAlimentarioController.php` - Mejorado manejo de errores
3. `app/Views/Modulos/plan_alimentario/calorimetria.php` - Agregado header AJAX y mejor manejo de errores
4. `app/Views/Modulos/plan_alimentario/plan.php` - Agregado header AJAX y mejor manejo de errores

## Próximos Pasos

1. **Recargar la página** y verificar si los errores persisten
2. **Revisar la consola del navegador** para ver errores específicos
3. **Verificar los logs** de CodeIgniter
4. **Verificar que las tablas tengan datos** ejecutando las consultas SQL arriba

Si los errores persisten, comparte:
- El mensaje exacto del error en la consola del navegador
- El contenido de la pestaña Network (respuesta del servidor)
- Cualquier error en los logs de CodeIgniter
