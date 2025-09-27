-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-09-2025 a las 02:59:59
-- Versión del servidor: 8.0.31
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nextline_pyme`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agenda`
--

DROP TABLE IF EXISTS `agenda`;
CREATE TABLE IF NOT EXISTS `agenda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `almuerzo_inicio` time DEFAULT NULL,
  `almuerzo_fin` time DEFAULT NULL,
  `estado_id` int DEFAULT NULL,
  `tipo_id` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_estado_agenda` (`estado_id`),
  KEY `fk_tipo_agenda` (`tipo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto`
--

DROP TABLE IF EXISTS `contacto`;
CREATE TABLE IF NOT EXISTS `contacto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `estado_id` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `numero` varchar(100) DEFAULT NULL,
  `correo` varchar(200) DEFAULT NULL,
  `observacion` varchar(3000) DEFAULT NULL,
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estado_id` (`estado_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_estado`
--

DROP TABLE IF EXISTS `contacto_estado`;
CREATE TABLE IF NOT EXISTS `contacto_estado` (
  `id` int NOT NULL AUTO_INCREMENT,
  `adm` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(1000) DEFAULT NULL,
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adm` (`adm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_historial`
--

DROP TABLE IF EXISTS `contacto_historial`;
CREATE TABLE IF NOT EXISTS `contacto_historial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contacto_id` int DEFAULT NULL,
  `estado_anterior` int DEFAULT NULL,
  `estado_actual` int DEFAULT NULL,
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacto_id` (`contacto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_agenda`
--

DROP TABLE IF EXISTS `detalle_agenda`;
CREATE TABLE IF NOT EXISTS `detalle_agenda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `agenda_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `orden` int NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` int NOT NULL DEFAULT '1',
  `estado_solicitud_id` int NOT NULL DEFAULT '1',
  `modalidad_id` int NOT NULL DEFAULT '3',
  `forma_asignacion` enum('Manual','Autogestionada') DEFAULT 'Manual',
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` datetime DEFAULT NULL,
  `usuario_especialidad_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_agenda` (`agenda_id`),
  KEY `fk_estadoSolicitud` (`estado_solicitud_id`),
  KEY `fk_modalidad` (`modalidad_id`),
  KEY `fk_estado_detalle` (`estado`),
  KEY `fk_detalle_servicio` (`usuario_especialidad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE IF NOT EXISTS `empresa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `rut` varchar(10) DEFAULT NULL,
  `dv` char(1) DEFAULT NULL,
  `mision` varchar(1000) DEFAULT NULL,
  `vision` varchar(1000) DEFAULT NULL,
  `servicios` varchar(1000) DEFAULT NULL,
  `dedicados` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidad`
--

DROP TABLE IF EXISTS `especialidad`;
CREATE TABLE IF NOT EXISTS `especialidad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_agenda`
--

DROP TABLE IF EXISTS `estado_agenda`;
CREATE TABLE IF NOT EXISTS `estado_agenda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_detalle_agenda`
--

DROP TABLE IF EXISTS `estado_detalle_agenda`;
CREATE TABLE IF NOT EXISTS `estado_detalle_agenda` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estado_detalle_agenda`
--

INSERT INTO `estado_detalle_agenda` (`id`, `nombre`) VALUES
(1, 'Disponible'),
(2, 'Ocupado'),
(3, 'Cancelado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_solicitud`
--

DROP TABLE IF EXISTS `estado_solicitud`;
CREATE TABLE IF NOT EXISTS `estado_solicitud` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estado_solicitud`
--

INSERT INTO `estado_solicitud` (`id`, `nombre`) VALUES
(1, 'Pendiente'),
(2, 'Aceptada'),
(3, 'Rechazada'),
(4, 'Anulada'),
(5, 'Espera'),
(6, 'Creado'),
(7, 'En proceso de Anulación'),
(8, 'Finalizada'),
(9, 'Expirada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria`
--

DROP TABLE IF EXISTS `galeria`;
CREATE TABLE IF NOT EXISTS `galeria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `portada` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id`, `nombre`, `descripcion`, `portada`, `fecha`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 'Nombre 2', 'Descripcion', 'lib/img/12102024/1728759458_3fb42e149f24aaaadb2f.jpg', '2024-10-09 00:00:00', 'A', '2024-10-12 18:32:05', '2024-10-12 18:57:38', NULL),
(2, 'asdasd', 'asdasd', 'lib/img/12102024/1728765341_098d2bebe8d7c5dd091b.jpg', '2024-10-18 00:00:00', 'A', '2024-10-12 20:35:41', '2024-10-12 20:35:41', NULL),
(6, '9', '9', 'lib/img/20082025/1755710180_fbe52c936718930d8a64.png', '2025-08-20 13:16:00', 'A', '2025-08-20 17:16:20', '2025-08-20 17:16:20', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria_detalle`
--

DROP TABLE IF EXISTS `galeria_detalle`;
CREATE TABLE IF NOT EXISTS `galeria_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `galeria_id` int DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `galeria_id` (`galeria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-08-17-000001', 'App\\Database\\Migrations\\ConvertMyISAMToInnoDB', 'default', 'App', 1755406406, 1),
(2, '2025-08-17-000002', 'App\\Database\\Migrations\\AddSecurityColumnsToUsuario', 'default', 'App', 1755406406, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modalidad_agenda`
--

DROP TABLE IF EXISTS `modalidad_agenda`;
CREATE TABLE IF NOT EXISTS `modalidad_agenda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `modalidad_agenda`
--

INSERT INTO `modalidad_agenda` (`id`, `nombre`) VALUES
(1, 'Presencial'),
(2, 'Online'),
(3, 'No Definido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo`
--

DROP TABLE IF EXISTS `modulo`;
CREATE TABLE IF NOT EXISTS `modulo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` varchar(500) NOT NULL,
  `ruta` varchar(100) DEFAULT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  `mostrar` char(1) DEFAULT 'S',
  `sa` char(1) NOT NULL DEFAULT 'S',
  `fcreacion` datetime NOT NULL,
  `factualizacion` datetime NOT NULL,
  `feliminacion` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `modulo`
--

INSERT INTO `modulo` (`id`, `nombre`, `descripcion`, `ruta`, `estado`, `mostrar`, `sa`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 'Usuario', '', '/dashboard/usuario', 'A', 'S', 'S', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Inicio', 'Primera pantalla que ve el usuario', '/dashboard/menu', 'A', 'S', 'N', '0000-00-00 00:00:00', '2024-09-24 23:05:03', '0000-00-00 00:00:00'),
(3, 'Perfil', '', '/dashboard/perfil', 'A', 'S', 'S', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'Modulo', '', '/dashboard/modulo', 'A', 'S', 'S', '2024-08-10 04:22:13', '2024-08-10 04:22:13', '2024-08-10 04:22:13'),
(6, 'Perfil Detalle', '', '/dashboard/perfil-detalle', 'A', 'S', 'S', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 'Modulo Detalle', 'En este modulo se encontraran los permisos creados para el mismo.', '/dashboard/modulo-detalle', 'A', 'S', 'S', '2024-08-11 04:13:21', '2024-08-11 04:13:21', '0000-00-00 00:00:00'),
(8, 'Servicios', 'Modulo para gestionar los servicios de la pagina web.', '/dashboard/servicio', 'A', 'S', 'N', '2024-08-16 01:15:58', '2024-10-12 06:08:28', '0000-00-00 00:00:00'),
(14, 'Galeria', 'Modulo web para gestionar galerias.', '/dashboard/galeria', 'A', 'S', 'N', '2024-10-12 18:26:18', '2024-10-12 18:26:18', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo_detalle`
--

DROP TABLE IF EXISTS `modulo_detalle`;
CREATE TABLE IF NOT EXISTS `modulo_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `modulo_id` int DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `ruta` varchar(100) DEFAULT NULL,
  `accion` varchar(50) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  `mostrar` char(1) DEFAULT 'S',
  `orden` int NOT NULL,
  `fcreacion` datetime NOT NULL,
  `factualizacion` datetime NOT NULL,
  `feliminacion` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modulo_id` (`modulo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `modulo_detalle`
--

INSERT INTO `modulo_detalle` (`id`, `modulo_id`, `descripcion`, `ruta`, `accion`, `estado`, `mostrar`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 1, 'Registrar usuario', '/registro', 'ver', 'A', 'S', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 1, 'Listar usuario', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 1, 'Editar usuario', '/editar', 'editar,eliminar', 'A', 'N', 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 1, 'Accion Registrar Usuarios', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 1, 'Acción Obtener usuarios para datatable', '/getUsuarios', 'ver,editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 1, 'Acción Editar usuario', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 1, 'Acción Editar Clave usuario', '/update/clave', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 3, 'Registrar Perfil', '/registro', 'registrar', 'A', 'S', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, 3, 'Listar perfil', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 3, 'Accion Registrar Perfil', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 3, 'Acción Obtener perfil para datatable', '/getPerfiles', 'ver,editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 3, 'Editar usuario', '/editar', 'editar,eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, 3, 'Acción Editar perfil', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 4, 'Registrar modulo', '/registro', 'ver', 'A', 'S', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 4, 'Listar Modulo', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 4, 'Acción Registrar Modulo', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 4, 'Acción Obtener modulos para datatable', '/getModulo', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '2024-10-12 05:40:52', '0000-00-00 00:00:00'),
(19, 4, 'Acción Editar modulo', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 4, 'Editar modulo', '/editar', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 6, 'Registrar Detalle Perfil', '/registro', 'ver', 'A', 'S', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 6, 'Listar Detalle Perfil', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 6, 'Acción Registrar Detalle Perfil', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 6, 'Acción Obtener detalle perfil para datatable', '/getPerfilDetalle', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 6, 'Editar Detalle Perfil', '/editar', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 6, 'Accion editar Detalle Perfil', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 7, 'Registrar Detalle Modulo', '/registro', 'ver', 'A', 'S', 1, '2024-08-11 04:28:36', '2024-08-11 04:28:36', '2024-08-11 04:28:36'),
(28, 7, 'Listar Detalle Modulo', '/lista', 'ver', 'A', 'S', 2, '2024-08-11 04:28:36', '2024-08-11 04:28:36', '2024-08-11 04:28:36'),
(29, 7, 'Acción Registrar Detalle Modulo', '/registrar', 'registrar', 'A', 'N', 0, '2024-08-11 04:28:36', '2024-08-11 04:28:36', '2024-08-11 04:28:36'),
(30, 7, 'Acción Obtener detalle modulo para datatable', '/getModuloDetalle', 'ver', 'A', 'N', 0, '2024-08-11 04:28:36', '2024-08-11 04:28:36', '2024-08-11 04:28:36'),
(31, 7, 'Accion editar Detalle Modulo', '/update', 'editar', 'A', 'N', 0, '2024-08-11 04:32:02', '2024-08-11 04:32:02', '2024-08-11 04:32:02'),
(33, 7, 'Editar detalle modulo', '/editar', 'ver', 'A', 'N', 0, '2024-08-11 05:20:22', '2024-08-11 05:20:22', '0000-00-00 00:00:00'),
(34, 1, 'Accion eliminar usuario', '/eliminar', 'eliminar', 'A', 'N', 0, '2024-08-16 00:31:25', '2024-08-16 00:31:25', '0000-00-00 00:00:00'),
(35, 6, 'Accion eliminar perfil detalle', '/eliminar', 'eliminar', 'A', 'N', 0, '2024-08-16 01:19:18', '2024-08-16 01:19:18', '0000-00-00 00:00:00'),
(36, 3, 'Accion eliminar perfil', '/eliminar', 'eliminar', 'A', 'N', 0, '2024-08-16 02:56:04', '2024-08-16 02:56:04', '0000-00-00 00:00:00'),
(38, 7, 'Accion eliminar modulo detalle', '/eliminar', 'eliminar', 'A', 'N', 0, '2024-08-16 03:23:50', '2024-08-16 03:23:50', '0000-00-00 00:00:00'),
(39, 2, 'Inicio', '/', 'ver', 'A', 'S', 1, '2024-08-16 03:31:56', '2024-08-16 03:32:52', '0000-00-00 00:00:00'),
(41, 4, 'Accion eliminar modulo', '/eliminar', 'eliminar', 'A', 'N', 0, '2024-08-16 03:35:20', '2024-08-16 03:35:20', '0000-00-00 00:00:00'),
(43, 8, 'Acción Registrar Servicio', '/registrar', 'registrar', 'A', 'N', 0, '2024-10-12 04:42:33', '2024-10-12 04:46:15', '0000-00-00 00:00:00'),
(44, 8, 'Acción eliminar servicio', '/eliminar', 'eliminar', 'A', 'N', 0, '2024-10-12 04:43:08', '2024-10-12 04:43:08', '0000-00-00 00:00:00'),
(48, 8, 'Listar Modulo', '/lista', 'ver', 'A', 'S', 2, '2024-10-12 04:44:51', '2024-10-12 18:38:58', '0000-00-00 00:00:00'),
(49, 8, 'Registrar servicio', '/registro', 'ver', 'A', 'S', 1, '2024-10-12 04:46:50', '2024-10-12 04:46:50', '0000-00-00 00:00:00'),
(50, 8, 'Acción Obtener servicios para datatable', '/getServicio', 'ver', 'A', 'N', 0, '2024-10-12 04:47:24', '2024-10-12 06:08:05', '0000-00-00 00:00:00'),
(52, 8, 'Acción Editar servicio', '/update', 'editar', 'A', 'N', 0, '2024-10-12 04:49:56', '2024-10-12 04:49:56', '0000-00-00 00:00:00'),
(53, 8, 'Editar servicio', '/editar', 'ver', 'A', 'N', 0, '2024-10-12 06:12:18', '2024-10-12 06:12:18', '0000-00-00 00:00:00'),
(54, 14, 'Acción Registrar Galeria', '/registrar', 'registrar', 'A', 'N', 0, '2024-10-12 18:27:06', '2024-10-12 18:27:06', '0000-00-00 00:00:00'),
(55, 14, 'Acción eliminar galeria', '/eliminar', 'eliminar', 'A', 'N', 0, '2024-10-12 18:27:29', '2024-10-12 18:29:30', '0000-00-00 00:00:00'),
(56, 14, 'Acción Obtener galeria para datatable', '/getGaleria', 'ver', 'A', 'N', 0, '2024-10-12 18:28:03', '2024-10-12 18:28:03', '0000-00-00 00:00:00'),
(57, 14, 'Acción editar galería', '/update', 'editar', 'A', 'N', 0, '2024-10-12 18:28:31', '2024-10-12 18:28:31', '0000-00-00 00:00:00'),
(58, 14, 'Editar galeria', '/editar', 'ver', 'A', 'N', 0, '2024-10-12 18:28:52', '2024-10-12 18:29:46', '0000-00-00 00:00:00'),
(59, 14, 'Registrar Galeria', '/registro', 'ver', 'A', 'S', 1, '2024-10-12 18:30:28', '2024-10-12 18:30:28', '0000-00-00 00:00:00'),
(60, 14, 'Listar Galeria', '/lista ', 'ver', 'A', 'S', 2, '2024-10-12 18:30:47', '2024-10-12 18:30:47', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil`
--

DROP TABLE IF EXISTS `perfil`;
CREATE TABLE IF NOT EXISTS `perfil` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `poder` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'A',
  `fcreacion` datetime NOT NULL,
  `factualizacion` datetime NOT NULL,
  `feliminacion` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `perfil`
--

INSERT INTO `perfil` (`id`, `nombre`, `poder`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 'Super Administrador', 3, 'A', '0000-00-00 00:00:00', '2024-08-10 08:48:55', '0000-00-00 00:00:00'),
(2, 'Trabajador', 1, 'A', '0000-00-00 00:00:00', '2024-08-10 06:28:25', '0000-00-00 00:00:00'),
(3, 'Administrador', 2, 'A', '0000-00-00 00:00:00', '2024-08-10 05:52:39', '0000-00-00 00:00:00'),
(4, 'Trabajador Portero', 0, 'A', '2024-08-10 04:42:14', '2024-08-16 03:00:46', '2024-08-16 03:00:46'),
(7, 'Administrador Web', 0, 'A', '2024-09-24 23:03:21', '2024-09-24 23:03:21', '0000-00-00 00:00:00'),
(8, 'Test Servicios', 0, 'A', '2024-10-12 04:50:17', '2024-10-12 04:50:17', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perfil_modulo`
--

DROP TABLE IF EXISTS `perfil_modulo`;
CREATE TABLE IF NOT EXISTS `perfil_modulo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `perfil_id` int DEFAULT NULL,
  `modulo_id` int DEFAULT NULL,
  `ver` tinyint(1) DEFAULT '1',
  `registrar` tinyint(1) NOT NULL DEFAULT '0',
  `editar` tinyint(1) DEFAULT '0',
  `eliminar` tinyint(1) DEFAULT '0',
  `analizar` tinyint(1) NOT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  `orden` int NOT NULL,
  `fcreacion` datetime NOT NULL,
  `factualizacion` datetime NOT NULL,
  `feliminacion` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `perfil_id` (`perfil_id`),
  KEY `modulo_id` (`modulo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `perfil_modulo`
--

INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 1, 1, 1, 1, 1, 1, 0, 'A', 2, '0000-00-00 00:00:00', '2024-08-11 05:56:33', '0000-00-00 00:00:00'),
(2, 1, 2, 1, 1, 1, 1, 0, 'A', 1, '0000-00-00 00:00:00', '2024-08-16 01:26:24', '2024-08-16 01:26:24'),
(4, 1, 4, 1, 1, 1, 1, 0, 'A', 4, '0000-00-00 00:00:00', '2024-08-16 03:35:52', '0000-00-00 00:00:00'),
(5, 1, 6, 1, 1, 1, 1, 0, 'A', 5, '2024-08-10 07:13:19', '2024-08-10 07:13:19', '2024-08-10 07:13:19'),
(8, 1, 7, 1, 1, 1, 1, 0, 'A', 6, '2024-08-11 04:14:07', '2024-08-11 04:14:07', '0000-00-00 00:00:00'),
(9, 1, 6, 1, 1, 1, 1, 0, 'I', 1, '2024-08-11 06:05:57', '2024-08-11 06:06:44', '0000-00-00 00:00:00'),
(16, 3, 8, 1, 1, 1, 0, 0, 'A', 4, '2024-08-16 01:33:08', '2024-08-16 03:59:23', '0000-00-00 00:00:00'),
(17, 3, 6, 1, 1, 0, 0, 0, 'A', 3, '2024-08-16 01:33:46', '2024-08-16 03:59:16', '0000-00-00 00:00:00'),
(18, 3, 2, 1, 0, 0, 0, 0, 'A', 1, '2024-08-16 01:39:26', '2024-08-16 03:59:58', '2024-08-16 03:06:10'),
(19, 1, 3, 1, 1, 1, 1, 0, 'A', 3, '2024-08-16 02:55:10', '2024-08-16 02:55:10', '0000-00-00 00:00:00'),
(20, 3, 1, 1, 1, 1, 1, 0, 'A', 2, '2024-08-16 03:58:57', '2024-09-24 23:07:02', '0000-00-00 00:00:00'),
(23, 3, 3, 1, 1, 1, 1, 0, 'A', 4, '2024-09-24 23:03:00', '2024-09-24 23:03:00', '0000-00-00 00:00:00'),
(24, 7, 2, 1, 1, 1, 1, 0, 'A', 1, '2024-09-24 23:05:11', '2024-09-24 23:05:11', '0000-00-00 00:00:00'),
(25, 7, 8, 1, 1, 1, 0, 0, 'A', 2, '2024-09-24 23:07:37', '2024-10-12 06:42:06', '0000-00-00 00:00:00'),
(26, 8, 2, 1, 1, 1, 1, 0, 'A', 1, '2024-10-12 04:50:50', '2024-10-12 04:50:50', '0000-00-00 00:00:00'),
(27, 8, 8, 1, 0, 1, 1, 0, 'A', 2, '2024-10-12 04:50:58', '2025-08-20 19:41:14', '0000-00-00 00:00:00'),
(28, 8, 14, 1, 1, 1, 1, 0, 'A', 3, '2024-10-12 18:31:05', '2025-08-20 17:21:10', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

DROP TABLE IF EXISTS `servicio`;
CREATE TABLE IF NOT EXISTS `servicio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcionCorta` varchar(500) DEFAULT NULL,
  `descripcionLarga` varchar(2000) DEFAULT NULL,
  `valor` int DEFAULT NULL,
  `foto` varchar(1000) DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id`, `nombre`, `descripcionCorta`, `descripcionLarga`, `valor`, `foto`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(5, 'Test3', 'a', 'b', 123, 'lib/img/12102024/1728714229_a4bf33087454af64546e.jpg', 'A', '2024-10-12 06:23:49', '2024-10-12 06:23:49', NULL),
(6, 'Test5', 'asd', 'asddd', 99000, 'lib/img/12102024/1728714259_24468acc9ea8743b4b45.png', 'A', '2024-10-12 06:24:19', '2024-10-12 06:24:19', NULL),
(9, 'Test 3', 'asdads', 'sdsasd', 2323, 'lib/img/20082025/1755711303_5e716ef108b2619be23a.png', 'A', '2025-08-20 17:35:03', '2025-08-20 17:35:03', NULL),
(10, 'test99', 'aasd', 'qwe', 2322, 'lib/img/20082025/1755711396_f34d95f3ebb2d62a0b5e.png', 'A', '2025-08-20 17:36:36', '2025-08-20 17:36:36', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_agenda`
--

DROP TABLE IF EXISTS `tipo_agenda`;
CREATE TABLE IF NOT EXISTS `tipo_agenda` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `clave` varchar(100) DEFAULT NULL,
  `perfil_id` int DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `login_attempts` int DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `mfa_secret` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_usuario_correo` (`correo`),
  KEY `perfil_id` (`perfil_id`),
  KEY `perfil_id_2` (`perfil_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `apellido`, `correo`, `telefono`, `clave`, `perfil_id`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`, `last_login`, `login_attempts`, `locked_until`, `reset_token`, `reset_expires`, `mfa_secret`) VALUES
(6, 'Rafael Mauricio', 'Villar Bahamondes', 'rvillar1995@gmail.com', '+569 91621564', '$2y$10$Rb.hLwjSnSQjRPXG7C7GMeN7O2i8zlKVYOM22SMBtrxrTc4RYh1t.', 1, 'A', '2024-06-24 01:29:15', '2024-06-24 01:29:15', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(11, 'Carolina', 'asd', 'asdasd5@gmail.com', 'rvillar1995@gmail.com', '$2y$10$IZ6SCnDGk11rpqnSK5B06u0lDaMY/M9czO80W1sIkkwXPMF2WkBD2', 1, 'A', '2024-07-08 04:09:29', '2024-07-16 23:58:18', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(14, 'admin', 'admin', 'admin@gmail.com', '123', '$2y$10$FGIDfOjjZgBOhnss4fjfoeOO3iMD6DNAp5zrM5gynfbAqQE/T.VDm', 3, 'A', '2024-08-11 05:58:00', '2025-08-17 06:21:16', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(15, 'Fernando', 'xxx', 'fernando@gmail.com', '1', '$2y$10$eKNug/Js4eJlmt92QaV/6e.bA.V2mC5dg.Ftapito.bZ55L8pl51e', 2, 'A', '2024-09-24 22:48:10', '2024-09-24 22:48:10', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(16, 'Administrador', 'Web', 'adminweb@gmail.com', '123', '$2y$10$0A2bGwfzRXvYeqcdSTG74OFDlWcOg5LoJOHMatHaoreFyBKFdEfBG', 7, 'A', '2024-09-24 23:07:10', '2024-09-24 23:07:10', NULL, NULL, 0, NULL, NULL, NULL, NULL),
(17, 'test', 'servicios', 'servicios@gmail.com', '991621564', '$2y$10$.zoDCoiMrp.uX3xrpcj7zeO4lrVcJ3wrXdJw0fo5wmofPuLH2M3H.', 8, 'A', '2024-10-12 04:50:38', '2025-08-20 17:14:29', NULL, NULL, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_especialidad`
--

DROP TABLE IF EXISTS `usuario_especialidad`;
CREATE TABLE IF NOT EXISTS `usuario_especialidad` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `especialidad_id` int NOT NULL,
  `tiempo` int NOT NULL,
  `estado` char(1) DEFAULT 'A',
  PRIMARY KEY (`id`),
  KEY `fk_usuario` (`usuario_id`),
  KEY `fk_especialidad` (`especialidad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `agenda`
--
ALTER TABLE `agenda`
  ADD CONSTRAINT `fk_estado_agenda` FOREIGN KEY (`estado_id`) REFERENCES `estado_agenda` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tipo_agenda` FOREIGN KEY (`tipo_id`) REFERENCES `tipo_agenda` (`id`);

--
-- Filtros para la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD CONSTRAINT `contacto_ibfk_1` FOREIGN KEY (`estado_id`) REFERENCES `contacto_estado` (`id`);

--
-- Filtros para la tabla `contacto_historial`
--
ALTER TABLE `contacto_historial`
  ADD CONSTRAINT `contacto_historial_ibfk_1` FOREIGN KEY (`contacto_id`) REFERENCES `contacto` (`id`);

--
-- Filtros para la tabla `detalle_agenda`
--
ALTER TABLE `detalle_agenda`
  ADD CONSTRAINT `fk_agenda` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detalle_servicio` FOREIGN KEY (`usuario_especialidad_id`) REFERENCES `usuario_especialidad` (`id`),
  ADD CONSTRAINT `fk_estado_detalle` FOREIGN KEY (`estado`) REFERENCES `estado_detalle_agenda` (`id`),
  ADD CONSTRAINT `fk_estadoSolicitud` FOREIGN KEY (`estado_solicitud_id`) REFERENCES `estado_solicitud` (`id`),
  ADD CONSTRAINT `fk_modalidad` FOREIGN KEY (`modalidad_id`) REFERENCES `modalidad_agenda` (`id`);

--
-- Filtros para la tabla `galeria_detalle`
--
ALTER TABLE `galeria_detalle`
  ADD CONSTRAINT `galeria_detalle_ibfk_1` FOREIGN KEY (`galeria_id`) REFERENCES `galeria` (`id`);

--
-- Filtros para la tabla `modulo_detalle`
--
ALTER TABLE `modulo_detalle`
  ADD CONSTRAINT `modulo_detalle_ibfk_1` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`);

--
-- Filtros para la tabla `perfil_modulo`
--
ALTER TABLE `perfil_modulo`
  ADD CONSTRAINT `perfil_modulo_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`),
  ADD CONSTRAINT `perfil_modulo_ibfk_2` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Filtros para la tabla `usuario_especialidad`
--
ALTER TABLE `usuario_especialidad`
  ADD CONSTRAINT `fk_especialidad` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidad` (`id`),
  ADD CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
