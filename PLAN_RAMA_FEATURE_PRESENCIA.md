# Plan Técnico: Rama feature/presencia
## Eliminación de Módulos Cliente y Cotizaciones

---

## 🎯 Objetivo

Crear una versión limpia de NextLine enfocada solo en **presencia web** (mostrar), eliminando módulos de **gestión** (cotizaciones y clientes).

---

## 📋 Módulos a MANTENER

✅ **Empresa** - `app/Controllers/Dashboard/EmpresaController.php`
✅ **Servicios** - `app/Controllers/Dashboard/ServicioController.php`
✅ **Categorías de Servicios** - `app/Controllers/Dashboard/CategoriasServicioController.php`
✅ **Galería** - `app/Controllers/Dashboard/GaleriaController.php`
✅ **Categorías de Galería** - `app/Controllers/Dashboard/GaleriaCategoriaController.php`
✅ **Proyectos** - `app/Controllers/Dashboard/ProyectoController.php`
✅ **Testimonios** - `app/Controllers/Dashboard/TestimonioController.php`
✅ **Contacto** - `app/Controllers/Dashboard/LeadController.php` (leads del formulario web)
✅ **Web pública** - Toda la parte pública

---

## ❌ Módulos a ELIMINAR

### 1. **Módulo CLIENTE**
```
app/Controllers/Dashboard/ClienteController.php
app/Models/Cliente.php
app/Views/Modulos/clientes/
  ├── lista.php
  ├── registro.php
  ├── editar.php
  └── detalle.php (si existe)
```

### 2. **Módulo COTIZACIONES**
```
app/Controllers/Dashboard/CotizacionController.php
app/Models/Cotizacion.php
app/Models/CotizacionDetalle.php (si existe)
app/Views/Modulos/cotizaciones/
  ├── lista.php
  ├── registro.php
  ├── editar.php
  ├── detalle.php
  └── pdf.php (si existe)
```

---

## 🔧 Cambios Necesarios por Área

### **1. MENÚ DEL DASHBOARD**

**Archivo:** `app/Views/layout/dashboard.php` (o donde esté el sidebar)

**Cambios:**
- ❌ Remover link "Clientes"
- ❌ Remover link "Cotizaciones"
- ✅ Mantener todo lo demás

**Ejemplo de ajuste:**
```php
// ANTES
<li><a href="<?= base_url('dashboard/clientes') ?>">Clientes</a></li>
<li><a href="<?= base_url('dashboard/cotizaciones') ?>">Cotizaciones</a></li>

// DESPUÉS
// (Eliminar estas líneas completamente)
```

---

### **2. RUTAS**

**Archivo:** `app/Config/Routes.php`

**Cambios:**
```php
// ELIMINAR estas líneas:

// Clientes
$routes->group('clientes', function($routes) {
    $routes->get('/', 'Dashboard\ClienteController::index');
    $routes->get('lista', 'Dashboard\ClienteController::lista');
    $routes->get('registro', 'Dashboard\ClienteController::registro');
    $routes->post('crear', 'Dashboard\ClienteController::crear');
    $routes->get('editar/(:num)', 'Dashboard\ClienteController::editar/$1');
    $routes->post('actualizar/(:num)', 'Dashboard\ClienteController::actualizar/$1');
    $routes->post('eliminar/(:num)', 'Dashboard\ClienteController::eliminar/$1');
    // ... cualquier otra ruta de clientes
});

// Cotizaciones
$routes->group('cotizaciones', function($routes) {
    $routes->get('/', 'Dashboard\CotizacionController::index');
    $routes->get('lista', 'Dashboard\CotizacionController::lista');
    $routes->get('registro', 'Dashboard\CotizacionController::registro');
    $routes->post('crear', 'Dashboard\CotizacionController::crear');
    $routes->get('editar/(:num)', 'Dashboard\CotizacionController::editar/$1');
    $routes->post('actualizar/(:num)', 'Dashboard\CotizacionController::actualizar/$1');
    $routes->post('eliminar/(:num)', 'Dashboard\CotizacionController::eliminar/$1');
    $routes->get('pdf/(:num)', 'Dashboard\CotizacionController::generarPDF/$1');
    // ... cualquier otra ruta de cotizaciones
});
```

---

### **3. PERMISOS Y ACCESOS**

**Archivo:** `app/Models/Perfil.php` o donde se manejen permisos

Si tienes sistema de permisos por módulo, comentar o eliminar referencias a:
- `modulo_clientes`
- `modulo_cotizaciones`

**Ejemplo:**
```php
// COMENTAR O ELIMINAR:
// 'clientes' => 'Gestión de Clientes',
// 'cotizaciones' => 'Gestión de Cotizaciones',
```

---

### **4. DASHBOARD (HOME)**

**Archivo:** `app/Controllers/Dashboard/DashboardController.php` o `app/Views/dashboard/home.php`

Si el dashboard principal muestra widgets o estadísticas de clientes/cotizaciones:

**Cambios:**
- ❌ Remover cards de "Total Clientes"
- ❌ Remover cards de "Cotizaciones Pendientes"
- ❌ Remover gráficos relacionados
- ✅ Mantener: leads, proyectos, testimonios, etc.

---

### **5. BASE DE DATOS**

**IMPORTANTE:** NO eliminar tablas físicamente (por si necesitas volver atrás).

**Opción 1 - CONSERVADORA (recomendada):**
- Dejar las tablas `clientes` y `cotizaciones` en la BD
- Solo eliminar código de la aplicación
- Si un cliente necesita esos datos después, están disponibles

**Opción 2 - AGRESIVA (solo si estás 100% seguro):**
- Crear archivo `database/migrations/remove_gestion_modules.sql`:
```sql
-- BACKUP PRIMERO
CREATE TABLE clientes_backup AS SELECT * FROM clientes;
CREATE TABLE cotizaciones_backup AS SELECT * FROM cotizaciones;

-- Opcional: Eliminar tablas (solo si estás seguro)
-- DROP TABLE IF EXISTS cotizaciones;
-- DROP TABLE IF EXISTS clientes;
```

**MI RECOMENDACIÓN:** Opción 1 (dejar las tablas, solo eliminar código)

---

### **6. DEPENDENCIAS ENTRE MÓDULOS**

**Verificar si hay relaciones:**

**A. ¿PROYECTOS está relacionado con CLIENTES?**
```php
// En Proyecto.php, verificar si hay:
protected $table = 'proyectos';
// ¿Hay campo cliente_id?

// Si SÍ:
// - Opción 1: Mantener campo pero no mostrar en UI
// - Opción 2: Cambiar a cliente_nombre (texto libre) en lugar de relación
```

**B. ¿TESTIMONIOS está relacionado con CLIENTES?**
```php
// Similar verificación
// Si hay relación, desacoplar
```

**C. ¿SERVICIOS está relacionado con COTIZACIONES?**
```php
// Probablemente no, pero verificar
```

---

### **7. VALIDACIONES Y LÓGICA DE NEGOCIO**

**Buscar referencias cruzadas:**

```bash
# Buscar menciones a "cliente" en código
grep -r "ClienteController" app/
grep -r "Cliente::" app/
grep -r "clientes" app/Views/ --include="*.php"

# Buscar menciones a "cotizacion"
grep -r "CotizacionController" app/
grep -r "Cotizacion::" app/
grep -r "cotizaciones" app/Views/ --include="*.php"
```

**Eliminar o comentar esas referencias.**

---

### **8. TESTS (si existen)**

```
tests/
  └── Controllers/
      ├── ClienteControllerTest.php  ❌ ELIMINAR
      └── CotizacionControllerTest.php  ❌ ELIMINAR
```

---

### **9. MIGRACIONES (si existen)**

```
app/Database/Migrations/
  ├── 2024_XX_XX_create_clientes_table.php  ⚠️ COMENTAR (no eliminar)
  └── 2024_XX_XX_create_cotizaciones_table.php  ⚠️ COMENTAR (no eliminar)
```

**Por qué comentar y no eliminar:**
- Si vuelves a main, necesitas las migraciones intactas
- Solo comenta el contenido del `up()` y `down()`

---

### **10. SEEDERS (datos de ejemplo)**

```
app/Database/Seeds/
  ├── ClientesSeeder.php  ❌ ELIMINAR o COMENTAR
  └── CotizacionesSeeder.php  ❌ ELIMINAR o COMENTAR
```

---

## 📝 CHECKLIST DE ELIMINACIÓN

### **PASO 1: Crear rama**
```bash
git checkout -b feature/presencia
```

### **PASO 2: Backup de seguridad**
```bash
# Exportar BD actual
mysqldump -u root nextline_pyme > backup_antes_presencia.sql

# Git: verificar que main esté limpio
git checkout main
git status
git checkout feature/presencia
```

### **PASO 3: Eliminar controladores**
```bash
rm app/Controllers/Dashboard/ClienteController.php
rm app/Controllers/Dashboard/CotizacionController.php
```

### **PASO 4: Eliminar modelos**
```bash
rm app/Models/Cliente.php
rm app/Models/Cotizacion.php
rm app/Models/CotizacionDetalle.php  # si existe
```

### **PASO 5: Eliminar vistas**
```bash
rm -rf app/Views/Modulos/clientes/
rm -rf app/Views/Modulos/cotizaciones/
```

### **PASO 6: Limpiar rutas**
- Abrir `app/Config/Routes.php`
- Buscar secciones de `clientes` y `cotizaciones`
- Eliminar esas líneas

### **PASO 7: Limpiar menú dashboard**
- Abrir `app/Views/layout/dashboard.php`
- Buscar links a "Clientes" y "Cotizaciones"
- Eliminar esas líneas

### **PASO 8: Limpiar dashboard home**
- Abrir `app/Controllers/Dashboard/DashboardController.php`
- Abrir `app/Views/dashboard/home.php` o similar
- Eliminar widgets/cards de clientes y cotizaciones

### **PASO 9: Verificar dependencias**
```bash
# Buscar referencias residuales
grep -r "ClienteController" app/
grep -r "CotizacionController" app/
grep -r "use App\\Models\\Cliente" app/
grep -r "use App\\Models\\Cotizacion" app/
```

### **PASO 10: Probar la aplicación**
```bash
# Iniciar servidor
php spark serve

# Probar:
# 1. Login al dashboard ✓
# 2. Menú no muestra Clientes ni Cotizaciones ✓
# 3. Todos los demás módulos funcionan ✓
# 4. Web pública funciona ✓
```

### **PASO 11: Limpiar linter**
```bash
# Ver si hay errores
php spark lint
```

### **PASO 12: Commit**
```bash
git status
git add .
git commit -m "feat: Crear versión NextLine Presencia

- Eliminados módulos Cliente y Cotizaciones
- Removidas rutas relacionadas
- Limpiado menú del dashboard
- Versión enfocada solo en presencia web (mostrar)
- Módulos mantenidos: Empresa, Servicios, Galería, Proyectos, Testimonios, Contacto

NextLine Presencia es la versión core del producto para PYMEs que necesitan presencia web profesional sin módulos de gestión."
```

---

## 🚨 PRECAUCIONES

### **1. NO HACER PUSH INMEDIATAMENTE**
```bash
# NO HAGAS ESTO AÚN:
# git push origin feature/presencia
```

Primero prueba localmente durante 1-2 días.

### **2. MANTENER MAIN INTACTO**
```bash
# main sigue teniendo todos los módulos
# feature/presencia es la versión limpia
```

### **3. DOCUMENTAR DIFERENCIAS**

Crear archivo `DIFERENCIAS_PRESENCIA.md` en la rama:
```markdown
# Diferencias entre main y feature/presencia

## Módulos eliminados en feature/presencia:
- ❌ Clientes
- ❌ Cotizaciones

## Razón:
NextLine Presencia es la versión enfocada en presencia web (mostrar),
sin módulos de gestión de negocio.

## Migrar de main a presencia:
No recomendado. Son productos diferentes.

## Migrar de presencia a main:
Posible. Solo merge a main y recuperar módulos.
```

---

## 🎯 RESULTADO ESPERADO

### **Después de estos cambios:**

**Dashboard tendrá:**
- ✅ Empresa
- ✅ Servicios
- ✅ Galería
- ✅ Proyectos
- ✅ Testimonios
- ✅ Leads (contactos)
- ❌ Clientes (eliminado)
- ❌ Cotizaciones (eliminado)

**Web pública sigue igual:**
- Todas las páginas funcionan
- Formulario de contacto funciona
- Galería, proyectos, servicios visibles

**Base de datos:**
- Tablas `clientes` y `cotizaciones` existen pero no se usan
- Ningún código las accede

**Beneficios:**
- 🚀 Código más limpio
- 🎯 Producto enfocado
- 📚 Más fácil de documentar
- 💰 Más fácil de vender
- ⚡ Onboarding más rápido

---

## 📊 IMPACTO EN ARCHIVOS

**Archivos a eliminar:** ~15-20
**Líneas de código eliminadas:** ~2,000-3,000
**Líneas en Routes.php eliminadas:** ~30-40
**Reducción de complejidad:** ~30%

---

## 🔄 COMANDOS COMPLETOS (COPY-PASTE)

```bash
# 1. Crear rama
cd C:\wamp64\www\codeigniter4\nextline_ci4
git checkout -b feature/presencia

# 2. Backup BD
mysqldump -u root nextline_pyme > backup_antes_presencia.sql

# 3. Eliminar archivos (PowerShell)
Remove-Item app\Controllers\Dashboard\ClienteController.php -ErrorAction SilentlyContinue
Remove-Item app\Controllers\Dashboard\CotizacionController.php -ErrorAction SilentlyContinue
Remove-Item app\Models\Cliente.php -ErrorAction SilentlyContinue
Remove-Item app\Models\Cotizacion.php -ErrorAction SilentlyContinue
Remove-Item app\Models\CotizacionDetalle.php -ErrorAction SilentlyContinue
Remove-Item app\Views\Modulos\clientes -Recurse -ErrorAction SilentlyContinue
Remove-Item app\Views\Modulos\cotizaciones -Recurse -ErrorAction SilentlyContinue

# 4. Verificar qué se eliminó
git status

# 5. Buscar referencias residuales
grep -r "ClienteController" app/
grep -r "CotizacionController" app/

# (Ahora edita manualmente Routes.php y dashboard.php)

# 6. Después de editar, verificar
php spark serve

# 7. Si todo funciona, commit
git add .
git commit -m "feat: Crear versión NextLine Presencia - Eliminados módulos Cliente y Cotizaciones"

# 8. Ver el estado
git log -1
git diff main feature/presencia --stat
```

---

## ✅ CRITERIOS DE ÉXITO

La rama `feature/presencia` está lista cuando:

- [ ] Dashboard carga sin errores
- [ ] Menú no muestra "Clientes" ni "Cotizaciones"
- [ ] Acceder a `/dashboard/clientes` da 404
- [ ] Acceder a `/dashboard/cotizaciones` da 404
- [ ] Todos los módulos de presencia funcionan (empresa, servicios, etc.)
- [ ] Web pública carga correctamente
- [ ] Formulario de contacto funciona
- [ ] No hay errores de linter
- [ ] No hay referencias a `ClienteController` o `CotizacionController` en código
- [ ] Commit hecho con mensaje claro

---

## 🎯 PRÓXIMOS PASOS DESPUÉS

1. **Probar localmente 2-3 días**
2. **Ajustar documentación** (README de la rama)
3. **Crear demos específicos** para NextLine Presencia
4. **Ejecutar el prompt** `PROMPT_NEXTLINE_PRESENCIA.md` para materiales comerciales
5. **Decidir estrategia de branches:**
   - `main` = Versión completa (legacy o futuro "NextLine Gestión")
   - `feature/presencia` = Versión comercial para lanzamiento
   - ¿Convertir `presencia` en `main`? (decisión estratégica)

---

**¿Listo para arrancar con los comandos?**

