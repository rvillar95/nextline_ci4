# 🔐 Control de Acceso: Paquetes vs Perfiles

## 📋 Resumen

El sistema tiene **DOS niveles de control de acceso**:

1. **Paquete (Nivel Empresa/Cliente)**: Define qué módulos están **disponibles** para la empresa que contrata el sistema
2. **Perfil (Nivel Usuario Final)**: Define qué módulos puede **ver/acceder** cada usuario dentro de esa empresa

## 🎯 Concepto Clave

**Paquetes = Para EMPRESAS (clientes que contratan el sistema)**
- Ejemplo: "Clínica Nutrición" contrata el paquete "NextLine Nutrición"
- Define qué módulos tiene disponibles esa empresa

**Perfiles = Para USUARIOS FINALES (personas que usan el sistema)**
- Ejemplo: Nutricionistas, Pacientes, Administradores dentro de esa empresa
- Define qué módulos puede ver cada usuario según su rol

## 🔧 Cómo Funciona

### Nivel 1: Paquete (Empresa/Cliente)
- Cada **empresa** (cliente que contrata el sistema) tiene un `paquete_id`
- El paquete define qué módulos están **incluidos** en `paquete_modulo`
- **Ejemplo**: 
  - Empresa: "Clínica Nutrición" 
  - Paquete: "NextLine Nutrición" (id=4)
  - Módulos disponibles: 33-39 (Agenda, Pacientes, Documentos, etc.)

### Nivel 2: Perfil (Usuario Final)
- Cada **usuario** (persona que usa el sistema) tiene un `perfil_id`
- El perfil define qué módulos puede **ver** en `perfil_modulo`
- **Ejemplo**: 
  - Usuario: "Dr. Juan Pérez" (Nutricionista)
  - Perfil: "Nutricionista" (id=9)
  - Puede ver: Módulos 33-39 (tiene permisos en `perfil_modulo`)
  
  - Usuario: "María González" (Paciente)
  - Perfil: "Paciente" (id=10, si existe)
  - Puede ver: Solo módulos limitados (ej: ver sus citas, documentos propios)

### Flujo de Control de Acceso

**⚠️ IMPORTANTE: El sistema actual tiene una inconsistencia**

#### Generación del Menú (lo que el usuario VE)
```
Usuario inicia sesión
    ↓
Sistema llama: ModuloDetalle::getMenu(perfil_id)
    ↓
Consulta: perfil_modulo WHERE perfil_id = X
    ↓
Muestra módulos según el PERFIL
```

#### Verificación de Acceso (lo que el sistema PERMITE)
```
Usuario intenta acceder a un módulo
    ↓
Sistema llama: fn_usuario_puede_ver_modulo(usuario_id, modulo_id)
    ↓
Consulta: paquete_modulo WHERE paquete_id = empresa.paquete_id
    ↓
Permite acceso según el PAQUETE
```

**⚠️ Problema:**
- El menú se genera según el **PERFIL**
- Pero el acceso se verifica según el **PAQUETE**
- Si hay módulos en el perfil que NO están en el paquete → El usuario los verá pero no podrá acceder
- Si hay módulos en el paquete que NO están en el perfil → El usuario no los verá en el menú

**✅ Solución:**
- Asegurar que los módulos en `perfil_modulo` (perfil) estén también en `paquete_modulo` (paquete)
- O modificar el sistema para que use ambos niveles correctamente

## ✅ Respuesta a tu Pregunta

**¿Los pacientes pueden ver lo mismo que un nutricionista?**

**NO**, porque:

1. **El paquete es para la EMPRESA** (el cliente que contrata el sistema)
   - Define qué módulos tiene disponibles la empresa
   - Todos los usuarios de esa empresa comparten el mismo paquete
   
2. **El perfil controla qué ve cada USUARIO FINAL**
   - Cada usuario tiene un perfil diferente (Nutricionista, Paciente, Admin, etc.)
   - Cada perfil tiene módulos diferentes asignados en `perfil_modulo`
   
3. **Si no existe un perfil "Paciente"** o ese perfil no tiene módulos asignados, los pacientes **NO verán nada**

## 📊 Estructura Completa

```
EMPRESA (Cliente que contrata)
  ├── paquete_id = 4 (NextLine Nutrición)
  │   └── Módulos disponibles: 33-39
  │
  └── USUARIOS (Personas que usan el sistema)
      ├── Usuario 1: perfil_id = 9 (Nutricionista)
      │   └── Puede ver: Módulos 33-39 (según perfil_modulo)
      │
      ├── Usuario 2: perfil_id = 10 (Paciente)
      │   └── Puede ver: Solo módulos limitados (según perfil_modulo)
      │
      └── Usuario 3: perfil_id = 8 (Administrador)
          └── Puede ver: Todos los módulos del paquete (según perfil_modulo)
```

## 🎯 Ejemplo Práctico

### Escenario: Empresa de Nutricionistas

**Empresa:**
- `id`: 1
- `nombre`: "Clínica Nutrición"
- `paquete_id`: 4 (NextLine Nutrición)

**Usuarios:**

1. **Nutricionista (Usuario A)**
   - `perfil_id`: 9 (Nutricionista)
   - `empresa_id`: 1
   - **Puede ver**: Módulos 33-39 (porque tiene permisos en `perfil_modulo`)

2. **Paciente (Usuario B)**
   - `perfil_id`: ? (¿Existe perfil Paciente?)
   - `empresa_id`: 1
   - **Puede ver**: Solo los módulos asignados a su perfil en `perfil_modulo`

## 🔍 Verificar Estado Actual

### 1. ¿Existe perfil "Paciente"?
```sql
SELECT id, nombre, poder, estado 
FROM perfil 
WHERE nombre LIKE '%Paciente%' OR nombre LIKE '%paciente%';
```

### 2. ¿Qué módulos puede ver el perfil de Nutricionista?
```sql
SELECT 
    pm.perfil_id,
    p.nombre AS perfil_nombre,
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.ver,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.perfil_id = 9  -- Nutricionista
  AND pm.estado = 'A'
ORDER BY pm.modulo_id;
```

### 3. ¿Qué módulos puede ver el perfil de Paciente? (si existe)
```sql
SELECT 
    pm.perfil_id,
    p.nombre AS perfil_nombre,
    pm.modulo_id,
    m.nombre AS modulo_nombre,
    pm.ver,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE p.nombre LIKE '%Paciente%'
  AND pm.estado = 'A'
ORDER BY pm.modulo_id;
```

## 💡 Recomendaciones

### Opción A: Crear Perfil "Paciente" con Módulos Limitados

Si los pacientes necesitan acceso al sistema, crear:

1. **Perfil "Paciente"** (ej: id=10)
2. **Asignar solo módulos relevantes** en `perfil_modulo`:
   - Ver sus propias citas (solo lectura)
   - Ver sus documentos (solo lectura)
   - Ver su historial (solo lectura)
   - **NO** acceso a: Agenda completa, Gestión de pacientes, Configuraciones

### Opción B: Los Pacientes NO Acceden al Dashboard

Si los pacientes NO necesitan acceso al dashboard:
- No crear perfil "Paciente"
- Los pacientes solo reciben notificaciones (Email, WhatsApp)
- Toda la gestión se hace desde el perfil "Nutricionista"

## 📝 Próximos Pasos

1. **Decidir**: ¿Los pacientes necesitan acceso al dashboard?
2. **Si SÍ**: Crear perfil "Paciente" con módulos limitados
3. **Si NO**: No hacer nada, el sistema ya está protegido por `perfil_modulo`

## 🔗 Referencias

- `app/Models/ModuloDetalle.php` - Método `getMenu($perfil)` usa `perfil_modulo`
- `nextline_pyme.sql` - Procedimiento `sp_obtener_menu_usuario` considera paquete Y perfil
- Tabla `perfil_modulo` - Control de acceso por perfil
- Tabla `paquete_modulo` - Control de disponibilidad por paquete
