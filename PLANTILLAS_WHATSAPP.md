# Plantillas de WhatsApp Business API

Documentación de las plantillas usadas en la aplicación para envío por WhatsApp Business API (Meta). Todas usan **parámetros con nombre** (`parameter_format: named`).

---

## 1. confirmacion_cita

**Uso:** Envío al aprobar una reserva / confirmar una cita (reemplaza el mensaje de texto cuando la plantilla está configurada).

**Configuración (.env):**
- `WHATSAPP_PLANTILLA_CONFIRMACION` = `confirmacion_cita` (o nombre personalizado; `0` o vacío = no usar plantilla)
- `WHATSAPP_PLANTILLA_IDIOMA` = `es_CL` (recomendado para Spanish Chile)

### Estructura en Meta Business Manager

- **Nombre:** `confirmacion_cita`
- **Idioma:** Spanish (Chile) → código `es_CL`
- **Categoría:** UTILITY (o la que corresponda)
- **Parameter format:** Named

**Header (1 variable):**
- Variable: `{{paciente}}`  
  Ejemplo: *¡Hola {{paciente}}!*

**Body (4 variables):**
- `{{nutricionista}}`, `{{fecha}}`, `{{hora}}`, `{{control}}`  
  Ejemplo: *Tu cita con {{nutricionista}} ha sido agendada para el {{fecha}} a las {{hora}}. Tipo: {{control}}.*

**Botones:** Opcional (por ejemplo CTA con enlace a Meet).

### Payload de envío (API v22.0)

```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "56912345678",
  "type": "template",
  "template": {
    "name": "confirmacion_cita",
    "language": { "code": "es_CL" },
    "components": [
      {
        "type": "header",
        "parameters": [
          {
            "type": "text",
            "parameter_name": "paciente",
            "text": "María González"
          }
        ]
      },
      {
        "type": "body",
        "parameters": [
          { "type": "text", "parameter_name": "nutricionista", "text": "Ana López" },
          { "type": "text", "parameter_name": "fecha", "text": "25-01-2026" },
          { "type": "text", "parameter_name": "hora", "text": "10:00" },
          { "type": "text", "parameter_name": "control", "text": "Control" }
        ]
      }
    ]
  }
}
```

**Origen de datos en la app:** `WhatsAppService::enviarConfirmacionCita()` — paciente (nombre+apellido), nutricionista (nombre usuario), fecha (cita), hora (hora_inicio), control (tipo_consulta o "Consulta").

---

## 2. hello_world

**Uso:** Solo para pruebas (plantilla por defecto de Meta sin variables).

### Estructura en Meta

- **Nombre:** `hello_world`
- **Idioma:** English (US) → `en_US`
- **Sin variables** (header/body fijos)

### Payload de envío

```json
{
  "messaging_product": "whatsapp",
  "recipient_type": "individual",
  "to": "56912345678",
  "type": "template",
  "template": {
    "name": "hello_world",
    "language": { "code": "en_US" }
  }
}
```

En el código no se llama por defecto; se puede usar pasando `enviarPorWhatsAppBusinessPlantilla($numero, 'hello_world', 'en_US', [], [])` para pruebas.

---

## 3. confirmacion_cita_presencial

**Uso:** Confirmación cuando la cita es **presencial**. Incluye la dirección de la empresa (consultorio).

- **Header:** `{{paciente}}`
- **Body:** `nutricionista`, `fecha`, `hora`, `control`, `direccion`
- **Idioma:** es_CL. Contenido con emoticonos en `PLANTILLAS_FACEBOOK_DEVELOPER.md`.

La app usa esta plantilla cuando la modalidad no es "online" y `WHATSAPP_PLANTILLA_CONFIRMACION` no es exactamente `confirmacion_cita` (p. ej. `=1`).

---

## 4. confirmacion_cita_online

**Uso:** Confirmación cuando la cita es **online**. Incluye el enlace de la reunión (Google Meet).

- **Header:** `{{paciente}}`
- **Body:** `nutricionista`, `fecha`, `hora`, `control`, `link_reunion`
- **Idioma:** es_CL. Contenido con emoticonos en `PLANTILLAS_FACEBOOK_DEVELOPER.md`.

La app usa esta plantilla cuando la modalidad es "online"; si no hay Meet link se envía "-".

---

## Resumen

| Plantilla                      | Idioma | Header (named) | Body (named)        | Uso en la app        |
|--------------------------------|--------|----------------|---------------------|----------------------|
| `confirmacion_cita`            | es_CL  | paciente       | nutricionista, fecha, hora, control | Confirmación genérica (4 vars) |
| `confirmacion_cita_presencial`| es_CL  | paciente       | + direccion         | Cita presencial (dirección empresa) |
| `confirmacion_cita_online`    | es_CL  | paciente       | + link_reunion      | Cita online (enlace Meet) |
| `hello_world`                  | en_US  | —              | —                   | Pruebas (opcional)   |

Para agregar más plantillas con parámetros con nombre, usar en `enviarPorWhatsAppBusinessPlantilla()` los arrays `$headerParamNames` y `$bodyParamNames` con los nombres de las variables en el mismo orden que los valores.
