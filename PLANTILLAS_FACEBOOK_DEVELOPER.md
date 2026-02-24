# Plantillas para agregar en Facebook Developer (WhatsApp)

Instrucciones para crear cada plantilla en **Meta for Developers** → tu app → **WhatsApp** → **Plantillas de mensajes**. Usa **parámetros con nombre** (named parameters). Incluyen **emoticonos** para un tono más cercano.

---

## 1. confirmacion_cita (genérica — 4 variables en body)

**Idioma:** Spanish (CHL) — código `es_CL`  
**Categoría:** Marketing  
**Nombre interno:** `confirmacion_cita`

### Contenido exacto para Meta

**Encabezado (Header)** — tipo: Texto  
```
¡Hola {{paciente}}! 👋
```
- Parámetro: `paciente`

**Cuerpo (Body)**  
```
Tu cita con {{nutricionista}} ha sido agendada: 📅

📆 Fecha: {{fecha}}
🕐 Hora: {{hora}}
📋 Tipo: {{control}}

¡Te esperamos! ✨

Este mensaje es automático; no respondas a este número.
```
- Parámetros en orden: `nutricionista`, `fecha`, `hora`, `control`

---

## 2. confirmacion_cita_presencial (cita presencial — incluye dirección)

**Idioma:** Spanish (CHL) — código `es_CL`  
**Categoría:** Marketing  
**Nombre interno:** `confirmacion_cita_presencial`

### Contenido exacto para Meta

**Encabezado (Header)** — tipo: Texto  
```
¡Hola {{paciente}}! 👋
```
- Parámetro: `paciente`

**Cuerpo (Body)**  
```
Tu cita presencial con {{nutricionista}} ha sido agendada: 📅

📆 Fecha: {{fecha}}
🕐 Hora: {{hora}}
📋 Tipo: {{control}}
📍 Dirección: {{direccion}}

¡Te esperamos! ✨

Este mensaje es automático; no respondas a este número.
```
- Parámetros en orden: `nutricionista`, `fecha`, `hora`, `control`, `direccion`

---

## 3. confirmacion_cita_online (cita online — incluye enlace de reunión)

**Idioma:** Spanish (CHL) — código `es_CL`  
**Categoría:** Marketing  
**Nombre interno:** `confirmacion_cita_online`

### Contenido exacto para Meta

**Encabezado (Header)** — tipo: Texto  
```
¡Hola {{paciente}}! 👋
```
- Parámetro: `paciente`

**Cuerpo (Body)**  
```
Tu cita online con {{nutricionista}} ha sido agendada: 📅

📆 Fecha: {{fecha}}
🕐 Hora: {{hora}}
📋 Tipo: {{control}}
🔗 Enlace de la reunión: {{link_reunion}}

¡Te recomendamos unirte unos minutos antes! ✨

Este mensaje es automático; no respondas a este número.
```
- Parámetros en orden: `nutricionista`, `fecha`, `hora`, `control`, `link_reunion`

---

## 4. cancelacion_cita (opcional)

**Idioma:** Spanish (CHL) — código `es_CL`  
**Categoría:** UTILITY  
**Nombre interno:** `cancelacion_cita`

### Contenido exacto para Meta

**Encabezado (Header)** — tipo: Texto  
```
Hola {{paciente}} 👋
```
- Parámetro: `paciente`

**Cuerpo (Body)**  
```
Te informamos que tu cita con {{nutricionista}} ha sido cancelada: ❌

📆 Fecha: {{fecha}}
🕐 Hora: {{hora}}
💬 Motivo: {{motivo}}

Si necesitas reagendar, contacta con tu nutricionista.

Gracias por tu comprensión. 🙏
```
- Parámetros en orden: `nutricionista`, `fecha`, `hora`, `motivo`  
  (Si no hay motivo, la app puede enviar "-".)

---

## Resumen para Facebook Developer

| Plantilla                      | Idioma      | Código | Categoría  | Header (1) | Body |
|--------------------------------|-------------|--------|------------|------------|------|
| confirmacion_cita              | Spanish (CHL) | es_CL  | Marketing  | paciente   | nutricionista, fecha, hora, control |
| confirmacion_cita_presencial   | Spanish (CHL) | es_CL  | Marketing  | paciente   | nutricionista, fecha, hora, control, direccion |
| confirmacion_cita_online       | Spanish (CHL) | es_CL  | Marketing  | paciente   | nutricionista, fecha, hora, control, link_reunion |
| cancelacion_cita               | Spanish (CHL) | es_CL  | UTILITY    | paciente   | nutricionista, fecha, hora, motivo |

**Uso en la app (.env):**  
- `WHATSAPP_PLANTILLA_CONFIRMACION=1` (o cualquier valor distinto de `confirmacion_cita`): la app elige por modalidad — **online** → `confirmacion_cita_online` (con enlace de reunión), **presencial** → `confirmacion_cita_presencial` (con dirección de la empresa).  
- `WHATSAPP_PLANTILLA_CONFIRMACION=confirmacion_cita`: se usa solo la plantilla genérica (4 variables, sin dirección ni link).  
- La dirección para presencial sale de la **empresa** del nutricionista (campo `direccion`). El enlace online es el de Google Meet al confirmar la cita.

Al crear cada plantilla en Meta, elige **"Parámetros con nombre"** (named) y usa exactamente los nombres indicados.
