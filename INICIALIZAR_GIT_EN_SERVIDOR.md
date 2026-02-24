# 🔧 Inicializar Git en Directorio Existente (cPanel)

## ⚠️ Problema

El error "You cannot use the `/home/nextline/nutrisync.nextline.cl` directory because it already contains files" ocurre porque cPanel requiere un directorio vacío para clonar.

## ✅ Solución: Inicializar Git en el Directorio Existente

Como tu proyecto ya está en el servidor, la mejor opción es inicializar Git en ese directorio y conectarlo con tu repositorio de GitHub.

### Opción 1: Usando Terminal de cPanel (Recomendada)

1. **Ve a cPanel → Terminal** (o usa SSH si tienes acceso)

2. **Navega al directorio:**
   ```bash
   cd /home/nextline/nutrisync.nextline.cl
   ```

3. **Inicializa Git:**
   ```bash
   git init
   ```

4. **Agrega el remote de GitHub:**
   ```bash
   git remote add origin https://github.com/rvillar95/nextline_ci4.git
   ```

5. **Obtén las ramas remotas:**
   ```bash
   git fetch origin
   ```

6. **Cambia a la rama feature/endgame:**
   ```bash
   git checkout -b feature/endgame origin/feature/endgame
   ```

7. **Configura el tracking:**
   ```bash
   git branch --set-upstream-to=origin/feature/endgame feature/endgame
   ```

8. **Verifica el estado:**
   ```bash
   git status
   ```

### Opción 2: Usando Git en cPanel (Si está disponible)

1. **Ve a "Git Version Control" en cPanel**

2. **Haz clic en "Create Repository"**
   - **NO actives "Clone a Repository"** (déjalo desactivado)
   - **Repository Path**: `/home/nextline/nutrisync.nextline.cl`
   - **Repository Name**: `nextline_ci4`
   - Haz clic en "Crear"

3. **Haz clic en "Manage" junto a tu repositorio**

4. **Ejecuta estos comandos en el terminal integrado:**
   ```bash
   git remote add origin https://github.com/rvillar95/nextline_ci4.git
   git fetch origin
   git checkout -b feature/endgame origin/feature/endgame
   git branch --set-upstream-to=origin/feature/endgame feature/endgame
   ```

### Opción 3: Hacer Pull sin Sobrescribir (Más Segura)

Si quieres mantener los archivos actuales y solo sincronizar cambios:

1. **Inicializa Git:**
   ```bash
   cd /home/nextline/nutrisync.nextline.cl
   git init
   git remote add origin https://github.com/rvillar95/nextline_ci4.git
   ```

2. **Haz fetch:**
   ```bash
   git fetch origin
   ```

3. **Agrega todos los archivos actuales:**
   ```bash
   git add .
   git commit -m "Estado actual del servidor"
   ```

4. **Fusiona con la rama remota:**
   ```bash
   git checkout -b feature/endgame
   git merge origin/feature/endgame --allow-unrelated-histories
   ```

   Si hay conflictos, resuélvelos manualmente.

---

## 🔄 Después de Inicializar

Una vez que Git esté configurado, puedes:

### Hacer Pull Manual:
```bash
cd /home/nextline/nutrisync.nextline.cl
git pull origin feature/endgame
```

### O Configurar Webhook Automático:

1. Usa el `deploy.php` que ya creamos
2. Configura el webhook en GitHub
3. Cada `git push` hará `git pull` automáticamente

---

## ⚠️ Advertencias

1. **Backup antes de hacer merge:**
   - Si hay archivos importantes en el servidor que no están en Git, haz backup primero
   - Puedes usar File Manager de cPanel para descargar una copia

2. **Archivos locales vs remotos:**
   - Los archivos en el servidor pueden diferir de los del repositorio
   - Decide si quieres mantener los del servidor o sobrescribirlos con los de GitHub

3. **Archivos sensibles:**
   - Asegúrate de que `.env` esté en `.gitignore`
   - No subas credenciales de producción a GitHub

---

## 📝 Verificación

Después de configurar, verifica:

```bash
cd /home/nextline/nutrisync.nextline.cl
git status
git remote -v
git branch -a
```

Deberías ver:
- `origin` apuntando a tu repositorio de GitHub
- La rama `feature/endgame` activa
- El estado de los archivos

---

## 🎯 Siguiente Paso

Una vez configurado Git, puedes:
1. Usar el webhook automático (`deploy.php`)
2. O hacer pull manual desde cPanel → Git Version Control → Pull
