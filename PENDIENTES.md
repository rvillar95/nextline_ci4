# 📋 Lista de Pendientes - NextLine CI4

## ✅ Completado

### 1. Módulo "Cancelar Horas Masivamente"
- ✅ Módulo creado en BD con permisos
- ✅ Formulario con selector de fechas (Flatpickr)
- ✅ Vista previa de citas con pacientes
- ✅ Mensajes personalizados por paciente
- ✅ Modal de confirmación
- ✅ Cancelación masiva de todas las citas en el rango
- ✅ Eliminación automática de eventos de Google Calendar
- ✅ Envío de notificaciones WhatsApp y Email
- ✅ Manejo correcto del token CSRF
- ✅ Citas canceladas se muestran en rojo en el calendario
- ✅ Botón en el header del calendario

### 2. Módulo de Configuraciones del Sistema
- ✅ Módulo creado en BD con permisos
- ✅ Vista de configuraciones con secciones:
  - Notificaciones (Email, WhatsApp, Recordatorios)
  - Integración con Calendario
  - Mensajes de Cancelación Masiva (con TinyMCE)
- ✅ Todas las configuraciones conectadas al proceso de notificaciones:
  - `enviar_email` - al agendar una cita
  - `enviar_whatsapp` - al confirmar una cita
  - `crear_evento_calendario` - al confirmar una cita
  - `agregar_paciente_como_invitado` - al crear eventos en calendario
  - `enviar_recordatorios_whatsapp` - en el comando de recordatorios
  - `horas_antes_recordatorio` - personalizado por nutricionista
- ✅ Mensajes de cancelación masiva configurables por estado (Pendiente, Confirmada, En Proceso)

### 3. Mejoras en Calendario
- ✅ Vista semanal como vista inicial
- ✅ Altura de slots aumentada (60px)
- ✅ Padding mejorado en vista diaria
- ✅ Divider con altura 0
- ✅ Citas canceladas se muestran en rojo

### 4. Sistema de Tags en Fichas Clínicas
- ✅ Campo `tags` (JSON) agregado a `historial_clinico` y `detalle_agenda`
- ✅ Tabla `historial_tags` creada para normalización y tracking
- ✅ Input de tags con Tagify.js en formularios de historial y consulta
- ✅ Búsqueda por tags implementada en módulo de Historial
- ✅ Autocompletado dinámico de tags con sugerencias
- ✅ Sincronización de tags entre detalle_agenda e historial_clinico
- ✅ Corrección de formato de guardado (strings simples en JSON)
- ✅ Corrección de errores SQL (ambigüedad de columnas, comillas en JSON_SEARCH)

### 5. Sistema de Paquetes para Nutricionistas
- ✅ Paquete "NextLine Nutrición" creado (id=4)
- ✅ Módulos asignados al paquete:
  - 33: Agenda
  - 34: Pacientes
  - 35: Documentos
  - 36: Historial Clínico
  - 37: Pagos
  - 38: Configuraciones
  - 39: Cancelar Horas
- ✅ Vista `vista_modulos_por_paquete` verificada y funcionando
- ✅ Módulo de Gestión de Paquetes creado (id=40) - Solo Super Admin
  - Lista de paquetes con DataTable (con filtros funcionando)
  - Crear/Editar paquetes
  - Gestionar módulos de cada paquete (con manejo correcto de CSRF)
  - Ver detalle de paquetes (módulos incluidos, empresas asignadas)
  - Activar/Desactivar paquetes
- ✅ Control de acceso unificado (paquetes + perfiles)
- ✅ Módulo de Gestión de Empresas mejorado para Super Admin
  - Lista completa de empresas con DataTable
  - Asignación de paquetes a empresas
  - CRUD completo de empresas
- ✅ Campo `empresa_id` agregado a usuarios
- ✅ Configuraciones migradas de usuario a empresa (`empresa_configuraciones`)
- ✅ Menú dinámico filtrando por paquete de la empresa del usuario
- ✅ Edición inline de campos en módulo_detalle (Mostrar, Estado)
- ✅ Corrección de errores CSRF en múltiples módulos:
  - `modulo_detalle/update-campo-inline`
  - `paquete/guardar-modulos`
  - `perfil-detalle/updateOrden`

## 🔄 Pendiente

### 1. Sistema de Paquetes para Nutricionistas
**Prioridad: Alta** ✅ **COMPLETADO - CERRADO**

**Descripción:**
- Implementar sistema de paquetes similar a NextLine Presencia
- Crear paquete "NextLine Nutrición" para módulos de nutricionistas
- Asignar módulos de nutricionistas al paquete correspondiente
- Crear módulo de gestión de paquetes para Super Admin
- Verificar que el sistema de permisos funcione correctamente

**Estado:** ✅ **COMPLETADO** - Sistema funcionando correctamente. Pendiente de revisión si surgen necesidades o fixes.

**Tareas:**
- [x] Crear paquete "NextLine Nutrición" en la tabla `paquetes` (id=4)
- [x] Asignar módulos de nutricionistas al paquete (33: Agenda, 34: Pacientes, 35: Documentos, 36: Historial, 37: Pagos, 38: Configuraciones, 39: Cancelar Horas)
- [x] Verificar que los módulos aparezcan en `vista_modulos_por_paquete`
- [x] Crear módulo de gestión de paquetes (id=40) - Solo Super Admin
  - [x] Controller `PaqueteController` con CRUD completo
  - [x] Vista de lista con DataTable (con filtros funcionando)
  - [x] Vista de crear/editar paquete
  - [x] Vista de gestionar módulos del paquete (con manejo correcto de CSRF)
  - [x] Vista de detalle del paquete
- [x] Unificar control de acceso (paquetes + perfiles) en `ModuloDetalle`
- [x] Mejorar módulo de gestión de empresas para Super Admin
  - [x] Lista completa de empresas con DataTable
  - [x] Asignación de paquetes a empresas
  - [x] CRUD completo de empresas
- [x] Agregar campo `empresa_id` a usuarios
- [x] Migrar configuraciones de usuario a empresa
- [x] Menú dinámico filtrando por paquete de la empresa
- [x] Corrección de errores CSRF en múltiples módulos
- [x] Edición inline de campos en módulo_detalle
- [x] Sistema de Perfiles por Empresa implementado
  - [x] Módulo "Perfiles de Empresa" creado (id=41)
  - [x] Nutricionistas pueden gestionar perfiles de su empresa
  - [x] Nutricionistas pueden asignar módulos del paquete a perfiles
  - [x] Rutas de perfil-detalle corregidas y funcionando

**Archivos creados:**
- ✅ `crear_paquete_nutricion.sql` - Script SQL para crear el paquete y asignar módulos (EJECUTADO)
- ✅ `asignar_paquete_a_empresas.sql` - Script para asignar el paquete a empresas de nutricionistas
- ✅ `crear_modulo_paquetes.sql` - Script SQL para crear el módulo de gestión de paquetes
- ✅ `insertar_modulo_detalle_paquetes.sql` - Script para insertar rutas del módulo paquetes
- ✅ `migrar_configuracion_a_empresa.sql` - Script para migrar configuraciones de usuario a empresa
- ✅ `crear_modulo_perfiles_empresa.sql` - Script para crear módulo "Perfiles de Empresa"
- ✅ `asignar_modulo_perfiles_empresa.sql` - Script para asignar módulo a paquete y perfil
- ✅ `corregir_rutas_perfil_detalle_final.sql` - Script para corregir rutas absolutas
- ✅ `app/Controllers/Dashboard/PaqueteController.php` - Controller completo
- ✅ `app/Controllers/Dashboard/EmpresaController.php` - Controller mejorado para Super Admin
- ✅ `app/Controllers/Dashboard/PerfilController.php` - Controller modificado para perfiles por empresa
- ✅ `app/Controllers/Dashboard/PerfilDetalleController.php` - Controller modificado para filtrar por paquete
- ✅ `app/Models/Paquete.php` - Model para paquetes
- ✅ `app/Models/EmpresaConfiguracion.php` - Model para configuraciones por empresa
- ✅ `app/Models/Perfil.php` - Model modificado para filtrar por empresa
- ✅ `app/Models/ModuloDetalle.php` - Model modificado para filtrar por paquete y perfil
- ✅ `app/Views/Modulos/paquete/lista.php` - Vista de lista
- ✅ `app/Views/Modulos/paquete/registro.php` - Vista crear/editar
- ✅ `app/Views/Modulos/paquete/gestionar_modulos.php` - Vista gestionar módulos
- ✅ `app/Views/Modulos/paquete/detalle.php` - Vista de detalle
- ✅ `app/Views/Modulos/empresa/lista.php` - Vista de lista de empresas
- ✅ `app/Views/Modulos/empresa/registro.php` - Vista crear/editar empresa
- ✅ `app/Views/Modulos/empresa/detalle.php` - Vista de detalle de empresa
- ✅ `CONTROL_ACCESO_PAQUETES_PERFILES.md` - Documentación del sistema de acceso
- ✅ `PROPUESTA_PERFILES_POR_EMPRESA.md` - Documentación de perfiles por empresa

**Referencias:**
- Ver `ANALISIS_SISTEMA_PAQUETES.md` para entender el concepto
- Ver rama `feature/presencia` para ver implementación similar
- Ver `SISTEMA_PAQUETES_MODULOS.md` en rama `feature/presencia` para documentación técnica

### 2. Portal del Paciente
**Prioridad: Alta** ⏳ **PENDIENTE**

**Descripción:**
- Permitir que los pacientes accedan al sistema para ver sus propios datos
- Crear módulo "Mi Portal" con secciones: Avances, Citas, Documentos, Logros
- Opción de crear usuario automáticamente al crear paciente
- Control del nutricionista sobre qué información puede ver cada paciente
- Los pacientes solo pueden ver/editar sus propios datos

**Tareas:**

**Fase 1: Base de Datos**
- [ ] Agregar campo `usuario_id` a tabla `pacientes` (relación opcional 1:1)
- [ ] Crear tabla `paciente_configuracion` para control de visibilidad (opcional)
- [ ] Verificar que perfil "Paciente" existe y está configurado

**Fase 2: Módulo "Mi Portal"**
- [ ] Crear módulo "Mi Portal" en BD (id=42, ruta: `dashboard/mi-portal`)
- [ ] Crear submódulos: Mi Perfil, Mis Avances, Mis Citas, Mis Documentos, Mis Logros
- [ ] Crear `MiPortalController` con métodos para cada sección
- [ ] Implementar validación de acceso (paciente solo ve sus datos)
- [ ] Crear vistas para cada sección del portal
- [ ] Asignar módulo al perfil "Paciente" y al paquete correspondiente

**Fase 3: Integración con Creación de Paciente**
- [ ] Modificar `PacienteController::registrar()` para crear usuario opcional
- [ ] Agregar checkbox "Crear usuario del sistema" en formulario de paciente
- [ ] Generar contraseña automáticamente o permitir ingresarla
- [ ] Vincular `paciente.usuario_id` con el usuario creado
- [ ] Implementar envío de email con credenciales al paciente

**Fase 4: Control de Visibilidad**
- [ ] Crear interfaz para nutricionista configurar qué ver cada paciente
- [ ] Implementar filtros en controladores según configuración
- [ ] Agregar opción "Compartir documento" en módulo de documentos
- [ ] Validar que paciente solo vea documentos compartidos

**Fase 5: Funcionalidades del Portal**
- [ ] **Mi Perfil**: Ver/editar datos personales básicos (no peso/altura/IMC)
- [ ] **Mis Avances**: Gráficos de evolución de peso, IMC, mediciones
- [ ] **Mis Citas**: Calendario de próximas citas, historial de citas pasadas
- [ ] **Mis Documentos**: Ver documentos compartidos por nutricionista, descargar PDFs
- [ ] **Mis Logros**: Metas alcanzadas, badges, progreso hacia objetivos

**Archivos a crear/modificar:**
- `agregar_usuario_id_a_pacientes.sql` - Script SQL para agregar campo usuario_id
- `crear_modulo_mi_portal.sql` - Script SQL para crear módulo "Mi Portal"
- `app/Controllers/Dashboard/MiPortalController.php` - Controller nuevo
- `app/Controllers/Dashboard/PacienteController.php` - Modificar para crear usuario opcional
- `app/Models/Paciente.php` - Agregar `usuario_id` a allowedFields
- `app/Views/Modulos/mi_portal/` - Vistas del portal (nuevo directorio)
- `app/Views/Modulos/paciente/registro.php` - Agregar checkbox "Crear usuario"
- `app/Views/Modulos/paciente/editar.php` - Agregar opción de vincular usuario existente
- `app/Libraries/EmailService.php` o similar - Para enviar credenciales (si no existe)

**Referencias:**
- Ver `PROPUESTA_PORTAL_PACIENTE.md` para detalles completos de la propuesta

### 3. Sistema de Perfiles por Empresa
**Prioridad: Media** ✅ **COMPLETADO**

**Descripción:**
- Permitir que cada nutricionista (empresa) cree y gestione sus propios perfiles
- Crear perfil predefinido "Paciente" con acceso limitado
- Los nutricionistas pueden asignar módulos del paquete a los perfiles

**Estado:** ✅ **COMPLETADO** - Sistema funcionando. Nutricionistas pueden gestionar perfiles de su empresa y asignar módulos del paquete.

**Tareas:**
- [x] Agregar campo `empresa_id` a tabla `perfil`
- [x] Crear perfil predefinido "Paciente" con permisos limitados
- [x] Modificar modelo `Perfil` para filtrar por `empresa_id`
- [x] Crear módulo "Perfiles de Empresa" (id=41) para nutricionistas
- [x] Modificar `PerfilController` para trabajar por empresa
- [x] Modificar `PerfilDetalleController` para filtrar módulos por paquete
- [x] Actualizar controladores para usar perfiles por empresa
- [x] Corregir rutas de perfil-detalle para que funcionen como submódulos

**Archivos creados:**
- ✅ `agregar_empresa_id_a_perfil.sql` - Script SQL para agregar campo empresa_id
- ✅ `crear_perfil_paciente_base.sql` - Script SQL para crear perfil Paciente
- ✅ `crear_modulo_perfiles_empresa.sql` - Script para crear módulo "Perfiles de Empresa"
- ✅ `asignar_modulo_perfiles_empresa.sql` - Script para asignar módulo a paquete y perfil
- ✅ `corregir_rutas_perfil_detalle_final.sql` - Script para corregir rutas absolutas
- ✅ `PROPUESTA_PERFILES_POR_EMPRESA.md` - Propuesta completa del sistema

**Referencias:**
- Ver `PROPUESTA_PERFILES_POR_EMPRESA.md` para detalles completos de la propuesta

### 4. Módulo de Botones de Pago (Mercado Pago)
**Prioridad: Alta**

**Descripción:**
- Integrar SDK de Mercado Pago para crear botones de pago
- Permitir a los nutricionistas crear planes/tarifas
- Generar botones de pago para citas
- Implementar flujo de estados: Pendiente → Por Pagar → Confirmado

**Tareas:**
- [ ] Investigar e integrar SDK de Mercado Pago
- [ ] Crear tabla `planes_pago` o similar para almacenar planes de nutricionistas
- [ ] Crear módulo "Planes de Pago" en el dashboard
- [ ] Agregar campo `plan_pago_id` a `detalle_agenda` para vincular citas con planes
- [ ] Modificar flujo de confirmación: cuando paciente confirma → estado "Por Pagar"
- [ ] Crear endpoint para generar botón de pago de Mercado Pago
- [ ] Enviar email con botón de pago cuando el paciente confirma
- [ ] Crear webhook de Mercado Pago para recibir notificaciones de pago
- [ ] Actualizar estado de cita a "Confirmado" cuando se recibe confirmación de pago
- [ ] Agregar estado "Por Pagar" al enum de `estado_cita` en `detalle_agenda`

**Archivos a crear/modificar:**
- `app/Libraries/MercadoPagoService.php` (nuevo)
- `app/Controllers/Dashboard/PlanesPagoController.php` (nuevo)
- `app/Models/PlanPago.php` (nuevo)
- `app/Views/Modulos/planes_pago/` (nuevo)
- `app/Controllers/Dashboard/AgendaController.php` (modificar)
- `app/Controllers/Webhooks/MercadoPagoController.php` (nuevo)
- Script SQL para agregar tabla `planes_pago` y campo `plan_pago_id`

### 5. Tags en Fichas Clínicas
**Prioridad: Media** ✅ **COMPLETADO**

**Descripción:**
- Agregar sistema de tags libres a cada ficha clínica
- Permitir filtrar y buscar historiales por tags
- Sugerir tags basados en historiales anteriores

**Estado:** ✅ **COMPLETADO** - Sistema de tags implementado completamente. Los tags se pueden agregar en historiales clínicos y en consultas de agenda, y se puede buscar historiales por tags.

**Tareas:**
- [x] Agregar campo `tags` (JSON) a tabla `historial_clinico`
- [x] Crear tabla `historial_tags` para normalizar tags y tracking de uso
- [x] Agregar campo `tags` (JSON) a tabla `detalle_agenda` para sincronización
- [x] Modificar vista de ficha clínica para agregar/editar tags
- [x] Implementar input de tags con autocompletado usando Tagify.js
- [x] Agregar tags en vista de consulta de agenda
- [x] Crear buscador/filtro por tags en el módulo de Historial
- [x] Agregar sugerencias de tags basadas en tags más usados (con AJAX)
- [x] Actualizar modelo `HistorialClinico` para manejar tags (procesarTags, buscarPorTags, getTagsMasUsados)
- [x] Sincronizar tags entre `detalle_agenda` y `historial_clinico`
- [x] Corregir formato de guardado de tags (evitar objetos JSON stringificados)
- [x] Corregir errores de SQL (ambigüedad de columnas, comillas en JSON_SEARCH)

**Archivos creados/modificados:**
- ✅ `agregar_tags_historial_clinico.sql` - Script SQL para agregar campo tags y tabla historial_tags
- ✅ `agregar_tags_detalle_agenda.sql` - Script SQL para agregar campo tags a detalle_agenda y migrar datos
- ✅ `app/Models/HistorialClinico.php` - Agregados métodos: procesarTags(), buscarPorTags(), getTagsMasUsados(), getTagsAsString(), normalizarTag()
- ✅ `app/Controllers/Dashboard/HistorialController.php` - Agregado getTagsSugeridos(), modificado getHistorial() para búsqueda por tags, modificado lista() para cargar tags sugeridos
- ✅ `app/Controllers/Dashboard/AgendaController.php` - Modificado consulta(), guardarNotasConsulta(), guardarMediciones() para manejar tags
- ✅ `app/Views/Modulos/historial/registro.php` - Agregado input de tags con Tagify
- ✅ `app/Views/Modulos/historial/editar.php` - Agregado input de tags con Tagify
- ✅ `app/Views/Modulos/historial/lista.php` - Agregado campo de búsqueda por tags con Tagify
- ✅ `app/Views/Modulos/historial/comparar.php` - Corregido acceso a nombre_completo
- ✅ `app/Views/Modulos/agenda/consulta.php` - Agregado input de tags con Tagify, sincronización con detalle_agenda

### 6. Mejoras Adicionales (Opcional)
**Prioridad: Baja**

- [ ] Mejorar UI/UX del calendario
- [ ] Agregar más opciones de configuración
- [ ] Implementar reportes y estadísticas
- [ ] Mejorar sistema de notificaciones
- [ ] Optimizar rendimiento de consultas

## 📝 Notas

- Todas las configuraciones del sistema están funcionando y conectadas
- El módulo de cancelación masiva está completamente funcional
- Los mensajes de cancelación pueden personalizarse con HTML (TinyMCE)
- El sistema de recordatorios respeta las configuraciones de cada nutricionista
- **Sistema de Paquetes COMPLETADO**: Los paquetes funcionan correctamente, el menú se genera dinámicamente según el paquete de la empresa, y el control de acceso está unificado. Sistema cerrado hasta que surjan necesidades o fixes.
- **Sistema de Perfiles por Empresa COMPLETADO**: Los nutricionistas pueden gestionar perfiles de su empresa y asignar módulos del paquete a cada perfil. Rutas corregidas y funcionando correctamente.
- **Configuraciones por Empresa**: Las configuraciones ahora son por empresa, no por usuario
- **Gestión de Empresas**: Super Admin puede gestionar todas las empresas y asignarles paquetes
- **Correcciones CSRF**: Se corrigieron errores de CSRF en múltiples módulos usando tokens dinámicos
- **SessionFilter Mejorado**: Ahora detecta y maneja correctamente rutas absolutas (que empiezan con `/dashboard/`)

## 🔗 Referencias

- Documentación de Mercado Pago: https://www.mercadopago.com.mx/developers/es/docs
- SDK PHP de Mercado Pago: https://github.com/mercadopago/sdk-php
