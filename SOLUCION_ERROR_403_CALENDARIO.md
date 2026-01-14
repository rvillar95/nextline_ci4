# 🔧 Solución Error 403: access_denied - Calendario

## ❌ Problema

Error: **"nextline-agenda no completó el proceso de verificación de Google"**

Esto ocurre porque tu aplicación OAuth está en **modo de prueba** y solo los usuarios de prueba pueden acceder.

## ✅ Solución Rápida: Agregar Usuarios de Prueba

### Paso 1: Ir a Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Selecciona tu proyecto
3. Ve a **APIs & Services** > **OAuth consent screen**

### Paso 2: Agregar Usuarios de Prueba

1. En la sección **"Test users"** (Usuarios de prueba)
2. Haz clic en **"+ ADD USERS"** (Agregar usuarios)
3. Agrega los emails de los usuarios que necesitan acceder:
   - `rvillar1995@gmail.com`
   - `m.amasadsad@gmail.com`
   - Cualquier otro email que vaya a usar la app
4. Haz clic en **"ADD"** (Agregar)

### Paso 3: Guardar y Esperar

- Los cambios se aplican inmediatamente
- Los usuarios agregados podrán autorizar la conexión

## 🔄 Solución Permanente: Publicar la App (Opcional)

Si quieres que cualquier usuario pueda usar la app sin agregarlos manualmente:

### Requisitos para Publicar:

1. **Completar la pantalla de consentimiento:**
   - Información de la app completa
   - Política de privacidad
   - Términos de servicio
   - Logo de la app

2. **Solicitar verificación de Google:**
   - Puede tardar varias semanas
   - Google revisará tu app
   - Requiere cumplir con políticas de privacidad

### Para Desarrollo/Pruebas:

**Recomendación:** Mantén la app en modo de prueba y agrega usuarios de prueba. Es más rápido y suficiente para desarrollo.

## 📝 Notas Importantes

- **Modo de prueba:** Máximo 100 usuarios de prueba
- **Los usuarios de prueba** pueden autorizar la app sin restricciones
- **Los usuarios no agregados** verán el error 403
- **Para producción:** Necesitarás publicar la app o usar Service Account

## 🚀 Después de Agregar Usuarios

1. Los usuarios agregados podrán autorizar la conexión
2. Podrán conectar sus calendarios sin problemas
3. Los eventos se crearán automáticamente en sus calendarios

## ⚠️ Alternativa: Service Account (Avanzado)

Si necesitas que la app funcione sin autorización de usuarios individuales, puedes usar **Service Account**, pero esto requiere configuración adicional y no es recomendado para este caso de uso.
