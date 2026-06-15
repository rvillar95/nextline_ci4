# NutriNext CSRF (`feature/gcp`)

## Archivo

`lib/js/nutrinext-csrf.js` → expone `window.NutriNextCsrf`

## Carga

Incluido en **`app/Views/template/header.php`** (después de jQuery), así está disponible en todo el dashboard.

Requisito en layout: `<meta name="csrf-token" content="<?= csrf_hash() ?>">` (ya en header).

## Para qué sirve

CodeIgniter **regenera el token CSRF en cada POST**. Sin actualizar el token en el cliente, el **segundo** AJAX POST falla con *"The action you requested is not allowed"*.

`NutriNextCsrf` lee/escribe el meta y los inputs hidden, y ofrece helpers para POST.

## API rápida

```javascript
NutriNextCsrf.getToken()           // token actual
NutriNextCsrf.getName()            // nombre del campo (ej. csrf_test_name)
NutriNextCsrf.setToken(nuevo)      // tras respuesta del servidor
NutriNextCsrf.applyFromJson(data)  // si data.csrf_token viene en JSON
NutriNextCsrf.postJson(url, body)  // fetch POST JSON con CSRF
```

Tras cada POST AJAX, si el backend devuelve `csrf_token` en JSON (o header `X-CSRF-TOKEN`), llamar `applyFromJson`.

## Ya integrado en gcp

| Archivo | Uso |
|---------|-----|
| `lib/js/modals.js` | Forms de eliminar / convertir cotización |
| `app/Views/layout/dashboard.php` | Notificaciones (marcar leída / todas) |

## En vistas con AJAX propio

Patrón recomendado:

```javascript
.then(function (data) {
    if (window.NutriNextCsrf) NutriNextCsrf.applyFromJson(data);
    // ...
});
```

Para el body del POST, usar siempre el token **actual**:

```javascript
var name = NutriNextCsrf ? NutriNextCsrf.getName() : 'csrf_test_name';
var token = NutriNextCsrf ? NutriNextCsrf.getToken() : $('meta[name="csrf-token"]').attr('content');
```

## Backend

En respuestas JSON de endpoints AJAX, incluir cuando aplique:

```php
'csrf_token' => csrf_hash(),
```

O header: `->setHeader('X-CSRF-TOKEN', csrf_hash())`

## Deuda técnica (obligatorio)

**P1 — Quitar exclusiones CSRF y atajos de SessionFilter** que saltan permisos.  
Plan completo, checklist y criterios de aceptación: **[PENDIENTES.md](./PENDIENTES.md)**.

Pacientes (`activar` / `eliminar`) ya sigue el modelo correcto; el resto del dashboard está pendiente.

## Rama `feature/gym`

El portal alumno y gym usan el mismo archivo; en `feature/gym` también se carga desde `layout/alumno.php`. No mezclar ramas sin commitear este archivo en ambas si hace falta.
