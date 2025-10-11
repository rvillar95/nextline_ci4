# 🏗️ Arquitectura de Permisos - NextLine

## 📊 Jerarquía de Usuarios

```
┌─────────────────────────────────────────┐
│     SUPER ADMIN (poder=3)               │
│     - Gestiona el SISTEMA               │
│     - empresa_id = NULL                 │
│     - Ve TODO (incluso Modulo)          │
└─────────────────┬───────────────────────┘
                  │ crea empresas
                  │ asigna paquetes
                  ▼
    ┌─────────────────────────────────────────┐
    │     EMPRESA (paquete_id)                │
    │     - NextLine Presencia                │
    │     - NextLine Gestión                  │
    │     - NextLine Custom                   │
    └─────────────┬───────────────────────────┘
                  │ tiene usuario Admin
                  ▼
        ┌─────────────────────────────────────┐
        │   ADMIN EMPRESA (poder=2)           │
        │   - Gestiona su EMPRESA             │
        │   - empresa_id = X                  │
        │   - Ve módulos del paquete          │
        │   - Ve: Usuario, Perfil             │
        │   - NO ve: Modulo, Modulo Detalle   │
        └─────────────┬───────────────────────┘
                      │ crea usuarios
                      ▼
            ┌─────────────────────────────────┐
            │ USUARIOS EMPRESA (poder<2)      │
            │ - Trabajador (poder=1)          │
            │ - Trabajador Portero (poder=0)  │
            │ - empresa_id = X                │
            │ - Permisos limitados            │
            └─────────────────────────────────┘
```

---

## 🔐 Módulos por Tipo

### **A) EXCLUSIVOS SUPER ADMIN** (sa='S')
Solo Super Admin puede ver y gestionar:

| ID | Módulo | Descripción |
|----|--------|-------------|
| 4 | Modulo | Gestionar módulos del SISTEMA |
| 7 | Modulo Detalle | Gestionar permisos de módulos |

**🎯 Propósito:** Gestionar la estructura del sistema mismo.

---

### **B) GESTIÓN DE USUARIOS Y PERFILES** (Todos los paquetes)
Admin Empresa y Super Admin pueden gestionar:

| ID | Módulo | Descripción |
|----|--------|-------------|
| 1 | Usuario | Crear/editar usuarios de la empresa |
| 3 | Perfil | Crear roles personalizados |
| 6 | Perfil Detalle | Asignar permisos a roles |

**🎯 Propósito:** Cada Admin de Empresa gestiona su propio equipo.

**⚠️ Restricción:** Admin Empresa solo ve perfiles con **poder < 2** (no puede crear otros Admin ni Super Admin).

---

### **C) MÓDULOS COMUNES** (Todos los paquetes)

| ID | Módulo | Descripción |
|----|--------|-------------|
| 2 | Inicio | Dashboard principal |
| 31 | Empresa | Editar datos de la empresa |

---

### **D) PAQUETE PRESENCIA** (Mostrar)
Módulos para sitio web de presentación:

| ID | Módulo | Descripción |
|----|--------|-------------|
| 8 | Servicios | Catálogo de servicios |
| 17 | Categorías Servicios | Organización de servicios |
| 14 | Galería | Banco de imágenes |
| 16 | Categorías Galería | Organización de galería |
| 18 | Proyectos | Portfolio de trabajos |
| 26 | Testimonios | Reseñas de clientes |
| 19 | Contacto (Leads) | Formularios recibidos |
| 30 | Ubicación | Regiones/comunas (auxiliar) |

**💰 Precio:** Setup $149.000 + $19.990/mes

---

### **E) PAQUETE GESTIÓN** (Presencia + CRM)
Todo lo de Presencia más:

| ID | Módulo | Descripción |
|----|--------|-------------|
| 28 | Clientes | CRM básico de clientes |
| 29 | Cotizaciones | Sistema de presupuestos |

**💰 Precio:** Setup $269.000 + $49.990/mes

---

### **F) PAQUETE CUSTOM**
Todos los módulos disponibles según necesidades del cliente.

---

## 🎭 Permisos por Poder

### **Super Admin (poder=3)**

✅ **Puede:**
- Ver TODOS los módulos (incluso sa='S')
- Crear empresas
- Asignar paquetes a empresas
- Crear usuarios Admin para cada empresa
- Ver todos los perfiles (incluso poder=3)
- Crear otros Super Admin
- Gestionar Modulo y Modulo Detalle

❌ **No tiene:**
- empresa_id (es NULL)

---

### **Admin Empresa (poder=2)**

✅ **Puede:**
- Ver módulos de su paquete
- Ver: Usuario, Perfil, Perfil Detalle
- Crear usuarios con poder < 2
- Ver perfiles con poder < 2 (Trabajador, Trabajador Portero)
- Gestionar su empresa

❌ **NO puede:**
- Ver Modulo ni Modulo Detalle
- Ver perfiles con poder >= 2 (otros Admin, Super Admin)
- Crear Admin ni Super Admin
- Cambiar su paquete

**🔗 Tiene:**
- empresa_id = ID de su empresa

---

### **Trabajador (poder=1)**

✅ **Puede:**
- Ver módulos según `perfil_modulo`
- Limitado por paquete de su empresa
- Ver perfiles con poder < 1 (solo Trabajador Portero)

❌ **NO puede:**
- Gestionar usuarios (normalmente)
- Ver módulos de mayor nivel

**🔗 Tiene:**
- empresa_id = ID de su empresa

---

### **Trabajador Portero (poder=0)**

✅ **Puede:**
- Permisos muy limitados
- Definidos por `perfil_modulo`

❌ **NO puede:**
- Ver ningún perfil (no hay perfiles con poder < 0)
- Gestionar usuarios

**🔗 Tiene:**
- empresa_id = ID de su empresa

---

## 🔄 Flujo de Trabajo Típico

### **1. Super Admin crea empresa**

```sql
INSERT INTO empresa (nombre, paquete_id) 
VALUES ('ACME Corp', 1);  -- Paquete Presencia
```

### **2. Super Admin crea Admin para esa empresa**

```sql
INSERT INTO usuario (nombre, perfil_id, empresa_id) 
VALUES ('Juan Admin', 3, 1);  
-- perfil_id=3 (Administrador), empresa_id=1
```

### **3. Admin Empresa inicia sesión**

```php
// El sistema carga:
$modulosDisponibles = sp_obtener_menu_usuario($usuarioId);
// Resultado: Inicio, Usuario, Perfil, Servicios, Galería, etc.
// NO ve: Modulo, Modulo Detalle, Clientes, Cotizaciones
```

### **4. Admin Empresa crea usuarios**

```php
// Dropdown de perfiles muestra:
$perfiles = $perfilModel->getActivePerfil(2); // poder=2
// Resultado: Trabajador (poder=1), Trabajador Portero (poder=0)
// NO ve: Super Administrador (poder=3), Administrador (poder=2)
```

### **5. Cliente hace upgrade**

```sql
UPDATE empresa SET paquete_id = 2 WHERE id = 1;  -- Upgrade a Gestión
```

```php
// Admin Empresa ahora ve además:
// - Clientes (id=28)
// - Cotizaciones (id=29)
```

---

## 🛡️ Validaciones en PHP

### **Método 1: Verificar acceso a módulo**

```php
$puede = fn_usuario_puede_ver_modulo($usuarioId, $moduloId);
if ($puede == 'S') {
    // Mostrar módulo
}
```

### **Método 2: Obtener menú completo**

```php
sp_obtener_menu_usuario($usuarioId);
// Retorna solo módulos permitidos
```

### **Método 3: Filtrar perfiles**

```php
// En UsuarioController
$perfiles = $perfilModel->getActivePerfil($this->poder);
// Si $this->poder = 3 → retorna TODOS
// Si $this->poder = 2 → retorna poder < 2
// Si $this->poder = 1 → retorna poder < 1
```

---

## 📝 Reglas de Negocio

### **✅ Permitido:**

1. Super Admin puede crear otros Super Admin
2. Admin Empresa puede crear usuarios con poder < 2
3. Admin Empresa puede ver y editar Usuario, Perfil, Perfil Detalle
4. Cambiar paquete de una empresa (solo Super Admin)
5. Upgrade/downgrade mantiene los datos (no se borran)

### **❌ NO Permitido:**

1. Admin Empresa NO puede ver Modulo ni Modulo Detalle
2. Admin Empresa NO puede ver perfiles con poder >= 2
3. Admin Empresa NO puede cambiar su propio paquete
4. Usuario con poder < 2 NO puede gestionar usuarios (normalmente)
5. Nadie puede ver usuarios con mayor poder que el suyo

---

## 🗄️ Tablas Clave

### **`paquetes`**
```sql
- id
- nombre (NextLine Presencia, NextLine Gestión)
- slug (presencia, gestion)
- precio_setup
- precio_mensual
```

### **`paquete_modulo`**
```sql
- paquete_id
- modulo_id
- incluido ('S'/'N')
```

### **`empresa`**
```sql
- id
- nombre
- paquete_id → paquetes(id)
```

### **`usuario`**
```sql
- id
- perfil_id → perfil(id)
- empresa_id → empresa(id)  -- NULL para Super Admin
```

### **`perfil`**
```sql
- id
- nombre
- poder (0, 1, 2, 3)
```

### **`modulo`**
```sql
- id
- nombre
- sa ('S'/'N')  -- S = Solo Super Admin
```

---

## 🎯 Resumen Visual

```
SUPER ADMIN
├─ Ve TODO
├─ Modulo ✅
├─ Modulo Detalle ✅
└─ Gestiona empresas

ADMIN EMPRESA (con Presencia)
├─ Usuario ✅
├─ Perfil ✅
├─ Perfil Detalle ✅
├─ Servicios ✅
├─ Galería ✅
├─ Proyectos ✅
├─ Testimonios ✅
├─ Leads ✅
├─ Modulo ❌
├─ Modulo Detalle ❌
├─ Clientes ❌
└─ Cotizaciones ❌

ADMIN EMPRESA (con Gestión)
├─ Todo lo de Presencia ✅
├─ Clientes ✅
└─ Cotizaciones ✅
```

---

## 🚀 Próximos Pasos

1. ✅ Ejecutar `SQL_SISTEMA_PAQUETES_INTEGRADO_V2.sql`
2. ✅ Verificar que Admin Empresa ve Usuario, Perfil, Perfil Detalle
3. ✅ Verificar que Admin Empresa NO ve Modulo, Modulo Detalle
4. ✅ Crear filtros en PHP para menú dinámico
5. ✅ Crear helper `moduloDisponible($moduloSlug)`
6. ✅ Actualizar vistas para ocultar módulos no permitidos

---

**📅 Última actualización:** 11 Octubre 2025
**👤 Arquitecto:** NextLine System

