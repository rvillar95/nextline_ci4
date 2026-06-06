# Gym + NutriNext — recordatorio deploy, CSRF y ramas

Documento vivo para no olvidar qué va en cada rama y qué subir al hosting.

---

## Ramas

| Rama | Qué es | Hosting típico |
|------|--------|----------------|
| `feature/gcp` | NutriNext clínico (dashboard nutricionista, agenda, pacientes) | `nutrinext.nextline.cl` |
| `feature/gym` | Módulo gym (coach + portal alumno) | Misma app; merge o deploy aparte |

El gym **no está completo** en `feature/gcp` hasta hacer merge (o cherry-pick) desde `feature/gym`.

---

## Código vs base de datos

| Acción | Herramienta | Qué actualiza |
|--------|-------------|---------------|
| Subir PHP/JS/CSS | `deploy.php` / `deploy.sh` (git pull) | Solo **código** |
| Cambiar tablas/permisos | `.sql` en phpMyAdmin o `php spark migrate` | Solo **BD** |

El deploy **no ejecuta SQL**. Hay que correr scripts a mano si falta schema.

---

## `lib/js/nutrinext-csrf.js` — importante

**Qué hace:** mantiene el token CSRF actualizado tras AJAX POST (CI4 regenera el token en cada POST). Sin esto, el **segundo** guardado en gym suele fallar con *"The action you requested is not allowed"*.

**Dónde se carga:**

- Dashboard: `app/Views/template/footer.php`
- Portal alumno: `app/Views/layout/alumno.php`

**Quién lo usa:**

- `lib/js/nutrinext-alumno-entrenar.js` — series, sensación
- `lib/js/nutrinext-gym-save.js` — builder rutina/programa
- `lib/js/modals.js` — formularios dinámicos
- Extender sesión en `layout/dashboard.php`

**Estado en ramas (revisar antes de deploy):**

| Archivo | `feature/gcp` | `feature/gym` remoto |
|---------|---------------|----------------------|
| `nutrinext-csrf.js` | ❌ | Verificar commit |
| `footer.php` con script | ❌ | Verificar commit |
| `layout/alumno.php` | ❌ | Verificar commit |
| `nutrinext-gym-save.js` | ❌ | Verificar commit |

En `feature/gcp`, `modals.js` usa CSRF básico (`meta` + `csrf_test_name` fijo). Basta para forms POST normales del clínico; **no** renueva token tras AJAX.

**DataTables gym (`getRutinas`, etc.):** usan **GET** → no dependen de CSRF.

---

## Checklist — subir gym al hosting

### 1. Código (git + deploy)

Confirmar que el remoto incluye (como mínimo):

```
lib/js/nutrinext-csrf.js
lib/js/nutrinext-gym-datatables.js
lib/js/nutrinext-gym-save.js
lib/js/nutrinext-alumno-entrenar.js
lib/js/modals.js                    (versión con NutriNextCsrf)
lib/css/nutrinext-alumno.css
lib/css/nutrinext-gym-dashboard.css
app/Views/template/footer.php
app/Views/layout/alumno.php
app/Views/Modulos/gym/**              (vistas + listas con base_url en AJAX)
app/Controllers/Dashboard/Gym/**
app/Controllers/Alumno/**
app/Filters/SessionFilter.php         (sub-rutas gym)
```

DataTables: URLs AJAX deben ser **absolutas** (`base_url('dashboard/gym/rutina/getRutinas')`), no relativas (`getRutinas`).

### 2. Base de datos (si no usas `php spark migrate` en prod)

Ejecutar en orden (idempotentes donde diga `IF NOT EXISTS`):

```
database/schema_gym.sql                    (solo instalación inicial)
database/seed_gym_modulos_perfiles.sql
database/migration_gym_entrenamiento.sql
database/migration_gym_ejercicio_pr.sql
database/migration_gym_video_recordatorios.sql
database/migration_gym_notas_coach.sql
database/migration_gym_ejercicio_sensacion.sql
database/migration_gym_alumno_subrutas_perm.sql   (opcional)
```

O en local/desarrollo:

```bash
php spark migrate
```

Scripts adicionales NutriNext (permisos, etc.): `app/Database/Sql/*.sql`

### 3. Probar en prod

- [ ] `/dashboard/gym/rutina/lista` — tabla carga (Network → `getRutinas` 200 JSON)
- [ ] Editar rutina → guardar ejercicios (POST, sin error CSRF al repetir)
- [ ] Portal alumno → entrenar → marcar serie dos veces seguidas
- [ ] Móvil: entrenar, cronómetro, finalizar

---

## Deploy automático (referencia)

- `deploy.php` / `deploy.sh` → rama configurada (ej. `feature/endgame`), path `/home/nextline/nutrinext.nextline.cl`
- Solo `git pull` + composer + limpiar caché CI4
- **No** incluye SQL ni `spark migrate`

---

## Merge gym → gcp (cuando toque)

1. Merge `feature/gym` → `feature/gcp` (o PR)
2. Resolver conflictos en `footer.php`, `Routes.php`, `SessionFilter.php`
3. Verificar que `nutrinext-csrf.js` quede en footer **antes** de `modals.js`
4. Deploy + SQL pendientes en hosting
5. Probar checklist arriba

---

*Última revisión: mayo 2026 — actualizar si cambian ramas de deploy o migraciones.*
