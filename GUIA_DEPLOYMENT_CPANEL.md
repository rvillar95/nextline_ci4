# 🚀 Guía de Deployment Directo: Git → cPanel Hosting

## 📋 Opciones de Deployment

### Opción 1: Git Version Control en cPanel (Recomendada) ⭐

Si tu cPanel tiene la herramienta "Git Version Control", esta es la forma más sencilla.

#### Pasos:

1. **En cPanel:**
   - Busca "Git Version Control" en el panel
   - Si no lo encuentras, busca en "Files" → "Git Version Control"

2. **Inicializar Git en el Directorio Existente:**
   
   ⚠️ **IMPORTANTE**: Como el directorio `/home/nextline/nutrisync.nextline.cl` ya contiene archivos, NO puedes clonar directamente. Debes inicializar Git en el directorio existente.
   
   **Opción A: Desde cPanel (si está disponible):**
   - Ve a "List Repositories"
   - Haz clic en "Create"
   - **NO actives "Clone a Repository"** (déjalo desactivado)
   - **Repository Path**: `/home/nextline/nutrisync.nextline.cl`
   - **Repository Name**: `nextline_ci4`
   - Haz clic en "Crear"
   - Luego haz clic en "Manage" y ejecuta:
     ```bash
     git remote add origin https://github.com/rvillar95/nextline_ci4.git
     git fetch origin
     git checkout -b feature/endgame origin/feature/endgame
     ```
   
   **Opción B: Desde SSH o Terminal de cPanel:**
   - Ve a cPanel → "Terminal" o usa SSH
   - Ejecuta:
     ```bash
     cd /home/nextline/nutrisync.nextline.cl
     git init
     git remote add origin https://github.com/rvillar95/nextline_ci4.git
     git fetch origin
     git checkout -b feature/endgame origin/feature/endgame
     git branch --set-upstream-to=origin/feature/endgame feature/endgame
     ```
   
   **⚠️ ADVERTENCIA**: Esto sobrescribirá los archivos locales con los del repositorio. Asegúrate de hacer backup primero si hay cambios importantes.

4. **Configurar Auto-Pull (Opcional):**
   - En la lista de repositorios, haz clic en "Manage"
   - Activa "Auto Pull" si está disponible
   - Esto hará pull automáticamente cuando hagas push a GitHub

5. **Hacer Pull Manual:**
   - Cada vez que hagas `git push`, entra a cPanel
   - Ve a "Git Version Control"
   - Haz clic en "Pull" junto a tu repositorio

---

### Opción 2: Webhook de GitHub + Script PHP (Automático) 🔄

Esta opción permite que cada vez que hagas `git push`, el servidor automáticamente haga `git pull`.

#### Paso 1: Crear Script de Deployment en el Servidor

Crea un archivo `deploy.php` en tu servidor. **Ya está creado en tu proyecto**, solo necesitas:

1. **Editar `deploy.php`** y cambiar:
   - `SECRET_KEY`: Genera una clave secreta
   - `REPO_PATH`: Ya está configurado como `/home/nextline/nutrisync.nextline.cl`
   - `BRANCH`: Ya está configurado como `feature/endgame`

2. **Subir `deploy.php` al servidor:**
   - **Opción 1 (Recomendada)**: `/home/nextline/deploy.php` (fuera del directorio web)
   - **Opción 2**: Dentro del proyecto: `/home/nextline/nutrisync.nextline.cl/deploy.php`
   
   **⚠️ IMPORTANTE**: Si lo pones dentro del directorio web, agrega protección en `.htaccess`

El archivo ya está listo en tu proyecto. Solo necesitas editarlo y subirlo:

```php
<?php
// deploy.php - Colocar en /home/nextline/deploy.php (fuera de nutrisync.nextline.cl)

// Configuración
$secret = 'TU_SECRET_KEY_AQUI'; // Cambia esto por una clave secreta
$repo_path = '/home/nextline/public_html'; // Ruta donde está tu proyecto
$branch = 'feature/endgame'; // Rama que quieres desplegar
$log_file = '/home/nextline/deploy.log';

// Verificar que es una petición POST de GitHub
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

// Verificar el secret (opcional pero recomendado)
$headers = getallheaders();
$hub_signature = $headers['X-Hub-Signature-256'] ?? '';

if (!empty($secret)) {
    $payload = file_get_contents('php://input');
    $calculated_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
    if (!hash_equals($calculated_signature, $hub_signature)) {
        http_response_code(403);
        die('Invalid signature');
    }
}

// Log
$log_entry = date('Y-m-d H:i:s') . " - Deployment iniciado\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Cambiar al directorio del repositorio
chdir($repo_path);

// Ejecutar git pull
$output = [];
$return_var = 0;
exec("git pull origin {$branch} 2>&1", $output, $return_var);

// Log del resultado
$log_entry = date('Y-m-d H:i:s') . " - Git pull ejecutado. Return code: {$return_var}\n";
$log_entry .= "Output: " . implode("\n", $output) . "\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);

// Ejecutar composer install si es necesario
if (file_exists($repo_path . '/composer.json')) {
    exec("cd {$repo_path} && composer install --no-dev --no-interaction 2>&1", $composer_output, $composer_return);
    $log_entry = date('Y-m-d H:i:s') . " - Composer ejecutado. Return code: {$composer_return}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Respuesta
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Deployment completed',
    'output' => $output
]);
```

#### Paso 2: Subir el Script al Servidor

1. Sube `deploy.php` a `/home/nextline/deploy.php` (fuera de `public_html`)
2. Configura permisos:
   ```bash
   chmod 755 /home/nextline/deploy.php
   ```

#### Paso 3: Configurar Webhook en GitHub

1. Ve a tu repositorio en GitHub: `https://github.com/rvillar95/nextline_ci4`
2. Ve a **Settings** → **Webhooks** → **Add webhook**
3. Configura:
   - **Payload URL**: `https://nextline.cl/deploy.php` (o tu dominio)
   - **Content type**: `application/json`
   - **Secret**: La misma clave secreta que pusiste en `deploy.php`
   - **Which events**: Selecciona "Just the push event"
   - **Active**: ✅ Activado
4. Haz clic en **Add webhook**

#### Paso 4: Probar el Webhook

1. Haz un cambio pequeño en tu código
2. Haz commit y push:
   ```bash
   git commit -m "Test deployment"
   git push
   ```
3. Verifica el log:
   ```bash
   tail -f /home/nextline/deploy.log
   ```

---

### Opción 3: SSH + Script de Deployment (Más Control) 🔐

Si tienes acceso SSH a tu servidor, puedes crear un script más robusto.

#### Crear Script de Deployment

Crea `deploy.sh` en tu servidor:

```bash
#!/bin/bash
# deploy.sh - Colocar en /home/nextline/deploy.sh

REPO_PATH="/home/nextline/nutrisync.nextline.cl"
BRANCH="feature/endgame"
LOG_FILE="/home/nextline/deploy.log"

echo "$(date '+%Y-%m-%d %H:%M:%S') - Iniciando deployment" >> $LOG_FILE

cd $REPO_PATH

# Hacer pull
git fetch origin
git checkout $BRANCH
git pull origin $BRANCH >> $LOG_FILE 2>&1

# Instalar dependencias
if [ -f "composer.json" ]; then
    composer install --no-dev --no-interaction >> $LOG_FILE 2>&1
fi

# Limpiar caché de CodeIgniter
if [ -d "writable/cache" ]; then
    rm -rf writable/cache/* >> $LOG_FILE 2>&1
fi

echo "$(date '+%Y-%m-%d %H:%M:%S') - Deployment completado" >> $LOG_FILE
```

#### Configurar Permisos

```bash
chmod +x /home/nextline/deploy.sh
```

#### Ejecutar Manualmente

```bash
/home/nextline/deploy.sh
```

#### O Configurar Webhook que Llame al Script

Modifica `deploy.php` para que ejecute el script:

```php
exec("/home/nextline/deploy.sh 2>&1", $output, $return_var);
```

---

### Opción 4: GitHub Actions (CI/CD Automático) 🤖

Crea un workflow de GitHub Actions que despliegue automáticamente.

#### Crear Workflow

Crea el archivo `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches:
      - feature/endgame  # Cambia por tu rama de producción

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /home/nextline/nutrisync.nextline.cl
          git pull origin feature/endgame
          composer install --no-dev --no-interaction
          rm -rf writable/cache/*
```

#### Configurar Secrets en GitHub

1. Ve a tu repositorio → **Settings** → **Secrets and variables** → **Actions**
2. Agrega:
   - `HOST`: IP o dominio de tu servidor
   - `USERNAME`: Usuario SSH (ej: `nextline`)
   - `SSH_KEY`: Tu clave privada SSH

---

## ⚠️ Consideraciones Importantes

### 1. Archivos que NO deben subirse

Asegúrate de que `.gitignore` incluya:
```
.env
writable/logs/*
writable/cache/*
writable/session/*
writable/uploads/*
```

### 2. Configuración del .env en Producción

- **NO** subas tu `.env` local
- Crea un `.env` en el servidor con las credenciales de producción
- Configura `CI_ENVIRONMENT = production`

### 3. Permisos de Archivos

Después del deployment, verifica permisos:
```bash
chmod -R 755 /home/nextline/nutrisync.nextline.cl
chmod -R 777 /home/nextline/nutrisync.nextline.cl/writable
```

### 4. Base de Datos

- Las migraciones SQL deben ejecutarse manualmente en producción
- Haz backup antes de ejecutar migraciones
- No ejecutes migraciones automáticamente en producción

### 5. Composer

Ejecuta en producción:
```bash
cd /home/nextline/nutrisync.nextline.cl
composer install --no-dev --optimize-autoloader
```

---

## 🎯 Recomendación

Para tu caso, recomiendo **Opción 1 (Git en cPanel)** si está disponible, o **Opción 2 (Webhook)** si quieres automatización completa.

La Opción 2 es la más práctica porque:
- ✅ Automática (no necesitas entrar a cPanel)
- ✅ Segura (usa secret key)
- ✅ Rápida (se ejecuta inmediatamente después del push)
- ✅ Con logs para debugging

---

## 📝 Checklist de Deployment

Antes de cada deployment:

- [ ] Hacer commit de todos los cambios
- [ ] Hacer push a GitHub
- [ ] Verificar que el webhook/script se ejecutó correctamente
- [ ] Revisar logs si hay errores
- [ ] Probar funcionalidades críticas en producción
- [ ] Verificar que `.env` esté configurado correctamente
- [ ] Verificar permisos de archivos

---

## 🔧 Troubleshooting

### Error: "Permission denied"
```bash
chmod +x /home/nextline/deploy.sh
chown nextline:nextline /home/nextline/deploy.sh
```

### Error: "Git pull failed"
Verifica que el repositorio esté clonado correctamente:
```bash
cd /home/nextline/public_html
git status
```

### Error: "Composer not found"
Instala Composer en el servidor o usa la ruta completa:
```bash
/usr/local/bin/composer install
```

### Webhook no se ejecuta
- Verifica que la URL sea accesible públicamente
- Revisa los logs de GitHub en Settings → Webhooks
- Verifica que el secret coincida
