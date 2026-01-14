# 📱 Configuración de Integración con WhatsApp

Esta guía explica cómo configurar la integración de WhatsApp con el sistema de agenda para permitir:
- **Agendamiento automático** desde mensajes de WhatsApp
- **Envío de confirmaciones** cuando se agenda una cita (con soporte para plantillas de Twilio)
- **Recordatorios automáticos** 24 horas antes de la cita
- **Recepción de mensajes** para procesar solicitudes de citas
- **Uso del SDK oficial de Twilio** para mejor integración y soporte de plantillas

## 🎯 Opciones de Integración

El sistema soporta dos proveedores de WhatsApp:

### 1. **Twilio WhatsApp API** (Recomendado para empezar)
- ✅ Más fácil de configurar
- ✅ Ideal para pruebas y desarrollo
- ✅ Soporte completo de webhooks
- ⚠️ Requiere cuenta de Twilio (puede tener costos)

### 2. **WhatsApp Business API** (Oficial de Meta)
- ✅ API oficial de Meta/Facebook
- ✅ Más robusto para producción
- ✅ Mejor integración con WhatsApp Business
- ⚠️ Requiere verificación de negocio y proceso más complejo

---

## 🔧 Configuración con Twilio

### Paso 1: Crear cuenta en Twilio

1. Regístrate en [Twilio](https://www.twilio.com/try-twilio)
2. Verifica tu número de teléfono
3. Obtén tu **Account SID** y **Auth Token** desde el dashboard

### Paso 2: Configurar WhatsApp Sandbox (Pruebas)

1. Ve a **Messaging > Try it out > Send a WhatsApp message**
2. Sigue las instrucciones para conectar tu WhatsApp personal al Sandbox
3. Anota el número de Twilio (formato: `whatsapp:+14155238886`)

### Paso 3: Instalar SDK de Twilio

El sistema usa el SDK oficial de Twilio. Instálalo ejecutando:

```bash
composer require twilio/sdk
```

O si ya está en `composer.json`, ejecuta:

```bash
composer install
```

### Paso 4: Configurar variables de entorno

Edita tu archivo `.env` y agrega:

```env
# Proveedor de WhatsApp (twilio o whatsapp_business)
WHATSAPP_PROVIDER=twilio

# Configuración de Twilio
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886

# Plantilla de Twilio para confirmaciones (opcional)
# Si no se configura, se usará mensaje de texto simple
# Para crear una plantilla: Content > Templates en Twilio Console
TWILIO_CONTENT_SID_CONFIRMACION=HX350d429d32e64a552466cafecbe95f3c
```

#### 📝 Sobre las Plantillas de Twilio

Las plantillas permiten enviar mensajes pre-aprobados por WhatsApp. Son útiles para:
- ✅ Mensajes profesionales y consistentes
- ✅ Cumplir con políticas de WhatsApp
- ✅ Mejor deliverability

**Para crear una plantilla:**
1. Ve a **Content > Templates** en Twilio Console
2. Crea una nueva plantilla de WhatsApp
3. Usa variables como `{{1}}` y `{{2}}` para datos dinámicos
4. Ejemplo: "Your appointment is coming up on {{1}} at {{2}}"
5. Copia el **Content SID** y configúralo en `.env`

**Variables de la plantilla:**
- `{{1}}`: Fecha (formato: "12/1" para 12 de enero)
- `{{2}}`: Hora (formato: "3pm" o "15:00")

Si no configuras `TWILIO_CONTENT_SID_CONFIRMACION`, el sistema usará mensajes de texto simples.

### Paso 5: Configurar Webhook en Twilio

**⚠️ IMPORTANTE:** La URL del webhook debe apuntar a TU servidor, no al demo de Twilio.

1. Ve a **Console > Messaging > Try it out > Send a WhatsApp message**
2. O ve directamente a la configuración del Sandbox
3. En **Sandbox Configuration**, configura:
   - **When a message comes in**: `https://tudominio.com/whatsapp/webhook`
     - Método: **POST**
   - **Status callback URL**: `https://tudominio.com/whatsapp/webhook`
     - Método: **POST**
4. Haz clic en **Save**

**Para desarrollo local**, puedes usar ngrok o similar:
- Instala ngrok: `https://ngrok.com/download`
- Ejecuta: `ngrok http 80` (o el puerto de tu servidor local)
- Usa la URL que te da ngrok: `https://xxxxx.ngrok.io/whatsapp/webhook`

**Ejemplo de URL correcta:**
```
https://tudominio.com/whatsapp/webhook
```

**❌ NO uses URLs como:**
```
https://timberwolf-mastiff-9776.twil.io/demo-reply  ← Esto es un demo de Twilio
```

### Paso 6: Probar la integración

1. Envía un mensaje de WhatsApp al número de Twilio
2. El sistema debería recibirlo y procesarlo automáticamente

---

## 🔧 Configuración con WhatsApp Business API

### Paso 1: Crear cuenta de Meta Business

1. Ve a [Meta Business](https://business.facebook.com/)
2. Crea una cuenta de negocio
3. Verifica tu negocio (puede tomar varios días)

### Paso 2: Configurar WhatsApp Business API

1. Ve a [Meta for Developers](https://developers.facebook.com/)
2. Crea una nueva aplicación
3. Agrega el producto **WhatsApp**
4. Configura tu número de teléfono de negocio

### Paso 3: Obtener credenciales

Necesitarás:
- **Access Token** (temporal o permanente)
- **Phone Number ID**
- **Business Account ID**
- **Verify Token** (cualquier string que elijas)

### Paso 4: Configurar variables de entorno

Edita tu archivo `.env` y agrega:

```env
# Proveedor de WhatsApp
WHATSAPP_PROVIDER=whatsapp_business

# Configuración de WhatsApp Business API
WHATSAPP_ACCESS_TOKEN=tu_access_token_aqui
WHATSAPP_PHONE_NUMBER_ID=123456789012345
WHATSAPP_BUSINESS_ACCOUNT_ID=123456789012345
WHATSAPP_VERIFY_TOKEN=nextline_verify_token_2024
```

### Paso 5: Configurar Webhook

1. En la consola de Meta for Developers, ve a **WhatsApp > Configuration**
2. En **Webhook**, configura:
   - **Callback URL**: `https://tudominio.com/whatsapp/webhook`
   - **Verify Token**: El mismo que configuraste en `.env`
3. Suscríbete a los eventos:
   - `messages`
   - `message_status`

### Paso 6: Verificar Webhook

El sistema incluye un endpoint de verificación automática. Meta enviará una petición GET para verificar:

```
GET /whatsapp/webhook?hub.mode=subscribe&hub.verify_token=TU_TOKEN&hub.challenge=CHALLENGE
```

Si el token coincide, el sistema responderá con el challenge.

---

## 📋 Configuración de Recordatorios Automáticos

### Opción 1: Cron Job (Recomendado)

Agrega esta línea a tu crontab (`crontab -e`):

```bash
# Enviar recordatorios todos los días a las 8:00 AM
0 8 * * * cd /ruta/a/tu/proyecto && php spark enviarRecordatoriosWhatsApp
```

### Opción 2: Tarea Programada de Windows

1. Abre **Programador de tareas**
2. Crea una tarea básica
3. Configura:
   - **Disparador**: Diario a las 8:00 AM
   - **Acción**: Iniciar programa
   - **Programa**: `php`
   - **Argumentos**: `spark enviarRecordatoriosWhatsApp`
   - **Iniciar en**: Ruta de tu proyecto

---

## 💬 Formato de Mensajes para Agendamiento

Los pacientes pueden enviar mensajes en formato natural. El sistema intentará extraer:

### Ejemplos de mensajes que funcionan:

- "Quiero agendar para el 15 de enero a las 10:00"
- "Necesito una cita el 20 de febrero a las 14:30"
- "Agendar para el 5 de marzo a las 9:00"

### Respuestas automáticas:

- ✅ **Cita creada**: El sistema confirma la cita y envía detalles
- ❌ **No disponible**: El sistema informa que no hay horario disponible
- ❓ **No reconocido**: El sistema pide más información

---

## 🔍 Verificación y Debugging

### Verificar que el webhook funciona:

1. **Twilio**: Ve al dashboard y revisa los logs de mensajes
2. **WhatsApp Business**: Ve a Meta for Developers > Webhooks y revisa los eventos

### Logs del sistema:

Los mensajes se registran en:
- `writable/logs/log-YYYY-MM-DD.log`
- Tabla `whatsapp_mensajes` en la base de datos

### Comandos útiles:

```bash
# Ver logs en tiempo real
tail -f writable/logs/log-*.log

# Probar envío manual (desde código)
$whatsappService = new \App\Libraries\WhatsAppService();
$resultado = $whatsappService->enviarMensaje('+56912345678', 'Mensaje de prueba');
```

---

## 🛡️ Seguridad

### Recomendaciones:

1. **Nunca expongas tus tokens** en el código o repositorios
2. **Usa HTTPS** para los webhooks (obligatorio en producción)
3. **Valida los webhooks** usando las firmas de Twilio/Meta
4. **Limita el acceso** a los endpoints de webhook si es posible

### Validación de webhooks:

- **Twilio**: Valida usando `X-Twilio-Signature`
- **WhatsApp Business**: Valida usando `X-Hub-Signature-256`

> ⚠️ **Nota**: La validación de firmas no está implementada en la versión actual. Se recomienda agregarla para producción.

---

## 📊 Monitoreo

### Tabla `whatsapp_mensajes`:

Esta tabla registra todos los mensajes enviados y recibidos:

- `tipo_mensaje`: agendamiento, confirmacion, recordatorio, cancelacion, otro
- `direccion`: enviado, recibido
- `estado_envio`: pendiente, enviado, entregado, leido, error
- `fecha_envio`, `fecha_entrega`, `fecha_lectura`

### Consultas útiles:

```sql
-- Mensajes fallidos
SELECT * FROM whatsapp_mensajes WHERE estado_envio = 'error';

-- Mensajes por paciente
SELECT * FROM whatsapp_mensajes WHERE paciente_id = 123 ORDER BY fecha_envio DESC;

-- Estadísticas de envío
SELECT tipo_mensaje, estado_envio, COUNT(*) 
FROM whatsapp_mensajes 
GROUP BY tipo_mensaje, estado_envio;
```

---

## 🚀 Próximos Pasos

1. ✅ Configurar proveedor (Twilio o WhatsApp Business)
2. ✅ Probar recepción de mensajes
3. ✅ Probar agendamiento automático
4. ✅ Configurar recordatorios automáticos
5. ✅ Monitorear logs y mensajes

---

## ❓ Solución de Problemas

### El webhook no recibe mensajes:

1. Verifica que la URL sea accesible públicamente (usa ngrok para desarrollo local)
2. Revisa los logs del servidor
3. Verifica que el webhook esté configurado correctamente en Twilio/Meta

### Los mensajes no se envían:

1. Verifica las credenciales en `.env`
2. Revisa los logs de errores
3. Verifica que el número de destino esté en formato correcto

### Los recordatorios no se envían:

1. Verifica que el cron job esté configurado
2. Ejecuta manualmente: `php spark enviarRecordatoriosWhatsApp`
3. Revisa que las citas tengan `estado_cita = 'confirmada'`

---

## 📞 Soporte

Para más información:
- [Documentación de Twilio WhatsApp](https://www.twilio.com/docs/whatsapp)
- [Documentación de WhatsApp Business API](https://developers.facebook.com/docs/whatsapp)
- [Issues del proyecto](https://github.com/tu-repo/issues)
