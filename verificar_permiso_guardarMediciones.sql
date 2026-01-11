-- =====================================================
-- VERIFICAR PERMISO DE GUARDAR MEDICIONES
-- =====================================================

-- Verificar si existe el permiso
SELECT 
    id, 
    modulo_id, 
    descripcion, 
    ruta, 
    accion, 
    estado,
    mostrar
FROM modulo_detalle 
WHERE modulo_id = 33 AND ruta = '/guardarMediciones';

-- Si no existe, ejecutar el script agregar_guardarMediciones_modulo.sql

-- Verificar que el módulo 33 (Agenda) existe
SELECT id, nombre, ruta 
FROM modulo 
WHERE id = 33;

-- Verificar todos los permisos de agenda
SELECT 
    md.id,
    md.descripcion,
    md.ruta,
    md.accion,
    md.estado
FROM modulo_detalle md
WHERE md.modulo_id = 33
ORDER BY md.orden;
