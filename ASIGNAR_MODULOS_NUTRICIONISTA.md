# Guía: Crear Perfil de Nutricionista y Asignar Módulos

## 📋 Módulos Necesarios para un Nutricionista

### Módulos Base (Obligatorios)
1. **Módulo 2: Inicio** 
   - Permiso: Solo ver
   - Descripción: Dashboard principal del sistema
   - Ruta: `/dashboard/menu`

2. **Módulo 1: Usuario**
   - Permisos: Ver y Editar (solo su propio perfil)
   - Descripción: Para que el nutricionista pueda ver y editar su información personal
   - Ruta: `/dashboard/usuario`

### Módulos de Nutricionistas (Esenciales)
3. **Módulo 33: Agenda**
   - Permisos: Ver, Registrar, Editar, Eliminar
   - Descripción: Gestión de agenda y citas de pacientes
   - Ruta: `/dashboard/agenda`
   - Funcionalidades:
     - Vista de calendario
     - Lista de citas
     - Agendar, confirmar y cancelar citas

4. **Módulo 34: Pacientes**
   - Permisos: Ver, Registrar, Editar, Eliminar
   - Descripción: Gestión de pacientes y su información clínica
   - Ruta: `/dashboard/paciente`
   - Funcionalidades:
     - Lista de pacientes
     - Registro y edición de pacientes
     - Información clínica (peso, altura, IMC, alergias, etc.)

5. **Módulo 35: Documentos**
   - Permisos: Ver, Registrar, Editar, Eliminar
   - Descripción: Gestión de documentos, pautas nutricionales y recetas
   - Ruta: `/dashboard/documento`
   - Funcionalidades:
     - Crear pautas nutricionales
     - Generar recetas
     - Enviar documentos a pacientes

6. **Módulo 36: Historial Clínico**
   - Permisos: Ver, Registrar, Editar, Eliminar
   - Descripción: Registro de consultas y evolución de pacientes
   - Ruta: `/dashboard/historial`
   - Funcionalidades:
     - Registrar consultas
     - Seguimiento de evolución
     - Historial completo por paciente

### Módulo Opcional
7. **Módulo 37: Pagos**
   - Permisos: Solo ver (o sin acceso)
   - Descripción: Gestión de pagos y suscripciones
   - Ruta: `/dashboard/pago`
   - Nota: Solo necesario si el nutricionista necesita ver información de pagos

## 🚀 Pasos para Crear el Perfil

### Opción 1: Usando el Script SQL
```sql
-- Ejecutar el archivo: crear_perfil_nutricionista.sql
-- Este script crea automáticamente el perfil y asigna todos los módulos
```

### Opción 2: Manual desde el Sistema
1. Ir a **Dashboard > Perfil > Registro**
2. Crear nuevo perfil:
   - Nombre: `Nutricionista`
   - Descripción: `Perfil para profesionales nutricionistas`
   - Estado: `Activo`
3. Ir a **Dashboard > Perfil Detalle > Registro**
4. Asignar cada módulo con sus permisos correspondientes

### Opción 3: Desde phpMyAdmin
1. Ejecutar el script `crear_perfil_nutricionista.sql`
2. Verificar que se creó correctamente:
   ```sql
   SELECT * FROM perfil WHERE nombre = 'Nutricionista';
   SELECT * FROM perfil_modulo WHERE perfil_id = 9;
   ```

## 👤 Asignar el Perfil a un Usuario

1. Ir a **Dashboard > Usuario > Editar** (del usuario que será nutricionista)
2. Seleccionar el perfil **"Nutricionista"** en el campo Perfil
3. Guardar cambios

O desde SQL:
```sql
UPDATE usuario 
SET perfil_id = 9 
WHERE id = [ID_DEL_USUARIO];
```

## ✅ Verificación

Para verificar que todo está correcto:

```sql
-- Ver el perfil creado
SELECT * FROM perfil WHERE id = 9;

-- Ver todos los módulos asignados al perfil
SELECT 
    pm.id,
    m.nombre as modulo,
    pm.ver,
    pm.registrar,
    pm.editar,
    pm.eliminar
FROM perfil_modulo pm
JOIN modulo m ON m.id = pm.modulo_id
WHERE pm.perfil_id = 9
ORDER BY pm.orden;
```

## 📊 Resumen de Permisos

| Módulo | Ver | Registrar | Editar | Eliminar | Orden |
|--------|-----|-----------|--------|----------|-------|
| Inicio | ✅ | ❌ | ❌ | ❌ | 1 |
| Usuario | ✅ | ❌ | ✅ | ❌ | 2 |
| Agenda | ✅ | ✅ | ✅ | ✅ | 3 |
| Pacientes | ✅ | ✅ | ✅ | ✅ | 4 |
| Documentos | ✅ | ✅ | ✅ | ✅ | 5 |
| Historial | ✅ | ✅ | ✅ | ✅ | 6 |
| Pagos | ✅ | ❌ | ❌ | ❌ | 7 |

## 🔒 Notas de Seguridad

- El perfil de nutricionista **NO** tiene acceso a:
  - Gestión de módulos del sistema
  - Gestión de perfiles
  - Gestión de usuarios (excepto su propio perfil)
  - Configuraciones del sistema

- El nutricionista **SÍ** puede:
  - Gestionar sus propios pacientes
  - Agendar y gestionar citas
  - Crear y enviar documentos
  - Registrar historial clínico
  - Ver su información de usuario
