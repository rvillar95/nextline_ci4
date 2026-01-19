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

### 4. Sistema de Paquetes para Nutricionistas
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
**Prioridad: Alta** ✅ **COMPLETADO**

**Descripción:**
- Implementar sistema de paquetes similar a NextLine Presencia
- Crear paquete "NextLine Nutrición" para módulos de nutricionistas
- Asignar módulos de nutricionistas al paquete correspondiente
- Crear módulo de gestión de paquetes para Super Admin
- Verificar que el sistema de permisos funcione correctamente

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

**Archivos creados:**
- ✅ `crear_paquete_nutricion.sql` - Script SQL para crear el paquete y asignar módulos (EJECUTADO)
- ✅ `asignar_paquete_a_empresas.sql` - Script para asignar el paquete a empresas de nutricionistas
- ✅ `crear_modulo_paquetes.sql` - Script SQL para crear el módulo de gestión de paquetes
- ✅ `insertar_modulo_detalle_paquetes.sql` - Script para insertar rutas del módulo paquetes
- ✅ `migrar_configuracion_a_empresa.sql` - Script para migrar configuraciones de usuario a empresa
- ✅ `app/Controllers/Dashboard/PaqueteController.php` - Controller completo
- ✅ `app/Controllers/Dashboard/EmpresaController.php` - Controller mejorado para Super Admin
- ✅ `app/Models/Paquete.php` - Model para paquetes
- ✅ `app/Models/EmpresaConfiguracion.php` - Model para configuraciones por empresa
- ✅ `app/Views/Modulos/paquete/lista.php` - Vista de lista
- ✅ `app/Views/Modulos/paquete/registro.php` - Vista crear/editar
- ✅ `app/Views/Modulos/paquete/gestionar_modulos.php` - Vista gestionar módulos
- ✅ `app/Views/Modulos/paquete/detalle.php` - Vista de detalle
- ✅ `app/Views/Modulos/empresa/lista.php` - Vista de lista de empresas
- ✅ `app/Views/Modulos/empresa/registro.php` - Vista crear/editar empresa
- ✅ `app/Views/Modulos/empresa/detalle.php` - Vista de detalle de empresa
- ✅ `CONTROL_ACCESO_PAQUETES_PERFILES.md` - Documentación del sistema de acceso

**Referencias:**
- Ver `ANALISIS_SISTEMA_PAQUETES.md` para entender el concepto
- Ver rama `feature/presencia` para ver implementación similar
- Ver `SISTEMA_PAQUETES_MODULOS.md` en rama `feature/presencia` para documentación técnica

### 2. Sistema de Perfiles por Empresa
**Prioridad: Media** ⏳ **PENDIENTE - DOCUMENTADO**

**Descripción:**
- Permitir que cada nutricionista (empresa) cree y gestione sus propios perfiles
- Crear perfil predefinido "Paciente" con acceso limitado
- Opción de crear usuario automáticamente al crear paciente
- Los pacientes solo pueden ver sus propios datos

**Tareas:**
- [ ] Agregar campo `empresa_id` a tabla `perfil`
- [ ] Crear perfil predefinido "Paciente" con permisos limitados
- [ ] Modificar modelo `Perfil` para filtrar por `empresa_id`
- [ ] Crear interfaz para que nutricionistas gestionen perfiles de su empresa
- [ ] Agregar opción en creación de paciente para crear usuario automáticamente
- [ ] Actualizar controladores para usar perfiles por empresa
- [ ] Implementar restricciones de acceso para pacientes (solo sus datos)

**Archivos creados (documentación):**
- ✅ `PROPUESTA_PERFILES_POR_EMPRESA.md` - Propuesta completa del sistema
- ✅ `agregar_empresa_id_a_perfil.sql` - Script SQL para agregar campo empresa_id
- ✅ `crear_perfil_paciente_base.sql` - Script SQL para crear perfil Paciente

**Referencias:**
- Ver `PROPUESTA_PERFILES_POR_EMPRESA.md` para detalles completos de la propuesta

### 3. Módulo de Botones de Pago (Mercado Pago)
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

### 4. Tags en Fichas Clínicas
**Prioridad: Media**

**Descripción:**
- Agregar sistema de tags libres a cada ficha clínica
- Permitir filtrar y buscar historiales por tags
- Sugerir tags basados en historiales anteriores

**Tareas:**
- [ ] Agregar campo `tags` (TEXT o JSON) a tabla `historial_clinico`
- [ ] Crear tabla `historial_tags` para normalizar tags (opcional, para mejor búsqueda)
- [ ] Modificar vista de ficha clínica para agregar/editar tags
- [ ] Implementar input de tags con autocompletado (usando librería como Select2 o TagsInput)
- [ ] Crear buscador/filtro por tags en el módulo de Historial
- [ ] Agregar sugerencias de tags basadas en tags más usados
- [ ] Actualizar modelo `HistorialClinico` para manejar tags

**Archivos a crear/modificar:**
- Script SQL para agregar campo `tags` a `historial_clinico`
- `app/Models/HistorialClinico.php` (modificar)
- `app/Controllers/Dashboard/HistorialController.php` (modificar)
- `app/Views/Modulos/historial/` (modificar)
- Script SQL para crear tabla `historial_tags` (opcional)

### 5. Mejoras Adicionales (Opcional)
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
- **Sistema de Paquetes COMPLETADO**: Los paquetes funcionan correctamente, el menú se genera dinámicamente según el paquete de la empresa, y el control de acceso está unificado
- **Configuraciones por Empresa**: Las configuraciones ahora son por empresa, no por usuario
- **Gestión de Empresas**: Super Admin puede gestionar todas las empresas y asignarles paquetes
- **Correcciones CSRF**: Se corrigieron errores de CSRF en múltiples módulos usando tokens dinámicos

## 🔗 Referencias

- Documentación de Mercado Pago: https://www.mercadopago.com.mx/developers/es/docs
- SDK PHP de Mercado Pago: https://github.com/mercadopago/sdk-php
