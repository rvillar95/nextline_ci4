# 🏢 Arquitectura Mono-Empresa - NextLine

## 📋 Resumen

Sistema simplificado para **una única empresa** donde el paquete contratado determina qué módulos ven los usuarios Admin.

---

## 🎯 Concepto

| Aspecto | Descripción |
|---------|-------------|
| **Empresas** | Solo **1 empresa activa** en el sistema |
| **Paquetes** | La empresa tiene asignado **1 paquete** (Presencia, Gestión, Custom) |
| **Usuarios** | **NO tienen `empresa_id`** (todos pertenecen a la única empresa) |
| **Módulos visibles** | Dependen del **paquete de la empresa** y del **poder del usuario** |

---

## 👥 Roles y Permisos

### 🔴 Super Admin (poder = 3)

| Puede Ver | Puede Hacer |
|-----------|-------------|
| ✅ **Todos los módulos** (incluye `Modulo` y `Modulo Detalle`) | • Cambiar paquete de la empresa |
| ✅ Todos los perfiles | • Crear otros Super Admins |
| ✅ Todos los usuarios | • Gestionar módulos del sistema |

### 🟡 Admin Empresa (poder = 2)

| Puede Ver | Puede Hacer |
|-----------|-------------|
| ✅ **Solo módulos del paquete** de la empresa | • Gestionar usuarios de la empresa |
| ✅ Perfiles con poder < 2 | • Asignar permisos de módulos del paquete |
| ✅ Usuarios con poder < 2 | • Configurar datos de la empresa |
| ❌ NO ve `Modulo` ni `Modulo Detalle` | ❌ NO puede cambiar el paquete |

### 🟢 Usuario Normal (poder < 2)

| Puede Ver | Puede Hacer |
|-----------|-------------|
| ✅ **Solo módulos según su perfil** | • Usar módulos según permisos asignados |
| ❌ NO gestiona perfiles ni usuarios | • Ver/Editar datos según su rol |

---

## 📦 Paquetes Disponibles

### NextLine Presencia

| Precio Setup | Precio Mensual | Incluye |
|--------------|----------------|---------|
| $149.000 | $19.990 | Inicio, Usuarios, Perfiles, Empresa, Servicios, Galería, Proyectos, Testimonios, Leads |

**Ideal para:** Sitios web de presentación profesional

### NextLine Gestión

| Precio Setup | Precio Mensual | Incluye |
|--------------|----------------|---------|
| $269.000 | $49.990 | **Todo de Presencia** + Clientes, Cotizaciones |

**Ideal para:** Empresas que necesitan CRM y gestión de ventas

### NextLine Custom

| Precio Setup | Precio Mensual | Incluye |
|--------------|----------------|---------|
| A cotizar | A cotizar | **Todo** + desarrollos personalizados |

**Ideal para:** Soluciones a medida con requerimientos especiales

---

## 🔄 Flujo de Datos

```
┌─────────────────────────────────────────────────────────┐
│  1. Usuario inicia sesión                               │
│     └─> Se guarda en sesión: poder (no empresa_id)    │
└─────────────────────────────────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│  2. BaseController carga datos del usuario             │
│     └─> $this->poder (de la sesión)                   │
└─────────────────────────────────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│  3. PerfilDetalleController carga módulos              │
│     └─> getModulosByPaqueteEmpresa($this->poder)      │
└─────────────────────────────────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│  4. Modelo Modulo decide qué devolver                  │
│     ┌───────────────────────────────────────────────┐  │
│     │ SI poder >= 3 (Super Admin)                   │  │
│     │   └─> Devuelve TODOS los módulos activos      │  │
│     │                                                │  │
│     │ SI poder < 3 (Admin/Usuario)                  │  │
│     │   └─> JOIN empresa → paquete → módulos        │  │
│     │   └─> Solo módulos con sa='N'                 │  │
│     └───────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                          ▼
┌─────────────────────────────────────────────────────────┐
│  5. Vista muestra solo módulos permitidos ✅           │
└─────────────────────────────────────────────────────────┘
```

---

## 💻 Implementación Técnica

### Modelo: `app/Models/Modulo.php`

```php
public function getModulosByPaqueteEmpresa(int $poderUsuario): array
{
    $db = \Config\Database::connect();
    
    // Super Admin ve todos los módulos
    if ($poderUsuario >= 3) {
        $sql = "SELECT * FROM modulo WHERE estado = 'A' ORDER BY nombre";
        return $db->query($sql)->getResultArray();
    }
    
    // Admin/Usuarios ven solo módulos del paquete de la empresa activa
    $sql = "
        SELECT DISTINCT m.*
        FROM modulo m
        INNER JOIN paquete_modulo pm ON m.id = pm.modulo_id
        INNER JOIN empresa e ON e.paquete_id = pm.paquete_id
        WHERE e.estado = 'A'
          AND m.estado = 'A'
          AND m.sa = 'N'
        ORDER BY m.nombre
    ";
    
    return $db->query($sql)->getResultArray();
}
```

### Controlador: `app/Controllers/Dashboard/PerfilDetalleController.php`

```php
public function registro()
{
    // ...
    $moduloModel = new Modulo();
    
    // Filtrar módulos por paquete de la empresa activa
    $data['modulos'] = $moduloModel->getModulosByPaqueteEmpresa($this->poder);
    
    return view('Base/perfil_detalle/registro', $data);
}
```

### Vista: `app/Views/Modulos/empresa/registro.php`

```php
<?php if ($poder_usuario >= 3): ?>
    <!-- Solo Super Admin ve este campo -->
    <div class="form-group">
        <label for="paquete_id">Paquete Contratado</label>
        <select name="paquete_id" id="paquete_id" class="form-select">
            <option value="">Seleccione un paquete...</option>
            <?php foreach ($paquetes as $paquete): ?>
                <option value="<?= $paquete['id'] ?>">
                    <?= $paquete['nombre'] ?> - $<?= number_format($paquete['precio_mensual']) ?>/mes
                </option>
            <?php endforeach; ?>
        </select>
    </div>
<?php endif; ?>
```

---

## 📊 Base de Datos

### Tabla: `paquetes`

```sql
CREATE TABLE `paquetes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `descripcion` text,
  `precio_setup` decimal(10,2) DEFAULT 0,
  `precio_mensual` decimal(10,2) DEFAULT 0,
  `activo` char(1) DEFAULT 'A',
  `orden` int DEFAULT 0,
  PRIMARY KEY (`id`)
);
```

### Tabla: `paquete_modulo`

```sql
CREATE TABLE `paquete_modulo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paquete_id` int NOT NULL,
  `modulo_id` int NOT NULL,
  `incluido` char(1) DEFAULT 'S',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`paquete_id`, `modulo_id`),
  FOREIGN KEY (`paquete_id`) REFERENCES `paquetes` (`id`),
  FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`)
);
```

### Tabla: `empresa` (modificada)

```sql
ALTER TABLE empresa ADD COLUMN paquete_id int NULL;
ALTER TABLE empresa ADD FOREIGN KEY (paquete_id) REFERENCES paquetes(id);
```

### Tabla: `modulo` (modificada)

```sql
-- Campo 'sa' indica si es exclusivo de Super Admin
UPDATE modulo SET sa = 'S' WHERE id IN (4, 7);  -- Modulo, Modulo Detalle
UPDATE modulo SET sa = 'N' WHERE id NOT IN (4, 7);
```

---

## 🚀 Instalación

### 1. Ejecutar SQL

```bash
mysql -u root -p nextline_pyme < SQL_SISTEMA_PAQUETES_SIMPLE.sql
```

O ejecutar desde phpMyAdmin.

### 2. Verificar Datos

```sql
-- Ver paquetes creados
SELECT * FROM paquetes;

-- Ver paquete asignado a la empresa
SELECT nombre, paquete_id FROM empresa WHERE estado = 'A';

-- Ver módulos por paquete
SELECT p.nombre as paquete, m.nombre as modulo
FROM paquete_modulo pm
JOIN paquetes p ON pm.paquete_id = p.id
JOIN modulo m ON pm.modulo_id = m.id
ORDER BY p.nombre, m.nombre;
```

### 3. Probar en el Dashboard

1. **Login como Super Admin (poder=3)**
   - Ve todos los módulos en Perfil Detalle
   - Ve dropdown de "Paquete Contratado" en Empresa

2. **Login como Admin (poder=2)**
   - Ve solo módulos del paquete asignado
   - NO ve dropdown de "Paquete Contratado"
   - NO ve módulos "Modulo" ni "Modulo Detalle"

---

## ✅ Ventajas de la Arquitectura Mono-Empresa

| Ventaja | Descripción |
|---------|-------------|
| 🔹 **Simplicidad** | No necesita `empresa_id` en usuarios |
| 🔹 **Rendimiento** | Consultas SQL más simples |
| 🔹 **Mantenimiento** | Menos relaciones en BD |
| 🔹 **Flexibilidad** | Fácil cambiar de paquete |
| 🔹 **Escalabilidad** | Puede migrar a multi-tenant si es necesario |

---

## 📝 Notas Importantes

1. **Solo 1 empresa activa:** Si tienes múltiples registros en `empresa`, solo el primero con `estado='A'` se considera.

2. **Módulos Super Admin:** Los módulos con `sa='S'` (id=4 y 7) NUNCA se asignan a paquetes. Solo los ve el Super Admin.

3. **Módulos comunes:** Usuario (1), Perfil (3), Perfil Detalle (6), Empresa (31) e Inicio (2) están en TODOS los paquetes para que Admin pueda gestionar su equipo.

4. **Cambio de paquete:** Solo el Super Admin puede cambiar el paquete desde el módulo Empresa. El cambio es inmediato para todos los usuarios Admin.

5. **Precio mostrado:** El dropdown muestra el precio mensual para facilitar la decisión del paquete a asignar.

---

## 🎉 ¡Sistema Listo!

Con esta arquitectura simplificada, tienes un sistema robusto y fácil de mantener para una única empresa, con la flexibilidad de cambiar de paquete según las necesidades del negocio. 🚀

