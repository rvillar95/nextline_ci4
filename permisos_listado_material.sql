-- =====================================================
-- SQL para crear permisos del módulo Listado de Materiales
-- =====================================================

-- PASO 1: Insertar el módulo principal en la tabla 'modulo'
INSERT INTO `modulo` (`nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`) 
VALUES 
('Listado de Materiales', 'Gestión de listados de materiales para proyectos', '/dashboard/listado-material', 'A', 'S', 'N', NOW(), NOW());

-- Obtener el ID del módulo recién creado (lo necesitaremos para los detalles)
SET @modulo_id = LAST_INSERT_ID();

-- PASO 2: Insertar los detalles del módulo (rutas) en 'modulo_detalle'
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`) 
VALUES
-- Vistas principales
(@modulo_id, 'Gestionar Materiales', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW()),
(@modulo_id, 'Registro material', '/registro', 'ver', 'A', 'N', 2, NOW(), NOW()),
(@modulo_id, 'Editar material', '/editar', 'editar', 'A', 'N', 3, NOW(), NOW()),
(@modulo_id, 'Detalle material', '/detalle', 'ver', 'A', 'N', 4, NOW(), NOW()),

-- Acciones CRUD
(@modulo_id, 'Acción Registrar Material', '/registrar', 'registrar', 'A', 'N', 5, NOW(), NOW()),
(@modulo_id, 'Acción Actualizar Material', '/update', 'editar', 'A', 'N', 6, NOW(), NOW()),
(@modulo_id, 'Acción Eliminar Material', '/eliminar', 'eliminar', 'A', 'N', 7, NOW(), NOW()),

-- Obtener datos (AJAX)
(@modulo_id, 'Obtener listados para DataTable', '/getListadosMateriales', 'ver', 'A', 'N', 8, NOW(), NOW()),
(@modulo_id, 'Obtener clientes para select', '/getClientesSelect', 'ver', 'A', 'N', 9, NOW(), NOW()),
(@modulo_id, 'Obtener proyectos por cliente', '/getProyectosByCliente', 'ver', 'A', 'N', 10, NOW(), NOW()),

-- PDF
(@modulo_id, 'Generar PDF del listado', '/generarPDF', 'ver', 'A', 'N', 11, NOW(), NOW());

-- PASO 3: Asignar permisos al perfil Administrador (ID 1)
-- Si tienes otro ID de perfil administrador, cámbialo aquí


-- =====================================================
-- OPCIONAL: Ver el módulo creado y sus detalles
-- =====================================================
-- Descomenta estas líneas si quieres verificar:

-- SELECT * FROM modulo WHERE id = @modulo_id;
-- SELECT * FROM modulo_detalle WHERE modulo_id = @modulo_id ORDER BY orden;
-- SELECT * FROM perfil_modulo WHERE modulo_id = @modulo_id;

-- =====================================================
-- ¡Listo! El módulo ya está disponible para el perfil administrador
-- =====================================================

-- NOTA: Si necesitas asignar a otros perfiles, usa este SQL:
-- INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `fcreacion`, `factualizacion`)
-- VALUES (ID_DEL_PERFIL, @modulo_id, NOW(), NOW());

