-- =====================================================
-- CREAR MÓDULO "PERFILES DE EMPRESA"
-- =====================================================
-- Este módulo permite a los nutricionistas (administradores de empresa)
-- gestionar los perfiles de su empresa
-- =====================================================

-- =====================================================
-- PASO 1: Crear el módulo "Perfiles de Empresa"
-- =====================================================
-- Primero verificar el siguiente ID disponible:
-- SELECT MAX(id) + 1 AS siguiente_id FROM modulo;

INSERT INTO `modulo` (`id`, `nombre`, `descripcion`, `sa`, `ruta`, `estado`, `mostrar`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Perfiles de Empresa', 'Gestión de perfiles y permisos de la empresa', 'N', 'dashboard/perfil', 'A', 'S', NOW(), NOW(), '0000-00-00 00:00:00');

-- Si el ID 41 ya existe, cambiar por el siguiente disponible
-- =====================================================
-- PASO 2: Crear las rutas (modulo_detalle) para el módulo
-- =====================================================
-- Usar el ID del módulo creado (en este caso 41)

-- Lista de Perfiles (ruta relativa - se concatena con dashboard/perfil)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Lista de Perfiles', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00');

-- Crear Perfil (ruta relativa)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Crear Perfil', '/registro', 'registrar', 'A', 'S', 2, NOW(), NOW(), '0000-00-00 00:00:00');

-- Editar Perfil (ruta relativa)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Editar Perfil', '/editar', 'editar', 'A', 'S', 3, NOW(), NOW(), '0000-00-00 00:00:00');

-- Eliminar Perfil
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Eliminar Perfil', '/eliminar', 'eliminar', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00');

-- Obtener Perfiles (AJAX para DataTable - ruta relativa)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Obtener Perfiles', '/getPerfiles', 'ver', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00');

-- Registrar Perfil (ruta relativa)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Registrar Perfil', '/registrar', 'registrar', 'A', 'N', 6, NOW(), NOW(), '0000-00-00 00:00:00');

-- Actualizar Perfil (ruta relativa)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Actualizar Perfil', '/update', 'editar', 'A', 'N', 7, NOW(), NOW(), '0000-00-00 00:00:00');

-- =====================================================
-- PERFIL DETALLE (Permisos de módulos por perfil)
-- =====================================================
-- Estas rutas se acceden desde dashboard/perfil-detalle/...
-- Pero se muestran como submódulos de "Perfiles de Empresa"

-- Lista de Perfil Detalle (Permisos) - RUTA ABSOLUTA (empieza con /dashboard/)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Permisos de Módulos', '/dashboard/perfil-detalle/lista', 'ver', 'A', 'S', 8, NOW(), NOW(), '0000-00-00 00:00:00');

-- Crear Permiso - RUTA ABSOLUTA
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Asignar Permisos', '/dashboard/perfil-detalle/registro', 'registrar', 'A', 'S', 9, NOW(), NOW(), '0000-00-00 00:00:00');

-- Editar Permiso - RUTA ABSOLUTA
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Editar Permisos', '/dashboard/perfil-detalle/editar', 'editar', 'A', 'S', 10, NOW(), NOW(), '0000-00-00 00:00:00');

-- Obtener Perfil Detalle (AJAX) - RUTA ABSOLUTA
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Obtener Permisos', '/dashboard/perfil-detalle/getPerfilDetalle', 'ver', 'A', 'N', 11, NOW(), NOW(), '0000-00-00 00:00:00');

-- Registrar Permiso - RUTA ABSOLUTA
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Registrar Permiso', '/dashboard/perfil-detalle/registrar', 'registrar', 'A', 'N', 12, NOW(), NOW(), '0000-00-00 00:00:00');

-- Actualizar Permiso - RUTA ABSOLUTA
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Actualizar Permiso', '/dashboard/perfil-detalle/update', 'editar', 'A', 'N', 13, NOW(), NOW(), '0000-00-00 00:00:00');

-- Eliminar Permiso - RUTA ABSOLUTA
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Eliminar Permiso', '/dashboard/perfil-detalle/eliminar', 'eliminar', 'A', 'N', 14, NOW(), NOW(), '0000-00-00 00:00:00');

-- Actualizar Orden (AJAX inline) - RUTA ABSOLUTA
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(41, 'Actualizar Orden', '/dashboard/perfil-detalle/updateOrden', 'editar', 'A', 'N', 15, NOW(), NOW(), '0000-00-00 00:00:00');

-- =====================================================
-- INSTRUCCIONES:
-- =====================================================
-- 1. Ejecutar primero la inserción del módulo
-- 2. Obtener el ID del módulo creado:
--    SELECT MAX(id) FROM modulo WHERE nombre = 'Perfiles de Empresa';
-- 3. Reemplazar @modulo_id en las siguientes queries con el ID real
-- 4. Ejecutar las inserciones de modulo_detalle
-- 5. Asignar el módulo al paquete Nutrición (id=4) en paquete_modulo
-- 6. Asignar permisos al perfil Nutricionista (id=9) en perfil_modulo
