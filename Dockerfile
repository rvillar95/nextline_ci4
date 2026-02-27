FROM php:8.2-apache

# Extensiones necesarias para CodeIgniter 4 con MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite para que funcionen las rutas de CodeIgniter
RUN a2enmod rewrite

# Establecer el DocumentRoot en la carpeta public de CodeIgniter
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Actualizar el VirtualHost por defecto para que apunte a public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Asegurar permisos y acceso al directorio public
RUN printf "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n" > /etc/apache2/conf-available/vitasync.conf \
    && a2enconf vitasync

WORKDIR /var/www/html

# Copiar el código de la aplicación
COPY . /var/www/html

# Exponer el puerto por defecto de Apache
EXPOSE 80


