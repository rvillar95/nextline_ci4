# 🔧 Solución: Error 500 en nutrisync.nextline.cl

## 🔍 Diagnóstico Rápido

Un error HTTP 500 generalmente indica un problema de configuración del servidor. Sigue estos pasos para identificar y solucionar el problema.

## ✅ Checklist de Verificación

### 1. Verificar Logs de Error

**Desde cPanel:**
1. Ve a **File Manager** → `/home/nextline/nutrisync.nextline.cl`
2. Navega a `writable/logs/`
3. Abre el archivo de log más reciente (ej: `log-2026-01-22.log`)
4. Revisa los últimos errores

**O desde Terminal:**
```bash
cd /home/nextline/nutrisync.nextline.cl
tail -50 writable/logs/log-*.log
```

### 2. Verificar Archivo .env

El archivo `.env` debe existir y estar configurado correctamente:

```bash
cd /home/nextline/nutrisync.nextline.cl
ls -la .env
```

**Configuración mínima requerida en `.env`:**
```env
CI_ENVIRONMENT = production
app.baseURL = 'https://nutrisync.nextline.cl/'

database.default.hostname = localhost
database.default.database = tu_base_datos
database.default.username = tu_usuario
database.default.password = tu_password
```

### 3. Verificar Permisos de Archivos

```bash
cd /home/nextline/nutrisync.nextline.cl

# Permisos para directorios
find . -type d -exec chmod 755 {} \;

# Permisos para archivos
find . -type f -exec chmod 644 {} \;

# Permisos especiales para writable
chmod -R 777 writable/
chmod -R 777 public/uploads/  # Si existe
```

### 4. Verificar Estructura de Directorios

Asegúrate de que existan estos directorios:
```bash
cd /home/nextline/nutrisync.nextline.cl
ls -la

# Debe existir:
# - app/
# - public/
# - writable/
# - vendor/ (si usas Composer)
# - .env
# - index.php
```

### 5. Verificar Composer Dependencies

Si usas Composer, instala las dependencias:
```bash
cd /home/nextline/nutrisync.nextline.cl
composer install --no-dev --optimize-autoloader
```

### 6. Verificar .htaccess

El archivo `.htaccess` debe estar en `public/.htaccess`:

```bash
ls -la public/.htaccess
```

Si no existe, créalo con este contenido:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>
```

### 7. Verificar Configuración del Servidor Web

**Para Apache:**
- El `DocumentRoot` debe apuntar a `/home/nextline/nutrisync.nextline.cl/public`
- `mod_rewrite` debe estar habilitado

**Verificar en cPanel:**
1. Ve a **Subdomains** o **Addon Domains**
2. Verifica que `nutrisync.nextline.cl` apunte a `/home/nextline/nutrisync.nextline.cl/public`

## 🐛 Errores Comunes y Soluciones

### Error: "No such file or directory" o "Class not found"

**Causa:** Dependencias de Composer no instaladas

**Solución:**
```bash
cd /home/nextline/nutrisync.nextline.cl
composer install --no-dev --optimize-autoloader
```

### Error: "Permission denied"

**Causa:** Permisos incorrectos

**Solución:**
```bash
cd /home/nextline/nutrisync.nextline.cl
chmod -R 755 .
chmod -R 777 writable/
```

### Error: "Database connection failed"

**Causa:** Credenciales incorrectas en `.env`

**Solución:**
1. Verifica las credenciales de la base de datos en `.env`
2. Verifica que la base de datos exista
3. Verifica que el usuario tenga permisos

### Error: "Unable to locate the specified file"

**Causa:** Rutas incorrectas o archivos faltantes

**Solución:**
1. Verifica que `app/Config/Paths.php` tenga las rutas correctas
2. Verifica que todos los archivos estén subidos

### Error: "Call to undefined function"

**Causa:** Extensiones de PHP faltantes

**Solución:**
Verifica que estas extensiones estén habilitadas:
- `php-mbstring`
- `php-curl`
- `php-json`
- `php-xml`
- `php-zip`

## 🔧 Pasos de Solución Rápida

### Paso 1: Habilitar Modo Debug Temporalmente

Edita `.env`:
```env
CI_ENVIRONMENT = development
```

Esto mostrará errores más detallados (solo para debugging, luego vuelve a `production`).

### Paso 2: Verificar Logs

```bash
tail -100 /home/nextline/nutrisync.nextline.cl/writable/logs/log-*.log
```

### Paso 3: Verificar PHP Version

CodeIgniter 4 requiere PHP 7.4 o superior:
```bash
php -v
```

### Paso 4: Verificar Error Logs del Servidor

En cPanel:
1. Ve a **Metrics** → **Errors**
2. Revisa los errores recientes

## 📝 Configuración Mínima Requerida

### Archivo `.env` mínimo:

```env
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'https://nutrisync.nextline.cl/'
app.forceGlobalSecureRequests = true

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = nombre_base_datos
database.default.username = usuario_db
database.default.password = password_db
database.default.DBDriver = MySQLi
database.default.port = 3306
```

## 🎯 Verificación Rápida

Ejecuta estos comandos en orden:

```bash
# 1. Ir al directorio
cd /home/nextline/nutrisync.nextline.cl

# 2. Verificar estructura
ls -la

# 3. Verificar permisos
ls -ld writable/

# 4. Verificar .env
cat .env | grep -E "CI_ENVIRONMENT|baseURL|database"

# 5. Verificar logs
tail -20 writable/logs/log-*.log

# 6. Verificar Composer
composer --version
composer install --no-dev --optimize-autoloader
```

## ⚠️ Importante

1. **NO subas el `.env` local a producción** - Crea uno nuevo con credenciales de producción
2. **Verifica permisos** - `writable/` debe ser escribible (777)
3. **Verifica rutas** - El `DocumentRoot` debe apuntar a `public/`
4. **Revisa logs** - Los logs te dirán exactamente qué está fallando

## 📞 Siguiente Paso

Después de verificar los logs, comparte el error específico que aparece y te ayudo a solucionarlo.
