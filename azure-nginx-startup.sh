#!/bin/bash
# Script de arranque para Azure App Service (PHP 8 + Nginx)
# Hace que /login funcione sin /index.php (rewrite a index.php)
# Copiar a /home/site/startup.sh en el servidor y configurar como "Comando de inicio" en Azure.

if [ -f /home/site/default ]; then
  cp /home/site/default /etc/nginx/sites-enabled/default
  service nginx reload
fi
