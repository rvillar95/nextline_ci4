# Configuración de Variables de Entorno (.env)

## Cómo crear tu archivo .env

1. Copia el siguiente contenido en un archivo llamado `.env` en la raíz del proyecto
2. Reemplaza los valores de ejemplo con tus credenciales reales
3. **NUNCA** subas el archivo `.env` a Git (ya está en `.gitignore`)

## Contenido del archivo .env

```env
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

app.baseURL = 'https://mansanchez.cl/'

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = localhost
database.default.database = nombre_base_datos
database.default.username = usuario_bd
database.default.password = password_bd
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

#--------------------------------------------------------------------
# EMAIL
#--------------------------------------------------------------------

email.fromEmail = contacto@mansanchez.cl
email.fromName = MANSANCHEZ Constructor
email.SMTPHost = mail.mansanchez.cl
email.SMTPUser = contacto@mansanchez.cl
email.SMTPPass = tu_password_email_aqui
email.SMTPPort = 465
email.SMTPCrypto = ssl

#--------------------------------------------------------------------
# RECAPTCHA
#--------------------------------------------------------------------

recaptcha.enabled = true
recaptcha.siteKey = tu_site_key_de_recaptcha_aqui
recaptcha.secretKey = tu_secret_key_de_recaptcha_aqui

#--------------------------------------------------------------------
# LOGGER
#--------------------------------------------------------------------

logger.threshold = 4

#--------------------------------------------------------------------
# WHATSAPP / TWILIO
#--------------------------------------------------------------------

# Proveedor de WhatsApp (twilio o whatsapp_business)
WHATSAPP_PROVIDER=twilio

# Configuración de Twilio
TWILIO_ACCOUNT_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=tu_auth_token_aqui
TWILIO_WHATSAPP_FROM=whatsapp:+1234567890

# Plantilla de Twilio para confirmaciones (opcional)
# Si no se configura, se usará mensaje de texto simple
TWILIO_CONTENT_SID_CONFIRMACION=HXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

# Deshabilitar verificación SSL para desarrollo local (solo si hay problemas con certificados)
# En producción, dejar en false o no configurar
TWILIO_DISABLE_SSL_VERIFY=true
```

## Cómo usar variables de entorno en tu código

### En archivos PHP (Controladores, Modelos, Config)

```php
// Obtener una variable
$baseURL = env('app.baseURL');
$siteKey = env('recaptcha.siteKey');

// Con valor por defecto
$enabled = env('recaptcha.enabled', true);
```

### En archivos de configuración (app/Config/*.php)

```php
<?php
namespace Config;

class Email extends BaseConfig
{
    public string $fromEmail = '';
    public string $fromName = '';
    
    public function __construct()
    {
        parent::__construct();
        
        // Cargar desde .env
        $this->fromEmail = env('email.fromEmail', 'contacto@mansanchez.cl');
        $this->fromName = env('email.fromName', 'MANSANCHEZ');
        $this->SMTPHost = env('email.SMTPHost', 'mail.mansanchez.cl');
        $this->SMTPUser = env('email.SMTPUser', '');
        $this->SMTPPass = env('email.SMTPPass', '');
        $this->SMTPPort = env('email.SMTPPort', 465);
        $this->SMTPCrypto = env('email.SMTPCrypto', 'ssl');
    }
}
```

### En vistas PHP

```php
<!-- Obtener site key de reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= env('recaptcha.siteKey') ?>"></script>

<script>
    grecaptcha.execute('<?= env('recaptcha.siteKey') ?>', {action: 'submit'})
        .then(function(token) {
            document.getElementById('g-recaptcha-response').value = token;
            form.submit();
        });
</script>
```

## Actualizar archivo Email.php

Reemplaza el contenido de `app/Config/Email.php`:

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';
    public string $userAgent = 'CodeIgniter';
    public string $protocol = 'smtp';
    public string $mailPath = '/usr/sbin/sendmail';
    public string $SMTPHost = '';
    public string $SMTPUser = '';
    public string $SMTPPass = '';
    public int $SMTPPort = 465;
    public int $SMTPTimeout = 5;
    public bool $SMTPKeepAlive = false;
    public string $SMTPCrypto = 'ssl';
    public bool $wordWrap = true;
    public int $wrapChars = 76;
    public string $mailType = 'html';
    public string $charset = 'UTF-8';
    public bool $validate = false;
    public int $priority = 3;
    public string $CRLF = "\r\n";
    public string $newline = "\r\n";
    public bool $BCCBatchMode = false;
    public int $BCCBatchSize = 200;
    public bool $DSN = false;

    public function __construct()
    {
        parent::__construct();
        
        // Cargar configuración desde .env
        $this->fromEmail = env('email.fromEmail', 'contacto@mansanchez.cl');
        $this->fromName = env('email.fromName', 'MANSANCHEZ Constructor');
        $this->SMTPHost = env('email.SMTPHost', 'mail.mansanchez.cl');
        $this->SMTPUser = env('email.SMTPUser', '');
        $this->SMTPPass = env('email.SMTPPass', '');
        $this->SMTPPort = (int) env('email.SMTPPort', 465);
        $this->SMTPCrypto = env('email.SMTPCrypto', 'ssl');
    }
}
```

## Actualizar ContactoController para reCAPTCHA

En `app/Controllers/Web/ContactoController.php`, método `validarRecaptcha()`:

```php
private function validarRecaptcha(): bool
{
    // Verificar si reCAPTCHA está habilitado
    if (!env('recaptcha.enabled', true)) {
        return true;
    }

    $recaptchaResponse = $this->request->getPost('g-recaptcha-response');

    if (empty($recaptchaResponse)) {
        log_message('warning', 'reCAPTCHA: Token no recibido');
        return false;
    }

    $secretKey = env('recaptcha.secretKey');
    
    if (empty($secretKey)) {
        log_message('error', 'reCAPTCHA: Secret key no configurada en .env');
        return true; // Permitir si no está configurado (para desarrollo)
    }

    // ... resto del código
}
```

## Actualizar vista contacto.php para reCAPTCHA

En `app/Views/Web/contacto.php`:

```html
<!-- Google reCAPTCHA v3 -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= env('recaptcha.siteKey') ?>"></script>
<script>
    grecaptcha.ready(function() {
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submit-btn');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';
            
            // Obtener token de reCAPTCHA usando la clave desde .env
            grecaptcha.execute('<?= env('recaptcha.siteKey') ?>', {action: 'submit'})
                .then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviando...';
                    form.submit();
                })
                .catch(function(error) {
                    console.error('Error reCAPTCHA:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    alert('Error en la verificación de seguridad. Por favor, recarga la página e intenta nuevamente.');
                });
        });
    });
</script>
```

## Verificar que funciona

1. Crear el archivo `.env` con tus credenciales
2. Verificar permisos: el archivo debe ser legible por el servidor web
3. Limpiar caché de CodeIgniter: `php spark cache:clear`
4. Probar el formulario de contacto y envío de emails

## Seguridad

- El archivo `.env` NO debe estar en Git
- Usa credenciales diferentes para desarrollo y producción
- Nunca expongas las variables de entorno en el frontend (excepto `siteKey` de reCAPTCHA que es público)
- Cambia las contraseñas regularmente

