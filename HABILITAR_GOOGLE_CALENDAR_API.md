# 🔧 Habilitar Google Calendar API

## ❌ Error Actual

```
Google Calendar API has not been used in project 611955325380 before or it is disabled.
```

**Reason:** `accessNotConfigured`  
**Domain:** `usageLimits`  
**Status:** `PERMISSION_DENIED`

## ✅ Solución

### Opción 1: Enlace Directo (Más Rápido)

1. Haz clic en este enlace:
   ```
   https://console.developers.google.com/apis/api/calendar-json.googleapis.com/overview?project=611955325380
   ```

2. Haz clic en el botón **"ENABLE"** (Habilitar)

3. Espera 2-5 minutos para que los cambios se propaguen

4. Intenta agendar una cita nuevamente

### Opción 2: Manualmente

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)

2. Asegúrate de estar en el proyecto correcto:
   - Proyecto ID: `611955325380`
   - Si no lo ves, búscalo en el selector de proyectos (arriba a la izquierda)

3. Ve a **"APIs & Services"** > **"Library"** (Biblioteca)

4. Busca **"Google Calendar API"** en la barra de búsqueda

5. Haz clic en el resultado **"Google Calendar API"**

6. Haz clic en el botón **"ENABLE"** (Habilitar)

7. Espera 2-5 minutos para que los cambios se propaguen

8. Intenta agendar una cita nuevamente

## ⏱️ Tiempo de Propagación

Después de habilitar la API, Google puede tardar **2-5 minutos** en propagar los cambios a todos sus sistemas. Si intentas agendar una cita inmediatamente después de habilitarla y aún ves el error 403, espera unos minutos y vuelve a intentar.

## ✅ Verificación

Una vez habilitada la API, deberías poder:

1. Agendar una cita desde el sistema
2. Ver que se crea automáticamente un evento en tu Google Calendar
3. Ver el evento con:
   - Fecha y hora de la cita
   - Nombre del paciente
   - Tipo de consulta
   - Modalidad (Presencial/Online)
   - Recordatorios automáticos

## 🔗 Enlaces Útiles

- [Google Cloud Console - Proyecto 611955325380](https://console.cloud.google.com/home/dashboard?project=611955325380)
- [Habilitar Google Calendar API (Enlace Directo)](https://console.developers.google.com/apis/api/calendar-json.googleapis.com/overview?project=611955325380)
- [Documentación de Google Calendar API](https://developers.google.com/calendar/api/v3/reference)

## 📝 Notas

- La API solo necesita habilitarse **una vez** por proyecto
- Una vez habilitada, todos los usuarios del proyecto podrán usarla
- No hay costo adicional por habilitar la API (solo se cobra por uso excesivo, que es muy raro)
