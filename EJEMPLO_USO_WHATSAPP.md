# 📱 Ejemplo de Uso de WhatsApp con Twilio

## ✅ Configuración Rápida

**NO necesitas poner el código de Twilio en ningún archivo PHP.** Todo ya está implementado. Solo configura tus credenciales en `.env`:

```env
# En tu archivo .env (raíz del proyecto)
WHATSAPP_PROVIDER=twilio
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=tu_auth_token_aqui
TWILIO_WHATSAPP_FROM=whatsapp:+1234567890
TWILIO_CONTENT_SID_CONFIRMACION=HXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

## 🚀 ¿Cómo Funciona?

El código que mostraste:
```php
$sid    = "ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$token  = "tu_auth_token_aqui";
$twilio = new Client($sid, $token);

$message = $twilio->messages->create("whatsapp:+56991621564", array(
    "from" => "whatsapp:+14155238886",
    "contentSid" => "HXb5b62575e6e4ff6129ad7c8efe1f983e",
    "contentVariables" => "{"1":"12/1","2":"3pm"}",
    "body" => "Your Message"
));
```

**Ya está implementado automáticamente en:**
- `app/Libraries/WhatsAppService.php` - Método `enviarPorTwilio()`
- Se usa automáticamente cuando:
  - Se agenda una cita → Envía confirmación
  - Se confirma una cita → Envía confirmación
  - Se ejecuta el comando de recordatorios → Envía recordatorios

## 📝 Uso Manual (Opcional)

Si quieres enviar un mensaje manualmente desde código, puedes hacerlo así:

```php
use App\Libraries\WhatsAppService;

// En cualquier controlador o comando
$whatsappService = new WhatsAppService();

// Opción 1: Mensaje simple
$resultado = $whatsappService->enviarMensaje(
    '+56991621564',  // Número destino
    'Hola, este es un mensaje de prueba'
);

// Opción 2: Con plantilla (como tu ejemplo)
$resultado = $whatsappService->enviarMensaje(
    '+56991621564',  // Número destino
    null,  // No body cuando hay plantilla
    null,  // paciente_id (opcional)
    null,  // agenda_id (opcional)
    null,  // nutricionista_id (opcional)
    'HXb5b62575e6e4ff6129ad7c8efe1f983e',  // contentSid
    '{"1":"12/1","2":"3pm"}'  // contentVariables
);
```

## 🎯 Flujo Automático

1. **Nutricionista agenda una cita** → 
   - `AgendaController::agendar()` se ejecuta
   - Llama a `enviarWhatsAppConfirmacion()`
   - Usa `WhatsAppService::enviarConfirmacionCita()`
   - Si hay `TWILIO_CONTENT_SID_CONFIRMACION` configurado, usa la plantilla
   - Si no, usa mensaje de texto simple

2. **Paciente confirma desde email** →
   - `AgendaController::confirmarCita()` se ejecuta
   - También envía WhatsApp automáticamente

3. **Recordatorios automáticos** →
   - Ejecuta: `php spark enviarRecordatoriosWhatsApp`
   - Envía recordatorios 24 horas antes

## 🔍 Verificar que Funciona

1. **Agenda una cita de prueba** desde el calendario
2. **Revisa los logs**: `writable/logs/log-YYYY-MM-DD.log`
3. **Revisa la tabla**: `whatsapp_mensajes` en la base de datos

## ⚠️ Importante

- **NUNCA** pongas las credenciales directamente en el código PHP
- **SIEMPRE** usa el archivo `.env` (ya está en `.gitignore`)
- Las credenciales se cargan automáticamente desde `.env`

## 📚 Más Información

Ver `CONFIGURACION_WHATSAPP.md` para:
- Configuración completa
- Webhooks
- Solución de problemas
- Creación de plantillas
