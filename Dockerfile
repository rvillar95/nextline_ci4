FROM php:8.2-apache

# Extensiones necesarias para CodeIgniter 4 con MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

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

# Exponer el puerto por defecto de Apache
EXPOSE 80


