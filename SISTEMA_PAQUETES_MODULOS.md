# Sistema de Paquetes de Módulos - NextLine

## 🎯 Concepto

Sistema que permite definir **paquetes de módulos** en la base de datos y activar/desactivar funcionalidades según el paquete contratado por el cliente.

---

## 📦 Estructura de Base de Datos

### **Tabla: `paquetes`**
Define los productos/planes disponibles

```sql
CREATE TABLE `paquetes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `descripcion` text,
  `precio_setup` decimal(10,2) DEFAULT 0,
  `precio_mensual` decimal(10,2) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `orden` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Datos iniciales:**
```sql
INSERT INTO `paquetes` (`nombre`, `slug`, `descripcion`, `precio_setup`, `precio_mensual`, `orden`) VALUES
('NextLine Presencia', 'presencia', 'Sitio web profesional para mostrar tu negocio: servicios, galería, proyectos, testimonios', 149000, 19990, 1),
('NextLine Gestión', 'gestion', 'Presencia + Cotizaciones y CRM para gestionar tu negocio', 269000, 49990, 2),
('NextLine Custom', 'custom', 'Solución a medida con desarrollos personalizados', 0, 0, 3);
```

---

### **Tabla: `modulos`**
Define todos los módulos disponibles en el sistema

```sql
CREATE TABLE `modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `descripcion` text,
  `icono` varchar(50) DEFAULT 'fa-cube',
  `ruta_menu` varchar(255) DEFAULT NULL,
  `orden` int(11) DEFAULT 0,
  `es_core` tinyint(1) DEFAULT 0 COMMENT '1 = módulo core (no se puede desactivar)',
  `activo` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Datos iniciales:**
```sql
INSERT INTO `modulos` (`nombre`, `slug`, `descripcion`, `icono`, `ruta_menu`, `orden`, `es_core`) VALUES
-- CORE (siempre activos)
('Dashboard', 'dashboard', 'Panel principal de estadísticas', 'fa-home', 'dashboard', 1, 1),
('Empresa', 'empresa', 'Información de la empresa', 'fa-building', 'dashboard/empresa', 2, 1),
('Usuarios', 'usuarios', 'Gestión de usuarios del sistema', 'fa-users', 'dashboard/usuarios', 3, 1),

-- PAQUETE PRESENCIA
('Servicios', 'servicios', 'Catálogo de servicios ofrecidos', 'fa-briefcase', 'dashboard/servicio', 10, 0),
('Categorías de Servicios', 'categorias-servicios', 'Organización de servicios', 'fa-tags', 'dashboard/categoria-servicio', 11, 0),
('Galería', 'galeria', 'Banco de imágenes del negocio', 'fa-images', 'dashboard/galeria', 12, 0),
('Categorías de Galería', 'categorias-galeria', 'Organización de galería', 'fa-folder', 'dashboard/galeria-categoria', 13, 0),
('Proyectos', 'proyectos', 'Portfolio de trabajos realizados', 'fa-project-diagram', 'dashboard/proyecto', 14, 0),
('Testimonios', 'testimonios', 'Reseñas de clientes', 'fa-star', 'dashboard/testimonio', 15, 0),
('Contacto (Leads)', 'leads', 'Formularios de contacto recibidos', 'fa-envelope', 'dashboard/leads', 16, 0),

-- PAQUETE GESTIÓN (adicionales)
('Clientes', 'clientes', 'CRM básico de clientes', 'fa-user-tie', 'dashboard/cliente', 20, 0),
('Cotizaciones', 'cotizaciones', 'Creación y envío de presupuestos', 'fa-file-invoice-dollar', 'dashboard/cotizacion', 21, 0);
```

---

### **Tabla: `paquete_modulos`**
Relación muchos a muchos entre paquetes y módulos

```sql
CREATE TABLE `paquete_modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paquete_id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  `incluido` tinyint(1) DEFAULT 1 COMMENT '1 = incluido, 0 = bloqueado',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paquete_modulo` (`paquete_id`, `modulo_id`),
  KEY `paquete_id` (`paquete_id`),
  KEY `modulo_id` (`modulo_id`),
  CONSTRAINT `fk_paquete_modulos_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_paquete_modulos_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Datos iniciales:**
```sql
-- PAQUETE PRESENCIA (id=1)
-- Módulos CORE (siempre incluidos, pero los registramos igual)
INSERT INTO `paquete_modulos` (`paquete_id`, `modulo_id`, `incluido`) VALUES
(1, 1, 1), -- Dashboard
(1, 2, 1), -- Empresa
(1, 3, 1), -- Usuarios

-- Módulos de Presencia
(1, 4, 1),  -- Servicios
(1, 5, 1),  -- Categorías Servicios
(1, 6, 1),  -- Galería
(1, 7, 1),  -- Categorías Galería
(1, 8, 1),  -- Proyectos
(1, 9, 1),  -- Testimonios
(1, 10, 1), -- Leads

-- Módulos bloqueados en Presencia
(1, 11, 0), -- Clientes (NO incluido)
(1, 12, 0); -- Cotizaciones (NO incluido)

-- PAQUETE GESTIÓN (id=2)
INSERT INTO `paquete_modulos` (`paquete_id`, `modulo_id`, `incluido`) VALUES
-- CORE
(2, 1, 1), -- Dashboard
(2, 2, 1), -- Empresa
(2, 3, 1), -- Usuarios

-- Presencia (todos incluidos)
(2, 4, 1),  -- Servicios
(2, 5, 1),  -- Categorías Servicios
(2, 6, 1),  -- Galería
(2, 7, 1),  -- Categorías Galería
(2, 8, 1),  -- Proyectos
(2, 9, 1),  -- Testimonios
(2, 10, 1), -- Leads

-- Gestión (adicionales)
(2, 11, 1), -- Clientes
(2, 12, 1); -- Cotizaciones

-- PAQUETE CUSTOM (id=3) - Todos los módulos disponibles
INSERT INTO `paquete_modulos` (`paquete_id`, `modulo_id`, `incluido`) 
SELECT 3, id, 1 FROM modulos;
```

---

### **Tabla: `empresa` (modificar)**
Agregar campo de paquete contratado

```sql
ALTER TABLE `empresa` 
ADD COLUMN `paquete_id` int(11) DEFAULT 1 AFTER `id`,
ADD KEY `fk_empresa_paquete` (`paquete_id`),
ADD CONSTRAINT `fk_empresa_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`);

-- Actualizar empresas existentes al paquete "Presencia" por defecto
UPDATE `empresa` SET `paquete_id` = 1 WHERE `paquete_id` IS NULL;
```

---

## 🔧 Implementación en Código

### **1. Modelo: `app/Models/Paquete.php`**

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class Paquete extends Model
{
    protected $table = 'paquetes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre', 
        'slug', 
        'descripcion', 
        'precio_setup', 
        'precio_mensual', 
        'activo', 
        'orden'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Obtener todos los módulos de un paquete
     */
    public function getModulosPorPaquete($paqueteId)
    {
        return $this->db->table('paquete_modulos pm')
            ->select('m.*, pm.incluido')
            ->join('modulos m', 'm.id = pm.modulo_id')
            ->where('pm.paquete_id', $paqueteId)
            ->where('pm.incluido', 1)
            ->orderBy('m.orden', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Verificar si un módulo está incluido en un paquete
     */
    public function tieneModulo($paqueteId, $moduloSlug)
    {
        $result = $this->db->table('paquete_modulos pm')
            ->select('pm.incluido')
            ->join('modulos m', 'm.id = pm.modulo_id')
            ->where('pm.paquete_id', $paqueteId)
            ->where('m.slug', $moduloSlug)
            ->where('pm.incluido', 1)
            ->get()
            ->getRow();

        return $result ? true : false;
    }
}
```

---

### **2. Modelo: `app/Models/Modulo.php`**

```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class Modulo extends Model
{
    protected $table = 'modulos';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre', 
        'slug', 
        'descripcion', 
        'icono', 
        'ruta_menu', 
        'orden', 
        'es_core', 
        'activo'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    /**
     * Obtener módulos activos
     */
    public function getActivos()
    {
        return $this->where('activo', 1)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener módulos core (siempre visibles)
     */
    public function getCore()
    {
        return $this->where('es_core', 1)
                    ->where('activo', 1)
                    ->orderBy('orden', 'ASC')
                    ->findAll();
    }
}
```

---

### **3. Helper: `app/Helpers/paquete_helper.php`**

```php
<?php

use App\Models\Empresa;
use App\Models\Paquete;

/**
 * Obtener el paquete actual de la empresa
 */
if (!function_exists('getPaqueteActual')) {
    function getPaqueteActual()
    {
        $empresaModel = new Empresa();
        $empresa = $empresaModel->first();
        
        if (!$empresa) {
            return null;
        }

        $paqueteModel = new Paquete();
        return $paqueteModel->find($empresa['paquete_id']);
    }
}

/**
 * Verificar si un módulo está disponible en el paquete actual
 */
if (!function_exists('moduloDisponible')) {
    function moduloDisponible($moduloSlug)
    {
        $empresaModel = new Empresa();
        $empresa = $empresaModel->first();
        
        if (!$empresa) {
            return false;
        }

        // Los módulos core siempre están disponibles
        $moduloModel = new \App\Models\Modulo();
        $modulo = $moduloModel->where('slug', $moduloSlug)->first();
        
        if ($modulo && $modulo['es_core'] == 1) {
            return true;
        }

        $paqueteModel = new Paquete();
        return $paqueteModel->tieneModulo($empresa['paquete_id'], $moduloSlug);
    }
}

/**
 * Obtener módulos del menú según paquete actual
 */
if (!function_exists('getModulosMenu')) {
    function getModulosMenu()
    {
        $empresaModel = new Empresa();
        $empresa = $empresaModel->first();
        
        if (!$empresa) {
            return [];
        }

        $paqueteModel = new Paquete();
        return $paqueteModel->getModulosPorPaquete($empresa['paquete_id']);
    }
}
```

---

### **4. Modificar Dashboard Controller**

```php
<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        // Cargar el helper
        helper('paquete');

        $data = [
            'paquete_actual' => getPaqueteActual(),
            'modulos_menu' => getModulosMenu(),
        ];

        return view('dashboard/home', $data);
    }
}
```

---

### **5. Vista del Menú: Generar dinámicamente**

En `app/Views/layout/dashboard.php`:

```php
<?php 
helper('paquete');
$modulosMenu = getModulosMenu();
$paqueteActual = getPaqueteActual();
?>

<!-- Menú lateral -->
<ul class="menu-categories">
    <?php foreach ($modulosMenu as $modulo): ?>
        <?php if ($modulo['ruta_menu']): ?>
            <li class="menu">
                <a href="<?= base_url($modulo['ruta_menu']) ?>" 
                   class="dropdown-toggle">
                    <div>
                        <i class="<?= $modulo['icono'] ?>"></i>
                        <span><?= $modulo['nombre'] ?></span>
                    </div>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>

<!-- Mostrar paquete actual en footer del sidebar -->
<div class="sidebar-footer">
    <small class="text-muted">
        Plan: <strong><?= $paqueteActual['nombre'] ?? 'N/A' ?></strong>
    </small>
</div>
```

---

### **6. Middleware: Proteger rutas según paquete**

`app/Filters/PaqueteFilter.php`:

```php
<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PaqueteFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('paquete');

        // Extraer módulo del URI (ej: dashboard/cliente -> cliente)
        $uri = $request->getUri();
        $segments = $uri->getSegments();
        
        if (count($segments) < 2) {
            return; // No es una ruta de módulo
        }

        $moduloRuta = $segments[1]; // "cliente", "cotizacion", etc.

        // Convertir ruta a slug del módulo
        $moduloSlugMap = [
            'cliente' => 'clientes',
            'cotizacion' => 'cotizaciones',
            'servicio' => 'servicios',
            'galeria' => 'galeria',
            'proyecto' => 'proyectos',
            'testimonio' => 'testimonios',
            'leads' => 'leads',
        ];

        $moduloSlug = $moduloSlugMap[$moduloRuta] ?? null;

        if ($moduloSlug && !moduloDisponible($moduloSlug)) {
            // Módulo no disponible en el paquete actual
            return redirect()
                ->to(base_url('dashboard'))
                ->with('error', 'Este módulo no está disponible en tu plan actual. Contacta para hacer upgrade.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada
    }
}
```

**Registrar filtro en `app/Config/Filters.php`:**

```php
public $filters = [
    'paquete' => ['before' => ['dashboard/*']],
];
```

---

## 🎯 Flujo de Uso

### **Escenario 1: Instalación Nueva**

```sql
-- Al instalar NextLine, se crea la empresa con paquete Presencia
INSERT INTO empresa (nombre, paquete_id, ...) VALUES ('Mi Empresa', 1, ...);

-- El sistema automáticamente:
-- 1. Carga solo los módulos de Presencia en el menú
-- 2. Bloquea acceso a /dashboard/cliente y /dashboard/cotizacion
-- 3. Muestra "Plan: NextLine Presencia" en el dashboard
```

### **Escenario 2: Upgrade de Presencia a Gestión**

```sql
-- Cliente decide hacer upgrade
UPDATE empresa SET paquete_id = 2 WHERE id = 1;

-- Automáticamente:
-- 1. Aparecen los módulos de Clientes y Cotizaciones en el menú
-- 2. Se habilita acceso a esas rutas
-- 3. Muestra "Plan: NextLine Gestión" en el dashboard
```

### **Escenario 3: Downgrade de Gestión a Presencia**

```sql
-- Cliente cancela módulos avanzados
UPDATE empresa SET paquete_id = 1 WHERE id = 1;

-- Automáticamente:
-- 1. Desaparecen Clientes y Cotizaciones del menú
-- 2. Si intenta acceder a la ruta, redirect con mensaje
-- 3. Los DATOS de clientes/cotizaciones permanecen en BD (no se borran)
```

---

## 💡 Ventajas de este Sistema

### **1. Flexibilidad Total**
- ✅ Cambiar de paquete es solo un UPDATE en BD
- ✅ No requiere reinstalar o modificar código
- ✅ Los datos permanecen intactos

### **2. Multi-tenant Ready**
Si en el futuro decides hacer multi-tenant:
```sql
-- Cada empresa tiene su propio paquete
SELECT e.nombre, p.nombre as paquete 
FROM empresa e 
JOIN paquetes p ON p.id = e.paquete_id;
```

### **3. Fácil de Escalar**
Para agregar un nuevo módulo:
```sql
-- 1. Crear el módulo
INSERT INTO modulos (nombre, slug, ...) VALUES ('Blog', 'blog', ...);

-- 2. Asignarlo a paquetes
INSERT INTO paquete_modulos (paquete_id, modulo_id) VALUES (2, 13); -- Solo en Gestión
```

### **4. Control de Acceso**
- Middleware automáticamente bloquea rutas no permitidas
- Menú se genera dinámicamente según paquete
- Imposible acceder a módulos no contratados

### **5. UX Profesional**
```php
// En cualquier vista puedes hacer:
<?php if (moduloDisponible('cotizaciones')): ?>
    <a href="<?= base_url('dashboard/cotizacion') ?>">Ver Cotizaciones</a>
<?php else: ?>
    <button class="btn-upgrade">
        Upgrade a Gestión para usar Cotizaciones
    </button>
<?php endif; ?>
```

---

## 📊 Dashboard de Paquete

Puedes crear una vista para que el cliente vea su plan:

```php
// app/Views/dashboard/mi-plan.php

<?php 
helper('paquete');
$paquete = getPaqueteActual();
$modulos = getModulosMenu();
?>

<div class="card-plan">
    <h2>Tu Plan Actual</h2>
    <div class="plan-badge">
        <i class="fas fa-star"></i>
        <?= $paquete['nombre'] ?>
    </div>
    
    <p><?= $paquete['descripcion'] ?></p>
    
    <h3>Módulos Incluidos:</h3>
    <ul class="modulos-list">
        <?php foreach ($modulos as $modulo): ?>
            <li>
                <i class="<?= $modulo['icono'] ?>"></i>
                <?= $modulo['nombre'] ?>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <?php if ($paquete['slug'] === 'presencia'): ?>
        <div class="upgrade-cta">
            <h4>¿Necesitas más funcionalidades?</h4>
            <p>Upgrade a NextLine Gestión y obtén:</p>
            <ul>
                <li>✅ CRM de Clientes</li>
                <li>✅ Sistema de Cotizaciones</li>
                <li>✅ Reportes Avanzados</li>
            </ul>
            <a href="<?= base_url('contacto-upgrade') ?>" class="btn-upgrade">
                Solicitar Upgrade
            </a>
        </div>
    <?php endif; ?>
</div>
```

---

## 🚀 Implementación Rápida

### **Archivo SQL completo:**

Crearé un archivo con todo el SQL necesario para implementar esto.

**¿Quieres que genere el archivo SQL completo listo para ejecutar?**

---

## 🎯 Resumen

Con este sistema:

1. **Instalación nueva:** `paquete_id = 1` (Presencia)
2. **Upgrade:** Cambiar `paquete_id` en tabla `empresa`
3. **Menú:** Se genera automáticamente según paquete
4. **Seguridad:** Middleware bloquea módulos no contratados
5. **Escalable:** Agregar módulos es solo insertar en BD

**¿Te gusta este enfoque? ¿Genero el SQL y el código PHP completo?**

