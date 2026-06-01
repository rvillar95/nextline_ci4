FROM php:8.2-apache

# Extensiones necesarias para CodeIgniter 4 (MySQL, intl, zip para Composer)
RUN apt-get update && apt-get install -y libicu-dev libzip-dev unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql intl zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite para que funcionen las rutas de CodeIgniter
RUN a2enmod rewrite

# Este proyecto tiene el front controller (index.php) en la raíz, no en public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Asegurar permisos y AllowOverride para la raíz (necesario para .htaccess de CodeIgniter)
# PassEnv hace que las variables inyectadas por Kubernetes lleguen a PHP (getenv)
RUN printf "ServerName localhost\n\
PassEnv APP_BASE_URL DATABASE_HOSTNAME DATABASE_PORT DATABASE_NAME DATABASE_USERNAME DATABASE_PASSWORD GOOGLE_CALENDAR_CLIENT_ID GOOGLE_CALENDAR_CLIENT_SECRET GOOGLE_CALENDAR_REDIRECT_URI CALENDAR_PROVIDER MERCADOPAGO_WEBHOOK_BASE_URL WHATSAPP_PLANTILLA_IDIOMA EMAIL_FROM_EMAIL EMAIL_FROM_NAME EMAIL_SMTP_HOST EMAIL_SMTP_USER EMAIL_SMTP_PASS EMAIL_SMTP_PORT EMAIL_SMTP_CRYPTO\n\
<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php\n\
</Directory>\n" > /etc/apache2/conf-available/nutrinext.conf \
    && a2enconf nutrinext

WORKDIR /var/www/html

# Instalar Composer e instalar dependencias (vendor/ no está en el repo)
COPY composer.json composer.lock /var/www/html/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --no-interaction --optimize-autoloader

# Copiar el resto del código de la aplicación
COPY . /var/www/html

# CodeIgniter 4 necesita escribir en writable/ (cache, logs, session, uploads)
RUN mkdir -p /var/www/html/writable/cache /var/www/html/writable/logs \
    /var/www/html/writable/session /var/www/html/writable/uploads \
    && chown -R www-data:www-data /var/www/html/writable

# Exponer el puerto por defecto de Apache
EXPOSE 80


