# 🏥 Propuesta: Portal del Paciente

## 🎯 Objetivo

Permitir que los pacientes accedan al sistema para ver:
- Sus avances (peso, IMC, mediciones)
- Sus dietas y planes nutricionales
- Sus citas y próximas consultas
- Sus documentos (recetas, pautas)
- Sus logros y metas alcanzadas

**Con control total del nutricionista sobre qué información puede ver cada paciente.**

---

## 📊 Arquitectura de Base de Datos

### 1. Relación Usuario ↔ Paciente

```sql
-- Agregar campo opcional en pacientes para vincular con usuario
ALTER TABLE pacientes 
ADD COLUMN usuario_id INT NULL UNIQUE,
ADD FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE SET NULL;

-- Índice para búsquedas rápidas
CREATE INDEX idx_pacientes_usuario ON pacientes(usuario_id);
```

**Ventajas:**
- ✅ Un paciente puede tener usuario (opcional)
- ✅ Un usuario puede no ser paciente (nutricionista, admin)
- ✅ Sin duplicación de datos cuando hay usuario
- ✅ Mantiene separación de responsabilidades

### 2. Estructura de Datos

```
usuario (id, nombre, email, password, perfil_id, empresa_id)
    ↓ (1:1 opcional)
paciente (id, usuario_id, nombre, apellido, email, peso_inicial...)
```

**Casos de uso:**
- **Paciente con usuario**: Tiene login, puede ver su portal
- **Paciente sin usuario**: Solo datos clínicos, sin acceso al sistema

---

## 🔐 Control de Acceso

### 1. Perfil "Paciente"

Ya existe el perfil "Paciente" (id=10) creado en `crear_perfil_paciente_base.sql`.

**Características:**
- `empresa_id`: Vinculado a la empresa del nutricionista
- Permisos limitados: Solo lectura de sus propios datos

### 2. Módulo "Mi Portal" o "Mi Área"

Crear un nuevo módulo específico para pacientes:

```sql
-- Módulo: Mi Portal (para pacientes)
INSERT INTO modulo (nombre, ruta, descripcion, sa, estado, mostrar, orden) 
VALUES ('Mi Portal', 'dashboard/mi-portal', 'Portal personal del paciente', 'N', 'A', 'S', 50);

-- Submódulos:
-- - Mi Perfil (ver/editar datos personales básicos)
-- - Mis Avances (gráficos de peso, IMC, mediciones)
-- - Mis Citas (ver próximas citas, historial)
-- - Mis Documentos (ver documentos compartidos por nutricionista)
-- - Mis Logros (metas alcanzadas, badges)
```

### 3. Restricciones de Acceso

**Regla de oro:** Un paciente SOLO puede ver/editar sus propios datos.

**Implementación en controladores:**

```php
// En cualquier controlador de "Mi Portal"
public function misAvances() {
    $usuarioId = session()->get('usuario')['id'];
    
    // Obtener paciente_id desde usuario_id
    $paciente = $this->pacienteModel
        ->where('usuario_id', $usuarioId)
        ->first();
    
    if (!$paciente) {
        return redirect()->to('/dashboard/mi-portal')
            ->with('errors', 'No se encontró información de paciente');
    }
    
    // Solo mostrar datos de ESTE paciente
    $avances = $this->historialModel
        ->where('paciente_id', $paciente->id)
        ->findAll();
    
    // Pasar a vista
    return view('mi_portal/avances', ['avances' => $avances]);
}
```

---

## 🎨 Módulos del Portal del Paciente

### 1. **Mi Perfil**
- Ver/editar datos personales básicos (nombre, teléfono, dirección)
- NO puede editar: peso, altura, IMC (solo el nutricionista)
- Ver información médica (alergias, medicamentos) - solo lectura

### 2. **Mis Avances**
- Gráfico de evolución de peso
- Gráfico de IMC
- Historial de mediciones
- Comparación antes/después
- Filtros por fecha

### 3. **Mis Citas**
- Próximas citas (calendario)
- Historial de citas pasadas
- Estado de cada cita
- Notas del nutricionista (si las comparte)

### 4. **Mis Documentos**
- Pautas nutricionales
- Recetas
- Planes de alimentación
- Documentos compartidos por el nutricionista
- Descargar PDFs

### 5. **Mis Logros**
- Metas alcanzadas
- Badges/insignias
- Progreso hacia objetivos
- Mensajes motivacionales del nutricionista

### 6. **Configuración de Notificaciones** (Opcional)
- Preferencias de email
- Preferencias de WhatsApp
- Recordatorios de citas

---

## 🔧 Implementación Técnica

### 1. Modificar Tabla `pacientes`

```sql
-- Script: agregar_usuario_id_a_pacientes.sql
ALTER TABLE pacientes 
ADD COLUMN usuario_id INT NULL UNIQUE COMMENT 'ID del usuario si el paciente tiene acceso al sistema',
ADD FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE SET NULL;

CREATE INDEX idx_pacientes_usuario ON pacientes(usuario_id);
```

### 2. Modificar `PacienteController`

**Al crear paciente:**
```php
public function registrar() {
    // ... validaciones ...
    
    // Crear paciente
    $pacienteId = $pacienteModel->insert($dataPaciente);
    
    // Si se marcó "Crear usuario"
    if ($post['crear_usuario'] == 'S') {
        // Crear usuario
        $usuarioData = [
            'nombre' => $post['nombre'],
            'apellido' => $post['apellido'],
            'email' => $post['email'],
            'password' => password_hash($passwordGenerada, PASSWORD_DEFAULT),
            'perfil_id' => $perfilPacienteId, // Perfil "Paciente" de la empresa
            'empresa_id' => session()->get('usuario')['empresa_id'],
            'estado' => 'A'
        ];
        
        $usuarioId = $usuarioModel->insert($usuarioData);
        
        // Vincular paciente con usuario
        $pacienteModel->update($pacienteId, ['usuario_id' => $usuarioId]);
        
        // Enviar email con credenciales
        $this->enviarCredenciales($post['email'], $passwordGenerada);
    }
}
```

### 3. Crear `MiPortalController`

```php
namespace App\Controllers\Dashboard;

class MiPortalController extends BaseController {
    
    private function getPacienteId() {
        $usuarioId = session()->get('usuario')['id'];
        $paciente = model('Paciente')
            ->where('usuario_id', $usuarioId)
            ->first();
        
        if (!$paciente) {
            throw new \Exception('Usuario no tiene paciente asociado');
        }
        
        return $paciente->id;
    }
    
    public function index() {
        $pacienteId = $this->getPacienteId();
        // Dashboard principal con resumen
    }
    
    public function avances() {
        $pacienteId = $this->getPacienteId();
        // Solo avances de este paciente
    }
    
    public function citas() {
        $pacienteId = $this->getPacienteId();
        // Solo citas de este paciente
    }
    
    // ... más métodos
}
```

### 4. Control de Visibilidad (Nutricionista decide qué ver)

**Tabla nueva: `paciente_configuracion`**

```sql
CREATE TABLE paciente_configuracion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paciente_id INT NOT NULL,
    mostrar_avances ENUM('S', 'N') DEFAULT 'S',
    mostrar_documentos ENUM('S', 'N') DEFAULT 'S',
    mostrar_notas ENUM('S', 'N') DEFAULT 'N',
    mostrar_objetivos ENUM('S', 'N') DEFAULT 'S',
    empresa_id INT NOT NULL,
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE CASCADE,
    FOREIGN KEY (empresa_id) REFERENCES empresa(id) ON DELETE CASCADE
);
```

**El nutricionista puede configurar:**
- ✅ Mostrar/Ocultar avances
- ✅ Mostrar/Ocultar documentos
- ✅ Mostrar/Ocultar notas de consultas
- ✅ Mostrar/Ocultar objetivos

---

## 📱 Flujo de Usuario

### Para el Nutricionista:

1. **Crear Paciente**
   ```
   Formulario de paciente
   ☑ Crear usuario del sistema para este paciente
      Email: paciente@ejemplo.com
      Contraseña: [Generar automáticamente]
   ```

2. **Configurar Visibilidad**
   ```
   En perfil del paciente:
   - Mostrar avances: ✅
   - Mostrar documentos: ✅
   - Mostrar notas: ❌
   - Mostrar objetivos: ✅
   ```

3. **Compartir Documentos**
   ```
   Subir documento → Marcar "Compartir con paciente"
   → El paciente lo verá en "Mis Documentos"
   ```

### Para el Paciente:

1. **Recibe Email con Credenciales**
   ```
   "Bienvenido a NextLine!
   Tu usuario: paciente@ejemplo.com
   Tu contraseña: xxxxxx
   Accede en: https://..."
   ```

2. **Inicia Sesión**
   ```
   Login → Dashboard "Mi Portal"
   ```

3. **Ve Solo Sus Datos**
   ```
   - Mis Avances: Solo sus gráficos
   - Mis Citas: Solo sus citas
   - Mis Documentos: Solo documentos compartidos
   ```

---

## 🔒 Seguridad

### 1. Validación en Cada Controlador

```php
// Middleware o método helper
private function validarAccesoPaciente($pacienteId) {
    $usuarioId = session()->get('usuario')['id'];
    $perfilId = session()->get('usuario')['perfil_id'];
    
    // Si es perfil "Paciente", solo puede ver sus propios datos
    if ($perfilId == $perfilPacienteId) {
        $paciente = model('Paciente')
            ->where('usuario_id', $usuarioId)
            ->where('id', $pacienteId)
            ->first();
        
        if (!$paciente) {
            throw new \Exception('No tiene permisos para acceder a estos datos');
        }
    }
}
```

### 2. Filtros en Queries

```php
// SIEMPRE filtrar por paciente_id cuando es perfil "Paciente"
$query = model('HistorialClinico')
    ->where('paciente_id', $pacienteId);

// Si es nutricionista, puede ver todos los pacientes de su empresa
if ($perfilId != $perfilPacienteId) {
    // No filtrar, pero validar empresa_id
}
```

---

## 📋 Checklist de Implementación

### Fase 1: Base de Datos
- [ ] Agregar `usuario_id` a tabla `pacientes`
- [ ] Crear tabla `paciente_configuracion` (opcional)
- [ ] Verificar que perfil "Paciente" existe

### Fase 2: Módulo "Mi Portal"
- [ ] Crear módulo en BD
- [ ] Crear `MiPortalController`
- [ ] Crear vistas (avances, citas, documentos, logros)
- [ ] Asignar módulo al perfil "Paciente"

### Fase 3: Integración
- [ ] Modificar `PacienteController` para crear usuario opcional
- [ ] Agregar opción "Crear usuario" en formulario
- [ ] Implementar envío de credenciales por email

### Fase 4: Control de Visibilidad
- [ ] Crear interfaz para nutricionista configurar visibilidad
- [ ] Implementar filtros en controladores según configuración
- [ ] Agregar opción "Compartir documento" en módulo de documentos

### Fase 5: Testing
- [ ] Probar creación de paciente con usuario
- [ ] Probar acceso del paciente (solo ve sus datos)
- [ ] Probar que nutricionista puede configurar visibilidad
- [ ] Probar compartir documentos

---

## 💡 Consideraciones Futuras

1. **App Móvil**: El portal del paciente puede ser la base para una app móvil
2. **Notificaciones Push**: Integrar notificaciones en tiempo real
3. **Chat con Nutricionista**: Mensajería directa (opcional)
4. **Gamificación**: Más badges, logros, desafíos
5. **Integración con Wearables**: Sincronizar datos de dispositivos

---

## 🎯 Conclusión

Esta arquitectura permite:
- ✅ Pacientes acceden al sistema de forma segura
- ✅ Solo ven sus propios datos
- ✅ Nutricionista controla qué información se muestra
- ✅ Escalable para futuras funcionalidades
- ✅ Mantiene separación de responsabilidades (Usuario vs Paciente)

**¿Te parece bien esta propuesta? ¿Quieres que empecemos a implementarla?**
