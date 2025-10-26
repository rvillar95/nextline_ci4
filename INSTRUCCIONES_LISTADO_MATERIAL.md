# 📋 Módulo de Listado de Materiales - Instrucciones de Instalación

## ✅ **Archivos Creados:**

### **1. Base de Datos:**
- `app/Database/Migrations/2025-10-26-193000_CreateListadoMaterialTables.php`
- `database_listado_material.sql` (SQL alternativo)

### **2. Modelos:**
- `app/Models/ListadoMaterial.php`
- `app/Models/ListadoMaterialItem.php`

### **3. Controlador:**
- `app/Controllers/Dashboard/ListadoMaterialController.php`

### **4. Vistas:**
- `app/Views/Modulos/listado_material/lista.php`
- `app/Views/Modulos/listado_material/registro.php`
- `app/Views/Modulos/listado_material/editar.php`
- `app/Views/Modulos/listado_material/detalle.php`

### **5. Rutas:**
- Agregadas en `app/Config/Routes.php` (líneas 200-213)

---

## 🚀 **Pasos para Probar Localmente:**

### **Paso 1: Crear las Tablas en la Base de Datos**

**Opción A - Usando la Migración de CodeIgniter:**
```bash
php spark migrate
```

**Opción B - Usando phpMyAdmin (más fácil):**
1. Abre phpMyAdmin
2. Selecciona tu base de datos `nextline_constructor`
3. Ve a la pestaña **SQL**
4. Copia y pega el contenido del archivo `database_listado_material.sql`
5. Haz clic en **Continuar**

---

### **Paso 2: Crear Permisos del Módulo** ⚠️ **IMPORTANTE**

En phpMyAdmin:
1. Selecciona tu base de datos `nextline_constructor`
2. Ve a la pestaña **SQL**
3. Copia y pega el contenido del archivo `permisos_listado_material.sql`
4. Haz clic en **Continuar**

Esto creará:
- ✅ El módulo "Listado de Materiales" en tu sistema
- ✅ Todas las rutas y permisos necesarios
- ✅ Asignación automática al perfil Administrador (ID 1)

**Nota:** Si tu perfil administrador tiene otro ID, modifica el SQL en la línea:
```sql
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, ...)
SELECT 1, @modulo_id, ...  -- Cambia el "1" por tu ID de perfil
```

---

### **Paso 3: Acceder al Módulo**

1. Inicia sesión en el dashboard
2. Accede directamente a la URL:
```
http://localhost:3306/nextline_ci4/dashboard/listado-material/lista
```

---

## 🔑 **URLs Disponibles:**

| Acción | URL |
|--------|-----|
| Lista | `/dashboard/listado-material/lista` |
| Nuevo | `/dashboard/listado-material/registro` |
| Editar | `/dashboard/listado-material/editar/1` |
| Detalle | `/dashboard/listado-material/detalle/1` |
| PDF | `/dashboard/listado-material/generarPDF/1` |

---

## 🎯 **Características del Módulo:**

### **✅ Funcionalidades Implementadas:**
1. ✅ CRUD completo (Crear, Listar, Editar, Eliminar, Ver detalle)
2. ✅ Generación automática de número de listado (LM-2025-0001)
3. ✅ Asociación opcional a Cliente y Proyecto
4. ✅ Items/materiales con campos opcionales (unidad, cantidad)
5. ✅ Estados (borrador, finalizado, enviado, archivado)
6. ✅ Observaciones generales
7. ✅ DataTable con búsqueda y filtros
8. ✅ Generación de PDF con diseño profesional
9. ✅ Validaciones frontend y backend
10. ✅ Interfaz moderna con gradientes morados/turquesas

### **📋 Estructura de Datos:**

**Listado Principal:**
- Número automático (LM-YYYY-####)
- Título
- Cliente (opcional)
- Proyecto (opcional)
- Fecha
- Estado
- Observaciones

**Items/Materiales:**
- Nombre del material (obligatorio)
- Descripción (opcional)
- Unidad de medida (opcional) - Si no se llena, no se muestra en PDF
- Cantidad (opcional) - Si no se llena, no se muestra en PDF

---

## 🎨 **Diseño del PDF:**

El PDF mantiene el diseño profesional de las cotizaciones:
- ✅ Header con gradiente morado (#667eea → #764ba2)
- ✅ Información general en caja con borde izquierdo
- ✅ Tabla de materiales con filas alternadas
- ✅ Campos opcionales se ocultan automáticamente si están vacíos
- ✅ Observaciones con caja amarilla si existen
- ✅ Footer con fecha de generación

---

## 🧪 **Para Probar:**

1. **Crear un listado:**
   - Ve a "Nuevo Listado"
   - Completa título y fecha
   - Selecciona cliente (opcional)
   - Agrega materiales (mínimo 1)
   - Guarda

2. **Ver detalle:**
   - Desde la lista, haz clic en el ícono del ojo

3. **Generar PDF:**
   - Desde detalle o lista, haz clic en el botón PDF
   - El PDF se abre en nueva pestaña

4. **Editar:**
   - Modifica cualquier campo
   - Agrega o elimina materiales

5. **Eliminar:**
   - Desde la lista, haz clic en el botón eliminar
   - Confirma la acción

---

## 🔧 **Agregar al Menú del Dashboard (Opcional):**

Si quieres que aparezca en el menú lateral:

1. Ve a tu gestor de módulos en el dashboard
2. Crea un nuevo módulo llamado "Listado de Materiales"
3. Agrega los detalles de módulo:
   - Lista: `/dashboard/listado-material/lista`
   - Registro: `/dashboard/listado-material/registro`
   - etc.

---

## 📝 **Notas Importantes:**

- ⚠️ Los campos **Unidad de Medida** y **Cantidad** son opcionales
- ⚠️ Si no se completan, NO aparecerán en el PDF
- ⚠️ Debe haber al menos 1 material para guardar el listado
- ⚠️ El número de listado se genera automáticamente
- ⚠️ Al eliminar un listado, se eliminan automáticamente todos sus items (CASCADE)

---

## 🐛 **Troubleshooting:**

### **Error: Tabla no existe**
→ Ejecuta el SQL en phpMyAdmin

### **Error 404 al acceder**
→ Verifica que las rutas estén en `app/Config/Routes.php`

### **PDF no se genera**
→ Verifica que `PDFGenerator` esté funcionando (ya lo usas en cotizaciones)

### **No aparecen clientes/proyectos**
→ Verifica que tengas clientes activos y proyectos en tu base de datos

---

## ✅ **¡Listo para Probar!**

Ejecuta el SQL y accede a:
```
http://localhost:3306/nextline_ci4/dashboard/listado-material/lista
```

¡Cualquier duda, avísame! 🚀

