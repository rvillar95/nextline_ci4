# 💳 Integración de Mercado Pago - Botones de Pago

## ✅ Completado

### 1. Base de Datos
- ✅ SQL creado: `agregar_campos_mercadopago_pagos.sql`
- ✅ Campos agregados a tabla `pagos`:
  - `mp_preference_id` - ID de la preferencia de Mercado Pago
  - `mp_payment_id` - ID del pago cuando se completa
  - `mp_status` - Estado del pago en Mercado Pago
  - `detalle_agenda_id` - Para vincular pagos con citas
- ✅ Enum `tipo_pago` actualizado para incluir 'cita'

### 2. Configuración
- ✅ `app/Config/MercadoPago.php` - Configuración global (fallback)
- ✅ **Configuración por Empresa**: Cada empresa tiene sus propias credenciales en `empresa_configuraciones`:
  - `mp_access_token` - Access Token de Mercado Pago
  - `mp_public_key` - Public Key de Mercado Pago
  - `mp_mode` - 'sandbox' o 'production'
  - `mp_habilitado` - 1 = habilitado, 0 = deshabilitado
- ✅ SQL creado: `agregar_mercadopago_empresa_configuraciones.sql`

### 3. Servicio
- ✅ `app/Services/MercadoPagoService.php` - Servicio completo para:
  - Crear preferencias de pago (recibe credenciales por empresa)
  - Obtener información de pagos
  - Procesar webhooks
  - Mapear estados
- ✅ **Modificado para recibir credenciales como parámetro** (por empresa)

### 4. Modelos
- ✅ `app/Models/Pago.php` actualizado:
  - Nuevos campos en `$allowedFields`
  - Métodos para Mercado Pago:
    - `crearPagoConPreferencia()`
    - `actualizarDesdeMercadoPago()`
    - `getPagoPorPreferencia()`
    - `getPagosPorCita()`

- ✅ `app/Models/EmpresaConfiguracion.php` actualizado:
  - Nuevos campos en `$allowedFields`: `mp_access_token`, `mp_public_key`, `mp_mode`, `mp_habilitado`
  - Métodos nuevos:
    - `obtenerCredencialesMercadoPago($empresaId)` - Obtener credenciales de una empresa
    - `mercadoPagoHabilitado($empresaId)` - Verificar si MP está habilitado

### 5. Controladores
- ✅ `app/Controllers/Dashboard/BotonPagoController.php`:
  - `lista()` - Lista de botones de pago
  - `crear()` - Formulario para crear botón
  - `generarBoton()` - Endpoint AJAX para generar preferencia
  - `ver()` - Ver estado de un pago
  - `success()`, `failure()`, `pending()` - Páginas de retorno

- ✅ `app/Controllers/Api/MercadoPagoWebhookController.php`:
  - `webhook()` - Endpoint público para recibir notificaciones
  - `verificarPago()` - Verificar estado manualmente

### 6. Rutas
- ✅ Rutas agregadas en `app/Config/Routes.php`:
  - `/dashboard/boton-pago/lista`
  - `/dashboard/boton-pago/crear`
  - `/dashboard/boton-pago/crear/{id}` - Con cita pre-seleccionada
  - `/dashboard/boton-pago/ver/{id}`
  - `/dashboard/boton-pago/generar` - POST para crear botón
  - `/api/mercadopago/webhook` - Webhook público

### 7. Dependencias
- ✅ `composer.json` actualizado con `mercadopago/dx-php`

---

## ⏳ Pendiente

### 1. Instalación del SDK
```bash
composer require mercadopago/dx-php
```

### 2. Ejecutar SQLs
Ejecutar los scripts en la base de datos:
1. `agregar_campos_mercadopago_pagos.sql` - Agrega campos a tabla `pagos`
2. `agregar_mercadopago_empresa_configuraciones.sql` - Agrega campos de MP a `empresa_configuraciones`

### 3. Crear Módulo en BD
Ejecutar el script SQL para crear el módulo "Botones de Pago":
```sql
-- Ejecutar: crear_modulo_botones_pago.sql
```
Este script:
- Crea el módulo "Botones de Pago" (ruta: `/dashboard/boton-pago`)
- Crea las rutas necesarias (lista, crear, ver, generar, success, failure, pending)
- Asigna permisos a perfiles (Administrador y Nutricionista por defecto)

**Nota:** El módulo puede estar incluido en un paquete base O ser un addon. Para hacerlo addon:
- Marcar `es_addon = 'S'` en la tabla `modulo`
- Asignar precio mensual si es necesario

### 4. Configurar Credenciales por Empresa
Cada empresa debe configurar sus credenciales de Mercado Pago en el módulo de Configuraciones:
- **Requisito:** La empresa debe tener acceso al módulo "Botones de Pago" (por paquete o addon)
- Ir a `/dashboard/configuracion`
- Sección "Mercado Pago" (solo visible si tiene acceso al módulo):
  - Habilitar/Deshabilitar Mercado Pago
  - Modo (Sandbox/Production)
  - Access Token
  - Public Key

**Lógica de acceso:**
- La sección de Mercado Pago en Configuraciones solo se muestra si la empresa tiene acceso al módulo "Botones de Pago"
- El acceso se verifica mediante `AccesoService::tieneAccesoModuloPorRuta()` que considera:
  - Módulos incluidos en el paquete base (`paquete_modulo`)
  - Add-ons activos (`empresa_addon`)

### 5. Crear Vistas
- [ ] `app/Views/Modulos/boton_pago/lista.php` - Lista de botones creados
- [ ] `app/Views/Modulos/boton_pago/crear.php` - Formulario para crear botón
- [ ] `app/Views/Modulos/boton_pago/ver.php` - Ver estado de pago

### 6. Configurar Webhook en Mercado Pago (por empresa)
Cada empresa debe configurar su webhook en su panel de Mercado Pago:
1. Ir a: https://www.mercadopago.com.mx/developers/panel/app
2. Configurar URL de webhook: `https://tu-dominio.com/api/mercadopago/webhook`
3. Seleccionar eventos: `payment`, `preference`

**Nota:** El webhook es compartido, pero el sistema identifica la empresa usando el `external_reference` (ID del pago) y las credenciales de cada empresa.

---

## 📋 Flujo de Uso

### Para el Nutricionista:

1. **Crear Botón de Pago para una Cita:**
   - Ir a `/dashboard/boton-pago/crear/{detalle_agenda_id}`
   - O crear desde cero: `/dashboard/boton-pago/crear`
   - Llenar formulario:
     - Título del pago
     - Descripción
     - Monto
     - Email del pagador
     - Nombre del pagador
   - Click en "Generar Botón de Pago"
   - Se genera un link que se puede compartir con el paciente

2. **Ver Estado de Pagos:**
   - Ir a `/dashboard/boton-pago/lista`
   - Ver todos los botones de pago creados
   - Ver estado de cada pago (pendiente, completado, fallido)

3. **Compartir Botón:**
   - Copiar el link generado
   - Enviarlo al paciente por WhatsApp, Email, etc.
   - El paciente hace clic y paga en Mercado Pago

### Para el Sistema:

1. **Webhook:**
   - Mercado Pago envía notificación cuando cambia el estado del pago
   - El webhook actualiza automáticamente el estado en nuestra BD

2. **Estados:**
   - `pendiente` - Botón creado, esperando pago
   - `procesando` - Pago en proceso
   - `completado` - Pago aprobado
   - `fallido` - Pago rechazado/cancelado
   - `reembolsado` - Pago reembolsado

---

## 🔧 Próximos Pasos

1. **Instalar SDK:** `composer require mercadopago/dx-php`
2. **Ejecutar SQLs:**
   - `agregar_campos_mercadopago_pagos.sql` - Campos en tabla `pagos`
   - `agregar_mercadopago_empresa_configuraciones.sql` - Campos en `empresa_configuraciones`
   - `crear_modulo_botones_pago.sql` - Crear módulo "Botones de Pago"
3. **Asignar módulo a paquetes o como addon:**
   - Si es parte de un paquete: `INSERT INTO paquete_modulo (paquete_id, modulo_id, incluido) VALUES (X, 42, 'S')`
   - Si es addon: `UPDATE modulo SET es_addon = 'S', precio_mensual = XX.XX WHERE id = 42`
4. **Crear vistas** para la interfaz de usuario
5. **Configurar credenciales** en `/dashboard/configuracion` (solo si tiene acceso al módulo)
6. **Probar en sandbox** antes de pasar a producción

---

## 📚 Referencias

- Documentación Mercado Pago: https://www.mercadopago.com.mx/developers/es/docs
- SDK PHP: https://github.com/mercadopago/sdk-php
- Preferencias de Pago: https://www.mercadopago.com.mx/developers/es/docs/checkout-pro/integration-configuration/preferences
- **Configurar Ambiente de Desarrollo**: Ver `CONFIGURAR_AMBIENTE_DESARROLLO_MERCADOPAGO.md`
- **Tarjetas de Prueba**: Ver `GUIA_TARJETAS_PRUEBA_MERCADOPAGO.md`
- **Configurar Webhook**: Ver `CONFIGURAR_WEBHOOK_MERCADOPAGO.md`
