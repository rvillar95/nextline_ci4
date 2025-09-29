-- Agregar módulo de Testimonios
INSERT INTO `modulo` (`nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`) VALUES
('Testimonios', 'Gestión de testimonios de clientes', 'dashboard/testimonio', 'A', 'S', 'N');

-- Obtener el ID del módulo recién creado
SET @testimonio_modulo_id = LAST_INSERT_ID();

-- Agregar detalles del módulo (vistas y acciones)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`) VALUES
-- Acciones (mostrar = N)
(@testimonio_modulo_id, 'Acción Registrar Testimonio', '/registrar', 'registrar', 'A', 'N', 0),
(@testimonio_modulo_id, 'Acción Eliminar Testimonio', '/eliminar', 'eliminar', 'A', 'N', 0),
(@testimonio_modulo_id, 'Acción Obtener Testimonios', '/getTestimonios', 'ver', 'A', 'N', 0),
(@testimonio_modulo_id, 'Acción Actualizar Testimonio', '/update', 'editar', 'A', 'N', 0),
(@testimonio_modulo_id, 'Editar Testimonio', '/editar', 'ver', 'A', 'N', 3),
-- Vistas (mostrar = S)
(@testimonio_modulo_id, 'Registro de Testimonio', '/registro', 'ver', 'A', 'S', 1),
(@testimonio_modulo_id, 'Lista de Testimonios', '/lista', 'ver', 'A', 'S', 2);





-- Agregar permisos para todos los perfiles existentes
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`) 
SELECT p.id, @testimonio_modulo_id, 1, 1, 1, 1 
FROM perfil p 
WHERE p.estado = 'A';
