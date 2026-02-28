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
RUN printf "<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php\n\
</Directory>\n" > /etc/apache2/conf-available/vitasync.conf \
    && a2enconf vitasync

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


