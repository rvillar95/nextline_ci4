# 🔧 Corregir Redirect URI del Calendario

## ❌ Problema

El callback redirige a una URL con `index.php`, pero el `redirect_uri` configurado en Google Cloud Console puede no coincidir.

## ✅ Solución

### Opción 1: Configurar Redirect URI en `.env` (Recomendado)

Agrega esta línea a tu archivo `.env`:

```env
# Google Calendar - Redirect URI (debe coincidir EXACTAMENTE con Google Cloud Console)
GOOGLE_CALENDAR_REDIRECT_URI=http://localhost/codeigniter4/nextline_ci4/index.php/dashboard/agenda/calendario/callback
```

**O si NO usas index.php en las URLs:**

```env
GOOGLE_CALENDAR_REDIRECT_URI=http://localhost/codeigniter4/nextline_ci4/dashboard/agenda/calendario/callback
```

### Opción 2: Actualizar en Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. **APIs & Services** > **Credentials**
3. Abre tu **OAuth 2.0 Client ID**
4. En **"Authorized redirect URIs"**, agrega **AMBAS** URLs:

```
http://localhost/codeigniter4/nextline_ci4/index.php/dashboard/agenda/calendario/callback
http://localhost/codeigniter4/nextline_ci4/dashboard/agenda/calendario/callback
```

5. **Guarda** los cambios
6. Espera 1-2 minutos para que se apliquen

## 🔍 Verificar qué URL se está generando

Para ver qué URL exacta se está generando, revisa los logs después de intentar conectar:

1. Intenta conectar: `/dashboard/agenda/calendario/connect`
2. Revisa los logs: `writable/logs/log-[fecha].log`
3. Busca el mensaje que contiene "redirect_uri"

O agrega temporalmente este código en `CalendarService.php` línea ~65:

```php
log_message('info', 'Redirect URI generado: ' . $config['redirect_uri']);
```

## 📝 Nota Importante

El `redirect_uri` debe coincidir **EXACTAMENTE** (carácter por carácter) con lo configurado en Google Cloud Console, incluyendo:
- ✅ Protocolo (`http://` o `https://`)
- ✅ Dominio completo
- ✅ Ruta completa
- ✅ `index.php` si lo usas o no si no lo usas
- ✅ Sin trailing slash al final

## 🚀 Después de Configurar

1. Guarda el `.env` o actualiza Google Cloud Console
2. Intenta conectar de nuevo
3. El callback debería funcionar correctamente
4. Los tokens se guardarán en `usuario_calendar_tokens`
