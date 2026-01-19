# ✅ Implementación: Sistema de Paquetes + Perfiles

## 🎯 Objetivo

Modificar el sistema para que:
1. **El paquete** defina qué módulos tiene disponibles la empresa
2. **El perfil** defina los permisos (ver, editar, eliminar) sobre esos módulos
3. El usuario solo vea módulos que estén en **AMBOS** (paquete Y perfil)

## 🔧 Cambios Realizados

### 1. Método `getMenu()` - Modificado
**Archivo:** `app/Models/ModuloDetalle.php`

**Cambios:**
- Ahora obtiene el `empresa_id` del usuario (desde la sesión)
- Consulta el `paquete_id` de la empresa
- Filtra módulos por `paquete_modulo` (módulos del paquete)
- Luego filtra por `perfil_modulo` (permisos del perfil)
- Solo muestra módulos que estén en **AMBOS**

**Lógica:**
```sql
SELECT módulos
FROM perfil_modulo
INNER JOIN paquete_modulo 
  ON módulo está en el paquete de la empresa
WHERE perfil_id = X
  AND módulo está activo
  AND módulo está en el paquete
```

**Excepciones:**
- Super Admin (poder=3): Ve todos los módulos del perfil (sin filtrar por paquete)
- Usuario sin empresa: Ve todos los módulos del perfil

### 2. Método `getMenuForPermissions()` - Modificado
**Archivo:** `app/Models/ModuloDetalle.php`

**Cambios:**
- Misma lógica que `getMenu()` pero sin filtrar por `mostrar = 'S'`
- Usado para verificación de permisos (no solo menú)

### 3. Método `getAllowedByPerfil()` - Modificado
**Archivo:** `app/Models/ModuloDetalle.php`

**Cambios:**
- Ahora también filtra por paquete
- Usado por `SessionFilter` para verificar permisos de rutas
- Obtiene `empresa_id` automáticamente de la sesión

## 🔐 Seguridad

### Protección Implementada

1. **Nivel Paquete (Límite Máximo)**
   - Define qué módulos tiene disponibles la empresa
   - Si un módulo NO está en el paquete → No se muestra ni se puede acceder

2. **Nivel Perfil (Permisos)**
   - Define permisos (ver, editar, eliminar) sobre los módulos del paquete
   - Si un módulo está en el paquete pero NO en el perfil → No se muestra

3. **Resultado Final**
   - Usuario solo ve módulos que estén en **AMBOS** (paquete Y perfil)
   - No puede ver módulos que no están en su paquete, aunque estén en su perfil
   - No puede ver módulos que no están en su perfil, aunque estén en su paquete

### Ejemplo de Seguridad

**Escenario:**
- Empresa tiene paquete "Free" (módulos: 33, 34, 35)
- En `perfil_modulo` le asignan módulo 36 (Premium)

**Resultado:**
- ❌ El módulo 36 NO aparece en el menú (no está en `paquete_modulo`)
- ❌ Aunque intente acceder directamente, `fn_usuario_puede_ver_modulo()` lo bloquea
- ✅ Solo ve módulos 33, 34, 35 (los que están en AMBOS)

## 📝 Compatibilidad

### Backward Compatibility

- Los métodos mantienen la misma firma (parámetro `$empresaId` es opcional)
- Si no se proporciona `$empresaId`, se obtiene automáticamente de la sesión
- Super Admin sigue viendo todos los módulos (sin cambios)
- Usuarios sin empresa siguen viendo todos los módulos del perfil

### Controladores

**No requieren cambios** porque:
- `getMenu()` se llama igual: `$modulo->getMenu($perfil_id)`
- El método obtiene `empresa_id` automáticamente de la sesión
- Todos los controladores existentes seguirán funcionando

## ✅ Verificación

### Para Probar

1. **Crear empresa con paquete "Free"**
   ```sql
   INSERT INTO empresa (nombre, paquete_id) VALUES ('Test Free', 4);
   ```

2. **Asignar módulos al paquete "Free"**
   ```sql
   -- Solo módulos básicos (ej: 33, 34, 35)
   INSERT INTO paquete_modulo (paquete_id, modulo_id, incluido) 
   VALUES (4, 33, 'S'), (4, 34, 'S'), (4, 35, 'S');
   ```

3. **Crear usuario con perfil "Nutricionista"**
   ```sql
   INSERT INTO usuario (nombre, correo, perfil_id, empresa_id) 
   VALUES ('Test', 'test@test.com', 9, [empresa_id]);
   ```

4. **Verificar que el usuario solo ve módulos 33, 34, 35**
   - Iniciar sesión
   - Verificar menú
   - Intentar acceder a módulo 36 (debe estar bloqueado)

## 🚀 Próximos Pasos

1. ✅ Implementación completada
2. ⏳ Probar en ambiente de desarrollo
3. ⏳ Verificar que Super Admin sigue funcionando correctamente
4. ⏳ Verificar que usuarios sin empresa siguen funcionando
5. ⏳ Documentar para el equipo

## 📚 Referencias

- `app/Models/ModuloDetalle.php` - Métodos modificados
- `app/Filters/SessionFilter.php` - Usa `getAllowedByPerfil()` (ya actualizado)
- `nextline_pyme.sql` - Función `fn_usuario_puede_ver_modulo()` (ya verifica paquete)
