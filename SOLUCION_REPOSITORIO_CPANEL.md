# 🔧 Solución: Repositorio Git en cPanel - Ruta Incorrecta

## ⚠️ Problema Actual

Tu repositorio Git está en:
- **Ruta actual**: `/home/nextline/repositories/nextline_ci4` ❌
- **Ruta necesaria**: `/home/nextline/nutrisync.nextline.cl` ✅

El botón "Update from Remote" actualizará archivos en la ruta incorrecta, no en tu sitio web.

## ✅ Solución: Dos Opciones

### Opción 1: Mover/Recrear Repositorio en la Ruta Correcta (Recomendada)

1. **Elimina el repositorio actual:**
   - En cPanel → Git Version Control → List Repositories
   - Haz clic en "Delete" junto a `nextline_ci4`
   - Esto NO elimina archivos, solo la configuración de Git en cPanel

2. **Inicializa Git en la ruta correcta:**
   - Ve a cPanel → Terminal (o SSH)
   - Ejecuta:
     ```bash
     cd /home/nextline/nutrisync.nextline.cl
     git init
     git remote add origin https://github.com/rvillar95/nextline_ci4.git
     git fetch origin
     git checkout -b feature/endgame origin/feature/endgame
     git branch --set-upstream-to=origin/feature/endgame feature/endgame
     ```

3. **Crea el repositorio en cPanel:**
   - Ve a Git Version Control → Create Repository
   - **NO actives "Clone a Repository"**
   - **Repository Path**: `/home/nextline/nutrisync.nextline.cl`
   - **Repository Name**: `nutrisync`
   - Haz clic en "Crear"

4. **Ahora podrás usar "Update from Remote"** y actualizará tu sitio web directamente.

---

### Opción 2: Usar el Botón "Update from Remote" (Temporal)

Si quieres probar primero sin mover el repositorio:

1. **Haz clic en "Update from Remote"** (el botón azul con la nube)
   - Esto hará `git pull` en `/home/nextline/repositories/nextline_ci4`
   - **PERO** esto NO actualizará tu sitio web en `nutrisync.nextline.cl`

2. **Luego copia manualmente los archivos:**
   - Desde File Manager, copia los archivos actualizados
   - O usa un script que copie desde `repositories/nextline_ci4` a `nutrisync.nextline.cl`

**⚠️ Esta opción NO es recomendada** porque es manual y propensa a errores.

---

### Opción 3: Usar Webhook Automático (Mejor para Producción)

En lugar de usar Git en cPanel, configura el webhook automático:

1. **No necesitas mover el repositorio de cPanel**
2. **Usa el `deploy.php` que ya creamos**
3. **Configura el webhook en GitHub**
4. Cada `git push` actualizará automáticamente `/home/nextline/nutrisync.nextline.cl`

**Ventajas:**
- ✅ No necesitas entrar a cPanel
- ✅ Automático
- ✅ Actualiza directamente el sitio web
- ✅ Con logs para debugging

---

## 🎯 Recomendación

**Para desarrollo/pruebas:** Usa **Opción 1** (mover repositorio a la ruta correcta) para poder usar el botón "Update from Remote" de cPanel.

**Para producción:** Usa **Opción 3** (webhook automático) para deployment sin intervención manual.

---

## 📝 Pasos Rápidos para Opción 1

1. **Desde Terminal de cPanel:**
   ```bash
   cd /home/nextline/nutrisync.nextline.cl
   git init
   git remote add origin https://github.com/rvillar95/nextline_ci4.git
   git fetch origin
   git checkout -b feature/endgame origin/feature/endgame
   ```

2. **En cPanel → Git Version Control:**
   - Elimina el repositorio `nextline_ci4` (si quieres)
   - O crea uno nuevo apuntando a `/home/nextline/nutrisync.nextline.cl`

3. **Listo!** Ahora "Update from Remote" actualizará tu sitio web.
