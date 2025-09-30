-- Módulo: Empresa (gestión de datos de la empresa)
-- Referencia: basado en modulo_categorias_galeria.sql

-- 1) Crear registro en tabla `modulo`
INSERT INTO `modulo` (`nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`)
VALUES ('Empresa', 'Gestión de datos de la empresa para encabezados, logos y datos corporativos', 'dashboard/empresa', 'A', 'S', 'N');

-- 2) Obtener el ID del módulo recién creado
SET @modulo_id = LAST_INSERT_ID();

-- 3) Crear entradas en `modulo_detalle`
-- Acciones del sistema (no se muestran en menú)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`) VALUES
(@modulo_id, 'Acción Registrar Empresa', '/registrar', 'registrar', 'A', 'N', 0),
(@modulo_id, 'Acción Eliminar Empresa', '/eliminar', 'eliminar', 'A', 'N', 0),
(@modulo_id, 'Acción Obtener Empresas para datatable', '/getEmpresa', 'ver', 'A', 'N', 0),
(@modulo_id, 'Acción Actualizar Empresa', '/update', 'editar', 'A', 'N', 0),
(@modulo_id, 'Acción Editar Empresa', '/editar', 'ver', 'A', 'N', 0),
(@modulo_id, 'Acción Detalle Empresa', '/detalle', 'ver', 'A', 'N', 0);

-- Elementos del menú (se muestran en menú)
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`) VALUES
(@modulo_id, 'Registrar Empresa', '/registro', 'ver', 'A', 'S', 1),
(@modulo_id, 'Listar Empresas', '/lista', 'ver', 'A', 'S', 2);

-- 4) Asignar permisos a perfiles existentes (ajustar IDs si corresponde)

-- Perfil 8 (Test Servicios): permisos completos para testing
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`)
VALUES (8, @modulo_id, 1, 1, 1, 1, 0, 'A', 8);


