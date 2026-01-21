-- =====================================================
-- ASIGNAR MÓDULO "PERFILES DE EMPRESA" AL PAQUETE Y PERFIL
-- =====================================================
-- Ejecutar DESPUÉS de crear_modulo_perfiles_empresa.sql
-- =====================================================

-- 1. Asignar el módulo al paquete Nutrición (id=4)
-- Nota: Si el módulo tiene un ID diferente a 41, cambiar en ambas queries
INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`) VALUES
(4, 41, 'S', NOW())
ON DUPLICATE KEY UPDATE incluido = 'S', fcreacion = NOW();

-- 2. Asignar permisos al perfil Nutricionista (id=9)
-- Nota: Si el módulo tiene un ID diferente a 41, cambiar aquí también
-- Nota: Verificar el orden antes de insertar
-- SELECT MAX(orden) FROM perfil_modulo WHERE perfil_id = 9;

INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(9, 41, 1, 1, 1, 1, 'A', 50, NOW(), NOW(), '0000-00-00 00:00:00')
ON DUPLICATE KEY UPDATE 
    ver = 1, 
    registrar = 1, 
    editar = 1, 
    eliminar = 1, 
    estado = 'A',
    factualizacion = NOW();

-- =====================================================
-- VERIFICACIÓN
-- =====================================================
-- Verificar que el módulo esté asignado al paquete:
-- SELECT pm.*, m.nombre, p.nombre AS paquete_nombre
-- FROM paquete_modulo pm
-- JOIN modulo m ON m.id = pm.modulo_id
-- JOIN paquete p ON p.id = pm.paquete_id
-- WHERE m.nombre = 'Perfiles de Empresa';

-- Verificar que el perfil tenga permisos:
-- SELECT pm.*, p.nombre AS perfil_nombre, m.nombre AS modulo_nombre
-- FROM perfil_modulo pm
-- JOIN perfil p ON p.id = pm.perfil_id
-- JOIN modulo m ON m.id = pm.modulo_id
-- WHERE m.nombre = 'Perfiles de Empresa' AND p.id = 9;
