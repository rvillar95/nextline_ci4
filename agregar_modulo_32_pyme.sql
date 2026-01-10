-- Script para agregar el módulo 32 (Listado de Materiales) a nextline_pyme
-- Ejecutar después de importar nextline_pyme (4).sql

-- 1. Agregar el módulo 32
INSERT INTO `modulo` (`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(32, 'Listado de Materiales', 'Gestión de listados de materiales para proyectos', '/dashboard/listado-material', 'A', 'S', 'N', '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00');

-- 2. Agregar los detalles del módulo 32
INSERT INTO `modulo_detalle` (`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(181, 32, 'Gestionar Materiales', '/lista', 'ver', 'A', 'S', 1, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(182, 32, 'Registro material', '/registro', 'ver', 'A', 'N', 2, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(183, 32, 'Editar material', '/editar', 'editar', 'A', 'N', 3, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(184, 32, 'Detalle material', '/detalle', 'ver', 'A', 'N', 4, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(185, 32, 'Acción Registrar Material', '/registrar', 'registrar', 'A', 'N', 5, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(186, 32, 'Acción Actualizar Material', '/update', 'editar', 'A', 'N', 6, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(187, 32, 'Acción Eliminar Material', '/eliminar', 'eliminar', 'A', 'N', 7, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(188, 32, 'Obtener listados para DataTable', '/getListadosMateriales', 'ver', 'A', 'N', 8, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(189, 32, 'Obtener clientes para select', '/getClientesSelect', 'ver', 'A', 'N', 9, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(190, 32, 'Obtener proyectos por cliente', '/getProyectosByCliente', 'ver', 'A', 'N', 10, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00'),
(191, 32, 'Generar PDF del listado', '/generarPDF', 'ver', 'A', 'N', 11, '2025-10-26 17:53:21', '2025-10-26 17:53:21', '0000-00-00 00:00:00');

-- 3. Agregar permisos del módulo 32 para el perfil 8 (Administrador)
-- Nota: El último ID en perfil_modulo es 88, así que usamos 89
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(89, 8, 32, 1, 1, 1, 1, 0, 'A', 20, '2025-10-26 17:54:57', '2025-10-26 17:54:57', '0000-00-00 00:00:00');

-- 4. (Opcional) Agregar el módulo 32 a los paquetes si es necesario
-- INSERT INTO `paquete_modulo` (`paquete_id`, `modulo_id`, `incluido`, `fcreacion`) VALUES
-- (2, 32, 'S', NOW()),  -- Paquete Gestión
-- (3, 32, 'S', NOW());  -- Paquete Custom
