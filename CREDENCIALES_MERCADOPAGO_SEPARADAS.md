# 🔐 Credenciales de Mercado Pago Separadas (Sandbox y Production)

## ✅ Implementación Completada

Ahora puedes guardar **ambas credenciales** (sandbox y production) y cambiar entre ellas simplemente cambiando el modo.

## 📋 Cambios Realizados

### 1. Base de Datos

**Script SQL:** `agregar_credenciales_mercadopago_separadas.sql`

**Nuevos campos agregados:**
- `mp_access_token_sandbox` - Access Token para pruebas
- `mp_public_key_sandbox` - Public Key para pruebas
- `mp_access_token_production` - Access Token para producción
- `mp_public_key_production` - Public Key para producción

**Migración automática:**
- Los datos existentes se migran automáticamente según el modo actual
- Si el modo es 'sandbox', se copian a campos sandbox
- Si el modo es 'production', se copian a campos production

### 2. Modelo (`EmpresaConfiguracion.php`)

**Método `obtenerCredencialesMercadoPago()` actualizado:**
- Ahora lee las credenciales según el modo configurado
- Si `mp_mode = 'sandbox'` → usa `mp_access_token_sandbox` y `mp_public_key_sandbox`
- Si `mp_mode = 'production'` → usa `mp_access_token_production` y `mp_public_key_production`
- **Compatibilidad:** Si los nuevos campos están vacíos, usa los campos antiguos como fallback

### 3. Vista (`configuracion/index.php`)

**Nuevos campos en el formulario:**
- Sección "Credenciales Sandbox (Pruebas)" con:
  - Access Token (Sandbox)
  - Public Key (Sandbox)
- Sección "Credenciales Producción" con:
  - Access Token (Producción)
  - Public Key (Producción)
- Selector de "Modo Activo" para cambiar entre sandbox y production

### 4. Controlador (`ConfiguracionController.php`)

**Método `guardar()` actualizado:**
- Guarda los 4 campos separados
- Mantiene compatibilidad guardando también en campos antiguos según el modo seleccionado

## 🎯 Cómo Funciona

### Configuración

1. **Habilitar Mercado Pago** → Activa el checkbox
2. **Configurar credenciales Sandbox:**
   - Access Token (Sandbox): `TEST-XXXXXXXXXXXXXXX`
   - Public Key (Sandbox): `TEST-XXXXXXXXXXXXXXX`
3. **Configurar credenciales Production:**
   - Access Token (Producción): `APP_USR-XXXXXXXXXXXXXXX`
   - Public Key (Producción): `APP_USR-XXXXXXXXXXXXXXX`
4. **Seleccionar Modo Activo:**
   - **Sandbox** → Usa credenciales de sandbox
   - **Producción** → Usa credenciales de production

### Uso Automático

Cuando el sistema necesita usar Mercado Pago:
1. Lee el `mp_mode` configurado
2. Automáticamente usa las credenciales correspondientes:
   - `sandbox` → `mp_access_token_sandbox` + `mp_public_key_sandbox`
   - `production` → `mp_access_token_production` + `mp_public_key_production`

### Cambiar de Modo

Para cambiar entre sandbox y production:
1. Ve a **Configuraciones**
2. Cambia el selector "Modo Activo"
3. Guarda
4. **¡Listo!** El sistema ahora usará las credenciales del modo seleccionado

## 📝 Pasos para Implementar

### 1. Ejecutar Script SQL

```sql
-- Ejecutar en phpMyAdmin o cliente MySQL
SOURCE agregar_credenciales_mercadopago_separadas.sql;
```

O copiar y pegar el contenido del archivo SQL.

### 2. Configurar Credenciales

1. Ve a **Dashboard → Configuraciones**
2. Activa "Habilitar Mercado Pago"
3. Completa las credenciales de **Sandbox** (para pruebas)
4. Completa las credenciales de **Production** (para pagos reales)
5. Selecciona el **Modo Activo** que quieres usar
6. Guarda

### 3. Verificar

- Los campos se guardan correctamente
- Al cambiar el modo, el sistema usa las credenciales correctas
- Los pagos funcionan con el modo seleccionado

## 🔄 Compatibilidad

- **Los campos antiguos (`mp_access_token`, `mp_public_key`) se mantienen** para compatibilidad
- Si los nuevos campos están vacíos, el sistema usa los antiguos como fallback
- La migración automática copia los datos existentes a los nuevos campos

## ⚠️ Notas Importantes

1. **No elimines los campos antiguos** hasta que todas las empresas hayan migrado
2. **Las credenciales de sandbox comienzan con "TEST-"**
3. **Las credenciales de production comienzan con "APP_USR-"**
4. **Siempre verifica el modo antes de hacer pagos reales**

## 🎉 Beneficios

- ✅ Puedes tener ambas credenciales configuradas
- ✅ Cambio rápido entre sandbox y production
- ✅ No necesitas cambiar credenciales manualmente
- ✅ Ideal para testing y producción en el mismo sistema
- ✅ Compatible con datos existentes
