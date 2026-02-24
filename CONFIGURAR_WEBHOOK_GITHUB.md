# 🔗 Configurar Webhook de GitHub para Deployment Automático

## 📋 Pasos para Configurar el Webhook

### Paso 1: Preparar el Script de Deployment

1. **Edita `deploy.php`:**
   - Abre `deploy.php`
   - Cambia `SECRET_KEY` por una clave secreta segura
   - Verifica que `REPO_PATH` apunte a tu directorio correcto
   - Verifica que `BRANCH` sea la rama que quieres desplegar

2. **Genera una clave secreta:**
   ```bash
   # En Linux/Mac:
   openssl rand -hex 32
   
   # O usa un generador online:
   # https://www.random.org/strings/
   ```

3. **Sube `deploy.php` al servidor:**
   - **Ruta recomendada**: `/home/nextline/deploy.php` (fuera de `public_html`)
   - **Permisos**: `chmod 755 /home/nextline/deploy.php`

### Paso 2: Configurar el Webhook en GitHub

1. **Ve a tu repositorio:**
   - https://github.com/rvillar95/nextline_ci4

2. **Ve a Settings:**
   - Haz clic en **Settings** (en la barra superior del repositorio)

3. **Ve a Webhooks:**
   - En el menú izquierdo, haz clic en **Webhooks**
   - Haz clic en **Add webhook**

4. **Configura el Webhook:**
   - **Payload URL**: 
     ```
     https://nutrisync.nextline.cl/deploy.php
     ```
     O si lo pusiste fuera de public_html:
     ```
     https://nextline.cl/deploy.php
     ```
     (Ajusta según donde subas el archivo deploy.php)
   
   - **Content type**: `application/json`
   
   - **Secret**: 
     ```
     [La misma clave que pusiste en deploy.php]
     ```
   
   - **Which events would you like to trigger this webhook?**
     - Selecciona: **Just the push event**
   
   - **Active**: ✅ Debe estar marcado

5. **Haz clic en "Add webhook"**

### Paso 3: Probar el Webhook

1. **Haz un cambio pequeño:**
   ```bash
   # En tu máquina local
   echo "// Test deployment" >> app/Controllers/Dashboard/TestController.php
   git add .
   git commit -m "Test deployment webhook"
   git push
   ```

2. **Verifica en GitHub:**
   - Ve a **Settings** → **Webhooks**
   - Haz clic en tu webhook
   - Deberías ver "Recent Deliveries" con una entrega reciente
   - Haz clic en la entrega para ver los detalles

3. **Verifica el log en el servidor:**
   ```bash
   # Si tienes acceso SSH:
   tail -f /home/nextline/deploy.log
   
   # O revisa el log desde cPanel → File Manager
   ```

### Paso 4: Verificar que Funcionó

1. **Revisa el log:**
   - Deberías ver mensajes como:
     ```
     [2026-01-22 12:00:00] Deployment iniciado
     [2026-01-22 12:00:01] Git pull exitoso
     [2026-01-22 12:00:02] Composer install exitoso
     [2026-01-22 12:00:03] Deployment completado exitosamente
     ```

2. **Verifica los archivos:**
   - Los cambios que hiciste deberían estar en el servidor

---

## 🔒 Seguridad

### ⚠️ IMPORTANTE: Proteger el Script

1. **NO pongas `deploy.php` en `public_html`:**
   - Si lo pones en `public_html`, cualquiera puede acceder
   - Mejor ponerlo en `/home/nextline/deploy.php`

2. **Si DEBES ponerlo en `public_html`:**
   - Agrega protección IP en `.htaccess`:
   ```apache
   <Files "deploy.php">
       Order Deny,Allow
       Deny from all
       Allow from 192.30.252.0/22  # GitHub IPs
       Allow from 185.199.108.0/22
       Allow from 140.82.112.0/20
   </Files>
   ```

3. **Usa siempre el SECRET_KEY:**
   - Nunca dejes el secret vacío en producción
   - GitHub enviará el signature en el header `X-Hub-Signature-256`

---

## 🐛 Troubleshooting

### El webhook no se ejecuta

1. **Verifica la URL:**
   - Debe ser accesible públicamente
   - Prueba abrirla en el navegador (debería dar error 405, no 404)

2. **Revisa los logs de GitHub:**
   - Ve a Settings → Webhooks → Tu webhook
   - Revisa "Recent Deliveries"
   - Si hay errores, verás el código de respuesta

3. **Revisa el log del servidor:**
   ```bash
   tail -100 /home/nextline/deploy.log
   ```

### Error 403 (Invalid signature)

- Verifica que el `SECRET_KEY` en `deploy.php` sea igual al `Secret` en GitHub
- Asegúrate de que GitHub esté enviando el signature correctamente

### Error 500 (Git pull failed)

1. **Verifica permisos:**
   ```bash
   ls -la /home/nextline/nutrisync.nextline.cl/.git
   ```

2. **Verifica que el repositorio esté clonado:**
   ```bash
   cd /home/nextline/nutrisync.nextline.cl
   git status
   ```

3. **Verifica la rama:**
   ```bash
   git branch
   ```

### Composer no se ejecuta

1. **Verifica que Composer esté instalado:**
   ```bash
   which composer
   composer --version
   ```

2. **Si no está instalado, instálalo o usa la ruta completa:**
   ```php
   // En deploy.php, cambia:
   exec("composer install ...");
   // Por:
   exec("/usr/local/bin/composer install ...");
   ```

---

## 📝 Notas Adicionales

- El webhook se ejecuta **inmediatamente** después de cada `git push`
- Si haces múltiples commits y luego push, solo se ejecutará una vez
- El script es idempotente: puedes ejecutarlo múltiples veces sin problemas
- Los logs se guardan en `/home/nextline/deploy.log` para debugging

---

## ✅ Checklist Final

- [ ] `deploy.php` subido al servidor
- [ ] `SECRET_KEY` configurado en `deploy.php`
- [ ] Permisos configurados (`chmod 755`)
- [ ] Webhook creado en GitHub
- [ ] `Secret` configurado en GitHub (igual que en `deploy.php`)
- [ ] Webhook probado con un push de prueba
- [ ] Log verificado y sin errores
- [ ] Cambios verificados en el servidor
