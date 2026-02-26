#!/bin/bash

echo "Aplicando configuración personalizada de Nginx..."

# Esperar a que el archivo exista (máx 60 s por si el deploy termina después)
MAX_WAIT=60
elapsed=0
while [ ! -f /home/site/wwwroot/default ] && [ $elapsed -lt $MAX_WAIT ]; do
  echo "Esperando archivo default..."
  sleep 2
  elapsed=$((elapsed + 2))
done

if [ ! -f /home/site/wwwroot/default ]; then
  echo "Timeout: default no encontrado después de ${MAX_WAIT}s."
  exit 1
fi

cp /home/site/wwwroot/default /etc/nginx/sites-enabled/default

echo "Recargando Nginx..."
service nginx reload

echo "Iniciando PHP-FPM..."
exec php-fpm
