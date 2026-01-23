# 🔔 Configurar Webhook de Notificaciones - Mercado Pago

## ✅ Estado Actual

El webhook **ya está implementado** en el código:
- ✅ Controlador: `app/Controllers/Api/MercadoPagoWebhookController.php`
- ✅ Ruta: `POST /api/mercadopago/webhook`
- ✅ Procesa notificaciones de pagos
- ✅ Actualiza el estado de los pagos automáticamente

**Lo que falta**: Configurar la URL del webhook en el panel de Mercado Pago.

## 📋 Pasos para Configurar el Webhook en el Panel

### Paso 1: Obtener la URL del Webhook

La URL de tu webhook es:
```
https://tu-dominio.com/api/mercadopago/webhook
```

**Si estás en localhost**, necesitas usar ngrok:
```bash
ngrok http 80
# Usa la URL que te da: https://abc123.ngrok.io/api/mercadopago/webhook
```

### Paso 2: Configurar en el Panel de Mercado Pago

1. Ve a: https://www.mercadopago.cl/developers/panel
2. Selecciona tu aplicación (ej: "NutrySync")
3. En el menú izquierdo, ve a **"Webhooks"** → **"Configurar notificaciones"**
4. Selecciona la pestaña **"Modo productivo"** (o "Modo prueba" para sandbox)
5. Ingresa la URL del webhook:
   ```
   https://tu-dominio.com/api/mercadopago/webhook
   ```
   O si usas ngrok:
   ```
   https://abc123.ngrok.io/api/mercadopago/webhook
   ```
6. Selecciona el evento **"Pagos"** (para recibir notificaciones de pagos)
7. Haz clic en **"Guardar configuración"**
8. **IMPORTANTE**: Se generará una **clave secreta** - guárdala, la necesitarás para validar las notificaciones

### Paso 3: Probar el Webhook

1. Después de guardar, haz clic en **"Simular"**
2. Selecciona la URL que configuraste
3. Selecciona el tipo de evento: **"Pagos"**
4. Ingresa un **Data ID** (puede ser cualquier número, ej: `123456`)
5. Haz clic en **"Enviar prueba"**
6. Verifica que recibas una respuesta exitosa

### Paso 4: Verificar que Funciona

1. Realiza una compra de prueba desde tu aplicación
2. Completa el pago en Mercado Pago
3. Revisa los logs de tu aplicación:
   ```bash
   tail -f writable/logs/log-YYYY-MM-DD.log | grep -i "webhook\|mercadopago"
   ```
4. Deberías ver:
   - `Webhook recibido de Mercado Pago`
   - `Pago actualizado: [ID] - Estado: approved`

## 🔐 Validación de Firma (Opcional pero Recomendado)

Mercado Pago envía una firma en el header `x-signature` para validar que la notificación es auténtica.

### Cómo Funciona

1. Mercado Pago genera una **clave secreta** cuando configuras el webhook
2. Esta clave se envía en cada notificación en el header `x-signature`
3. Tu servidor debe validar esta firma usando HMAC SHA256

### Implementación

El código actual **no valida la firma** (para facilitar las pruebas). Para producción, se recomienda agregar la validación.

**Para implementar la validación:**
1. Guarda la clave secreta del panel de Mercado Pago
2. Agrega un método de validación en `MercadoPagoWebhookController`
3. Valida la firma antes de procesar la notificación

**Ejemplo de validación (según documentación de Mercado Pago):**
```php
// Extraer ts y v1 del header x-signature
$xSignature = "ts=1742505638683,v1=ced36ab6d33566bb1e16c125819b8d840d6b8ef136b0b9127c76064466f5229b";
$parts = explode(',', $xSignature);
$ts = null;
$hash = null;
foreach ($parts as $part) {
    list($key, $value) = explode('=', $part, 2);
    if ($key === 'ts') $ts = $value;
    if ($key === 'v1') $hash = $value;
}

// Crear el manifest
$manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";

// Generar HMAC
$secret = "TU_CLAVE_SECRETA_DEL_PANEL";
$calculatedHash = hash_hmac('sha256', $manifest, $secret);

// Validar
if ($calculatedHash === $hash) {
    // Firma válida
} else {
    // Firma inválida - rechazar notificación
}
```

## 📊 Qué Hace el Webhook Actualmente

Cuando Mercado Pago envía una notificación:

1. **Recibe la notificación** en `/api/mercadopago/webhook`
2. **Extrae el tipo y el ID** del pago
3. **Busca el pago** en la base de datos usando:
   - `external_reference` (ID del pago local)
   - `preference_id` (ID de la preferencia)
4. **Obtiene el pago actualizado** desde la API de Mercado Pago
5. **Actualiza el estado** del pago en la base de datos
6. **Actualiza el estado de la cita** si el pago está aprobado:
   - Si el pago está `approved` y tiene `detalle_agenda_id`, cambia `estado_cita` a `agendada`

## ⚠️ Notas Importantes

1. **El webhook debe responder 200 OK** en menos de 22 segundos
   - Si no responde, Mercado Pago reintentará cada 15 minutos
   - El código actual siempre responde 200 OK

2. **URL debe ser HTTPS** (o usar ngrok para desarrollo)

3. **La ruta es pública** (sin autenticación) para que Mercado Pago pueda acceder

4. **En sandbox**, puedes omitir la validación de firma para facilitar las pruebas

5. **En producción**, se recomienda implementar la validación de firma

## 🔍 Verificar que el Webhook Funciona

### Método 1: Revisar Logs

```bash
# Ver logs en tiempo real
tail -f writable/logs/log-2026-01-23.log | grep -i "webhook"
```

### Método 2: Verificar en la Base de Datos

Después de un pago, verifica:
```sql
SELECT id, estado_pago, mp_payment_id, mp_status, detalle_agenda_id 
FROM pagos 
WHERE mp_preference_id IS NOT NULL 
ORDER BY fcreacion DESC 
LIMIT 5;
```

### Método 3: Usar el Endpoint de Verificación Manual

```php
// Desde el código o una herramienta como Postman
GET /api/mercadopago/verificar-pago/{pago_id}
```

## 📚 Referencias

- [Documentación: Configurar notificaciones de pago](https://www.mercadopago.cl/developers/es/docs/checkout-pro/payment-notifications)
- [Panel de Desarrolladores](https://www.mercadopago.cl/developers/panel)
- [Validación de firma (HMAC)](https://www.mercadopago.cl/developers/es/docs/checkout-pro/payment-notifications#editor_1)

## ⚡ Resumen

1. ✅ El webhook ya está implementado en el código
2. ⚠️ Falta configurar la URL en el panel de Mercado Pago
3. ⚠️ Falta implementar validación de firma (opcional pero recomendado)
4. ✅ El webhook actualiza automáticamente los estados de pago y citas
