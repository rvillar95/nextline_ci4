# ✅ Completar Compra de Prueba - Mercado Pago (13% → Siguiente Nivel)

## 🎯 Objetivo

Completar una compra de prueba exitosa para avanzar del **13%** al siguiente nivel en la integración de Mercado Pago Checkout Pro.

## 📋 Pasos para Completar la Compra de Prueba

### Paso 1: Preparar el Entorno

1. **Abre una pestaña de incógnito** en tu navegador
   - Chrome: `Ctrl+Shift+N` (Windows) o `Cmd+Shift+N` (Mac)
   - Firefox: `Ctrl+Shift+P` (Windows) o `Cmd+Shift+P` (Mac)
   - Edge: `Ctrl+Shift+N` (Windows) o `Cmd+Shift+N` (Mac)

   **⚠️ IMPORTANTE**: Usa siempre una pestaña de incógnito para evitar errores por duplicidad de credenciales.

### Paso 2: Iniciar Sesión con Cuenta de Comprador de Prueba

1. Ve a: https://www.mercadopago.cl/developers/panel
2. Ve a **"Tus integraciones"** → **"NutrySync"** → **"Cuentas de prueba"**
3. Si no tienes una cuenta de comprador de prueba, créala:
   - Haz clic en "Crear cuenta de prueba"
   - Selecciona "Comprador"
   - Completa el formulario
   - **Guarda el email y contraseña**

4. **Inicia sesión en Mercado Pago** con la cuenta de comprador de prueba:
   - Ve a: https://www.mercadopago.cl
   - Inicia sesión con el email y contraseña de la cuenta de comprador de prueba
   - Si te pide un código por email, ingresa los **últimos 6 dígitos del User ID** de la cuenta de prueba (lo encuentras en "Cuentas de prueba")

### Paso 3: Crear un Pago de Prueba desde tu Aplicación

1. **Desde tu aplicación** (en la pestaña normal, no incógnito):
   - Crea una nueva cita con un paciente
   - Selecciona un botón de pago (plantilla)
   - Confirma la cita
   - Recibe el email con el botón de pago

2. **Copia la URL del botón de pago** del email

### Paso 4: Realizar la Compra de Prueba

1. **En la pestaña de incógnito**, pega la URL del botón de pago
2. Si te pide iniciar sesión, usa la **cuenta de comprador de prueba** (no tu cuenta personal)
3. **Completa el formulario de pago** con los siguientes datos:

#### ✅ Para un Pago Aprobado (Recomendado para avanzar):

```
Número de tarjeta: 5031 7557 3453 0604
CVV: 123
Vencimiento: 11/30 (o cualquier fecha futura)
Nombre del titular: APRO
Tipo de documento: Otro
Número de documento: 123456789
```

**O usa estas tarjetas alternativas:**

| Tipo | Número | CVV | Vencimiento |
|------|--------|-----|-------------|
| Crédito Mastercard | 5416 7526 0258 2580 | 123 | 11/30 |
| Crédito Visa | 4168 8188 4444 7115 | 123 | 11/30 |
| Débito Mastercard | 5241 0198 2664 6950 | 123 | 11/30 |
| Débito Visa | 4023 6535 2391 4373 | 123 | 11/30 |

4. **Haz clic en "Continuar"** y luego en **"Pagar"**

### Paso 5: Verificar el Resultado

1. **Si el pago es exitoso**, deberías ver:
   - Pantalla de éxito de Mercado Pago
   - Redirección a tu URL de éxito (si está configurada)
   - El progreso en el panel debería aumentar

2. **Verifica en el panel de Mercado Pago**:
   - Ve a: https://www.mercadopago.cl/developers/panel
   - Selecciona "NutrySync"
   - Verifica que el progreso haya aumentado (debería estar por encima del 13%)

3. **Verifica en tu aplicación**:
   - El estado del pago debería actualizarse a "completado" o "aprobado"
   - El estado de la cita debería cambiar a "agendada"

## 🔍 Escenarios de Prueba Adicionales

Si quieres probar diferentes escenarios, usa estos nombres de titular:

| Resultado | Nombre del Titular | Documento |
|-----------|-------------------|-----------|
| ✅ Pago aprobado | **APRO** | 123456789 |
| ❌ Rechazado (error general) | **OTHE** | 123456789 |
| ⏳ Pendiente | **CONT** | - |
| ❌ Rechazado (validación) | **CALL** | - |
| ❌ Rechazado (fondos insuficientes) | **FUND** | - |
| ❌ Rechazado (CVV inválido) | **SECU** | - |
| ❌ Rechazado (fecha vencida) | **EXPI** | - |

## ⚠️ Problemas Comunes

### Error: "Algo salió mal... No pudimos procesar tu pago"

**Soluciones:**
1. Verifica que estés usando una **pestaña de incógnito**
2. Verifica que estés iniciado sesión con la **cuenta de comprador de prueba** (no tu cuenta personal)
3. Verifica que uses exactamente los datos de tarjeta listados arriba
4. Verifica que el nombre del titular sea exactamente **"APRO"** (en mayúsculas)

### Error: "No se puede procesar el pago"

**Soluciones:**
1. Asegúrate de que la configuración del ambiente de desarrollo esté completada
2. Verifica que las credenciales sean de la pestaña "Prueba"
3. Intenta crear una nueva preferencia de pago (nueva cita)

### El progreso no aumenta después del pago

**Soluciones:**
1. Espera unos minutos - el panel puede tardar en actualizarse
2. Refresca la página del panel
3. Verifica que el pago se haya completado correctamente
4. Revisa los logs de tu aplicación para ver si hay errores

## 📊 Verificación del Progreso

Después de completar la compra de prueba:

1. Ve a: https://www.mercadopago.cl/developers/panel
2. Selecciona "NutrySync"
3. Verifica el progreso en "Etapas de integración: Checkout Pro"
4. Deberías ver:
   - ✅ "Probar la integración" marcado como completado
   - 📈 Progreso aumentado (probablemente 25% o más)
   - ➡️ Siguiente paso disponible: "Salir a producción" o configuraciones adicionales

## 🎯 Siguiente Paso Después de Completar la Compra

Una vez que el progreso haya aumentado:

1. **Configurar notificaciones de pago** (webhook)
2. **Probar diferentes escenarios** (pagos rechazados, pendientes, etc.)
3. **Revisar la calidad de la integración** en el panel
4. **Prepararse para salir a producción** (cuando esté listo)

## 📚 Referencias

- [Realizar compras de prueba - Documentación Oficial](https://www.mercadopago.cl/developers/es/docs/checkout-pro/integration-test/test-purchases)
- [Configurar URLs de retorno](https://www.mercadopago.cl/developers/es/docs/checkout-pro/configure-back-urls)
- [Agregar SDK al frontend](https://www.mercadopago.cl/developers/es/docs/checkout-pro/web-integration/add-frontend-sdk)
- [Panel de Desarrolladores](https://www.mercadopago.cl/developers/panel)

## ⚡ Resumen Rápido

1. ✅ Abre pestaña de incógnito
2. ✅ Inicia sesión con cuenta de comprador de prueba
3. ✅ Crea un pago desde tu aplicación
4. ✅ Completa el pago con tarjeta `5031 7557 3453 0604`, CVV `123`, titular `APRO`
5. ✅ Verifica que el progreso haya aumentado en el panel
