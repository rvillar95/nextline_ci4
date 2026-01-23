# 📋 Checklist: Subir Proyecto a Producción para Mercado Pago

## ✅ Ventajas de Subir a Producción

1. **Webhooks funcionan directamente** (no necesitas ngrok)
2. **URLs públicas** para back_urls de Mercado Pago
3. **Pruebas más realistas** del flujo completo
4. **Mejor para completar la integración** en el panel de Mercado Pago

## ⚠️ Consideraciones Antes de Subir

### 1. Configuración del Entorno

- [ ] Cambiar `ENVIRONMENT` a `production` en `.env` o `app/Config/Boot/production.php`
- [ ] Verificar que `app.baseURL` esté configurado correctamente
- [ ] Asegurar que las rutas funcionen correctamente

### 2. Base de Datos

- [ ] Hacer backup de la base de datos actual
- [ ] Ejecutar todas las migraciones SQL necesarias:
  - `agregar_campos_mercadopago_pagos.sql`
  - `agregar_mercadopago_empresa_configuraciones.sql`
  - `crear_tabla_plantillas_botones_pago.sql`
  - `actualizar_flujo_estados_cita.sql`
- [ ] Verificar que todas las tablas estén creadas

### 3. Credenciales de Mercado Pago

- [ ] **IMPORTANTE**: Usar credenciales de **PRUEBA** (sandbox) en producción temporalmente
- [ ] Configurar en `/dashboard/configuracion`:
  - Access Token de prueba
  - Public Key de prueba
  - Modo: `sandbox`
- [ ] **NO usar credenciales de producción** hasta que todo esté probado

### 4. Configuración de Mercado Pago en el Panel

Una vez subido, configurar con tu dominio `nutrisync.nextline.cl`:

- [ ] **URLs de retorno** en el panel:
  - Success: `https://nutrisync.nextline.cl/dashboard/pago/success`
  - Failure: `https://nutrisync.nextline.cl/dashboard/pago/failure`
  - Pending: `https://nutrisync.nextline.cl/dashboard/pago/pending`
- [ ] **Webhook URL**:
  - `https://nutrisync.nextline.cl/api/mercadopago/webhook`
- [ ] **Evento**: Seleccionar "Pagos"

### 5. Archivos y Permisos

- [ ] Subir todos los archivos del proyecto
- [ ] Configurar permisos:
  - `writable/` debe ser escribible (755 o 775)
  - `writable/logs/` debe ser escribible
  - `writable/uploads/` debe ser escribible
- [ ] Verificar que `.env` esté configurado correctamente

### 6. Composer y Dependencias

- [ ] Ejecutar `composer install --no-dev` en el servidor
- [ ] Verificar que `mercadopago/dx-php` esté instalado

### 7. Configuración del Servidor

- [ ] Verificar que PHP 8.0+ esté instalado
- [ ] Verificar que las extensiones necesarias estén habilitadas:
  - `curl`
  - `json`
  - `mbstring`
  - `openssl`
- [ ] Configurar el `.htaccess` o equivalente para CodeIgniter 4

## 📝 Pasos Recomendados

### Paso 1: Preparar el Proyecto Localmente

1. Verificar que todo funcione en localhost
2. Hacer commit de todos los cambios
3. Crear un backup de la base de datos

### Paso 2: Subir al Servidor

1. Subir archivos vía FTP/SFTP o Git
2. Configurar `.env` en el servidor
3. Ejecutar migraciones SQL
4. Instalar dependencias con Composer

### Paso 3: Configurar Mercado Pago

1. Ir al panel de Mercado Pago
2. Configurar URLs de retorno con tu dominio real
3. Configurar webhook con tu dominio real
4. Probar con una compra de prueba

### Paso 4: Verificar

1. Crear una cita de prueba
2. Confirmar la cita
3. Completar el pago
4. Verificar que el webhook funcione
5. Verificar que el estado se actualice correctamente

## 🔒 Seguridad

- [ ] No subir archivos sensibles (`.env` con credenciales reales)
- [ ] Usar credenciales de prueba (sandbox) inicialmente
- [ ] Verificar que el servidor tenga SSL/HTTPS configurado
- [ ] Revisar permisos de archivos y directorios

## 🧪 Pruebas Después de Subir

1. **Probar creación de preferencia:**
   - Crear un botón de pago
   - Verificar que se genere la preferencia

2. **Probar flujo completo:**
   - Agendar cita con botón de pago
   - Confirmar cita desde email
   - Recibir email con botón de pago
   - Completar pago en Mercado Pago
   - Verificar que el webhook actualice el estado

3. **Verificar logs:**
   - Revisar logs en `writable/logs/`
   - Verificar que no haya errores

## 📚 Archivos de Configuración Importantes

- `.env` - Variables de entorno
- `app/Config/App.php` - Configuración base
- `app/Config/Database.php` - Configuración de BD
- `app/Config/MercadoPago.php` - Configuración de Mercado Pago (fallback)

## ⚡ Resumen Rápido

1. ✅ Subir proyecto al servidor
2. ✅ Configurar `.env` con datos del servidor
3. ✅ Ejecutar migraciones SQL
4. ✅ Instalar dependencias (composer)
5. ✅ Configurar URLs en panel de Mercado Pago
6. ✅ Probar flujo completo

## 🎯 Después de Subir

Una vez que todo funcione en el servidor:

1. Completar la compra de prueba en el panel de Mercado Pago
2. El progreso debería aumentar (de 13% a más)
3. Configurar webhook y probarlo
4. Cuando todo esté probado, cambiar a credenciales de producción
