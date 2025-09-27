-- Agregar módulo de categorías de servicios
INSERT INTO `modulo` (`nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`) VALUES
('Categorías de Servicios', 'Gestión de categorías para servicios', 'dashboard/servicio-categoria', 'A', 'S', 'N');

-- Obtener el ID del módulo recién creado
SET @modulo_id = LAST_INSERT_ID();

-- Agregar detalles del módulo
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`) VALUES
-- Acciones del sistema (no se muestran en menú)
(@modulo_id, 'Acción Registrar Categoría', '/registrar', 'registrar', 'A', 'N', 0),
(@modulo_id, 'Acción eliminar categoría', '/eliminar', 'eliminar', 'A', 'N', 0),
(@modulo_id, 'Acción Obtener categorías para datatable', '/getServicioCategoria', 'ver', 'A', 'N', 0),
(@modulo_id, 'Acción Editar categoría', '/update', 'editar', 'A', 'N', 0),
(@modulo_id, 'Acción Editar categoría', '/editar', 'ver', 'A', 'N', 0),

-- Elementos del menú (se muestran en menú)
(@modulo_id, 'Registrar categoría', '/registro', 'ver', 'A', 'S', 1),
(@modulo_id, 'Listar Categorías', '/lista', 'ver', 'A', 'S', 2);

-- Asignar permisos completos a todos los perfiles existentes
-- Perfil 1 (Super Administrador): todos los permisos
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`) 
VALUES (1, @modulo_id, 1, 1, 1, 1, 0, 'A', 8);

-- Perfil 3 (Administrador): permisos limitados (ver, registrar, editar, sin eliminar)
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`) 
VALUES (3, @modulo_id, 1, 1, 1, 0, 0, 'A', 6);

-- Perfil 7 (Administrador Web): permisos completos para gestión web
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`) 
VALUES (7, @modulo_id, 1, 1, 1, 1, 0, 'A', 7);

-- Perfil 8 (Test Servicios): permisos completos para testing
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`) 
VALUES (8, @modulo_id, 1, 1, 1, 1, 0, 'A', 9);
