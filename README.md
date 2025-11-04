# MANSANCHEZ - Sistema de Gestión para Constructora

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4.svg)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EF4223.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Sistema web completo para la gestión de construcción, proyectos, servicios y contactos de MANSANCHEZ Constructor.

## 🏗️ Características Principales

### Sitio Web Público
- **Home moderno y responsivo** con diseño profesional
- **Catálogo de Servicios** con categorías y detalles
- **Galería de Proyectos** con imágenes y especificaciones
- **Formulario de Contacto** con reCAPTCHA v3 y validación
- **Páginas legales** (Política de Privacidad, Términos y Condiciones)
- **SEO optimizado** con robots.txt y sitemap.xml

### Panel de Administración (Dashboard)
- Sistema de **autenticación** con sesiones seguras
- Gestión de **Usuarios y Perfiles**
- Gestión de **Proyectos** con imágenes y detalles
- Gestión de **Servicios** y categorías
- **Galería** con categorización de imágenes
- **Listado de Materiales** con generación de PDF
- Gestión de **Clientes** y contactos
- Sistema de **Testimonios** con calificaciones
- **Cotizaciones** con archivos adjuntos

## 🚀 Tecnologías Utilizadas

- **Backend**: PHP 8.1+ con CodeIgniter 4.x
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **UI Framework**: Bootstrap 5
- **Base de Datos**: MySQL/MariaDB
- **PDF Generation**: DomPDF
- **Email**: PHPMailer con SMTP
- **Security**: reCAPTCHA v3, CSRF Protection, XSS Filtering

## 📋 Requisitos del Sistema

### Servidor
- PHP 8.1 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Apache 2.4+ o Nginx
- Composer 2.x

### Extensiones PHP Requeridas
- `intl`
- `mbstring`
- `json`
- `mysqlnd`
- `curl`
- `xml`
- `gd` (para procesamiento de imágenes)

### Módulos Apache Recomendados
- `mod_rewrite` (requerido)
- `mod_headers` (para seguridad)
- `mod_expires` (para caché)
- `mod_deflate` (para compresión)

## 🔧 Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/mansanchez.git
cd mansanchez
```

### 2. Instalar Dependencias

```bash
composer install
```

### 3. Configurar Variables de Entorno

Crear archivo `.env` basado en el archivo de configuración (ver `CONFIGURACION_ENV.md`):

```bash
# Copiar y editar con tus credenciales
nano .env
```

**Configuraciones esenciales:**
```env
CI_ENVIRONMENT = production
app.baseURL = 'https://mansanchez.cl/'

database.default.hostname = localhost
database.default.database = tu_base_datos
database.default.username = tu_usuario
database.default.password = tu_password

email.SMTPHost = mail.mansanchez.cl
email.SMTPUser = contacto@mansanchez.cl
email.SMTPPass = tu_password_email

recaptcha.siteKey = tu_site_key
recaptcha.secretKey = tu_secret_key
```

### 4. Importar Base de Datos

```bash
mysql -u usuario -p nombre_bd < nextline_constructor.sql
mysql -u usuario -p nombre_bd < database_listado_material.sql
mysql -u usuario -p nombre_bd < permisos_listado_material.sql
```

### 5. Configurar Permisos

```bash
chmod -R 777 writable/
chmod -R 755 uploads/
chmod -R 755 public/uploads/
```

### 6. Configurar Servidor Web

#### Apache (ejemplo de Virtual Host)

```apache
<VirtualHost *:80>
    ServerName mansanchez.cl
    ServerAlias www.mansanchez.cl
    DocumentRoot /var/www/mansanchez/public
    
    <Directory /var/www/mansanchez/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mansanchez_error.log
    CustomLog ${APACHE_LOG_DIR}/mansanchez_access.log combined
</VirtualHost>
```

#### Nginx (ejemplo)

```nginx
server {
    listen 80;
    server_name mansanchez.cl www.mansanchez.cl;
    root /var/www/mansanchez/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🔐 Seguridad

El sistema incluye múltiples capas de seguridad:

- ✅ **CSRF Protection** activado globalmente
- ✅ **XSS Filtering** en todas las entradas
- ✅ **SQL Injection** prevención con Query Builder
- ✅ **Password Hashing** con bcrypt
- ✅ **reCAPTCHA v3** en formularios públicos
- ✅ **Security Headers** configurados en `.htaccess`
- ✅ **Session Management** con regeneración automática
- ✅ **File Upload** validación estricta
- ✅ **Rate Limiting** en formularios de contacto

## 📁 Estructura del Proyecto

```
mansanchez/
├── app/
│   ├── Config/          # Configuraciones
│   ├── Controllers/     # Controladores
│   │   ├── Dashboard/   # Panel de administración
│   │   └── Web/         # Sitio público
│   ├── Models/          # Modelos de datos
│   ├── Views/           # Vistas
│   │   ├── Web/         # Vistas públicas
│   │   ├── Modulos/     # Vistas del dashboard
│   │   └── layout/      # Layouts
│   ├── Libraries/       # Librerías personalizadas
│   ├── Validation/      # Reglas de validación
│   └── Filters/         # Filtros (autenticación, etc.)
├── public/              # Punto de entrada web
│   ├── uploads/         # Archivos subidos
│   └── index.php        # Front controller
├── writable/            # Archivos temporales y logs
├── lib/                 # Assets frontend (CSS, JS, imágenes)
├── uploads/             # Uploads de proyectos
├── .htaccess            # Configuración Apache
├── robots.txt           # SEO - Directivas para bots
├── sitemap.xml          # SEO - Mapa del sitio
└── README.md            # Este archivo
```

## 🎨 Módulos Principales

### Módulo de Proyectos
- Galería con imágenes portada
- Especificaciones técnicas (área, ubicación, etc.)
- Estado del proyecto (planificación, construcción, finalizado)
- Filtros y categorización

### Módulo de Listado de Materiales
- Creación y edición de listados
- Asignación a clientes y proyectos
- **Vista móvil optimizada** para adultos
- Generación automática de PDF
- Envío por email

### Módulo de Cotizaciones
- Sistema completo de cotizaciones
- Adjuntar archivos PDF
- Estados de cotización
- Historial de versiones

### Módulo de Testimonios
- Sistema de calificaciones (1-5 estrellas)
- Aprobación y moderación
- Mostrar en home destacados

## 📊 Base de Datos

Tablas principales:
- `usuario` - Usuarios del sistema
- `perfil` - Perfiles y roles
- `proyecto` - Proyectos de construcción
- `servicio` - Servicios ofrecidos
- `cliente` - Base de datos de clientes
- `listado_material` - Listados de materiales
- `cotizacion` - Cotizaciones generadas
- `testimonio` - Testimonios de clientes
- `galeria` - Imágenes organizadas
- `lead` - Contactos desde formulario web

## 🌐 SEO y Performance

- **robots.txt** configurado para bots
- **sitemap.xml** con todas las páginas
- **Open Graph** meta tags (pendiente implementar)
- **Compresión GZIP** activada
- **Browser Caching** configurado
- **Lazy loading** de imágenes
- **Minificación** de assets (pendiente)

## 📱 Responsive Design

- Diseño **mobile-first**
- Breakpoints optimizados
- Formularios **adaptados para adultos** (textos grandes, botones amplios)
- Testing en dispositivos reales

## 🔄 Deployment a Producción

### Checklist Pre-Deployment

- [ ] Configurar archivo `.env` de producción
- [ ] Cambiar `CI_ENVIRONMENT` a `production`
- [ ] Actualizar `app.baseURL` a dominio real
- [ ] Configurar credenciales de base de datos
- [ ] Configurar credenciales de email
- [ ] Generar claves reCAPTCHA de producción
- [ ] Importar base de datos
- [ ] Configurar permisos de archivos
- [ ] Configurar SSL/HTTPS
- [ ] Descomentar HSTS en `.htaccess`
- [ ] Configurar backup automático
- [ ] Verificar logs (`writable/logs/`)

### Comandos Útiles

```bash
# Limpiar caché
php spark cache:clear

# Ver rutas
php spark routes

# Ejecutar migraciones
php spark migrate

# Ver versión
php spark --version
```

## 📝 Documentación Adicional

- [CONFIGURACION_ENV.md](CONFIGURACION_ENV.md) - Guía de variables de entorno
- [INSTRUCCIONES_LISTADO_MATERIAL.md](INSTRUCCIONES_LISTADO_MATERIAL.md) - Módulo de materiales

## 👥 Credenciales por Defecto

**⚠️ CAMBIAR INMEDIATAMENTE EN PRODUCCIÓN**

Panel de administración:
- URL: `https://mansanchez.cl/login`
- Usuario: (verificar en base de datos)
- Contraseña: (verificar en base de datos)

## 🐛 Troubleshooting

### Error 500 - Internal Server Error
- Verificar permisos de `writable/`
- Revisar logs en `writable/logs/`
- Verificar configuración de `.env`

### Email no se envía
- Verificar credenciales SMTP en `.env`
- Revisar logs de email
- Verificar puerto y encriptación

### reCAPTCHA falla
- Verificar claves en `.env`
- Verificar dominio en Google reCAPTCHA
- Revisar consola del navegador

## 📞 Soporte

Para soporte técnico o consultas:
- Email: info@mansanchez.cl
- Sitio Web: https://mansanchez.cl

## 📄 Licencia

Este proyecto es propiedad de MANSANCHEZ Constructor. Todos los derechos reservados.

## 🙏 Créditos

Desarrollado con ❤️ usando CodeIgniter 4

---

**MANSANCHEZ Constructor** - Construyendo tus sueños desde 2008
