# Configuración de Email para Desarrollo Local

## Opción 1: Gmail SMTP (Recomendado para desarrollo)

### Pasos:

1. **Habilitar verificación en 2 pasos** en tu cuenta de Gmail
2. **Generar una contraseña de aplicación**:
   - Ve a: https://myaccount.google.com/apppasswords
   - Selecciona "Correo" y "Otro (nombre personalizado)"
   - Ingresa "CodeIgniter Local" y genera la contraseña
   - Copia la contraseña de 16 caracteres (sin espacios)

3. **Configurar en `.env`**:
```env
# Email Configuration - Gmail SMTP
email.fromEmail = "tu-email@gmail.com"
email.fromName = "Tu Nombre"
email.SMTPHost = "smtp.gmail.com"
email.SMTPUser = "tu-email@gmail.com"
email.SMTPPass = "tu-contraseña-de-aplicacion-16-caracteres"
email.SMTPPort = 587
email.SMTPCrypto = "tls"
```

### Ventajas:
- ✅ Funciona inmediatamente
- ✅ No requiere software adicional
- ✅ Los emails se envían realmente

### Desventajas:
- ⚠️ Límite de 500 emails/día con cuenta gratuita
- ⚠️ Requiere cuenta de Gmail

---

## Opción 2: Outlook/Hotmail SMTP

### Configurar en `.env`:
```env
# Email Configuration - Outlook SMTP
email.fromEmail = "tu-email@outlook.com"
email.fromName = "Tu Nombre"
email.SMTPHost = "smtp-mail.outlook.com"
email.SMTPUser = "tu-email@outlook.com"
email.SMTPPass = "tu-contraseña"
email.SMTPPort = 587
email.SMTPCrypto = "tls"
```

---

## Opción 3: Mailtrap (Solo para Testing - NO envía emails reales)

Mailtrap captura los emails y los muestra en un dashboard web. Perfecto para desarrollo.

### Pasos:

1. **Crear cuenta gratuita**: https://mailtrap.io/
2. **Obtener credenciales** del inbox de prueba
3. **Configurar en `.env`**:
```env
# Email Configuration - Mailtrap (Testing)
email.fromEmail = "test@example.com"
email.fromName = "Test Local"
email.SMTPHost = "smtp.mailtrap.io"
email.SMTPUser = "tu-usuario-mailtrap"
email.SMTPPass = "tu-password-mailtrap"
email.SMTPPort = 2525
email.SMTPCrypto = "tls"
```

### Ventajas:
- ✅ No envía emails reales (seguro)
- ✅ Ve todos los emails en un dashboard
- ✅ Perfecto para desarrollo
- ✅ Cuenta gratuita con 500 emails/mes

---

## Opción 4: MailHog (Servidor SMTP Local)

MailHog es un servidor SMTP local que captura todos los emails.

### Instalación (Windows con WAMP):

1. **Descargar MailHog**: https://github.com/mailhog/MailHog/releases
2. **Ejecutar MailHog.exe** (se abre en http://localhost:8025)
3. **Configurar en `.env`**:
```env
# Email Configuration - MailHog (Local)
email.fromEmail = "test@localhost"
email.fromName = "Test Local"
email.SMTPHost = "localhost"
email.SMTPUser = ""
email.SMTPPass = ""
email.SMTPPort = 1025
email.SMTPCrypto = ""
```

### Ventajas:
- ✅ 100% local, no requiere internet
- ✅ Ve emails en http://localhost:8025
- ✅ No envía emails reales

---

## Probar el Envío de Emails

### Crear un script de prueba:

Crea `app/Controllers/TestEmail.php`:

```php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Services;

class TestEmail extends BaseController
{
    public function index()
    {
        $email = Services::email();
        
        $email->setFrom(env('email.fromEmail'), env('email.fromName'));
        $email->setTo('tu-email-destino@gmail.com');
        $email->setSubject('Prueba de Email desde Local');
        $email->setMessage('<h1>¡Funciona!</h1><p>Este email fue enviado desde tu entorno local.</p>');
        
        if ($email->send()) {
            echo "✅ Email enviado exitosamente!";
        } else {
            echo "❌ Error: " . $email->printDebugger(['headers', 'subject', 'body']);
        }
    }
}
```

### Agregar ruta en `app/Config/Routes.php`:

```php
$routes->get('test-email', 'TestEmail::index');
```

### Probar:
Visita: `http://localhost/codeigniter4/nextline_ci4/test-email`

---

## Solución de Problemas

### Error: "Connection timed out"
- Verifica que el puerto no esté bloqueado por firewall
- Prueba con `SMTPPort = 587` y `SMTPCrypto = "tls"` en lugar de 465/ssl

### Error: "Authentication failed"
- Verifica usuario y contraseña
- Para Gmail, asegúrate de usar contraseña de aplicación, no tu contraseña normal

### Error: "Could not instantiate mail function"
- Verifica que `protocol = 'smtp'` en `app/Config/Email.php`
- No uses `protocol = 'mail'` en Windows local

---

## Recomendación para Desarrollo

**Usa Mailtrap o MailHog** para desarrollo local:
- No envías emails reales por error
- Puedes ver todos los emails enviados
- No afecta tu cuenta de Gmail/Outlook

**Usa Gmail SMTP** solo cuando necesites probar envíos reales.
