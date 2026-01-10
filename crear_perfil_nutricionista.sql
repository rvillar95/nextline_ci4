-- =====================================================
-- CREAR PERFIL DE NUTRICIONISTA Y ASIGNAR MÓDULOS
-- =====================================================
-- Este script crea un perfil para nutricionistas y
-- le asigna todos los módulos necesarios para trabajar
-- =====================================================

-- Nota: Verificar IDs antes de ejecutar
-- SELECT MAX(id) FROM perfil;
-- SELECT MAX(id) FROM perfil_modulo;

-- --------------------------------------------------------
-- 1. Crear el perfil "Nutricionista"
-- --------------------------------------------------------
-- Nota: La tabla perfil tiene: id, nombre, poder, estado, fcreacion, factualizacion, feliminacion
-- El campo 'poder' es un número (0 = sin poder, 1-3 = niveles de poder)
INSERT INTO `perfil` (`id`, `nombre`, `poder`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(9, 'Nutricionista', 1, 'A', NOW(), NOW(), '0000-00-00 00:00:00');

-- --------------------------------------------------------
-- 2. Asignar módulos base (necesarios para el sistema)
-- --------------------------------------------------------

-- Módulo 2: Inicio (Dashboard principal)
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(100, 9, 2, 1, 0, 0, 0, 0, 'A', 1, NOW(), NOW(), '0000-00-00 00:00:00');

-- Módulo 1: Usuario (para ver/editar su propio perfil)
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(101, 9, 1, 1, 0, 1, 0, 0, 'A', 2, NOW(), NOW(), '0000-00-00 00:00:00');

-- --------------------------------------------------------
-- 3. Asignar módulos de nutricionistas
-- --------------------------------------------------------

-- Módulo 33: Agenda (con todos los permisos)
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(102, 9, 33, 1, 1, 1, 1, 0, 'A', 3, NOW(), NOW(), '0000-00-00 00:00:00');

-- Módulo 34: Pacientes (con todos los permisos)
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(103, 9, 34, 1, 1, 1, 1, 0, 'A', 4, NOW(), NOW(), '0000-00-00 00:00:00');

-- Módulo 35: Documentos (con todos los permisos)
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(104, 9, 35, 1, 1, 1, 1, 0, 'A', 5, NOW(), NOW(), '0000-00-00 00:00:00');

-- Módulo 36: Historial Clínico (con todos los permisos)
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(105, 9, 36, 1, 1, 1, 1, 0, 'A', 6, NOW(), NOW(), '0000-00-00 00:00:00');

-- Módulo 37: Pagos (solo ver - opcional, puede quitarse si no necesita ver pagos)
INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(106, 9, 37, 1, 0, 0, 0, 0, 'A', 7, NOW(), NOW(), '0000-00-00 00:00:00');

-- =====================================================
-- RESUMEN DE MÓDULOS ASIGNADOS
-- =====================================================
-- Módulo 2:  Inicio (Dashboard) - Solo ver
-- Módulo 1:  Usuario - Ver y editar (su propio perfil)
-- Módulo 33: Agenda - Todos los permisos (ver, registrar, editar, eliminar)
-- Módulo 34: Pacientes - Todos los permisos
-- Módulo 35: Documentos - Todos los permisos
-- Módulo 36: Historial Clínico - Todos los permisos
-- Módulo 37: Pagos - Solo ver (opcional)
-- =====================================================

-- NOTA: Si el perfil 9 ya existe, primero eliminarlo:
-- DELETE FROM perfil_modulo WHERE perfil_id = 9;
-- DELETE FROM perfil WHERE id = 9;
