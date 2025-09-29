# 🗺️ Base de Datos de Regiones y Comunas de Chile

## 📋 Descripción

Sistema completo de gestión de ubicaciones geográficas de Chile para integrar en el sistema de clientes de NextLine CI4. Incluye todas las regiones y comunas oficiales del país actualizadas a 2024.

## 🚀 Instalación

### 1. Ejecutar Script SQL

```bash
# Opción 1: Ejecutar el script PHP
php execute_chile_regiones_comunas.php

# Opción 2: Importar directamente el SQL
mysql -u root -p nextline_ci4 < chile_regiones_comunas.sql
```

### 2. Verificar Instalación

```sql
-- Verificar que las tablas se crearon correctamente
SHOW TABLES LIKE '%regiones%';
SHOW TABLES LIKE '%comunas%';

-- Verificar datos
SELECT COUNT(*) as total_regiones FROM regiones WHERE activo = 1;
SELECT COUNT(*) as total_comunas FROM comunas WHERE activo = 1;
```

## 📊 Estructura de Datos

### Tabla `regiones`
- `id`: ID único
- `codigo`: Código oficial (ej: "RM", "V", "VIII")
- `nombre`: Nombre completo (ej: "Metropolitana", "Valparaíso")
- `numero`: Número de región (1-16)
- `activo`: Estado activo/inactivo

### Tabla `comunas`
- `id`: ID único
- `codigo`: Código oficial (ej: "13101", "05101")
- `nombre`: Nombre de la comuna
- `region_id`: ID de la región padre
- `activo`: Estado activo/inactivo

## 🔧 Uso en el Sistema

### 1. Modelos Disponibles

```php
// Cargar modelos
$regionModel = new \App\Models\Region();
$comunaModel = new \App\Models\Comuna();

// Obtener regiones activas
$regiones = $regionModel->getRegionesActivas();

// Obtener comunas de una región
$comunas = $comunaModel->getComunasPorRegion($regionId);
```

### 2. Controlador AJAX

El controlador `UbicacionController` proporciona endpoints para:

- `GET /dashboard/ubicacion/regiones` - Obtener todas las regiones
- `GET /dashboard/ubicacion/comunas/{region_id}` - Obtener comunas de una región
- `GET /dashboard/ubicacion/buscar-comunas?q=termino` - Buscar comunas
- `POST /dashboard/ubicacion/validar` - Validar región/comuna

### 3. Integración en Formularios

Ver archivo `ejemplo_formulario_clientes_actualizado.html` para ver cómo integrar en formularios de clientes.

## 📈 Estadísticas

- **16 Regiones** activas
- **346 Comunas** activas
- Datos oficiales del INE (Instituto Nacional de Estadísticas)
- Actualizado a 2024

## 🔄 Actualización del Modelo Cliente

Para usar las nuevas tablas en el modelo Cliente, actualizar:

```php
// En app/Models/Cliente.php
protected $allowedFields = [
    // ... campos existentes ...
    'region_id', 'comuna_id'  // Agregar estos campos
];

// Agregar relaciones
public function getRegion()
{
    return $this->belongsTo(Region::class, 'region_id');
}

public function getComuna()
{
    return $this->belongsTo(Comuna::class, 'comuna_id');
}
```

## 🎯 Próximos Pasos

1. **Ejecutar el script SQL** para crear las tablas
2. **Actualizar el formulario de clientes** usando el ejemplo proporcionado
3. **Modificar el modelo Cliente** para incluir region_id y comuna_id
4. **Actualizar el controlador de clientes** para manejar los nuevos campos
5. **Probar la funcionalidad** de selección dinámica región/comuna

## 📝 Notas Importantes

- Los datos están basados en la división político-administrativa oficial de Chile
- Las tablas incluyen campos `activo` para futuras actualizaciones
- Se mantiene integridad referencial entre regiones y comunas
- Los códigos oficiales facilitan integración con sistemas externos

## 🆘 Soporte

Si encuentras algún problema o necesitas actualizar los datos:

1. Verificar que las tablas se crearon correctamente
2. Revisar los logs de error de la aplicación
3. Confirmar que las rutas están configuradas correctamente
4. Validar que los modelos están cargados correctamente
