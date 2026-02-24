#!/bin/bash
# Script de Deployment para cPanel
# 
# INSTRUCCIONES:
# 1. Sube este archivo a: /home/nextline/deploy.sh
# 2. Configura permisos: chmod +x /home/nextline/deploy.sh
# 3. Puedes ejecutarlo manualmente o desde deploy.php

# ============================================
# CONFIGURACIÓN
# ============================================

REPO_PATH="/home/nextline/nutrisync.nextline.cl"
BRANCH="feature/endgame"
LOG_FILE="/home/nextline/deploy.log"

# ============================================
# FUNCIONES
# ============================================

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# ============================================
# PROCESO DE DEPLOYMENT
# ============================================

log "========================================"
log "Iniciando deployment"

# Verificar que el directorio existe
if [ ! -d "$REPO_PATH" ]; then
    log "ERROR: Directorio no existe: $REPO_PATH"
    exit 1
fi

# Cambiar al directorio del repositorio
cd "$REPO_PATH" || exit 1

# Verificar que es un repositorio Git
if [ ! -d ".git" ]; then
    log "ERROR: No es un repositorio Git"
    exit 1
fi

# 1. Hacer git fetch y pull
log "Ejecutando: git fetch origin"
git fetch origin >> "$LOG_FILE" 2>&1

log "Ejecutando: git checkout $BRANCH"
git checkout "$BRANCH" >> "$LOG_FILE" 2>&1

log "Ejecutando: git pull origin $BRANCH"
if git pull origin "$BRANCH" >> "$LOG_FILE" 2>&1; then
    log "Git pull exitoso"
else
    log "ERROR: Git pull falló"
    exit 1
fi

# 2. Instalar dependencias de Composer
if [ -f "composer.json" ]; then
    log "Ejecutando: composer install"
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader --no-interaction >> "$LOG_FILE" 2>&1
        if [ $? -eq 0 ]; then
            log "Composer install exitoso"
        else
            log "WARNING: Composer install tuvo problemas (continuando...)"
        fi
    else
        log "WARNING: Composer no encontrado, saltando instalación de dependencias"
    fi
fi

# 3. Limpiar caché de CodeIgniter
if [ -d "writable/cache" ]; then
    log "Limpiando caché de CodeIgniter"
    find writable/cache -type f -delete 2>/dev/null
    log "Caché limpiado"
fi

# 4. Limpiar sesiones antiguas (opcional)
if [ -d "writable/session" ]; then
    log "Limpiando sesiones antiguas (más de 1 hora)"
    find writable/session -type f -mmin +60 -delete 2>/dev/null
fi

# 5. Verificar permisos (opcional)
log "Verificando permisos"
chmod -R 755 "$REPO_PATH" 2>/dev/null
chmod -R 777 "$REPO_PATH/writable" 2>/dev/null

log "Deployment completado exitosamente"
log "========================================"

exit 0
