# VitaSync (Nextline CI4)

Aplicación CodeIgniter 4 desplegada en **Google Cloud Platform (GKE)** con base de datos **Cloud SQL (MySQL)**.

---

## Desarrollo local

- PHP 8.2, extensión MySQLi, intl.
- Copiar `.env` desde `.env.example` y configurar base de datos local.
- `composer install` y servidor con `php spark serve` o Apache apuntando a la raíz del proyecto (el front controller es `index.php` en la raíz, no en `public/`).

---

## Despliegue en GCP (rama `feature/gcp`)

El pipeline **GitHub Actions** (`.github/workflows/google.yml`) se ejecuta en cada push a `feature/gcp` y:

1. Construye la imagen Docker (PHP 8.2 + Apache, extensiones mysqli, pdo_mysql, intl, zip).
2. Sube la imagen a **Artifact Registry** (`southamerica-west1-docker.pkg.dev/vitasync-dev/vitasync-repository/vitasync-app`).
3. Despliega en **GKE** con Kustomize (Deployment + Service LoadBalancer).

### Requisitos previos

- **Secret de GitHub** (Settings → Secrets and variables → Actions, o en el environment `production`):
  - `GCP_CREDENTIALS`: JSON de la cuenta de servicio de GCP con permisos para GKE, Artifact Registry y (opcional) Cloud SQL.
  - `DATABASE_PASSWORD`: contraseña del usuario de la base de datos en Cloud SQL (usado para crear el Secret de Kubernetes `vitasync-db`).

### Conexión a Cloud SQL

La app lee la configuración de base de datos desde **variables de entorno** cuando están definidas (en local se usan los valores por defecto de `app/Config/Database.php`):

| Variable            | Origen en GKE                    | Ejemplo / descripción   |
|---------------------|-----------------------------------|--------------------------|
| `APP_BASE_URL`      | Deployment (env)                  | URL pública de la app (p. ej. `http://34.176.25.59` o `https://tudominio.com`) para redirects y `base_url()` |
| `DATABASE_HOSTNAME` | Deployment (env)                  | IP de la instancia Cloud SQL |
| `DATABASE_PORT`     | Deployment (env)                  | `3306`                   |
| `DATABASE_NAME`     | Deployment (env)                  | `nextline_pyme`          |
| `DATABASE_USERNAME` | Deployment (env)                  | `root` (o el usuario que uses) |
| `DATABASE_PASSWORD` | Secret de K8s `vitasync-db` (key `password`) | Se rellena desde el secret de GitHub `DATABASE_PASSWORD` en el workflow |

El workflow crea o actualiza el Secret `vitasync-db` en el clúster antes de desplegar, usando el valor de `DATABASE_PASSWORD` del repositorio. No pongas la contraseña en el código ni en los YAML.

### IP pública de Cloud SQL y seguridad

**¿La IP pública tiene que ser siempre así? ¿No es más vulnerable?**

No es obligatorio usar IP pública. Es **más seguro** no exponer la base de datos a internet:

- **IP pública**  
  La instancia tiene una IP pública y sueles abrirla a “redes autorizadas” (IPs de salida de GKE, tu oficina, etc.). Cualquier fuga de esa IP o un error en el firewall aumenta el riesgo.

- **IP privada (recomendado)**  
  Si habilitas **conectividad por IP privada** en Cloud SQL y tu clúster GKE está en la misma VPC (o una VPC conectada por VPC peering), la app puede conectarse a la instancia por **IP privada** (p. ej. `10.x.x.x`). El tráfico no sale a internet y no hace falta exponer MySQL públicamente.

**Recomendación:** en producción, habilita **IP privada** en Cloud SQL, configura la VPC y el private IP range, y en el Deployment usa la **IP privada** de la instancia como `DATABASE_HOSTNAME` en lugar de la IP pública. Así la base de datos no es accesible desde internet.

**Configurar IP privada (Acceso privado a servicios, PSA):**

1. En la instancia Cloud SQL → Editar → **IP privada** + **Acceso privado a servicios (PSA)**.
2. Elige la red VPC (p. ej. `default`; debe ser la misma donde corre GKE).
3. Si aparece "No se configuró la conexión de red" y "Service Networking API: No habilitada", pulsa **Confirmar configuración de red**. Eso habilita la API y asigna un rango de IP en la VPC (1–2 min).
4. Guarda los cambios y anota la **IP privada** asignada a la instancia.
5. En `k8s/deployment.yaml`, cambia `DATABASE_HOSTNAME` al valor de esa IP privada (y opcionalmente desactiva la IP pública de la instancia).

### Docker

- **Dockerfile** en la raíz: base `php:8.2-apache`, extensiones (mysqli, pdo_mysql, intl, zip), `unzip` y Composer para instalar dependencias.
- **.dockerignore** excluye `.env` para que la imagen no lo lleve: en GKE la configuración de BD debe venir solo de las variables de entorno (`DATABASE_*`). Si `.env` estuviera en la imagen, CodeIgniter cargaría `database.default.hostname=localhost` y la conexión a Cloud SQL fallaría.
- El directorio **writable** (cache, logs, session, uploads) se crea/ajusta en la imagen y se asigna a `www-data` para que Apache pueda escribir.
- DocumentRoot de Apache: `/var/www/html` (index en la raíz del proyecto).

### Kubernetes (Kustomize)

- **kustomization.yaml**: incluye `k8s/deployment.yaml` y `k8s/service.yaml`.
- **Deployment** `vitasync-api`: env vars de base de datos y contraseña desde el Secret `vitasync-db`. La imagen se sustituye en el workflow con el tag correspondiente al commit.
- **Service** tipo LoadBalancer: IP estática reservada en GCP (p. ej. `34.176.25.59`). El dominio (DNS) debe apuntar con un registro A a esa IP para acceder a la app.

### Dominio y HTTPS (Cloudflare)

- El dominio **vitasync.cl** apunta a la IP del LoadBalancer (`34.176.25.59`). La variable **`APP_BASE_URL`** en el deployment debe coincidir con la URL pública (p. ej. `http://vitasync.cl` o `https://vitasync.cl`) para que redirects y `base_url()` usen el dominio correcto.
- Con **Cloudflare proxy** (registro A en modo "Proxied"): en SSL/TLS puedes usar **Flexible** para que los usuarios entren por `https://vitasync.cl` aunque el servidor siga en HTTP. Si más adelante quieres HTTPS hasta el servidor, cambia `APP_BASE_URL` a `https://vitasync.cl` y configura certificado en GKE (p. ej. cert-manager) o Cloudflare "Full (strict)".
- **ForceHTTPS:** El filtro que redirige a HTTPS solo se activa en producción si la variable de entorno **`FORCE_HTTPS=true`** está definida. Así, con `APP_BASE_URL=http://...` (o detrás de Cloudflare Flexible) los recursos se sirven por HTTP y la página no intenta cargar CSS/JS desde `https://` (evitando 522 y contenido mixto). Cuando tengas HTTPS real en origen, define `FORCE_HTTPS=true` en el deployment para forzar HTTPS.

### Resumen de archivos relevantes

| Archivo | Descripción |
|---------|-------------|
| `.github/workflows/google.yml` | Build, push a Artifact Registry, creación del Secret de BD y deploy a GKE. |
| `Dockerfile` | Imagen PHP 8.2 + Apache, extensiones, Composer, permisos de `writable`. |
| `k8s/deployment.yaml` | Deployment de la app y variables de entorno / Secret para Cloud SQL. |
| `k8s/service.yaml` | Service LoadBalancer e IP estática. |
| `app/Config/Database.php` | Configuración de BD; usa env vars en GKE cuando están definidas. |

---

## Licencia y contacto

Según la configuración del proyecto.
