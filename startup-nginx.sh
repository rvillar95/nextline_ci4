#!/bin/bash
# Espera a que exista el archivo default en wwwroot (el deploy puede terminar después del arranque)
# y luego aplica la config de Nginx. Usar como Comando de inicio en Azure, seguido de ; php-fpm

CONFIG_SRC="/home/site/wwwroot/default"
CONFIG_DST="/etc/nginx/sites-enabled/default"
MAX_WAIT=60
SLEEP=3

count=0
while [ ! -f "$CONFIG_SRC" ] && [ $count -lt $MAX_WAIT ]; do
  sleep $SLEEP
  count=$((count + SLEEP))
done

if [ -f "$CONFIG_SRC" ]; then
  cp "$CONFIG_SRC" "$CONFIG_DST"
  service nginx reload
fi
