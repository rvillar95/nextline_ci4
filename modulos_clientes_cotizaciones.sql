-- =============================================
-- MÓDULOS DE CLIENTES Y COTIZACIONES
-- =============================================

-- Insertar módulo de Clientes
INSERT INTO `modulo` (`nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`) VALUES
('Clientes', 'Gestión de clientes particulares, empresas y organizaciones', 'dashboard/cliente', 'A', 'S', 'N');

-- Obtener el ID del módulo de Clientes
SET @cliente_modulo_id = LAST_INSERT_ID();

-- Insertar detalles del módulo de Clientes
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`) VALUES
-- Acciones (mostrar = N)
(@cliente_modulo_id, 'Acción Registrar Cliente', '/registrar', 'registrar', 'A', 'N', 0),
(@cliente_modulo_id, 'Acción Eliminar Cliente', '/eliminar', 'eliminar', 'A', 'N', 0),
(@cliente_modulo_id, 'Acción Obtener Clientes', '/getClientes', 'ver', 'A', 'N', 0),
(@cliente_modulo_id, 'Acción Actualizar Cliente', '/update', 'editar', 'A', 'N', 0),
(@cliente_modulo_id, 'Acción Obtener Clientes Select', '/getClientesSelect', 'ver', 'A', 'N', 0),
(@cliente_modulo_id, 'Editar Cliente', '/editar', 'ver', 'A', 'N', 3),
(@cliente_modulo_id, 'Detalle Cliente', '/detalle', 'ver', 'A', 'N', 4),
-- Vistas (mostrar = S)
(@cliente_modulo_id, 'Registro de Cliente', '/registro', 'ver', 'A', 'S', 1),
(@cliente_modulo_id, 'Lista de Clientes', '/lista', 'ver', 'A', 'S', 2);

-- Insertar módulo de Cotizaciones
INSERT INTO `modulo` (`nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`) VALUES
('Cotizaciones', 'Gestión de cotizaciones y presupuestos', 'dashboard/cotizacion', 'A', 'S', 'N');

-- Obtener el ID del módulo de Cotizaciones
SET @cotizacion_modulo_id = LAST_INSERT_ID();

-- Insertar detalles del módulo de Cotizaciones
INSERT INTO `modulo_detalle` (`modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`) VALUES
-- Acciones (mostrar = N)
(@cotizacion_modulo_id, 'Acción Registrar Cotización', '/registrar', 'registrar', 'A', 'N', 0),
(@cotizacion_modulo_id, 'Acción Eliminar Cotización', '/eliminar', 'eliminar', 'A', 'N', 0),
(@cotizacion_modulo_id, 'Acción Obtener Cotizaciones', '/getCotizaciones', 'ver', 'A', 'N', 0),
(@cotizacion_modulo_id, 'Acción Actualizar Cotización', '/update', 'editar', 'A', 'N', 0),
(@cotizacion_modulo_id, 'Acción Obtener Clientes Select', '/getClientesSelect', 'ver', 'A', 'N', 0),
(@cotizacion_modulo_id, 'Acción Generar PDF', '/generarPDF', 'ver', 'A', 'N', 0),
(@cotizacion_modulo_id, 'Editar Cotización', '/editar', 'ver', 'A', 'N', 3),
(@cotizacion_modulo_id, 'Detalle Cotización', '/detalle', 'ver', 'A', 'N', 4),
-- Vistas (mostrar = S)
(@cotizacion_modulo_id, 'Registro de Cotización', '/registro', 'ver', 'A', 'S', 1),
(@cotizacion_modulo_id, 'Lista de Cotizaciones', '/lista', 'ver', 'A', 'S', 2);

-- =============================================
-- PERMISOS PARA TODOS LOS PERFILES
-- =============================================

-- Agregar permisos para todos los perfiles existentes
INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`) 
SELECT p.id, @cliente_modulo_id, 1, 1, 1, 1 
FROM perfil p 
WHERE p.estado = 'A';

INSERT INTO `perfil_modulo` (`perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`) 
SELECT p.id, @cotizacion_modulo_id, 1, 1, 1, 1 
FROM perfil p 
WHERE p.estado = 'A';

-- =============================================
-- VERIFICACIÓN
-- =============================================

-- Mostrar módulos creados
SELECT 'Módulos creados:' as info;
SELECT id, nombre, descripcion, icono, orden, estado FROM modulo WHERE nombre IN ('Clientes', 'Cotizaciones');

-- Mostrar detalles de módulos
SELECT 'Detalles de módulos:' as info;
SELECT md.id, m.nombre as modulo, md.nombre as detalle, md.url, md.icono, md.orden, md.estado 
FROM modulo_detalle md 
JOIN modulo m ON m.id = md.modulo_id 
WHERE m.nombre IN ('Clientes', 'Cotizaciones')
ORDER BY m.orden, md.orden;

-- Mostrar permisos asignados
SELECT 'Permisos asignados:' as info;
SELECT pm.id, p.nombre as perfil, m.nombre as modulo, pm.estado
FROM perfil_modulo pm
JOIN perfil p ON p.id = pm.perfil_id
JOIN modulo m ON m.id = pm.modulo_id
WHERE m.nombre IN ('Clientes', 'Cotizaciones')
ORDER BY p.nombre, m.nombre;
