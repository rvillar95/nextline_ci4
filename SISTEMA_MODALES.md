# Sistema de Modales Estandarizados

## 📋 Descripción
Sistema reutilizable de modales Bootstrap para reemplazar `alert()`, `confirm()` y otros diálogos nativos del navegador.

## 🚀 Archivos Creados

### 1. `app/Views/components/modals.php`
Componente con todos los modales reutilizables:
- Modal de confirmación de eliminación
- Modal de información
- Modal de éxito
- Modal de error

### 2. `public/js/modals.js`
Funciones JavaScript reutilizables para manejar los modales.

## 💻 Uso en las Vistas

### Incluir el componente de modales:
```php
<?= view('components/modals') ?>
```

### Incluir el JavaScript:
```html
<script src="<?= base_url('js/modals.js') ?>"></script>
```

## 🔧 Funciones Disponibles

### 1. Eliminar con confirmación
```javascript
// Reemplaza: if (confirm('¿Eliminar?')) { ... }
eliminarConConfirmacion(url, mensaje);
```

### 2. Mostrar información
```javascript
// Reemplaza: alert('Mensaje')
mostrarModalInformacion('Mensaje informativo');
```

### 3. Mostrar éxito
```javascript
mostrarModalExito('Operación completada exitosamente');
```

### 4. Mostrar error
```javascript
mostrarModalError('Ha ocurrido un error');
```

### 5. Confirmar acción personalizada
```javascript
confirmarAccion('¿Estás seguro?', function() {
    // Código a ejecutar
});
```

## 📝 Ejemplos de Implementación

### En botones de eliminar:
```javascript
window.eliminarElemento = function(id) {
    eliminarConConfirmacion(
        '<?= base_url('dashboard/modulo/eliminar') ?>/' + id,
        '¿Estás seguro de que deseas eliminar este elemento?'
    );
};
```

### En formularios con validación:
```javascript
if (errores.length > 0) {
    mostrarErroresValidacion(errores);
    return false;
}
```

### Para mostrar mensajes de PHP:
```javascript
// En la vista PHP, dentro del script:
$(document).ready(function() {
    <?php if (session()->getFlashdata('success')): ?>
        mostrarModalExito('<?= addslashes(session()->getFlashdata('success')) ?>');
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        mostrarModalError('<?= addslashes(session()->getFlashdata('error')) ?>');
    <?php endif; ?>
});
```

## 🎨 Personalización

### Cambiar mensajes por defecto:
```javascript
// En modals.js, modificar las funciones
function mostrarModalEliminar(mensaje, callback) {
    $('#modalMensaje').text(mensaje || 'Tu mensaje personalizado');
    // ...
}
```

### Agregar nuevos tipos de modal:
1. Agregar el HTML en `components/modals.php`
2. Crear la función JavaScript en `modals.js`
3. Usar en las vistas

## ✅ Beneficios

1. **Consistencia**: Todos los modales tienen el mismo diseño
2. **Reutilización**: Un solo componente para todo el proyecto
3. **Mantenibilidad**: Cambios centralizados
4. **UX mejorada**: Mejor experiencia de usuario que alerts nativos
5. **Responsive**: Los modales se adaptan a móviles
6. **Accesibilidad**: Mejor soporte para lectores de pantalla

## 🔄 Migración de Código Existente

### Antes:
```javascript
if (confirm('¿Eliminar?')) {
    window.location.href = url;
}
```

### Después:
```javascript
eliminarConConfirmacion(url, '¿Eliminar?');
```

### Antes:
```javascript
alert('Mensaje');
```

### Después:
```javascript
mostrarModalInformacion('Mensaje');
```
