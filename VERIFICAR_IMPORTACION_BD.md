# Verificar que la importación de la BD esté completa

Si al importar el dump en otro servidor (por ejemplo Azure) sospechas que **no se importó todo** (tablas o datos faltantes), usa lo siguiente.

## 1. Verificar tablas y conteo de filas

En el servidor **donde importaste** (y donde corre la app), ejecuta:

```bash
php spark db:check
```

El comando:

- Comprueba que existan todas las tablas que la aplicación usa.
- Muestra el **número de filas** de cada tabla.

**Qué hacer con el resultado:**

- Si alguna tabla aparece como **(no existe)**: falta esa tabla en el dump o la importación falló. Re-exporta desde el origen incluyendo esa tabla y vuelve a importar.
- Si todas existen pero los **números de filas** son mucho menores que en el servidor de origen: es posible que el dump esté truncado (límite de tamaño, timeout, etc.). Re-exporta sin límites y re-importa.

## 2. Comparar con el servidor de origen

En el servidor **de origen** (donde está la BD “buena”):

1. Configura temporalmente `.env` (o una conexión) para apuntar a esa BD.
2. Ejecuta allí también `php spark db:check`.
3. Compara los conteos tabla por tabla con los del servidor donde importaste.

Si en origen hay muchas más filas en tablas críticas (`detalle_agenda`, `pacientes`, `usuario`, `empresa`, etc.), el dump no incluyó todos los datos.

## 3. Re-exportar el dump (origen)

Para evitar que se trunque o falte algo:

- **mysqldump** sin límite de tamaño (por tiempo o buffer). Ejemplo:

  ```bash
  mysqldump -u USUARIO -p --single-transaction --routines --triggers NOMBRE_BD > nextline_pyme.sql
  ```

- Si usas phpMyAdmin o similar: exportar “sin límite” y formato SQL.

## 4. Re-importar en destino (Azure u otro)

Con el archivo `.sql` en el servidor (por ejemplo en `writable/nextline_pyme.sql`):

```bash
php spark import:sql writable/nextline_pyme.sql --fresh
```

`--fresh` borra las tablas que vienen en el dump antes de importar, para evitar errores de claves duplicadas. **Solo úsalo si quieres reemplazar por completo la BD de ese entorno.**

Después de importar, vuelve a ejecutar `php spark db:check` y compara de nuevo con el origen.

## 5. Migraciones (y restaurar AUTO_INCREMENT)

Al importar un dump, a veces el esquema no se copia igual: por ejemplo **`agenda.id` puede quedar sin AUTO_INCREMENT** cuando en el origen sí lo tiene. Eso no debería pasar, pero si ocurre, después de importar ejecuta:

```bash
php spark migrate
```

Las migraciones del proyecto incluyen una que **asegura que `agenda.id` tenga AUTO_INCREMENT**; así el esquema queda igual que en el origen. También se aplican otras (p. ej. `AlterPacientesTipoPacienteEnum`).
