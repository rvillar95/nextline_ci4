# Correos yendo a spam (Gmail, etc.)

Los correos enviados desde la app (noreply@vitasync.cl) llegan pero caen en **spam** porque el dominio del remitente no autoriza al servidor que envía (mail.nextline.cl).

## Qué hacer (DNS del dominio vitasync.cl)

1. **SPF** – En el DNS de **vitasync.cl**, agregar un registro TXT (o ajustar el existente) con la política SPF que incluya al servidor de correo de nextline. El valor exacto lo da quien administra mail.nextline.cl (ej. zglobalhost), algo como:
   - `v=spf1 include:zglobalhost.com ~all` o el `include` que indiquen.

2. **DKIM** – Pedir al proveedor de mail.nextline.cl la clave DKIM y el registro TXT que hay que crear en el DNS de vitasync.cl (o del dominio que firme). Sin DKIM es más fácil que caiga en spam.

3. **DMARC** (opcional) – Registro TXT en `_dmarc.vitasync.cl` con política (p=quarantine o p=none) para indicar qué hacer con correos que no pasen SPF/DKIM.

## Quién lo configura

- DNS de **vitasync.cl**: quien lo administre (ej. Cloudflare).
- Valores de SPF/DKIM: soporte o panel del proveedor de **mail.nextline.cl** (zglobalhost / nextline).

## Mientras tanto

Los usuarios pueden marcar un correo de “Sistema de Agenda” como **“No es spam”** y moverlo a Bandeja de entrada; eso ayuda a que Gmail deje de filtrar esos envíos.
