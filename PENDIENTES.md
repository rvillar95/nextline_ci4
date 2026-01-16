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

## 🔄 Pendiente

### 1. Módulo de Botones de Pago (Mercado Pago)
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

### 2. Tags en Fichas Clínicas
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

### 3. Mejoras Adicionales (Opcional)
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

## 🔗 Referencias

- Documentación de Mercado Pago: https://www.mercadopago.com.mx/developers/es/docs
- SDK PHP de Mercado Pago: https://github.com/mercadopago/sdk-php
