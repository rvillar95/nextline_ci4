# 📦 Análisis: Sistema de Paquetes de NextLine Presencia

## 🎯 ¿Qué es NextLine Presencia?

Es una **versión simplificada** de NextLine enfocada solo en **presencia web** (mostrar), eliminando módulos de **gestión** (cotizaciones y clientes).

### Concepto Clave:
- **Sistema de Paquetes**: Cada empresa tiene un `paquete_id` que determina qué módulos puede usar
- **Tabla `paquete_modulo`**: Relaciona qué módulos están incluidos en cada paquete
- **Menú Dinámico**: Se genera automáticamente según el paquete contratado
- **Control de Acceso**: Middleware bloquea rutas de módulos no incluidos

---

## 📋 Paquetes Disponibles

### 1. **NextLine Presencia** (paquete_id = 1)
- **Precio**: $149k setup + $19.990/mes
- **Módulos**: Empresa, Servicios, Galería, Proyectos, Testimonios, Contacto
- **NO incluye**: Clientes, Cotizaciones

### 2. **NextLine Gestión** (paquete_id = 2)
- **Precio**: $269k setup + $49.990/mes
- **Módulos**: Todo de Presencia + Clientes + Cotizaciones

### 3. **NextLine Custom** (paquete_id = 3)
- **Precio**: A cotizar
- **Módulos**: Todo + desarrollos personalizados

---

## 🔧 Cómo Funciona

### Estructura de Base de Datos:

```sql
-- Tabla de paquetes
paquetes (id, nombre, slug, precio_setup, precio_mensual)

-- Tabla de relación
paquete_modulo (paquete_id, modulo_id, incluido)

-- Tabla empresa (modificada)
empresa (id, nombre, paquete_id, ...)
```

### Flujo:

1. **Usuario inicia sesión** → Sistema obtiene `empresa_id` del usuario
2. **Sistema consulta** → `empresa.paquete_id`
3. **Sistema consulta** → `paquete_modulo` donde `paquete_id = X` y `incluido = 'S'`
4. **Genera menú** → Solo muestra módulos incluidos en el paquete
5. **Middleware protege** → Bloquea acceso a rutas de módulos no incluidos

---

## ✅ ¿Es Replicable para Nutricionistas?

**¡SÍ!** Es perfectamente replicable. Podríamos crear:

### **Paquete: NextLine Nutrición** (paquete_id = 4)

**Módulos incluidos:**
- ✅ Agenda (módulo 33)
- ✅ Pacientes (módulo 34)
- ✅ Documentos (módulo 35)
- ✅ Historial Clínico (módulo 36)
- ✅ Configuraciones (módulo 34 - nuevo)
- ✅ Cancelar Horas (módulo 38 - nuevo)
- ✅ Pagos (módulo 37 - opcional)

**Módulos NO incluidos:**
- ❌ Servicios, Galería, Proyectos (de Presencia)
- ❌ Clientes, Cotizaciones (de Gestión)

---

## 🚀 Ventajas de Aplicarlo

### 1. **Separación Clara de Productos**
- NextLine Presencia → Sitios web
- NextLine Gestión → CRM + Cotizaciones
- NextLine Nutrición → Sistema para nutricionistas

### 2. **Flexibilidad**
- Un mismo código base
- Diferentes productos según el paquete
- Fácil agregar nuevos módulos a paquetes específicos

### 3. **Control de Acceso**
- Los nutricionistas solo ven módulos relevantes
- No se confunden con módulos de otros productos
- Menú más limpio y enfocado

### 4. **Escalabilidad**
- Agregar nuevos módulos de nutrición es fácil
- Solo asignarlos al paquete correcto
- No afecta otros productos

---

## 📝 Pasos para Aplicarlo

### 1. **Crear Paquete "NextLine Nutrición"**
```sql
INSERT INTO paquetes (nombre, slug, descripcion, precio_setup, precio_mensual, activo, orden)
VALUES ('NextLine Nutrición', 'nutricion', 'Sistema completo para nutricionistas', 0, 0, 'A', 4);
```

### 2. **Asignar Módulos al Paquete**
```sql
-- Módulos de nutricionistas
INSERT INTO paquete_modulo (paquete_id, modulo_id, incluido)
VALUES
(4, 33, 'S'), -- Agenda
(4, 34, 'S'), -- Pacientes
(4, 35, 'S'), -- Documentos
(4, 36, 'S'), -- Historial Clínico
(4, 37, 'S'), -- Pagos (opcional)
(4, 34, 'S'), -- Configuraciones (nuevo)
(4, 38, 'S'); -- Cancelar Horas (nuevo)
```

### 3. **Asignar Paquete a Empresas de Nutricionistas**
```sql
-- Si hay una empresa específica para nutricionistas
UPDATE empresa SET paquete_id = 4 WHERE id = X;
```

### 4. **Verificar que el Sistema Funcione**
- El menú debe mostrar solo módulos de nutrición
- Las rutas de otros módulos deben estar bloqueadas
- El sistema debe respetar el paquete asignado

---

## 🤔 Consideraciones

### **Opción A: Sistema Multi-Producto (Recomendado)**
- Un solo código base
- Diferentes paquetes para diferentes productos
- Flexibilidad total

### **Opción B: Ramas Separadas**
- `feature/presencia` → Sitios web
- `feature/nutricion` → Nutricionistas
- `main` → Versión completa

**Recomendación**: Opción A (sistema de paquetes) porque:
- ✅ Un solo código base
- ✅ Fácil mantenimiento
- ✅ Reutilización de código
- ✅ Flexibilidad para agregar módulos

---

## 🎯 Conclusión

**El sistema de paquetes es perfectamente replicable y aplicable aquí.**

**Ventajas:**
- ✅ Separación clara de productos
- ✅ Control de acceso por paquete
- ✅ Menú dinámico según paquete
- ✅ Fácil de escalar

**Próximos pasos:**
1. Crear paquete "NextLine Nutrición"
2. Asignar módulos de nutricionistas al paquete
3. Verificar que el sistema de permisos funcione
4. Probar que el menú se genere correctamente

**¿Quieres que implemente esto ahora?**
