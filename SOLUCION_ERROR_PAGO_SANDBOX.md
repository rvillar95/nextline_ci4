# 🔧 Solución: Error "Algo salió mal... No pudimos procesar tu pago" en Sandbox

## ⚠️ Problema

Aunque estés usando:
- ✅ Tarjetas de prueba correctas (`5031 7557 3453 0604`)
- ✅ Iniciado sesión con cuenta de prueba
- ✅ CVV correcto (`123`)

Sigue apareciendo el error: **"Algo salió mal... No pudimos procesar tu pago"**

## 🔍 Causas Posibles

### 1. **Configuración del Ambiente de Desarrollo Incompleta** (MÁS PROBABLE)

El panel de Mercado Pago muestra **0% de progreso** y **"1 tarea pendiente: Configurar ambiente de desarrollo"**.

**Solución**: Sigue la guía en `CONFIGURAR_AMBIENTE_DESARROLLO_MERCADOPAGO.md`

### 2. **Cuentas de Prueba No Configuradas Correctamente**

Mercado Pago requiere que crees cuentas de prueba específicas en el panel.

**Pasos para crear cuentas de prueba:**

1. Ve a: https://www.mercadopago.cl/developers/es/docs/checkout-api/additional-content/your-integrations/test/accounts
2. O desde el panel: https://www.mercadopago.cl/developers/panel → "Cuentas de prueba"
3. Crea dos cuentas:
   - **Cuenta de Vendedor** (tu aplicación)
   - **Cuenta de Comprador** (para probar pagos)

4. **IMPORTANTE**: Usa la cuenta de comprador para iniciar sesión en el checkout de Mercado Pago

### 3. **Credenciales Incorrectas**

Verifica que estés usando las credenciales correctas:

1. En el panel de Mercado Pago, ve a **"Credenciales"**
2. Asegúrate de estar en la pestaña **"Prueba"** (no "Productivas")
3. Verifica que:
   - **Public Key** comience con `TEST-` o `APP_USR-`
   - **Access Token** comience con `TEST-` o `APP_USR-`
4. Copia estas credenciales y guárdalas en tu sistema:
   - En `/dashboard/configuracion` (si tienes acceso)
   - O verifica que estén correctas en la base de datos

### 4. **URLs de Retorno con localhost**

Si estás usando `localhost`, Mercado Pago no puede acceder a tus URLs de retorno.

**Solución**: Usa ngrok o un dominio público:

```bash
# Instalar ngrok
# Descargar desde: https://ngrok.com/download

# Iniciar ngrok
ngrok http 80

# Usar la URL que te da (ej: https://abc123.ngrok.io)
```

Luego actualiza las URLs en:
- Panel de Mercado Pago → Configurar ambiente de desarrollo
- O en tu código (aunque el código ya maneja esto automáticamente)

### 5. **Email del Pagador No Válido para Sandbox**

El sistema genera automáticamente un email de prueba (`test_user_XXXXXXXX@testuser.com`), pero puede haber un problema.

**Verificación**:
- Revisa los logs en `writable/logs/log-YYYY-MM-DD.log`
- Busca: `MercadoPagoService - Email final que se usará`
- Debe mostrar un email como: `test_user_123456789@testuser.com`

## 📋 Checklist de Verificación

Antes de intentar un pago, verifica:

- [ ] **Panel de Mercado Pago**: Progreso de integración > 0% (no 0%)
- [ ] **Panel de Mercado Pago**: "Configurar ambiente de desarrollo" está completado
- [ ] **Credenciales**: Estás usando credenciales de la pestaña "Prueba"
- [ ] **Cuentas de Prueba**: Has creado cuentas de prueba (vendedor y comprador)
- [ ] **Sesión**: Estás iniciado sesión con la cuenta de comprador de prueba
- [ ] **Tarjeta**: Usas `5031 7557 3453 0604` con CVV `123`
- [ ] **URLs**: Si usas localhost, tienes ngrok corriendo
- [ ] **Logs**: Revisa los logs para ver si hay errores específicos

## 🔧 Pasos de Solución Paso a Paso

### Paso 1: Completar Configuración del Panel

1. Ve a: https://www.mercadopago.cl/developers/panel
2. Selecciona tu aplicación
3. Haz clic en **"Comenzar >"** junto a "Configurar ambiente de desarrollo"
4. Completa todas las configuraciones:
   - URLs de retorno (usa ngrok si estás en localhost)
   - Webhook URL
   - Guarda los cambios

### Paso 2: Crear Cuentas de Prueba

1. Ve a: https://www.mercadopago.cl/developers/es/docs/checkout-api/additional-content/your-integrations/test/accounts
2. Crea una cuenta de comprador
3. Anota el email y contraseña de la cuenta de comprador

### Paso 3: Probar el Pago

1. **Crea un nuevo pago** desde tu aplicación
2. **Haz clic en el botón de pago** (te llevará a Mercado Pago)
3. **Inicia sesión** con la cuenta de comprador de prueba (NO con tu cuenta personal)
4. **Completa el pago** con:
   - Tarjeta: `5031 7557 3453 0604`
   - CVV: `123`
   - Vencimiento: `11/30` (o cualquier fecha futura)
   - Nombre: `APRO`
   - Documento: `123456789`

### Paso 4: Verificar Logs

Si sigue fallando, revisa los logs:

```bash
# Ver los últimos logs
tail -n 100 writable/logs/log-2026-01-23.log | grep -i "mercadopago\|preferencia\|error"
```

Busca específicamente:
- Errores de API de Mercado Pago
- Problemas con la creación de preferencias
- Errores de validación

## 🆘 Si Nada Funciona

### Opción 1: Contactar Soporte de Mercado Pago

1. Ve a: https://www.mercadopago.cl/developers/support
2. Explica el problema con:
   - El preference_id que falla
   - Los logs de error
   - Los pasos que has seguido

### Opción 2: Verificar Estado de la Preferencia

Puedes verificar el estado de una preferencia usando la API:

```bash
curl -X GET \
  'https://api.mercadopago.com/checkout/preferences/3153483094-5e6bbfb2-1462-4f02-8818-bbb2eb78fef8' \
  -H 'Authorization: Bearer TU_ACCESS_TOKEN'
```

Esto te mostrará si hay algún problema con la preferencia específica.

### Opción 3: Crear una Nueva Preferencia

A veces las preferencias antiguas pueden tener problemas. Intenta:
1. Crear una nueva cita
2. Confirmar la cita
3. Usar el nuevo botón de pago generado

## 📚 Referencias

- [Configurar Ambiente de Desarrollo](https://www.mercadopago.cl/developers/es/docs/checkout-pro/configure-development-enviroment)
- [Cuentas de Prueba](https://www.mercadopago.cl/developers/es/docs/checkout-api/additional-content/your-integrations/test/accounts)
- [Tarjetas de Prueba](https://www.mercadopago.cl/developers/es/docs/checkout-api-payments/additional-content/your-integrations/test/cards)
- [Soporte de Mercado Pago](https://www.mercadopago.cl/developers/support)

## ⚡ Resumen Rápido

**Lo más probable es que necesites:**
1. ✅ Completar "Configurar ambiente de desarrollo" en el panel
2. ✅ Crear cuentas de prueba
3. ✅ Iniciar sesión con la cuenta de comprador de prueba (no tu cuenta personal)
4. ✅ Usar ngrok si estás en localhost
