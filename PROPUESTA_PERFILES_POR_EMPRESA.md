# 🎯 Propuesta: Sistema de Perfiles por Empresa

## 📋 Resumen Ejecutivo

Implementar un sistema donde cada nutricionista (empresa) pueda crear y gestionar sus propios perfiles personalizados, permitiendo asignar diferentes niveles de acceso a sus usuarios (pacientes, asistentes, etc.).

---

## 🏗️ Arquitectura Propuesta

### 1. **Perfiles por Empresa**
- Cada empresa (nutricionista) puede crear sus propios perfiles
- Los perfiles son independientes entre empresas
- Perfiles globales del sistema (Super Admin) siguen siendo globales

### 2. **Estructura de Datos**

#### Tabla `perfil` (modificada)
```sql
ALTER TABLE perfil ADD COLUMN empresa_id INT NULL;
```

- `empresa_id = NULL`: Perfiles globales del sistema (Super Admin, etc.)
- `empresa_id = X`: Perfiles específicos de la empresa X

#### Perfiles Predefinidos por Empresa
Cuando se crea una nueva empresa, se crean automáticamente:
- **"Nutricionista"**: Perfil completo con todos los módulos del paquete
- **"Paciente"**: Perfil limitado (solo ver sus propios datos)

---

## 🎨 Funcionalidades Propuestas

### 1. **Gestión de Perfiles por Empresa**

#### Vista: Lista de Perfiles
- El nutricionista ve solo los perfiles de su empresa
- Puede crear, editar, eliminar perfiles
- Puede ver qué usuarios tienen cada perfil

#### Vista: Crear/Editar Perfil
- Nombre del perfil
- Estado (Activo/Inactivo)
- Asignación de módulos y permisos:
  - Ver
  - Registrar
  - Editar
  - Eliminar
- Solo puede asignar módulos que están en su paquete

### 2. **Perfil "Paciente" Predefinido**

#### Características:
- **Acceso limitado**: Solo puede ver sus propios datos
- **Módulos disponibles**:
  - Ver sus citas (Agenda - solo las suyas)
  - Ver sus documentos (Documentos - solo los suyos)
  - Ver su historial clínico (Historial - solo el suyo)
  - Ver sus pagos (Pagos - solo los suyos)
  - Editar su perfil de usuario

#### Restricciones:
- No puede ver otros pacientes
- No puede crear/editar citas
- No puede acceder a configuraciones
- No puede ver reportes globales

### 3. **Creación de Usuario al Crear Paciente**

#### Opción en formulario de paciente:
```
☑ Crear usuario del sistema para este paciente
   Email: [campo email]
   Contraseña: [generar automáticamente o permitir ingresar]
   Perfil: [Paciente] (pre-seleccionado)
```

#### Flujo:
1. Nutricionista crea paciente
2. Si marca "Crear usuario":
   - Se crea registro en `pacientes`
   - Se crea registro en `usuario` con:
     - `empresa_id` = empresa del nutricionista
     - `perfil_id` = perfil "Paciente" de esa empresa
     - Email y contraseña
   - Se envía email al paciente con credenciales

### 4. **Asignación de Perfiles**

#### Al crear usuario manualmente:
- Dropdown de perfiles filtrado por empresa
- Solo muestra perfiles de la empresa del usuario actual

#### Al editar usuario:
- Puede cambiar el perfil
- Solo puede asignar perfiles de su empresa

---

## 🔧 Cambios Técnicos Necesarios

### 1. Base de Datos
- ✅ Script: `agregar_empresa_id_a_perfil.sql`
- ✅ Script: `crear_perfil_paciente_base.sql`

### 2. Modelo `Perfil`
- Agregar `empresa_id` a `allowedFields`
- Modificar métodos para filtrar por `empresa_id`:
  - `getPerfilAll()` → `getPerfilAll($empresaId = null)`
  - `getActivePerfil()` → `getActivePerfil($maxPoder, $empresaId = null)`
- Nuevo método: `getPerfilesPorEmpresa($empresaId)`

### 3. Controlador `PerfilController`
- Filtrar perfiles por empresa del usuario actual
- Validar que solo pueda crear/editar perfiles de su empresa
- Al crear perfil, asignar automáticamente `empresa_id`

### 4. Controlador `PacienteController`
- Agregar opción "Crear usuario" en formulario
- Al crear paciente, opcionalmente crear usuario
- Asignar perfil "Paciente" automáticamente

### 5. Controlador `UsuarioController`
- Filtrar perfiles por empresa al crear/editar usuario
- Validar que solo pueda asignar perfiles de su empresa

### 6. Vistas
- Actualizar formularios para mostrar solo perfiles de la empresa
- Agregar sección "Gestión de Perfiles" en dashboard del nutricionista

---

## 📊 Flujo de Usuario

### Escenario 1: Nutricionista crea un perfil personalizado

1. Nutricionista va a "Perfiles" → "Crear Perfil"
2. Ingresa nombre: "Asistente"
3. Selecciona módulos y permisos:
   - Agenda: Ver, Registrar, Editar
   - Pacientes: Ver, Registrar, Editar
   - Documentos: Ver, Registrar
   - (No puede eliminar ni ver configuraciones)
4. Guarda el perfil
5. Al crear un usuario "Asistente", puede asignarle este perfil

### Escenario 2: Crear paciente con acceso al sistema

1. Nutricionista va a "Pacientes" → "Nuevo Paciente"
2. Completa datos del paciente
3. Marca checkbox "Crear usuario del sistema"
4. Ingresa email del paciente
5. Sistema genera contraseña automáticamente
6. Al guardar:
   - Se crea el paciente
   - Se crea el usuario con perfil "Paciente"
   - Se envía email con credenciales
7. El paciente puede iniciar sesión y ver solo sus datos

---

## 🎯 Beneficios

1. **Flexibilidad**: Cada nutricionista personaliza los perfiles según sus necesidades
2. **Seguridad**: Los pacientes solo ven sus propios datos
3. **Escalabilidad**: Fácil agregar nuevos tipos de usuarios (asistentes, secretarias, etc.)
4. **Autonomía**: El nutricionista no depende del Super Admin para crear perfiles

---

## ⚠️ Consideraciones

1. **Migración de datos**: Los perfiles existentes necesitan `empresa_id`
2. **Perfiles globales**: Super Admin sigue viendo todos los perfiles
3. **Validación**: Asegurar que solo se asignen módulos del paquete de la empresa
4. **Permisos de pacientes**: Implementar lógica para que solo vean sus propios datos

---

## 🚀 Próximos Pasos

1. ✅ Crear scripts SQL para modificar estructura
2. ⏳ Modificar modelo `Perfil`
3. ⏳ Actualizar controladores
4. ⏳ Crear vistas de gestión de perfiles
5. ⏳ Implementar creación de usuario al crear paciente
6. ⏳ Agregar restricciones de acceso para pacientes

---

¿Te parece bien esta propuesta? ¿Quieres que implemente alguna parte específica primero?
