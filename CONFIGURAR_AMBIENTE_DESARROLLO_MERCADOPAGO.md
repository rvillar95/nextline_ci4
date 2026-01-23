# 🔧 Configurar Ambiente de Desarrollo - Mercado Pago Checkout Pro

## ⚠️ Problema

Si ves en el panel de Mercado Pago:
- **"Etapas de integración: Checkout Pro"** con **0% de progreso**
- **"1 tarea pendiente: Configurar ambiente de desarrollo"**
- Y obtienes errores al procesar pagos en sandbox

Necesitas completar la configuración del ambiente de desarrollo en el panel de Mercado Pago.

## 📋 Pasos para Configurar el Ambiente de Desarrollo

### Paso 1: Acceder al Panel de Desarrolladores

1. Ve a: https://www.mercadopago.cl/developers/panel
2. Inicia sesión con tu cuenta de Mercado Pago
3. Selecciona tu aplicación (ej: "NutrySync")

### Paso 2: Completar "Configurar ambiente de desarrollo"

1. En la sección **"Etapas de integración: Checkout Pro"**, haz clic en el botón **"Comenzar >"** junto a "Configurar ambiente de desarrollo"

2. **Configurar URLs de Retorno (Back URLs):**
   - **URL de éxito**: `http://localhost/codeigniter4/nextline_ci4/dashboard/pago/success`
     - O si tienes un dominio: `https://tudominio.com/dashboard/pago/success`
   - **URL de fallo**: `http://localhost/codeigniter4/nextline_ci4/dashboard/pago/failure`
   - **URL de pendiente**: `http://localhost/codeigniter4/nextline_ci4/dashboard/pago/pending`

   **Nota**: Si estás usando `localhost`, Mercado Pago puede tener limitaciones. Considera usar un servicio como ngrok para pruebas locales:
   ```
   ngrok http 80
   ```
   Y usar la URL de ngrok en lugar de localhost.

3. **Configurar Webhook URL:**
   - **URL del webhook**: `http://localhost/codeigniter4/nextline_ci4/api/mercadopago/webhook`
     - O con ngrok: `https://tu-url-ngrok.ngrok.io/api/mercadopago/webhook`
   - **Eventos a escuchar**: Selecciona "Pagos" o "Todos los eventos"

4. **Guardar configuración**

### Paso 3: Verificar Credenciales

1. En la sección **"Credenciales"**, asegúrate de estar en la pestaña **"Prueba"** (no "Productivas")
2. Verifica que tengas:
   - **Public Key**: Debe comenzar con `TEST-` o `APP_USR-`
   - **Access Token**: Debe comenzar con `TEST-` o `APP_USR-`
3. Copia estas credenciales y guárdalas en tu sistema:
   - En `/dashboard/configuracion` (si tienes acceso al módulo)
   - O en el archivo `.env`:
     ```
     MERCADOPAGO_ACCESS_TOKEN=TEST-tu-access-token
     MERCADOPAGO_PUBLIC_KEY=TEST-tu-public-key
     MERCADOPAGO_MODE=sandbox
     ```

### Paso 4: Crear Cuentas de Prueba (OBLIGATORIO para Probar Pagos)

**⚠️ IMPORTANTE**: Para poder procesar pagos en sandbox, DEBES crear cuentas de prueba.

1. Ve a: https://www.mercadopago.cl/developers/es/docs/checkout-api/additional-content/your-integrations/test/accounts
   - O desde el panel: https://www.mercadopago.cl/developers/panel → "Cuentas de prueba"
2. **Crea una cuenta de comprador de prueba:**
   - Haz clic en "Crear cuenta de prueba"
   - Selecciona "Comprador"
   - Completa el formulario (email, contraseña, etc.)
   - **Guarda el email y contraseña** - los necesitarás para iniciar sesión en el checkout
3. **Crea una cuenta de vendedor de prueba** (opcional, pero recomendado)
4. **IMPORTANTE**: Cuando pruebes un pago:
   - Haz clic en el botón de pago (te llevará a Mercado Pago)
   - **Inicia sesión con la cuenta de comprador de prueba** (NO con tu cuenta personal de Mercado Pago)
   - Completa el pago con las tarjetas de prueba

### Paso 5: Probar la Integración

1. Después de completar la configuración, el progreso debería cambiar a **25%** o más
2. Intenta crear un pago de prueba desde tu aplicación
3. Usa las tarjetas de prueba listadas en `GUIA_TARJETAS_PRUEBA_MERCADOPAGO.md`

## 🔍 Verificación

### ✅ Configuración Correcta

Después de completar los pasos, deberías ver:
- **Progreso de integración**: Al menos 25% o más
- **"Configurar ambiente de desarrollo"**: Marcado como completado
- **"Realizar integración"**: Siguiente paso disponible

### ❌ Problemas Comunes

#### Error: "URL inválida"
- **Causa**: Mercado Pago no acepta `localhost` directamente
- **Solución**: Usa ngrok o un dominio público para pruebas

#### Error: "Credenciales inválidas"
- **Causa**: Estás usando credenciales de producción en sandbox o viceversa
- **Solución**: Asegúrate de usar credenciales de la pestaña **"Prueba"** en sandbox

#### Error: "Webhook no responde"
- **Causa**: La URL del webhook no es accesible públicamente
- **Solución**: Usa ngrok o un servidor público para exponer tu aplicación

## 🛠️ Usar ngrok para Pruebas Locales

Si estás desarrollando en localhost, necesitas exponer tu aplicación:

1. **Instalar ngrok:**
   ```bash
   # Descargar desde: https://ngrok.com/download
   # O con npm: npm install -g ngrok
   ```

2. **Iniciar ngrok:**
   ```bash
   ngrok http 80
   # O el puerto que uses: ngrok http 8080
   ```

3. **Usar la URL de ngrok:**
   - ngrok te dará una URL como: `https://abc123.ngrok.io`
   - Usa esta URL en las configuraciones de Mercado Pago:
     - Back URLs: `https://abc123.ngrok.io/dashboard/pago/success`
     - Webhook: `https://abc123.ngrok.io/api/mercadopago/webhook`

4. **Nota**: La URL de ngrok cambia cada vez que lo reinicias (en el plan gratuito). Para una URL fija, necesitas el plan de pago.

## 📚 Referencias

- [Documentación: Configurar Ambiente de Desarrollo](https://www.mercadopago.cl/developers/es/docs/checkout-pro/configure-development-enviroment)
- [Documentación: Credenciales](https://www.mercadopago.cl/developers/es/docs/checkout-api/additional-content/your-integrations/credentials)
- [Documentación: Cuentas de Prueba](https://www.mercadopago.cl/developers/es/docs/checkout-api/additional-content/your-integrations/test/accounts)
- [Panel de Desarrolladores](https://www.mercadopago.cl/developers/panel)

## ⚡ Siguiente Paso

Una vez completada la configuración del ambiente de desarrollo:
1. El progreso debería aumentar
2. Los pagos en sandbox deberían funcionar correctamente
3. Puedes continuar con "Realizar integración" y "Probar la integración"
