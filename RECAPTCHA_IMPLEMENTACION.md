# 🛡️ Implementación de Google reCAPTCHA v3

## ✅ IMPLEMENTACIÓN COMPLETA

Se ha implementado **Google reCAPTCHA v3** (invisible) en el formulario de contacto para protegerlo contra spam y bots.

---

## 📋 PASO 1: Configurar el `.env`

Agrega estas líneas al final de tu archivo `.env`:

```env
# ────────────────────────────────────────────────────────────
# Google reCAPTCHA v3 - Formulario de Contacto
# ────────────────────────────────────────────────────────────
recaptcha.siteKey = 6LfzneMrAAAAALCg8CWYl0aAdXgfKActSs6qip2_
recaptcha.secretKey = 6LfzneMrAAAAABT4irksZucBsYQeg1bxLMh3WNB-
recaptcha.enabled = true
recaptcha.minScore = 0.5
```

### 📌 Parámetros:

| Parámetro | Valor | Descripción |
|-----------|-------|-------------|
| `recaptcha.siteKey` | `6LfzneMrAAAAALCg8CWYl0aAdXgfKActSs6qip2_` | Clave pública (frontend) |
| `recaptcha.secretKey` | `6LfzneMrAAAAABT4irksZucBsYQeg1bxLMh3WNB-` | Clave privada (backend) |
| `recaptcha.enabled` | `true` | Activar/desactivar reCAPTCHA |
| `recaptcha.minScore` | `0.5` | Puntuación mínima (0.0 - 1.0) |

---

## 🎯 ¿Cómo Funciona?

### **Para el Usuario (Invisible):**
1. Usuario llena el formulario normalmente
2. Al hacer clic en "Enviar Consulta":
   - Botón cambia a "Verificando..." ⏳
   - reCAPTCHA analiza el comportamiento (invisible)
   - Botón cambia a "Enviando..." 📧
   - Formulario se envía
3. Todo el proceso toma 1-2 segundos

### **Para el Sistema:**
1. JavaScript obtiene un **token** de Google
2. El token se envía con el formulario (campo oculto)
3. PHP valida el token con Google
4. Google responde con un **score** (0.0 a 1.0)
   - **1.0** = Definitivamente humano ✅
   - **0.5** = Probablemente humano ⚠️
   - **0.0** = Probablemente bot ❌
5. Si el score es ≥ 0.5, se acepta el formulario

---

## 📊 Puntuaciones (Score)

### Configuración Recomendada:

| Score | Descripción | Acción |
|-------|-------------|--------|
| **0.9 - 1.0** | Usuario legítimo | ✅ Aceptar siempre |
| **0.5 - 0.8** | Probablemente humano | ✅ Aceptar |
| **0.3 - 0.4** | Sospechoso | ⚠️ Revisar manualmente |
| **0.0 - 0.2** | Muy sospechoso | ❌ Rechazar |

### Ajustar Sensibilidad:

En tu `.env`, cambia `recaptcha.minScore`:

```env
recaptcha.minScore = 0.3   # Más permisivo (acepta más, pero más spam)
recaptcha.minScore = 0.5   # Balanceado (RECOMENDADO) ⭐
recaptcha.minScore = 0.7   # Más estricto (rechaza más, menos spam)
```

---

## 🔍 Archivos Modificados

### 1️⃣ **app/Controllers/Web/ContactoController.php**

✅ Agregado método `validarRecaptcha()`
✅ Validación antes de guardar el lead
✅ Logs detallados para debugging

### 2️⃣ **app/Views/Web/contacto.php**

✅ Script de reCAPTCHA v3 cargado
✅ Campo oculto para el token
✅ JavaScript para obtener token al enviar
✅ Indicador visual "Protegido por reCAPTCHA"

---

## 🧪 Probar la Implementación

### **1. Prueba Normal (Humano):**

1. Ve a: `http://localhost/codeigniter4/nextline_ci4/contacto`
2. Llena el formulario normalmente
3. Haz clic en "Enviar Consulta"
4. Deberías ver:
   - "Verificando..." (1 segundo)
   - "Enviando..." (1 segundo)
   - Redirigir a página "Gracias"
5. Revisa los logs: `writable/logs/log-2025-10-09.log`
   ```
   INFO - reCAPTCHA: Score obtenido = 0.9, mínimo requerido = 0.5
   ```

### **2. Prueba Bot (Simulación):**

Puedes simular un bot enviando el formulario sin el token:

```bash
# Usando curl (sin token reCAPTCHA)
curl -X POST http://localhost/codeigniter4/nextline_ci4/contacto/enviar \
  -d "nombre=Test Bot" \
  -d "correo=bot@test.com" \
  -d "mensaje=Spam message"
```

Resultado esperado:
- ❌ Formulario rechazado
- Log: `WARNING - reCAPTCHA: Token no recibido`

---

## 📈 Monitoreo en Google Admin

### **Ver Estadísticas:**

1. Ve a: https://www.google.com/recaptcha/admin
2. Selecciona tu sitio: **"MANSANCHEZ Constructor - Formulario Contacto"**
3. Verás gráficas con:
   - ✅ Solicitudes totales
   - ✅ Distribución de scores
   - ✅ Tasa de rechazo
   - ✅ Intentos sospechosos bloqueados

---

## 🔧 Configuración Avanzada

### **Deshabilitar reCAPTCHA Temporalmente:**

En tu `.env`:
```env
recaptcha.enabled = false
```

Útil para:
- Pruebas locales sin conexión a internet
- Debugging del formulario
- Testing automatizado

### **Múltiples Acciones:**

Puedes usar diferentes acciones para diferentes formularios:

```javascript
// Formulario de contacto
grecaptcha.execute('SITE_KEY', {action: 'contact'})

// Formulario de cotización
grecaptcha.execute('SITE_KEY', {action: 'quote'})

// Login
grecaptcha.execute('SITE_KEY', {action: 'login'})
```

Luego en el backend, puedes validar la acción específica.

---

## 🚨 Solución de Problemas

### **Problema 1: "Token no recibido"**

**Causa:** El script de reCAPTCHA no se cargó correctamente.

**Solución:**
1. Verifica tu conexión a internet
2. Revisa la consola del navegador (F12)
3. Asegúrate de que no hay bloqueadores de ads/scripts

---

### **Problema 2: "Score demasiado bajo"**

**Causa:** Google detectó comportamiento sospechoso.

**Solución:**
1. Verifica que `recaptcha.minScore` no sea muy alto
2. Prueba desde un navegador normal (no modo incógnito)
3. No uses VPN o Tor para pruebas

---

### **Problema 3: "Error al conectar con Google"**

**Causa:** WAMP no puede conectar con la API de Google.

**Solución:**
1. Verifica que `curl` esté habilitado en PHP
2. En `php.ini`, descomenta: `extension=curl`
3. Reinicia WAMP

---

## 📝 Logs y Debugging

Todos los eventos de reCAPTCHA se registran en los logs:

```php
// Ubicación: writable/logs/log-2025-10-09.log

// Éxito
INFO - reCAPTCHA: Score obtenido = 0.9, mínimo requerido = 0.5

// Advertencias
WARNING - reCAPTCHA: Token no recibido
WARNING - reCAPTCHA: Score demasiado bajo (0.3 < 0.5)

// Errores
ERROR - reCAPTCHA: Secret key no configurada
ERROR - reCAPTCHA: Error al conectar con Google
```

---

## 🌍 Dominios Autorizados

### **Para Producción:**

Cuando subas el sitio a producción:

1. Ve a: https://www.google.com/recaptcha/admin
2. Selecciona tu sitio
3. En **"Dominios"**, agrega:
   - `mansanchez.cl`
   - `www.mansanchez.cl`
4. Guarda cambios

**Nota:** `localhost` ya está autorizado por defecto para pruebas.

---

## ✅ Checklist de Implementación

- [x] Obtener claves de Google reCAPTCHA
- [x] Agregar claves al `.env`
- [x] Modificar `ContactoController.php`
- [x] Agregar script a `contacto.php`
- [ ] **Reiniciar WAMP** (para cargar nuevas variables `.env`)
- [ ] Probar formulario desde el navegador
- [ ] Verificar logs en `writable/logs/`
- [ ] Revisar estadísticas en Google Admin

---

## 🎉 Resultado Final

### **Antes:**
- ❌ Vulnerable a spam bots
- ❌ Sin protección contra envíos automatizados
- ❌ Posibles leads falsos

### **Después:**
- ✅ Protección invisible contra bots
- ✅ Sin molestar a usuarios reales
- ✅ Logs detallados para monitoreo
- ✅ Estadísticas en tiempo real
- ✅ Configuración flexible (score ajustable)

---

## 📞 Soporte

**Documentación oficial de Google:**
https://developers.google.com/recaptcha/docs/v3

**Dashboard de administración:**
https://www.google.com/recaptcha/admin

---

**🛡️ ¡Tu formulario de contacto ahora está protegido contra spam y bots!**

