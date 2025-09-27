-- Script para agregar módulo de Proyectos al sistema de permisos
-- Basado en la estructura existente de otros módulos

-- Agregar módulo de Proyectos
INSERT INTO `modulo` (`nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`) VALUES
('Proyectos', 'Gestión completa de proyectos de construcción', 'dashboard/proyecto', 'A', 'S', 'N');

-- Obtener el ID del módulo recién creado
SET @modulo_id = LAST_INSERT_ID();

-- Agregar detalles del módulo (acciones)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`) VALUES
(@modulo_id, 'Acción Registrar Proyecto', '/registrar', 'registrar', 'A', 'N', 0),
(@modulo_id, 'Acción eliminar proyecto', '/eliminar', 'eliminar', 'A', 'N', 0),
(@modulo_id, 'Acción Obtener proyectos para datatable', '/getProyecto', 'ver', 'A', 'N', 0),
(@modulo_id, 'Acción Editar proyecto', '/update', 'editar', 'A', 'N', 0),
(@modulo_id, 'Acción Editar proyecto', '/editar', 'ver', 'A', 'N', 0),
(@modulo_id, 'Acción Establecer imagen portada', '/setPortada', 'editar', 'A', 'N', 0),
(@modulo_id, 'Acción Eliminar imagen', '/eliminarImagen', 'eliminar', 'A', 'N', 0),
(@modulo_id, 'Registrar proyecto', '/registro', 'ver', 'A', 'S', 1),
(@modulo_id, 'Listar Proyectos', '/lista', 'ver', 'A', 'S', 2);

-- Asignar permisos completos a todos los perfiles existentes
-- Perfil 1: Administrador (permisos completos)
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`) 
VALUES (1, @modulo_id, 1, 1, 1, 1, 0, 'A', 9);

-- Perfil 3: Usuario con permisos limitados (sin eliminar)
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`) 
VALUES (3, @modulo_id, 1, 1, 1, 0, 0, 'A', 7);

-- Perfil 7: Usuario con permisos completos
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`) 
VALUES (7, @modulo_id, 1, 1, 1, 1, 0, 'A', 8);

-- Perfil 8: Usuario con permisos completos
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`) 
VALUES (8, @modulo_id, 1, 1, 1, 1, 0, 'A', 10);

-- Verificar que se insertó correctamente
SELECT 
    m.id as modulo_id,
    m.nombre as modulo_nombre,
    COUNT(md.id) as total_acciones,
    COUNT(pm.id) as total_permisos
FROM modulo m
LEFT JOIN modulo_detalle md ON m.id = md.modulo_id
LEFT JOIN perfil_modulo pm ON m.id = pm.modulo_id
WHERE m.nombre = 'Proyectos'
GROUP BY m.id, m.nombre;
