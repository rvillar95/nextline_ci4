# Almacenamiento de documentos en hosting (sin GCP)

Guía para **nutrinext.cl**, **gym.nextline.cl** o cualquier hosting con PHP + Apache/LiteSpeed, equivalente a lo que configurarías en un bucket de GCP.

---

## Equivalencia con las pantallas de GCP

| En GCP (bucket) | En hosting NutriNext |
|-----------------|----------------------|
| Ubicación **us** multi-región | Disco del servidor (misma región que el sitio, ej. Chile) |
| Clase **Standard** | Carpeta en disco SSD/HDD del plan |
| **Prevención acceso público: Sí** | Documentos en `writable/storage/private/` (no URL directa) |
| Control **Uniforme** | Solo la app PHP lee/escribe; permisos por sesión |
| Soft delete 7 días | Backup manual del hosting (cPanel / copia `writable/`) |
| Encriptación Google | HTTPS del sitio + permisos 750 en carpetas |

---

## Estructura de carpetas (recomendada)

En **GCS** o disco local (`private/`), misma lógica:

```
private/
  empresa/{empresa_id}/              ← ID fijo de la consultora
    nutricionista/{nutricionista_id}/
      credenciales/                  ← títulos, diplomas (mi perfil)
      pacientes/{paciente_id}/
        documentos/                  ← pautas, recetas, informes
```

Ejemplo en el bucket `nutrinext-document`:

`private/empresa/3/nutricionista/12/pacientes/45/documentos/1748871234_a1b2.pdf`

En BD (`documentos.archivo_ruta`) se guarda la clave completa desde `private/...`.

- **Público:** `https://tudominio.cl/uploads/perfil/foto.jpg`
- **Privado:** solo con login → `dashboard/mi-perfil/credencial/{id}/descargar`

---

## Configuración en `.env` (opcional)

```env
#--------------------------------------------------------------------
# ALMACENAMIENTO (hosting local)
#--------------------------------------------------------------------
storage.driver = local
storage.publicRoot = uploads
storage.privateRoot = storage/private
storage.maxDocumentMb = 5
storage.maxImageMb = 2
```

### Google Cloud Storage (producción)

```env
storage.driver = gcs
GCS_BUCKET = nutrinext-document
GCS_PROJECT_ID = nutrinext-dev
GOOGLE_APPLICATION_CREDENTIALS = writable/credentials/gcp-storage.json
# En hosting con SSL válido SIEMPRE false (no copiar el true de WAMP):
storage.gcsInsecureSsl = false
```

En el servidor ejecute `composer install --no-dev` para tener `google/cloud-storage` y `vendor/` completo.

Si aparece `Guzzle6HttpHandler not found`, suele ser `vendor` incompleto o `storage.gcsInsecureSsl=true` en producción.

Si no defines nada, se usan esos valores por defecto (`app/Config/Storage.php`).

---

## PHP en el hosting (cPanel / WAMP)

En `.htaccess` raíz ya hay (o el panel del hosting):

- `upload_max_filesize` ≥ **50M**
- `post_max_size` ≥ **50M**
- `memory_limit` ≥ **256M**

En **LiteSpeed/cPanel** no uses `php_value` en `.htaccess` si el host lo prohíbe; configúralo en “Select PHP Version” → Options.

---

## Permisos recomendados (FTP / SSH)

| Carpeta | Permiso |
|---------|---------|
| `writable/` | 755 o 775 |
| `writable/storage/private/` | **750** (solo el usuario del servidor web escribe) |
| `uploads/` | 755 |
| `uploads/perfil/` | 755 |

Tras el deploy, verifica que exista `writable/storage/private/credenciales/` (se crea sola al subir el primer documento).

---

## Código en la aplicación

- **Servicio:** `app/Services/StorageService.php`
- **Config:** `app/Config/Storage.php`
- **Descarga privada:** `GET dashboard/mi-perfil/credencial/{id}/descargar`

Los documentos nuevos de **Mi perfil → Credenciales** se guardan como:

`private/credenciales/{id_usuario}/archivo.pdf`

en BD (`archivo_ruta`), no como URL pública.

---

## Copias de seguridad

1. Base de datos MySQL (phpMyAdmin / backup automático del hosting).
2. Carpetas:
   - `writable/storage/private/`
   - `uploads/perfil/` (y lo que uses en `uploads/`)

Programa backup semanal en el panel del hosting hasta migrar a GCP.

---

## Migración futura a GCP

Cuando tengas presupuesto:

1. Crear bucket (región `southamerica-west1`, privado, Standard).
2. Añadir `google/cloud-storage` y driver `gcs` en `StorageService`.
3. Script `gsutil rsync` desde `writable/storage/private` al bucket.
4. Cambiar `storage.driver = gcs` en producción.

Las rutas en BD (`private/...`) pueden mantenerse; solo cambia el driver.

---

## Comprobar que funciona

1. Login como nutricionista → **Mi perfil** → credencial con PDF.
2. En el servidor debe aparecer el archivo en  
   `writable/storage/private/credenciales/{tu_id}/`.
3. “Ver” el documento debe abrir la URL  
   `/dashboard/mi-perfil/credencial/X/descargar` (con sesión).
4. Probar en el navegador (sin login):  
   `https://tudominio.cl/writable/storage/private/...` → debe dar **403/404**, no el PDF.

---

## Producción nutrinext.cl / gym.nextline.cl

1. Subir código con `StorageService` y carpetas `.htaccess`.
2. Ajustar `RewriteBase /` en `.htaccess` raíz si el sitio está en la raíz del dominio.
3. Crear `writable/storage/private` con permisos de escritura.
4. No commitear archivos subidos por usuarios (solo `.gitkeep` si hace falta).
