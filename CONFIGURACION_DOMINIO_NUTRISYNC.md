# 🌐 Configuración para nutrisync.nextline.cl

## ✅ Configuración del Dominio

Tu dominio será: **`nutrisync.nextline.cl`**

## 📋 Configuraciones Necesarias

### 1. Configurar `.env` en el Servidor

Una vez que subas el proyecto, configura el archivo `.env`:

```env
# Entorno
CI_ENVIRONMENT = production

# Base URL
app.baseURL = 'https://nutrisync.nextline.cl/'

# Base de datos (configura con tus credenciales del servidor)
database.default.hostname = localhost
database.default.database = tu_base_de_datos
database.default.username = tu_usuario
database.default.password = tu_contraseña
database.default.DBDriver = MySQLi

# Mercado Pago (usa credenciales de PRUEBA inicialmente)
MERCADOPAGO_ACCESS_TOKEN = TEST-tu-access-token-de-prueba
MERCADOPAGO_PUBLIC_KEY = TEST-tu-public-key-de-prueba
MERCADOPAGO_MODE = sandbox
MERCADOPAGO_WEBHOOK_BASE_URL = https://nutrisync.nextline.cl
```

### 2. Configurar en el Panel de Mercado Pago

Una vez que el dominio esté funcionando:

#### URLs de Retorno:
- **Success**: `https://nutrisync.nextline.cl/dashboard/pago/success`
- **Failure**: `https://nutrisync.nextline.cl/dashboard/pago/failure`
- **Pending**: `https://nutrisync.nextline.cl/dashboard/pago/pending`

#### Webhook:
- **URL**: `https://nutrisync.nextline.cl/api/mercadopago/webhook`
- **Evento**: Pagos

### 3. Verificar SSL/HTTPS

- [ ] Asegúrate de que `nutrisync.nextline.cl` tenga SSL/HTTPS configurado
- [ ] Si no lo tienes, puedes usar Let's Encrypt (gratis) desde cPanel

### 4. Estructura de Directorios

Si el document root es `/nutrisync.nextline.cl` o `/home/nextline/nutrisync.nextline.cl`:

```
/nutrisync.nextline.cl/
├── app/
├── public/          (o root del proyecto)
├── writable/
├── .env
├── composer.json
└── ...
```

**IMPORTANTE**: En CodeIgniter 4, el punto de entrada debe ser el directorio `public/`. Si tu hosting no permite cambiar el document root, necesitarás ajustar la configuración.

### 5. Configurar .htaccess

Si tu hosting usa Apache, asegúrate de tener un `.htaccess` en el root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

## 🔧 Pasos Después de Crear el Dominio

1. **Subir archivos** al directorio del dominio
2. **Configurar `.env`** con el dominio y credenciales
3. **Ejecutar migraciones SQL** en la base de datos del servidor
4. **Instalar dependencias**: `composer install --no-dev`
5. **Configurar permisos**:
   ```bash
   chmod -R 755 writable/
   chmod -R 755 public/
   ```
6. **Probar acceso**: `https://nutrisync.nextline.cl`
7. **Configurar Mercado Pago** con las URLs del dominio

## ✅ Verificación

Después de subir, verifica:

1. **Acceso al sitio**: `https://nutrisync.nextline.cl`
2. **Login funciona**: `https://nutrisync.nextline.cl/dashboard`
3. **Rutas funcionan**: Verifica que las rutas principales carguen
4. **Base de datos conecta**: Verifica que puedas acceder a los datos

## 🎯 URLs Específicas para Mercado Pago

Una vez configurado, estas serán tus URLs:

- **Panel de configuración**: `https://nutrisync.nextline.cl/dashboard/configuracion`
- **Botones de pago**: `https://nutrisync.nextline.cl/dashboard/boton-pago`
- **Agenda**: `https://nutrisync.nextline.cl/dashboard/agenda`
- **Webhook**: `https://nutrisync.nextline.cl/api/mercadopago/webhook`
- **Success**: `https://nutrisync.nextline.cl/dashboard/pago/success`
- **Failure**: `https://nutrisync.nextline.cl/dashboard/pago/failure`
- **Pending**: `https://nutrisync.nextline.cl/dashboard/pago/pending`

## ⚠️ Importante

1. **No compartas el document root** con `nextline.cl` si quieres contenido independiente
2. **Usa HTTPS** - Mercado Pago requiere URLs HTTPS
3. **Credenciales de prueba primero** - No uses credenciales de producción hasta que todo esté probado
4. **Backup antes de subir** - Haz backup de tu base de datos local

## 📝 Checklist Rápido

- [ ] Dominio creado: `nutrisync.nextline.cl`
- [ ] SSL/HTTPS configurado
- [ ] Archivos subidos al servidor
- [ ] `.env` configurado con el dominio
- [ ] Base de datos configurada
- [ ] Migraciones SQL ejecutadas
- [ ] Composer install ejecutado
- [ ] Permisos de `writable/` configurados
- [ ] Sitio accesible en `https://nutrisync.nextline.cl`
- [ ] URLs configuradas en panel de Mercado Pago
