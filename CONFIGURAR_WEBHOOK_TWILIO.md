# 🔧 Configurar Webhook de Twilio WhatsApp

## ⚠️ Problema Común

Si ves en la configuración del Sandbox una URL como:
```
https://timberwolf-mastiff-9776.twil.io/demo-reply
```

**Esto NO funcionará** porque es un demo de Twilio, no tu servidor.

## ✅ Solución: Configurar tu URL

### Paso 1: Obtener tu URL del Webhook

Tu webhook debe ser:
```
https://TU-DOMINIO.com/whatsapp/webhook
```

**Ejemplos:**
- Producción: `https://nextline.cl/whatsapp/webhook`
- Desarrollo local con ngrok: `https://abc123.ngrok.io/whatsapp/webhook`

### Paso 2: Configurar en Twilio

1. Ve a **Twilio Console > Messaging > Try it out > Send a WhatsApp message**
2. O ve directamente a: **Console > Messaging > Settings > WhatsApp Sandbox Settings**

3. En la sección **"Sandbox Configuration"**:

   **a) "When a message comes in":**
   - URL: `https://TU-DOMINIO.com/whatsapp/webhook`
   - Método: **POST** (debe estar seleccionado)

   **b) "Status callback URL":**
   - URL: `https://TU-DOMINIO.com/whatsapp/webhook`
   - Método: **POST** (debe estar seleccionado)

4. Haz clic en **Save**

### Paso 3: Para Desarrollo Local (ngrok)

Si estás desarrollando en localhost, necesitas exponer tu servidor:

1. **Instalar ngrok:**
   ```bash
   # Windows: Descarga desde https://ngrok.com/download
   # O con chocolatey: choco install ngrok
   ```

2. **Iniciar tu servidor local:**
   ```bash
   php spark serve
   # O si usas WAMP/XAMPP, asegúrate que esté corriendo
   ```

3. **Exponer con ngrok:**
   ```bash
   ngrok http 80
   # O el puerto que uses: ngrok http 8080
   ```

4. **Copiar la URL HTTPS que te da ngrok:**
   ```
   Forwarding: https://abc123def456.ngrok.io -> http://localhost:80
   ```

5. **Usar esa URL en Twilio:**
   ```
   https://abc123def456.ngrok.io/whatsapp/webhook
   ```

6. **⚠️ IMPORTANTE:** Cada vez que reinicies ngrok, la URL cambia. Debes actualizarla en Twilio.

### Paso 4: Verificar que Funciona

1. **Envía un mensaje de prueba** desde WhatsApp al número de Twilio: `+1 415 523 8886`
2. **Revisa los logs** de tu aplicación:
   ```bash
   tail -f writable/logs/log-*.log
   ```
3. **Deberías ver** algo como:
   ```
   INFO - Mensaje recibido de Twilio: +56991621564 - Hola
   ```

4. **Revisa la tabla** `whatsapp_mensajes` en tu base de datos

### Paso 5: Verificar el Endpoint

Puedes probar manualmente que tu endpoint funciona:

```bash
# Probar con curl (desde terminal)
curl -X POST https://TU-DOMINIO.com/whatsapp/webhook \
  -d "From=whatsapp:+56991621564" \
  -d "Body=Test" \
  -d "MessageSid=test123"
```

O desde el navegador, deberías ver un error 400 (porque falta autenticación), pero confirma que el endpoint existe.

## 🔍 Troubleshooting

### El webhook no recibe mensajes

1. **Verifica que la URL sea HTTPS** (no HTTP)
2. **Verifica que sea accesible públicamente** (no localhost)
3. **Revisa los logs de Twilio** en la consola
4. **Verifica que el método sea POST**

### Error 404 en el webhook

- Verifica que la ruta esté configurada en `app/Config/Routes.php`:
  ```php
  $routes->post('whatsapp/webhook', 'WhatsAppWebhookController::webhook');
  ```

### Error 500 en el webhook

- Revisa los logs de tu aplicación: `writable/logs/`
- Verifica que las credenciales de Twilio estén en `.env`
- Verifica que el SDK de Twilio esté instalado: `composer show twilio/sdk`

## 📝 Configuración Final

Una vez configurado correctamente, deberías ver en Twilio:

**Sandbox Configuration:**
- When a message comes in: `https://TU-DOMINIO.com/whatsapp/webhook` [POST]
- Status callback URL: `https://TU-DOMINIO.com/whatsapp/webhook` [POST]

**Sandbox Participants:**
- Tu número: `whatsapp:+56991621564` ✅ (ya está unido)

## ✅ Checklist

- [ ] URL del webhook apunta a tu servidor (no al demo de Twilio)
- [ ] URL es HTTPS (no HTTP)
- [ ] URL es accesible públicamente
- [ ] Método configurado como POST
- [ ] Guardado los cambios en Twilio
- [ ] Probado enviando un mensaje
- [ ] Verificado en los logs que llega el mensaje
