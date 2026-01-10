-- =====================================================
-- MÓDULOS PARA SISTEMA DE NUTRICIONISTAS
-- =====================================================
-- Este script agrega los módulos necesarios para el
-- sistema de gestión de nutricionistas
-- =====================================================

-- Nota: Verificar el último ID de módulo antes de ejecutar
-- SELECT MAX(id) FROM modulo;

-- --------------------------------------------------------
-- Módulo 33: Agenda
-- --------------------------------------------------------
INSERT INTO `modulo` (`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(33, 'Agenda', 'Gestión de agenda y citas de pacientes', '/dashboard/agenda', 'A', 'S', 'N', NOW(), NOW(), '0000-00-00 00:00:00');

-- Detalles del módulo Agenda
INSERT INTO `modulo_detalle` (`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(192, 33, 'Gestionar Agenda', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(193, 33, 'Vista Calendario', '/calendario', 'ver', 'A', 'S', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(194, 33, 'Registrar Horario', '/registro', 'ver', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(195, 33, 'Editar Horario', '/editar', 'editar', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(196, 33, 'Agendar Cita', '/agendar', 'registrar', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00'),
(197, 33, 'Acción Registrar Horario', '/registrar', 'registrar', 'A', 'N', 6, NOW(), NOW(), '0000-00-00 00:00:00'),
(198, 33, 'Acción Actualizar Horario', '/update', 'editar', 'A', 'N', 7, NOW(), NOW(), '0000-00-00 00:00:00'),
(199, 33, 'Acción Eliminar Horario', '/eliminar', 'eliminar', 'A', 'N', 8, NOW(), NOW(), '0000-00-00 00:00:00'),
(200, 33, 'Obtener agenda para DataTable', '/getAgenda', 'ver', 'A', 'N', 9, NOW(), NOW(), '0000-00-00 00:00:00'),
(201, 33, 'Obtener eventos para calendario', '/getEventos', 'ver', 'A', 'N', 10, NOW(), NOW(), '0000-00-00 00:00:00'),
(202, 33, 'Confirmar cita', '/confirmarCita', 'editar', 'A', 'N', 11, NOW(), NOW(), '0000-00-00 00:00:00'),
(203, 33, 'Cancelar cita', '/cancelarCita', 'editar', 'A', 'N', 12, NOW(), NOW(), '0000-00-00 00:00:00');

-- --------------------------------------------------------
-- Módulo 34: Pacientes
-- --------------------------------------------------------
INSERT INTO `modulo` (`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(34, 'Pacientes', 'Gestión de pacientes y su información clínica', '/dashboard/paciente', 'A', 'S', 'N', NOW(), NOW(), '0000-00-00 00:00:00');

-- Detalles del módulo Pacientes
INSERT INTO `modulo_detalle` (`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(204, 34, 'Gestionar Pacientes', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(205, 34, 'Registrar Paciente', '/registro', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(206, 34, 'Editar Paciente', '/editar', 'editar', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(207, 34, 'Detalle Paciente', '/detalle', 'ver', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(208, 34, 'Acción Registrar Paciente', '/registrar', 'registrar', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00'),
(209, 34, 'Acción Actualizar Paciente', '/update', 'editar', 'A', 'N', 6, NOW(), NOW(), '0000-00-00 00:00:00'),
(210, 34, 'Acción Eliminar Paciente', '/eliminar', 'eliminar', 'A', 'N', 7, NOW(), NOW(), '0000-00-00 00:00:00'),
(211, 34, 'Obtener pacientes para DataTable', '/getPacientes', 'ver', 'A', 'N', 8, NOW(), NOW(), '0000-00-00 00:00:00'),
(212, 34, 'Obtener pacientes para select', '/getPacientesSelect', 'ver', 'A', 'N', 9, NOW(), NOW(), '0000-00-00 00:00:00');

-- --------------------------------------------------------
-- Módulo 35: Documentos
-- --------------------------------------------------------
INSERT INTO `modulo` (`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(35, 'Documentos', 'Gestión de documentos, pautas nutricionales y recetas', '/dashboard/documento', 'A', 'S', 'N', NOW(), NOW(), '0000-00-00 00:00:00');

-- Detalles del módulo Documentos
INSERT INTO `modulo_detalle` (`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(213, 35, 'Gestionar Documentos', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(214, 35, 'Crear Documento', '/registro', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(215, 35, 'Editar Documento', '/editar', 'editar', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(216, 35, 'Ver Documento', '/detalle', 'ver', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(217, 35, 'Acción Registrar Documento', '/registrar', 'registrar', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00'),
(218, 35, 'Acción Actualizar Documento', '/update', 'editar', 'A', 'N', 6, NOW(), NOW(), '0000-00-00 00:00:00'),
(219, 35, 'Acción Eliminar Documento', '/eliminar', 'eliminar', 'A', 'N', 7, NOW(), NOW(), '0000-00-00 00:00:00'),
(220, 35, 'Obtener documentos para DataTable', '/getDocumentos', 'ver', 'A', 'N', 8, NOW(), NOW(), '0000-00-00 00:00:00'),
(221, 35, 'Enviar documento', '/enviar', 'editar', 'A', 'N', 9, NOW(), NOW(), '0000-00-00 00:00:00'),
(222, 35, 'Descargar documento', '/descargar', 'ver', 'A', 'N', 10, NOW(), NOW(), '0000-00-00 00:00:00');

-- --------------------------------------------------------
-- Módulo 36: Historial Clínico
-- --------------------------------------------------------
INSERT INTO `modulo` (`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(36, 'Historial Clínico', 'Registro de consultas y evolución de pacientes', '/dashboard/historial', 'A', 'S', 'N', NOW(), NOW(), '0000-00-00 00:00:00');

-- Detalles del módulo Historial Clínico
INSERT INTO `modulo_detalle` (`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(223, 36, 'Gestionar Historial', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(224, 36, 'Registrar Consulta', '/registro', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(225, 36, 'Editar Consulta', '/editar', 'editar', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(226, 36, 'Ver Consulta', '/detalle', 'ver', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(227, 36, 'Acción Registrar Consulta', '/registrar', 'registrar', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00'),
(228, 36, 'Acción Actualizar Consulta', '/update', 'editar', 'A', 'N', 6, NOW(), NOW(), '0000-00-00 00:00:00'),
(229, 36, 'Acción Eliminar Consulta', '/eliminar', 'eliminar', 'A', 'N', 7, NOW(), NOW(), '0000-00-00 00:00:00'),
(230, 36, 'Obtener historial para DataTable', '/getHistorial', 'ver', 'A', 'N', 8, NOW(), NOW(), '0000-00-00 00:00:00'),
(231, 36, 'Obtener historial por paciente', '/getHistorialPaciente', 'ver', 'A', 'N', 9, NOW(), NOW(), '0000-00-00 00:00:00');

-- --------------------------------------------------------
-- Módulo 37: Pagos
-- --------------------------------------------------------
INSERT INTO `modulo` (`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(37, 'Pagos', 'Gestión de pagos y suscripciones', '/dashboard/pago', 'A', 'S', 'N', NOW(), NOW(), '0000-00-00 00:00:00');

-- Detalles del módulo Pagos
INSERT INTO `modulo_detalle` (`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(232, 37, 'Gestionar Pagos', '/lista', 'ver', 'A', 'S', 1, NOW(), NOW(), '0000-00-00 00:00:00'),
(233, 37, 'Registrar Pago', '/registro', 'ver', 'A', 'N', 2, NOW(), NOW(), '0000-00-00 00:00:00'),
(234, 37, 'Editar Pago', '/editar', 'editar', 'A', 'N', 3, NOW(), NOW(), '0000-00-00 00:00:00'),
(235, 37, 'Acción Registrar Pago', '/registrar', 'registrar', 'A', 'N', 4, NOW(), NOW(), '0000-00-00 00:00:00'),
(236, 37, 'Acción Actualizar Pago', '/update', 'editar', 'A', 'N', 5, NOW(), NOW(), '0000-00-00 00:00:00'),
(237, 37, 'Obtener pagos para DataTable', '/getPagos', 'ver', 'A', 'N', 6, NOW(), NOW(), '0000-00-00 00:00:00'),
(238, 37, 'Procesar pago', '/procesar', 'editar', 'A', 'N', 7, NOW(), NOW(), '0000-00-00 00:00:00');

-- --------------------------------------------------------
-- Permisos para Super Administrador (perfil_id = 1)
-- --------------------------------------------------------
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(90, 1, 33, 1, 1, 1, 1, 0, 'A', 21, NOW(), NOW(), '0000-00-00 00:00:00'),
(91, 1, 34, 1, 1, 1, 1, 0, 'A', 22, NOW(), NOW(), '0000-00-00 00:00:00'),
(92, 1, 35, 1, 1, 1, 1, 0, 'A', 23, NOW(), NOW(), '0000-00-00 00:00:00'),
(93, 1, 36, 1, 1, 1, 1, 0, 'A', 24, NOW(), NOW(), '0000-00-00 00:00:00'),
(94, 1, 37, 1, 1, 1, 1, 0, 'A', 25, NOW(), NOW(), '0000-00-00 00:00:00');

-- --------------------------------------------------------
-- Permisos para Administrador (perfil_id = 8)
-- --------------------------------------------------------
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(95, 8, 33, 1, 1, 1, 1, 0, 'A', 21, NOW(), NOW(), '0000-00-00 00:00:00'),
(96, 8, 34, 1, 1, 1, 1, 0, 'A', 22, NOW(), NOW(), '0000-00-00 00:00:00'),
(97, 8, 35, 1, 1, 1, 1, 0, 'A', 23, NOW(), NOW(), '0000-00-00 00:00:00'),
(98, 8, 36, 1, 1, 1, 1, 0, 'A', 24, NOW(), NOW(), '0000-00-00 00:00:00'),
(99, 8, 37, 1, 1, 1, 1, 0, 'A', 25, NOW(), NOW(), '0000-00-00 00:00:00');

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
-- Notas:
-- 1. Verificar IDs antes de ejecutar:
--    - SELECT MAX(id) FROM modulo;
--    - SELECT MAX(id) FROM modulo_detalle;
--    - SELECT MAX(id) FROM perfil_modulo;
--
-- 2. Ajustar los IDs si hay conflictos
--
-- 3. Los módulos creados son:
--    - 33: Agenda
--    - 34: Pacientes
--    - 35: Documentos
--    - 36: Historial Clínico
--    - 37: Pagos
-- =====================================================
