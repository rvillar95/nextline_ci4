# 🔧 Solución: Error al Importar Procedimiento Almacenado

## ⚠️ Errores Encontrados

### 1. Errores de "Análisis Estático"
Los errores que ves como:
- "Tipo de declaración desconocida. (near "DECLARE" at position 133)"
- "Tipo de declaración desconocida. (near "IF" at position 482)"

**Son FALSOS POSITIVOS**. El IDE está interpretando el SQL como código PHP. Puedes ignorarlos.

### 2. Error Real de MySQL
```
#1227 - Acceso denegado. Usted necesita (al menos un(os)) privilegio(s) SET USER para esta operación
```

**Causa**: El procedimiento tiene `DEFINER=`root`@`localhost`` y tu usuario no tiene permisos `SET USER`.

## ✅ Solución

### Opción 1: Usar el Script Corregido (Recomendado)

He creado `sp_obtener_menu_usuario_corregido.sql` que:
- ✅ Elimina el `DEFINER` (usa el usuario actual)
- ✅ Funciona sin permisos especiales
- ✅ Mantiene toda la funcionalidad

**Pasos:**
1. Abre phpMyAdmin o tu cliente MySQL
2. Selecciona la base de datos `nextline_pyme`
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido de `sp_obtener_menu_usuario_corregido.sql`
5. Ejecuta

### Opción 2: Modificar el SQL Original

Si prefieres usar el archivo original, elimina o modifica la línea `DEFINER`:

**Antes:**
```sql
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_menu_usuario` ...
```

**Después:**
```sql
CREATE PROCEDURE `sp_obtener_menu_usuario` ...
```

O usa:
```sql
CREATE DEFINER=CURRENT_USER PROCEDURE `sp_obtener_menu_usuario` ...
```

### Opción 3: Dar Permisos al Usuario

Si necesitas mantener el `DEFINER`, puedes dar permisos al usuario:

```sql
GRANT SET USER ON *.* TO 'tu_usuario'@'localhost';
FLUSH PRIVILEGES;
```

**Nota**: Esto requiere permisos de administrador en MySQL.

## 📝 Nota Importante

**El procedimiento `sp_obtener_menu_usuario` NO parece estar siendo usado** en el código actual. El sistema usa `ModuloDetalle::getMenu()` en su lugar.

Si no necesitas este procedimiento, puedes:
- **Omitirlo** - El sistema funcionará sin él
- **Importarlo de todas formas** - Por si lo necesitas más adelante

## 🔍 Verificar si se Usa

Para verificar si el procedimiento se usa en algún lugar:

```sql
-- Buscar en el código (desde terminal o grep)
grep -r "CALL sp_obtener_menu_usuario" app/
grep -r "sp_obtener_menu_usuario" app/
```

Si no encuentra nada, el procedimiento no se está usando actualmente.

## ⚡ Solución Rápida

1. Usa el archivo `sp_obtener_menu_usuario_corregido.sql`
2. Ignora los errores de "análisis estático" (son falsos positivos)
3. Si el procedimiento no se usa, puedes omitirlo completamente
