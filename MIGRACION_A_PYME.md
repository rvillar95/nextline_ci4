# Guía de Migración: nextline_constructor → nextline_pyme

## Pasos para cambiar de base de datos

### 1. Backup de la BD actual
```sql
-- Hacer backup de nextline_constructor (o la BD actual)
mysqldump -u root -p nextline_constructor > backup_constructor_$(date +%Y%m%d).sql
```

### 2. Importar nextline_pyme
```sql
-- Crear la nueva base de datos
CREATE DATABASE IF NOT EXISTS nextline_pyme CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;

-- Importar el archivo SQL
-- Desde phpMyAdmin o línea de comandos:
mysql -u root -p nextline_pyme < "c:\Users\rafae\Downloads\nextline_pyme (4).sql"
```

### 3. Agregar el módulo 32
```sql
-- Ejecutar el script para agregar el módulo 32
mysql -u root -p nextline_pyme < agregar_modulo_32_pyme.sql
```

O desde phpMyAdmin, ejecutar el contenido de `agregar_modulo_32_pyme.sql`

### 4. Actualizar configuración de la aplicación

**Opción A: Actualizar Database.php**
```php
// app/Config/Database.php
'database' => 'nextline_pyme',  // Cambiar de 'nextline_web' o 'nextline_constructor'
```

**Opción B: Actualizar .env** (si existe)
```env
database.default.database = nextline_pyme
```

### 5. Verificar conexión
- Acceder al dashboard
- Verificar que los módulos se carguen correctamente
- Verificar que el módulo 32 aparezca en el menú

## Notas importantes

- ✅ Todos los módulos existentes se mantienen
- ✅ Se agrega el módulo 32 (Listado de Materiales)
- ✅ Se mantiene todo el código existente
- ✅ Sistema de paquetes/suscripciones disponible
- ⚠️ Hacer backup antes de migrar
- ⚠️ Verificar que no haya conflictos de IDs

## Estructura de la nueva BD

### Nuevas tablas en nextline_pyme:
- `paquetes` - Sistema de suscripciones
- `paquete_modulo` - Módulos por paquete
- Procedimientos almacenados para permisos por paquete

### Módulos disponibles:
- Todos los módulos de constructor (1-31)
- Módulo 32 agregado (Listado de Materiales)
- Sistema de agenda completo
- Sistema de especialidades
