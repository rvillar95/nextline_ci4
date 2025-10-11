-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 11-10-2025 a las 23:56:02
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
-- Base de datos: `nextline_constructor`
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
-- Estructura de tabla para la tabla `banner`
--

DROP TABLE IF EXISTS `banner`;
CREATE TABLE IF NOT EXISTS `banner` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subtitulo` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `imagen` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `link` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `orden` int NOT NULL DEFAULT '1',
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'A',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo_cliente` enum('particular','empresa','organizacion') NOT NULL DEFAULT 'particular',
  `nombre_razon_social` varchar(200) NOT NULL,
  `rut_dni` varchar(20) DEFAULT NULL,
  `contacto_nombre` varchar(100) DEFAULT NULL,
  `contacto_cargo` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(300) DEFAULT NULL,
  `region_id` int DEFAULT NULL,
  `comuna_id` int DEFAULT NULL,
  `comuna` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `sitio_web` varchar(200) DEFAULT NULL,
  `observaciones` text,
  `estado` enum('A','I') DEFAULT 'A',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tipo_cliente` (`tipo_cliente`),
  KEY `idx_nombre` (`nombre_razon_social`),
  KEY `idx_rut` (`rut_dni`),
  KEY `idx_clientes_region` (`region_id`),
  KEY `idx_clientes_comuna` (`comuna_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `tipo_cliente`, `nombre_razon_social`, `rut_dni`, `contacto_nombre`, `contacto_cargo`, `telefono`, `email`, `direccion`, `region_id`, `comuna_id`, `comuna`, `region`, `sitio_web`, `observaciones`, `estado`, `fcreacion`, `factualizacion`) VALUES
(1, 'particular', 'Don Rafael Villar', '', 'Rafael Villar', '', '+56912345678', 'rafael@email.com', 'Av. Principal 123, Santiago', NULL, NULL, '', '', '', 'test', 'A', '2025-09-27 23:54:25', '2025-09-29 02:03:54'),
(2, 'empresa', 'Constructora ABC S.A.', NULL, 'María González', NULL, '+56987654321', 'maria@constructoraabc.cl', 'Av. Empresarial 456, Las Condes', NULL, NULL, NULL, NULL, NULL, NULL, 'I', '2025-09-27 23:54:25', '2025-09-29 05:16:21'),
(3, 'organizacion', 'Municipalidad de Santiago', NULL, 'Carlos Rodríguez', NULL, '+56911223344', 'carlos@munistgo.cl', 'Plaza de Armas 1, Santiago', NULL, NULL, NULL, NULL, NULL, NULL, 'I', '2025-09-27 23:54:25', '2025-09-29 05:16:16'),
(4, 'particular', 'Rafael Villar', '18.983.058-0', 'asd', 'asd', '991621564', 'rvillar1995@gmail.com', 'aaaa', 9, 186, NULL, NULL, 'https://www.google.com', 'www', 'A', '2025-09-29 02:50:38', '2025-09-29 03:03:35'),
(5, 'particular', 'asd', '', 'asd', 'asd', '56991621564', 'asd@gmail.com', 'asd', 16, 338, NULL, NULL, 'https://www.google.cl', 'asd', 'I', '2025-09-29 03:13:50', '2025-09-29 05:13:08'),
(6, 'particular', 'Manuel Sanchez', '', '', '', '', 'manuelsanchez@gmail.com', '', 9, 186, NULL, NULL, '', '', 'A', '2025-10-01 06:39:11', '2025-10-01 06:39:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunas`
--

DROP TABLE IF EXISTS `comunas`;
CREATE TABLE IF NOT EXISTS `comunas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `region_id` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `region_id` (`region_id`),
  KEY `idx_comunas_region` (`region_id`),
  KEY `idx_comunas_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=342 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comunas`
--

INSERT INTO `comunas` (`id`, `codigo`, `nombre`, `region_id`, `activo`) VALUES
(1, '15101', 'Arica', 1, 1),
(2, '15102', 'Camarones', 1, 1),
(3, '15201', 'Putre', 1, 1),
(4, '15202', 'General Lagos', 1, 1),
(5, '01101', 'Iquique', 2, 1),
(6, '01107', 'Alto Hospicio', 2, 1),
(7, '01401', 'Pozo Almonte', 2, 1),
(8, '01402', 'Camiña', 2, 1),
(9, '01403', 'Colchane', 2, 1),
(10, '01404', 'Huara', 2, 1),
(11, '01405', 'Pica', 2, 1),
(12, '02101', 'Antofagasta', 3, 1),
(13, '02102', 'Mejillones', 3, 1),
(14, '02103', 'Sierra Gorda', 3, 1),
(15, '02104', 'Taltal', 3, 1),
(16, '02201', 'Calama', 3, 1),
(17, '02202', 'Ollagüe', 3, 1),
(18, '02203', 'San Pedro de Atacama', 3, 1),
(19, '02301', 'Tocopilla', 3, 1),
(20, '02302', 'María Elena', 3, 1),
(21, '03101', 'Copiapó', 4, 1),
(22, '03102', 'Caldera', 4, 1),
(23, '03103', 'Tierra Amarilla', 4, 1),
(24, '03201', 'Chañaral', 4, 1),
(25, '03202', 'Diego de Almagro', 4, 1),
(26, '03301', 'Vallenar', 4, 1),
(27, '03302', 'Alto del Carmen', 4, 1),
(28, '03303', 'Freirina', 4, 1),
(29, '03304', 'Huasco', 4, 1),
(30, '04101', 'La Serena', 5, 1),
(31, '04102', 'Coquimbo', 5, 1),
(32, '04103', 'Andacollo', 5, 1),
(33, '04104', 'La Higuera', 5, 1),
(34, '04105', 'Paiguano', 5, 1),
(35, '04106', 'Vicuña', 5, 1),
(36, '04201', 'Illapel', 5, 1),
(37, '04202', 'Canela', 5, 1),
(38, '04203', 'Los Vilos', 5, 1),
(39, '04204', 'Salamanca', 5, 1),
(40, '04301', 'Ovalle', 5, 1),
(41, '04302', 'Combarbalá', 5, 1),
(42, '04303', 'Monte Patria', 5, 1),
(43, '04304', 'Punitaqui', 5, 1),
(44, '04305', 'Río Hurtado', 5, 1),
(45, '05101', 'Valparaíso', 6, 1),
(46, '05102', 'Casablanca', 6, 1),
(47, '05103', 'Concón', 6, 1),
(48, '05104', 'Juan Fernández', 6, 1),
(49, '05105', 'Puchuncaví', 6, 1),
(50, '05107', 'Quintero', 6, 1),
(51, '05109', 'Viña del Mar', 6, 1),
(52, '05201', 'Isla de Pascua', 6, 1),
(53, '05301', 'Los Andes', 6, 1),
(54, '05302', 'Calle Larga', 6, 1),
(55, '05303', 'Rinconada', 6, 1),
(56, '05304', 'San Esteban', 6, 1),
(57, '05401', 'La Ligua', 6, 1),
(58, '05402', 'Cabildo', 6, 1),
(59, '05403', 'Papudo', 6, 1),
(60, '05404', 'Petorca', 6, 1),
(61, '05405', 'Zapallar', 6, 1),
(62, '05501', 'Quillota', 6, 1),
(63, '05502', 'Calera', 6, 1),
(64, '05503', 'Hijuelas', 6, 1),
(65, '05504', 'La Cruz', 6, 1),
(66, '05506', 'Nogales', 6, 1),
(67, '05601', 'San Antonio', 6, 1),
(68, '05602', 'Algarrobo', 6, 1),
(69, '05603', 'Cartagena', 6, 1),
(70, '05604', 'El Quisco', 6, 1),
(71, '05605', 'El Tabo', 6, 1),
(72, '05606', 'Santo Domingo', 6, 1),
(73, '05701', 'San Felipe', 6, 1),
(74, '05702', 'Catemu', 6, 1),
(75, '05703', 'Llaillay', 6, 1),
(76, '05704', 'Panquehue', 6, 1),
(77, '05705', 'Putaendo', 6, 1),
(78, '05706', 'Santa María', 6, 1),
(79, '13101', 'Santiago', 7, 1),
(80, '13102', 'Cerrillos', 7, 1),
(81, '13103', 'Cerro Navia', 7, 1),
(82, '13104', 'Conchalí', 7, 1),
(83, '13105', 'El Bosque', 7, 1),
(84, '13106', 'Estación Central', 7, 1),
(85, '13107', 'Huechuraba', 7, 1),
(86, '13108', 'Independencia', 7, 1),
(87, '13109', 'La Cisterna', 7, 1),
(88, '13110', 'La Florida', 7, 1),
(89, '13111', 'La Granja', 7, 1),
(90, '13112', 'La Pintana', 7, 1),
(91, '13113', 'La Reina', 7, 1),
(92, '13114', 'Las Condes', 7, 1),
(93, '13115', 'Lo Barnechea', 7, 1),
(94, '13116', 'Lo Espejo', 7, 1),
(95, '13117', 'Lo Prado', 7, 1),
(96, '13118', 'Macul', 7, 1),
(97, '13119', 'Maipú', 7, 1),
(98, '13120', 'Ñuñoa', 7, 1),
(99, '13121', 'Pedro Aguirre Cerda', 7, 1),
(100, '13122', 'Peñalolén', 7, 1),
(101, '13123', 'Providencia', 7, 1),
(102, '13124', 'Pudahuel', 7, 1),
(103, '13125', 'Quilicura', 7, 1),
(104, '13126', 'Quinta Normal', 7, 1),
(105, '13127', 'Recoleta', 7, 1),
(106, '13128', 'Renca', 7, 1),
(107, '13129', 'San Joaquín', 7, 1),
(108, '13130', 'San Miguel', 7, 1),
(109, '13131', 'San Ramón', 7, 1),
(110, '13132', 'Vitacura', 7, 1),
(111, '13201', 'Puente Alto', 7, 1),
(112, '13202', 'Pirque', 7, 1),
(113, '13203', 'San José de Maipo', 7, 1),
(114, '13301', 'Colina', 7, 1),
(115, '13302', 'Lampa', 7, 1),
(116, '13303', 'Tiltil', 7, 1),
(117, '13401', 'San Bernardo', 7, 1),
(118, '13402', 'Buin', 7, 1),
(119, '13403', 'Calera de Tango', 7, 1),
(120, '13404', 'Paine', 7, 1),
(121, '13501', 'Melipilla', 7, 1),
(122, '13502', 'Alhué', 7, 1),
(123, '13503', 'Curacaví', 7, 1),
(124, '13504', 'María Pinto', 7, 1),
(125, '13505', 'San Pedro', 7, 1),
(126, '13601', 'Talagante', 7, 1),
(127, '13602', 'El Monte', 7, 1),
(128, '13603', 'Isla de Maipo', 7, 1),
(129, '13604', 'Padre Hurtado', 7, 1),
(130, '13605', 'Peñaflor', 7, 1),
(131, '06101', 'Rancagua', 8, 1),
(132, '06102', 'Codegua', 8, 1),
(133, '06103', 'Coinco', 8, 1),
(134, '06104', 'Coltauco', 8, 1),
(135, '06105', 'Doñihue', 8, 1),
(136, '06106', 'Graneros', 8, 1),
(137, '06107', 'Las Cabras', 8, 1),
(138, '06108', 'Machalí', 8, 1),
(139, '06109', 'Malloa', 8, 1),
(140, '06110', 'Mostazal', 8, 1),
(141, '06111', 'Olivar', 8, 1),
(142, '06112', 'Peumo', 8, 1),
(143, '06113', 'Pichidegua', 8, 1),
(144, '06114', 'Quinta de Tilcoco', 8, 1),
(145, '06115', 'Rengo', 8, 1),
(146, '06116', 'Requínoa', 8, 1),
(147, '06117', 'San Vicente', 8, 1),
(148, '06201', 'Pichilemu', 8, 1),
(149, '06202', 'La Estrella', 8, 1),
(150, '06203', 'Litueche', 8, 1),
(151, '06204', 'Marchihue', 8, 1),
(152, '06205', 'Navidad', 8, 1),
(153, '06206', 'Paredones', 8, 1),
(154, '06301', 'San Fernando', 8, 1),
(155, '06302', 'Chépica', 8, 1),
(156, '06303', 'Chimbarongo', 8, 1),
(157, '06304', 'Lolol', 8, 1),
(158, '06305', 'Nancagua', 8, 1),
(159, '06306', 'Palmilla', 8, 1),
(160, '06307', 'Peralillo', 8, 1),
(161, '06308', 'Placilla', 8, 1),
(162, '06309', 'Pumanque', 8, 1),
(163, '06310', 'Santa Cruz', 8, 1),
(164, '07101', 'Talca', 9, 1),
(165, '07102', 'Constitución', 9, 1),
(166, '07103', 'Curepto', 9, 1),
(167, '07104', 'Empedrado', 9, 1),
(168, '07105', 'Maule', 9, 1),
(169, '07106', 'Pelarco', 9, 1),
(170, '07107', 'Pencahue', 9, 1),
(171, '07108', 'Río Claro', 9, 1),
(172, '07109', 'San Clemente', 9, 1),
(173, '07110', 'San Rafael', 9, 1),
(174, '07201', 'Cauquenes', 9, 1),
(175, '07202', 'Chanco', 9, 1),
(176, '07203', 'Pelluhue', 9, 1),
(177, '07301', 'Curicó', 9, 1),
(178, '07302', 'Hualañé', 9, 1),
(179, '07303', 'Licantén', 9, 1),
(180, '07304', 'Molina', 9, 1),
(181, '07305', 'Rauco', 9, 1),
(182, '07306', 'Romeral', 9, 1),
(183, '07307', 'Sagrada Familia', 9, 1),
(184, '07308', 'Teno', 9, 1),
(185, '07309', 'Vichuquén', 9, 1),
(186, '07401', 'Linares', 9, 1),
(187, '07402', 'Colbún', 9, 1),
(188, '07403', 'Longaví', 9, 1),
(189, '07404', 'Parral', 9, 1),
(190, '07405', 'Retiro', 9, 1),
(191, '07406', 'San Javier', 9, 1),
(192, '07407', 'Villa Alegre', 9, 1),
(193, '07408', 'Yerbas Buenas', 9, 1),
(194, '16101', 'Chillán', 10, 1),
(195, '16102', 'Bulnes', 10, 1),
(196, '16103', 'Chillán Viejo', 10, 1),
(197, '16104', 'El Carmen', 10, 1),
(198, '16105', 'Pemuco', 10, 1),
(199, '16106', 'Pinto', 10, 1),
(200, '16107', 'Quillón', 10, 1),
(201, '16108', 'San Ignacio', 10, 1),
(202, '16109', 'Yungay', 10, 1),
(203, '16201', 'Quirihue', 10, 1),
(204, '16202', 'Cobquecura', 10, 1),
(205, '16203', 'Coelemu', 10, 1),
(206, '16204', 'Ninhue', 10, 1),
(207, '16205', 'Portezuelo', 10, 1),
(208, '16206', 'Ránquil', 10, 1),
(209, '16207', 'Treguaco', 10, 1),
(210, '16301', 'San Carlos', 10, 1),
(211, '16302', 'Coihueco', 10, 1),
(212, '16303', 'Ñiquén', 10, 1),
(213, '16304', 'San Fabián', 10, 1),
(214, '16305', 'San Nicolás', 10, 1),
(215, '08101', 'Concepción', 11, 1),
(216, '08102', 'Coronel', 11, 1),
(217, '08103', 'Chiguayante', 11, 1),
(218, '08104', 'Florida', 11, 1),
(219, '08105', 'Hualpén', 11, 1),
(220, '08106', 'Hualqui', 11, 1),
(221, '08107', 'Lota', 11, 1),
(222, '08108', 'Penco', 11, 1),
(223, '08109', 'San Pedro de la Paz', 11, 1),
(224, '08110', 'Santa Juana', 11, 1),
(225, '08111', 'Talcahuano', 11, 1),
(226, '08112', 'Tomé', 11, 1),
(227, '08201', 'Lebu', 11, 1),
(228, '08202', 'Arauco', 11, 1),
(229, '08203', 'Cañete', 11, 1),
(230, '08204', 'Contulmo', 11, 1),
(231, '08205', 'Curanilahue', 11, 1),
(232, '08206', 'Los Álamos', 11, 1),
(233, '08207', 'Tirúa', 11, 1),
(234, '08301', 'Los Ángeles', 11, 1),
(235, '08302', 'Antuco', 11, 1),
(236, '08303', 'Cabrero', 11, 1),
(237, '08304', 'Laja', 11, 1),
(238, '08305', 'Mulchén', 11, 1),
(239, '08306', 'Nacimiento', 11, 1),
(240, '08307', 'Negrete', 11, 1),
(241, '08308', 'Quilaco', 11, 1),
(242, '08309', 'Quilleco', 11, 1),
(243, '08310', 'San Rosendo', 11, 1),
(244, '08311', 'Santa Bárbara', 11, 1),
(245, '08312', 'Tucapel', 11, 1),
(246, '08313', 'Yumbel', 11, 1),
(247, '08314', 'Alto Biobío', 11, 1),
(248, '09101', 'Temuco', 12, 1),
(249, '09102', 'Carahue', 12, 1),
(250, '09103', 'Cunco', 12, 1),
(251, '09104', 'Curarrehue', 12, 1),
(252, '09105', 'Freire', 12, 1),
(253, '09106', 'Galvarino', 12, 1),
(254, '09107', 'Gorbea', 12, 1),
(255, '09108', 'Lautaro', 12, 1),
(256, '09109', 'Loncoche', 12, 1),
(257, '09110', 'Melipeuco', 12, 1),
(258, '09111', 'Nueva Imperial', 12, 1),
(259, '09112', 'Padre Las Casas', 12, 1),
(260, '09113', 'Perquenco', 12, 1),
(261, '09114', 'Pitrufquén', 12, 1),
(262, '09115', 'Pucón', 12, 1),
(263, '09116', 'Saavedra', 12, 1),
(264, '09117', 'Teodoro Schmidt', 12, 1),
(265, '09118', 'Toltén', 12, 1),
(266, '09119', 'Vilcún', 12, 1),
(267, '09120', 'Villarrica', 12, 1),
(268, '09201', 'Angol', 12, 1),
(269, '09202', 'Collipulli', 12, 1),
(270, '09203', 'Curacautín', 12, 1),
(271, '09204', 'Ercilla', 12, 1),
(272, '09205', 'Lonquimay', 12, 1),
(273, '09206', 'Los Sauces', 12, 1),
(274, '09207', 'Lumaco', 12, 1),
(275, '09208', 'Purén', 12, 1),
(276, '09209', 'Renaico', 12, 1),
(277, '09210', 'Traiguén', 12, 1),
(278, '09211', 'Victoria', 12, 1),
(279, '14101', 'Valdivia', 13, 1),
(280, '14102', 'Corral', 13, 1),
(281, '14103', 'Lanco', 13, 1),
(282, '14104', 'Los Lagos', 13, 1),
(283, '14105', 'Máfil', 13, 1),
(284, '14106', 'Mariquina', 13, 1),
(285, '14107', 'Paillaco', 13, 1),
(286, '14108', 'Panguipulli', 13, 1),
(287, '14201', 'La Unión', 13, 1),
(288, '14202', 'Futrono', 13, 1),
(289, '14203', 'Lago Ranco', 13, 1),
(290, '14204', 'Río Bueno', 13, 1),
(291, '10101', 'Puerto Montt', 14, 1),
(292, '10102', 'Calbuco', 14, 1),
(293, '10103', 'Cochamó', 14, 1),
(294, '10104', 'Fresia', 14, 1),
(295, '10105', 'Frutillar', 14, 1),
(296, '10106', 'Los Muermos', 14, 1),
(297, '10107', 'Llanquihue', 14, 1),
(298, '10108', 'Maullín', 14, 1),
(299, '10109', 'Puerto Varas', 14, 1),
(300, '10201', 'Castro', 14, 1),
(301, '10202', 'Ancud', 14, 1),
(302, '10203', 'Chonchi', 14, 1),
(303, '10204', 'Curaco de Vélez', 14, 1),
(304, '10205', 'Dalcahue', 14, 1),
(305, '10206', 'Puqueldón', 14, 1),
(306, '10207', 'Queilén', 14, 1),
(307, '10208', 'Quellón', 14, 1),
(308, '10209', 'Quemchi', 14, 1),
(309, '10210', 'Quinchao', 14, 1),
(310, '10301', 'Osorno', 14, 1),
(311, '10302', 'Puerto Octay', 14, 1),
(312, '10303', 'Purranque', 14, 1),
(313, '10304', 'Puyehue', 14, 1),
(314, '10305', 'Río Negro', 14, 1),
(315, '10306', 'San Juan de la Costa', 14, 1),
(316, '10307', 'San Pablo', 14, 1),
(317, '10401', 'Chaitén', 14, 1),
(318, '10402', 'Futaleufú', 14, 1),
(319, '10403', 'Hualaihué', 14, 1),
(320, '10404', 'Palena', 14, 1),
(321, '11101', 'Coyhaique', 15, 1),
(322, '11102', 'Lago Verde', 15, 1),
(323, '11201', 'Aysén', 15, 1),
(324, '11202', 'Cisnes', 15, 1),
(325, '11203', 'Guaitecas', 15, 1),
(326, '11301', 'Cochrane', 15, 1),
(327, '11302', 'O\'Higgins', 15, 1),
(328, '11303', 'Tortel', 15, 1),
(329, '11401', 'Chile Chico', 15, 1),
(330, '11402', 'Río Ibáñez', 15, 1),
(331, '12101', 'Punta Arenas', 16, 1),
(332, '12102', 'Laguna Blanca', 16, 1),
(333, '12103', 'Río Verde', 16, 1),
(334, '12104', 'San Gregorio', 16, 1),
(335, '12201', 'Cabo de Hornos', 16, 1),
(336, '12202', 'Antártica', 16, 1),
(337, '12301', 'Porvenir', 16, 1),
(338, '12302', 'Primavera', 16, 1),
(339, '12303', 'Timaukel', 16, 1),
(340, '12401', 'Natales', 16, 1),
(341, '12402', 'Torres del Paine', 16, 1);

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
-- Estructura de tabla para la tabla `cotizaciones`
--

DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE IF NOT EXISTS `cotizaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `numero_cotizacion` varchar(50) DEFAULT NULL,
  `proyecto_nombre` varchar(200) NOT NULL,
  `proyecto_descripcion` text,
  `proyecto_tipo` enum('residencial','comercial','industrial','mantenimiento','otro') DEFAULT 'residencial',
  `proyecto_area` decimal(10,2) DEFAULT NULL,
  `proyecto_ubicacion` varchar(300) DEFAULT NULL,
  `proyecto_direccion` varchar(300) DEFAULT NULL,
  `fecha_cotizacion` date NOT NULL,
  `fecha_validez` date DEFAULT NULL,
  `vigencia_dias` int DEFAULT '30',
  `estado` enum('borrador','enviada','revisada','aprobada','rechazada','expirada') DEFAULT 'borrador',
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `subtotal_materiales` decimal(12,2) DEFAULT '0.00',
  `subtotal_mano_obra` decimal(12,2) DEFAULT '0.00',
  `subtotal_servicios` decimal(12,2) DEFAULT '0.00',
  `descuento_porcentaje` decimal(5,2) DEFAULT '0.00',
  `descuento_monto` decimal(12,2) DEFAULT '0.00',
  `subtotal_sin_iva` decimal(15,2) DEFAULT '0.00' COMMENT 'Subtotal antes de aplicar IVA',
  `iva_porcentaje` decimal(5,2) DEFAULT '19.00',
  `iva_monto` decimal(12,2) DEFAULT '0.00',
  `total_general` decimal(12,2) DEFAULT '0.00',
  `forma_pago` enum('contado','credito','mixto') DEFAULT 'contado',
  `plazo_pago_dias` int DEFAULT '0',
  `anticipo_porcentaje` decimal(5,2) DEFAULT '0.00',
  `anticipo_monto` decimal(12,2) DEFAULT '0.00',
  `tiempo_ejecucion_dias` int DEFAULT NULL,
  `fecha_inicio_estimada` date DEFAULT NULL,
  `fecha_fin_estimada` date DEFAULT NULL,
  `condiciones_generales` text,
  `observaciones_especiales` text,
  `garantia_meses` int DEFAULT '12',
  `slug` varchar(200) DEFAULT NULL,
  `meta_titulo` varchar(200) DEFAULT NULL,
  `meta_descripcion` varchar(300) DEFAULT NULL,
  `creado_por` int DEFAULT NULL,
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_cotizacion` (`numero_cotizacion`),
  KEY `idx_cliente` (`cliente_id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_cotizacion` (`fecha_cotizacion`),
  KEY `idx_numero` (`numero_cotizacion`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cotizaciones`
--

INSERT INTO `cotizaciones` (`id`, `cliente_id`, `numero_cotizacion`, `proyecto_nombre`, `proyecto_descripcion`, `proyecto_tipo`, `proyecto_area`, `proyecto_ubicacion`, `proyecto_direccion`, `fecha_cotizacion`, `fecha_validez`, `vigencia_dias`, `estado`, `prioridad`, `subtotal_materiales`, `subtotal_mano_obra`, `subtotal_servicios`, `descuento_porcentaje`, `descuento_monto`, `subtotal_sin_iva`, `iva_porcentaje`, `iva_monto`, `total_general`, `forma_pago`, `plazo_pago_dias`, `anticipo_porcentaje`, `anticipo_monto`, `tiempo_ejecucion_dias`, `fecha_inicio_estimada`, `fecha_fin_estimada`, `condiciones_generales`, `observaciones_especiales`, `garantia_meses`, `slug`, `meta_titulo`, `meta_descripcion`, `creado_por`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 1, 'COT-2025-0001', '', NULL, 'residencial', NULL, NULL, NULL, '2025-09-29', '2025-10-29', 30, '', 'baja', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '670152.80', '4197272.80', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, NULL, NULL, 12, 'presupuesto-cobertizo', NULL, NULL, NULL, '2025-09-29 03:40:12', '2025-09-29 05:02:54', '2025-09-29 05:02:54'),
(2, 1, 'COT-2025-0002', 'Cobertizo', '• _ Fabricación Radier allanado\nde piso con acma para loza\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\n• Sacar calefón interior e instalar afuera,sellar muro.\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\n• Eliminar llave y desagüe actual patio\n• Correr Pilar metálico entrada ( portón )\n• Instalación tubería gris sanitaria (futuro baño segundo nivel)\n• instalación eléctrica 12 focos a elección más 2 enchufes.', 'residencial', NULL, NULL, NULL, '2025-09-29', '2025-10-29', 30, 'borrador', 'baja', '2837120.00', '0.00', '690000.00', '0.00', '0.00', '3527120.00', '0.00', '0.00', '3527120.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'cobertizo', NULL, NULL, 17, '2025-09-29 04:06:49', '2025-09-29 05:16:46', '2025-09-29 05:16:46'),
(3, 1, 'COT-2025-0003', 'Cotizacion cobertizo', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico entrada ( portón )\r\n• Instalación tubería gris sanitaria (futuro baño segundo nivel)\r\n• instalación eléctrica 12 focos a elección más 2 enchufes.', 'residencial', '0.00', 'Linares', 'Hugo Gonzalez Isami 2080', '2025-09-29', '2025-10-29', 30, 'borrador', 'baja', '2837120.00', '0.00', '690000.00', '0.00', '0.00', '3527120.00', '0.00', '0.00', '3527120.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'cotizacion-cobertizo', NULL, NULL, 17, '2025-09-29 04:45:08', '2025-09-29 05:16:48', '2025-09-29 05:16:48'),
(4, 1, 'COT-2025-0004', 'Cotizacion cobertizo', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico entrada ( portón )\r\n• Instalación tubería gris sanitaria (futuro baño segundo nivel)\r\n• instalación eléctrica 12 focos a elección más 2 enchufes.', 'residencial', '0.00', 'Linares', 'Hugo Gonzalez Isami 2080', '2025-09-29', '2025-10-29', 30, 'borrador', 'baja', '2837120.00', '0.00', '690000.00', '0.00', '0.00', '3527120.00', '0.00', '0.00', '3527120.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'cotizacion-cobertizo-1', NULL, NULL, 17, '2025-09-29 04:47:35', '2025-09-29 05:16:51', '2025-09-29 05:16:51'),
(5, 1, 'COT-2025-0005', 'Cotizacion cobertizo', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico entrada ( portón )\r\n• Instalación tubería gris sanitaria (futuro baño segundo nivel)\r\n• instalación eléctrica 12 focos a elección más 2 enchufes.', 'residencial', '0.00', 'Linares', 'Hugo Gonzalez Isami 2080', '2025-09-29', '2025-10-29', 30, 'borrador', 'baja', '2837120.00', '0.00', '690000.00', '0.00', '0.00', '3527120.00', '0.00', '0.00', '3527120.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'cotizacion-cobertizo-2', NULL, NULL, 17, '2025-09-29 04:49:09', '2025-09-29 05:16:52', '2025-09-29 05:16:52'),
(6, 1, 'COT-2025-0006', 'Cotizacion cobertizo', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico entrada ( portón )\r\n• Instalación tubería gris sanitaria (futuro baño segundo nivel)\r\n• instalación eléctrica 12 focos a elección más 2 enchufes.', 'residencial', '0.00', 'Linares', 'Hugo Gonzalez Isami 2080', '2025-09-29', '2025-10-29', 30, 'borrador', 'baja', '2837120.00', '0.00', '690000.00', '0.00', '0.00', '3527120.00', '0.00', '0.00', '3527120.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'cotizacion-cobertizo-3', NULL, NULL, 17, '2025-09-29 04:50:19', '2025-09-29 05:16:43', '2025-09-29 05:16:43'),
(7, 1, 'COT-2025-0007', 'Cotizacion cobertizo 2', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico entrada ( portón )\r\n• Instalación tubería gris sanitaria (futuro baño segundo nivel)\r\n• instalación eléctrica 12 focos a elección más 2 enchufes.', 'residencial', NULL, NULL, NULL, '2025-09-29', '2025-10-29', 30, 'aprobada', 'baja', '2837120.00', '0.00', '690000.00', '0.00', '0.00', '5027120.00', '0.00', '0.00', '5027120.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'cotizacion-cobertizo-2', NULL, NULL, 17, '2025-09-29 04:51:03', '2025-09-30 00:04:14', NULL),
(8, 4, 'COT-2025-0008', 'Construccion de Cobertizo', 'Haremos un cobertizo', 'residencial', NULL, NULL, NULL, '2025-09-29', '2025-10-29', 30, '', 'baja', '3060000.00', '0.00', '0.00', '0.00', '0.00', '3060000.00', '0.00', '0.00', '3060000.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'construccion-de-cobertizo', NULL, NULL, 17, '2025-09-29 23:36:20', '2025-09-29 23:42:48', NULL),
(9, 1, 'COT-2025-0009', 'Test', 'asdasd\r\nasdasd\r\nasdasd', 'residencial', '120.00', 'Linares', 'Empresario Hugo Gonzalez Isami 2080', '2025-09-29', '2025-10-29', 30, 'borrador', 'baja', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'test', NULL, NULL, 17, '2025-09-29 23:44:42', '2025-09-30 00:08:00', NULL),
(10, 6, 'COT-2025-0010', 'Ejemplo de cotizacion', '', '', '60.00', 'Linares', 'Valles de Linares', '2025-10-01', '2025-10-31', 30, 'borrador', 'baja', '840000.00', '0.00', '0.00', '0.00', '0.00', '960000.00', '0.00', '0.00', '960000.00', 'contado', 0, '0.00', '0.00', NULL, NULL, NULL, '', '', 12, 'ejemplo-de-cotizacion', NULL, NULL, 17, '2025-10-01 06:41:35', '2025-10-01 06:41:35', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_archivos`
--

DROP TABLE IF EXISTS `cotizacion_archivos`;
CREATE TABLE IF NOT EXISTS `cotizacion_archivos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cotizacion_id` int NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `tipo_archivo` varchar(100) DEFAULT NULL,
  `tamaño_bytes` bigint DEFAULT NULL,
  `descripcion` text,
  `es_principal` tinyint(1) DEFAULT '0',
  `orden` int DEFAULT '0',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cotizacion` (`cotizacion_id`),
  KEY `idx_tipo` (`tipo_archivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_items`
--

DROP TABLE IF EXISTS `cotizacion_items`;
CREATE TABLE IF NOT EXISTS `cotizacion_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cotizacion_id` int NOT NULL,
  `categoria` enum('material','mano_obra','servicio','equipo','transporte','otros') NOT NULL,
  `subcategoria` varchar(100) DEFAULT NULL,
  `codigo_item` varchar(50) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `especificaciones` text,
  `cantidad` double NOT NULL DEFAULT '1',
  `unidad` varchar(50) NOT NULL DEFAULT 'unidad',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento_porcentaje` decimal(5,2) DEFAULT '0.00',
  `descuento_monto` decimal(10,2) DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `orden` int DEFAULT '0',
  `es_opcional` tinyint(1) DEFAULT '0',
  `observaciones` text,
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_cotizacion` (`cotizacion_id`),
  KEY `idx_categoria` (`categoria`),
  KEY `idx_orden` (`orden`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cotizacion_items`
--

INSERT INTO `cotizacion_items` (`id`, `cotizacion_id`, `categoria`, `subcategoria`, `codigo_item`, `descripcion`, `especificaciones`, `cantidad`, `unidad`, `precio_unitario`, `descuento_porcentaje`, `descuento_monto`, `subtotal`, `orden`, `es_opcional`, `observaciones`, `fcreacion`) VALUES
(130, 8, 'material', '', NULL, 'Radier', NULL, 55, 'm2', '12000.00', '0.00', '0.00', '660000.00', 1, 0, NULL, '2025-09-29 20:42:48'),
(131, 8, 'material', '', NULL, 'Cobertizo', NULL, 60, 'm2', '40000.00', '0.00', '0.00', '2400000.00', 2, 0, NULL, '2025-09-29 20:42:48'),
(160, 7, 'mano_obra', '', NULL, 'Radier', NULL, 54.56, 'm2', '12000.00', '0.00', '0.00', '654720.00', 1, 0, NULL, '2025-09-29 20:52:12'),
(161, 7, 'mano_obra', '', NULL, 'Cobertizo', NULL, 54.56, 'm2', '40000.00', '0.00', '0.00', '2182400.00', 2, 0, NULL, '2025-09-29 20:52:12'),
(162, 7, 'servicio', '', NULL, 'Mover calenfo hacia el exterior', NULL, 1, 'servicio', '200000.00', '0.00', '0.00', '200000.00', 3, 0, NULL, '2025-09-29 20:52:12'),
(163, 7, 'servicio', '', NULL, 'Aguas para logía', NULL, 1, 'servicio', '150000.00', '0.00', '0.00', '150000.00', 4, 0, NULL, '2025-09-29 20:52:12'),
(164, 7, 'servicio', '', NULL, 'Sacar lavadero actual patio', NULL, 1, 'servicio', '40000.00', '0.00', '0.00', '40000.00', 5, 0, NULL, '2025-09-29 20:52:12'),
(165, 7, 'servicio', '', NULL, 'Correr Pilar portón', NULL, 1, 'servicio', '50000.00', '0.00', '0.00', '50000.00', 6, 0, NULL, '2025-09-29 20:52:12'),
(166, 7, 'servicio', '', NULL, 'Instalación tubería sanitario', NULL, 1, 'servicio', '200000.00', '0.00', '0.00', '200000.00', 7, 0, NULL, '2025-09-29 20:52:12'),
(167, 7, 'servicio', '', NULL, 'Eliminacion llave lavadora interior', NULL, 1, 'servicio', '50000.00', '0.00', '0.00', '50000.00', 8, 0, NULL, '2025-09-29 20:52:12'),
(168, 7, 'servicio', '', NULL, 'Quincho', NULL, 1, 'servicio', '1500000.00', '0.00', '0.00', '1500000.00', 9, 0, NULL, '2025-09-29 20:52:12'),
(169, 9, 'material', '', NULL, 'Test', NULL, 1, 'unidad', '0.00', '0.00', '0.00', '0.00', 1, 0, NULL, '2025-09-29 21:08:00'),
(170, 10, 'material', '', NULL, 'Radier', NULL, 60, 'm2', '14000.00', '0.00', '0.00', '840000.00', 1, 0, NULL, '2025-10-01 03:41:35'),
(171, 10, 'transporte', '', NULL, 'Bencina', NULL, 2, 'unidad', '60000.00', '0.00', '0.00', '120000.00', 2, 0, NULL, '2025-10-01 03:41:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cotizacion_seguimiento`
--

DROP TABLE IF EXISTS `cotizacion_seguimiento`;
CREATE TABLE IF NOT EXISTS `cotizacion_seguimiento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cotizacion_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `accion` enum('creada','enviada','revisada','aprobada','rechazada','modificada','recordatorio') NOT NULL,
  `descripcion` text,
  `fecha_accion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `proxima_accion` text,
  `fecha_proxima_accion` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cotizacion` (`cotizacion_id`),
  KEY `idx_fecha` (`fecha_accion`)
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
  `nombre` varchar(255) NOT NULL,
  `nombre_comercial` varchar(255) DEFAULT NULL,
  `rut` varchar(20) DEFAULT NULL,
  `direccion` text,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `sitio_web` varchar(255) DEFAULT NULL,
  `logo_path` varchar(500) DEFAULT NULL,
  `descripcion` text,
  `mision` text,
  `vision` text,
  `valores` text,
  `estado` enum('A','I') DEFAULT 'A',
  `fcreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fmodificacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id`, `nombre`, `nombre_comercial`, `rut`, `direccion`, `telefono`, `email`, `sitio_web`, `logo_path`, `descripcion`, `mision`, `vision`, `valores`, `estado`, `fcreacion`, `fmodificacion`) VALUES
(1, 'MANSANCHEZ', 'MANSANCHEZ Construcciones', '', 'Región del Maule, Linares, Chile', '+56 9 3499 3516', 'rvillar1995@gmail.com', 'https://www.mansanchez.cl', 'lib/images/logo-min.jpg', 'Empresa especializada en construcciones y remodelaciones residenciales y comerciales.', 'Proporcionar servicios de construcción de alta calidad, cumpliendo con los más altos estándares de seguridad y excelencia.', 'Ser la empresa líder en construcciones y remodelaciones, reconocida por nuestra calidad, innovación y compromiso con el cliente.', 'Calidad, Honestidad, Compromiso, Innovación, Trabajo en equipo', 'A', '2025-09-29 03:23:28', '2025-10-09 05:37:42');

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
  `categoria_id` int DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `portada` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` char(1) NOT NULL DEFAULT 'A',
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_galeria_categoria` (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `galeria`
--

INSERT INTO `galeria` (`id`, `nombre`, `categoria_id`, `descripcion`, `portada`, `fecha`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(7, 'Test Galeria', 10, 'Es un quincho profesional', 'lib/img/27092025/1758944603_d0dd8c987cbe82476f52.jpg', NULL, 'A', '2025-09-27 03:43:23', '2025-09-28 00:28:45', NULL),
(9, 'Quincho', 10, 'Quincho', 'lib/img/01102025/1759286786_48db861a5fa2d0a9f62b.jpg', NULL, 'A', '2025-10-01 02:46:26', '2025-10-01 02:46:26', NULL),
(10, 'Ampliación', 5, 'Ampliación', 'lib/img/01102025/1759286803_7ddd27d40b4d41c6e422.jpg', NULL, 'A', '2025-10-01 02:46:43', '2025-10-01 02:46:43', NULL),
(11, 'Radier', 8, 'Radier allanado', 'lib/img/01102025/1759286868_038c0a1cef8797e5b384.jpg', NULL, 'A', '2025-10-01 02:47:48', '2025-10-01 02:47:48', NULL),
(12, 'Porton', 8, 'Porton de dos hojas', 'lib/img/01102025/1759286890_cbbb95834beae7950e2a.jpg', NULL, 'A', '2025-10-01 02:48:10', '2025-10-01 02:48:10', NULL),
(13, 'Quincho', 10, 'Quincho', 'lib/img/01102025/1759286909_6f056fa9df62d0cd0b97.jpg', NULL, 'A', '2025-10-01 02:48:29', '2025-10-01 02:48:29', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeria_categoria`
--

DROP TABLE IF EXISTS `galeria_categoria`;
CREATE TABLE IF NOT EXISTS `galeria_categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `meta_titulo` varchar(255) DEFAULT NULL,
  `meta_descripcion` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `orden` int DEFAULT '0',
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `galeria_categoria`
--

INSERT INTO `galeria_categoria` (`id`, `nombre`, `slug`, `meta_titulo`, `meta_descripcion`, `meta_keywords`, `descripcion`, `icono`, `color`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(2, 'Casas Residenciales', 'casas-residenciales', 'Casas Residenciales - NextLine Constructor', 'Construcción de casas y viviendas familiares Galería de obras de construcción en Linares, Maule, Chile.', 'casas residenciales, construcción, constructor, Linares, Maule, Chile, galería, obras, proyectos, hogar, familia, residencial', 'Construcción de casas y viviendas familiares', 'fas fa-house-user', '#3498db', 'A', 2, '2025-09-27 00:14:47', '2025-09-29 22:54:47', NULL),
(3, 'Edificios Comerciales', 'edificios-comerciales', 'Edificios Comerciales - NextLine Constructor', 'Construcción de edificios de oficinas y locales Galería de obras de construcción en Linares, Maule, Chile.', 'edificios comerciales, construcción, constructor, Linares, Maule, Chile, galería, obras, proyectos, comercial, oficinas, locales', 'Construcción de edificios de oficinas y locales', 'fas fa-building', '#f39c12', 'A', 3, '2025-09-27 00:14:47', '2025-09-29 22:55:02', NULL),
(4, 'Remodelaciones', 'remodelaciones-1', 'Remodelaciones - NextLine Constructor', 'Transformación y renovación de espacios existentes Galería de obras de construcción en Linares, Maule, Chile.', 'remodelaciones, construcción, constructor, Linares, Maule, Chile, galería, obras, proyectos, renovación, mejoras, reforma', 'Transformación y renovación de espacios existentes', 'fas fa-hammer', '#27ae60', 'A', 4, '2025-09-27 00:14:47', '2025-09-29 22:56:38', NULL),
(5, 'Ampliaciones', 'ampliaciones', 'Ampliaciones - NextLine Constructor', 'Extensión de construcciones existentes Galería de obras de construcción en Linares, Maule, Chile.', 'ampliaciones, construcción, constructor, Linares, Maule, Chile, galería, obras, proyectos, extensión, agregar, crecer', 'Extensión de construcciones existentes', 'fas fa-expand-arrows-alt', '#9b59b6', 'A', 5, '2025-09-27 00:14:47', '2025-09-29 22:54:32', NULL),
(6, 'Piscinas', 'piscinas', 'Piscinas - NextLine Constructor', 'Construcción de piscinas y áreas acuáticas Galería de obras de construcción en Linares, Maule, Chile.', 'piscinas, construcción, constructor, Linares, Maule, Chile, galería, obras, proyectos', 'Construcción de piscinas y áreas acuáticas', 'fas fa-swimming-pool', '#1abc9c', 'A', 6, '2025-09-27 00:14:47', '2025-09-29 22:56:00', NULL),
(7, 'Jardines', 'jardines', 'Jardines - NextLine Constructor', 'Diseño y construcción de jardines y paisajismo Galería de obras de construcción en Linares, Maule, Chile.', 'jardines, construcción, constructor, Linares, Maule, Chile, galería, obras, proyectos', 'Diseño y construcción de jardines y paisajismo', 'fas fa-tree', '#2ecc71', 'A', 7, '2025-09-27 00:14:47', '2025-09-29 22:55:18', NULL),
(8, 'Obras Menores', 'obras-menores', 'Obras Menores - NextLine Constructor', 'Trabajos de menor envergadura y mantenimiento Galería de obras de construcción en Linares, Maule, Chile.', 'obras menores, construcción, constructor, Linares, Maule, Chile, galería, obras, proyectos', 'Trabajos de menor envergadura y mantenimiento', 'fas fa-stamp', '#95a5a6', 'A', 8, '2025-09-27 00:14:47', '2025-09-29 22:55:39', NULL),
(10, 'Quinchos', 'quinchos-1', 'Quinchos - NextLine Constructor', 'Galería de quinchos y áreas de recreación construidos Galería de obras de construcción en Linares, Maule, Chile.', 'quinchos, construcción, constructor, Linares, Maule, Chile, galería, obras, proyectos, parrilla, jardín, recreación', 'Galería de quinchos y áreas de recreación construidos', 'fas fa-fire', '#e71313', 'A', 1, '2025-09-27 06:07:01', '2025-09-29 22:56:25', NULL);

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
-- Estructura de tabla para la tabla `imagenes`
--

DROP TABLE IF EXISTS `imagenes`;
CREATE TABLE IF NOT EXISTS `imagenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta` varchar(500) NOT NULL,
  `tipo` enum('galeria','proyecto','servicio') NOT NULL,
  `entidad_id` int NOT NULL,
  `es_portada` tinyint(1) DEFAULT '0',
  `orden` int DEFAULT '0',
  `descripcion` varchar(500) DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tipo_entidad` (`tipo`,`entidad_id`),
  KEY `idx_portada` (`es_portada`),
  KEY `idx_orden` (`orden`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `imagenes`
--

INSERT INTO `imagenes` (`id`, `nombre_archivo`, `ruta`, `tipo`, `entidad_id`, `es_portada`, `orden`, `descripcion`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, '1758951181_a6c080c4a69c09d9aec9.jpg', 'writable/uploads/proyectos/1758951181_a6c080c4a69c09d9aec9.jpg', 'proyecto', 4, 0, 1, '', 'A', '2025-09-27 05:33:01', '2025-09-30 20:52:01', '2025-09-27 05:36:32'),
(2, '1758951181_9dfb66a785b3be08e058.jpg', 'writable/uploads/proyectos/1758951181_9dfb66a785b3be08e058.jpg', 'proyecto', 4, 0, 2, '', 'A', '2025-09-27 05:33:01', '2025-09-30 20:52:01', '2025-09-27 05:36:35'),
(3, '1758951181_41a64ae9fc37de67c395.jpg', 'writable/uploads/proyectos/1758951181_41a64ae9fc37de67c395.jpg', 'proyecto', 4, 0, 3, '', 'A', '2025-09-27 05:33:01', '2025-09-30 20:52:01', '2025-09-27 05:36:37'),
(4, '1758951181_7d9e7330352a7e10b18f.jpg', 'writable/uploads/proyectos/1758951181_7d9e7330352a7e10b18f.jpg', 'proyecto', 4, 0, 4, '', 'A', '2025-09-27 05:33:01', '2025-09-30 20:52:01', '2025-09-27 05:36:39'),
(5, '1758951406_01b3c386b5b7db118506.jpg', 'uploads/proyectos/1758951406_01b3c386b5b7db118506.jpg', 'proyecto', 4, 1, 1, '', 'A', '2025-09-27 05:36:46', '2025-09-30 20:52:01', NULL),
(6, '1758951406_c86d34fa6f4650b89e31.jpg', 'uploads/proyectos/1758951406_c86d34fa6f4650b89e31.jpg', 'proyecto', 4, 0, 2, '', 'A', '2025-09-27 05:36:46', '2025-09-30 20:52:01', NULL),
(7, '1758951406_684b3fb7e0a6020076ca.jpg', 'uploads/proyectos/1758951406_684b3fb7e0a6020076ca.jpg', 'proyecto', 4, 0, 3, '', 'A', '2025-09-27 05:36:46', '2025-09-30 20:52:01', NULL),
(8, '1758951406_ff92a20114128cc9e7a5.jpg', 'uploads/proyectos/1758951406_ff92a20114128cc9e7a5.jpg', 'proyecto', 4, 0, 4, '', 'A', '2025-09-27 05:36:46', '2025-09-30 20:52:01', NULL),
(9, '1758951406_90e1c103b63e3714d318.jpg', 'uploads/proyectos/1758951406_90e1c103b63e3714d318.jpg', 'proyecto', 4, 0, 5, '', 'A', '2025-09-27 05:36:46', '2025-09-30 20:52:01', '2025-09-28 01:12:03'),
(10, '1759023506_be0b9fcaf411b45e51d7.jpg', 'uploads/proyectos/1759023506_be0b9fcaf411b45e51d7.jpg', 'proyecto', 4, 0, 5, '', 'A', '2025-09-28 01:38:26', '2025-09-30 20:52:01', NULL),
(11, '1759023708_d0cc7310acd0e82bdae7.jpg', 'uploads/proyectos/1759023708_d0cc7310acd0e82bdae7.jpg', 'proyecto', 10, 0, 1, '', 'A', '2025-09-28 01:41:48', '2025-09-28 01:41:57', NULL),
(12, '1759023708_a98f7a1faac1047f52bf.jpg', 'uploads/proyectos/1759023708_a98f7a1faac1047f52bf.jpg', 'proyecto', 10, 0, 2, '', 'A', '2025-09-28 01:41:48', '2025-09-28 01:41:57', NULL),
(13, '1759023708_7934bac43a5f1b0b29fc.jpg', 'uploads/proyectos/1759023708_7934bac43a5f1b0b29fc.jpg', 'proyecto', 10, 0, 3, '', 'A', '2025-09-28 01:41:48', '2025-09-28 01:41:57', NULL),
(14, '1759023708_de1c7b770d534940075c.jpg', 'uploads/proyectos/1759023708_de1c7b770d534940075c.jpg', 'proyecto', 10, 0, 4, '', 'A', '2025-09-28 01:41:48', '2025-09-28 01:41:57', NULL),
(15, '1759023708_afa4e853fb0876e1cde4.jpg', 'uploads/proyectos/1759023708_afa4e853fb0876e1cde4.jpg', 'proyecto', 10, 1, 5, '', 'A', '2025-09-28 01:41:48', '2025-09-28 01:41:57', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lead_contacto`
--

DROP TABLE IF EXISTS `lead_contacto`;
CREATE TABLE IF NOT EXISTS `lead_contacto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mensaje` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `servicio_id` int DEFAULT NULL,
  `estado_id` int NOT NULL DEFAULT '1',
  `utm_source` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `utm_medium` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `utm_campaign` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fcreacion` datetime NOT NULL,
  `factualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servicio_id` (`servicio_id`),
  KEY `estado_id` (`estado_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lead_contacto`
--

INSERT INTO `lead_contacto` (`id`, `nombre`, `correo`, `telefono`, `mensaje`, `servicio_id`, `estado_id`, `utm_source`, `utm_medium`, `utm_campaign`, `fcreacion`, `factualizacion`) VALUES
(2, 'asdasd', 'rvillar1995@gmail.com', '+569 91621564', 'asdasd', 6, 3, NULL, NULL, NULL, '2025-09-27 15:35:41', '2025-10-09 03:17:55'),
(4, 'Rafael Villar Bahamondes', 'm.amasadsad@gmail.com', '+56991621564', '🎯 Envío Automático:\r\n✅ Email se envía automáticamente después de guardar el contacto\r\n✅ No afecta el flujo si falla (try-catch)\r\n✅ Logs detallados para monitoreo\r\n🎨 Diseño Moderno del Email:\r\n✅ Header con gradiente azul corporativo\r\n✅ Badge de alerta naranja destacado\r\n✅ Cards individuales para cada dato\r\n✅ Iconos coloridos con gradientes\r\n✅ Datos clickeables (email y teléfono)\r\n✅ Botón CTA para responder al cliente\r\n✅ Footer corporativo con copyright', 29, 1, NULL, NULL, NULL, '2025-10-09 02:38:23', '2025-10-09 02:38:23'),
(5, 'Rafael Mauricio Villar Bahamondes', 'm.amasadsad@gmail.com', '+56991621564', 'Esta seccion no me termina de converser se ve mal, puedes hacerlo de 0 siguiente los que ya tenemos?\r\n\r\n¡Por supuesto! Voy a crear una versión completamente nueva y moderna de esa sección \"Información de la Imagen\" desde cero, siguiendo el estilo que ya tenemos establecido.\r\nAhora voy a agregar el CSS completamente nuevo para esta sección:\r\nAhora voy a agregar los estilos responsive para esta nueva sección:\r\nAhora voy a verificar que no haya errores de linting:\r\n🎨 Sección \"Información de la Imagen\" Completamente Rediseñada', 18, 1, NULL, NULL, NULL, '2025-10-09 03:03:10', '2025-10-09 03:03:10'),
(6, 'Rafael Mauricio Villar Bahamondes', 'rvillar1995@gmail.com', '991621564', 'Testtt', 25, 1, NULL, NULL, NULL, '2025-10-09 03:06:06', '2025-10-09 03:06:06'),
(7, 'Rafael Villar Bahamondes', 'rvillar1995@gmail.com', '991621564', 'Tesst', 24, 1, NULL, NULL, NULL, '2025-10-09 03:11:10', '2025-10-09 03:11:10'),
(8, 'rafael villar', 'rvillar1995@gmail.com', '+56991621564', 'asdasdasd', 25, 1, NULL, NULL, NULL, '2025-10-09 03:17:43', '2025-10-09 03:17:43'),
(9, 'rafael villar', 'rvillar1995@gmail.com', '991621564', 'qqqqqq', 25, 1, NULL, NULL, NULL, '2025-10-09 03:19:40', '2025-10-09 03:19:40'),
(10, 'aaaa', 'bbbb@gmail.com', '991621564', 'asdasd', 27, 1, NULL, NULL, NULL, '2025-10-09 03:23:31', '2025-10-09 03:23:31'),
(11, 'aaaaa', 'asdasd@gmail.com', '991621564', 'asdaqweqwdasdsd fsdfsdfasdasd asd', 22, 1, NULL, NULL, NULL, '2025-10-09 03:24:54', '2025-10-09 03:24:54'),
(12, 'Rafael Villar Bahamondes', 'rvillar1995@gmail.com', '991621564', 'Tes de mensaje', 24, 1, NULL, NULL, NULL, '2025-10-09 03:27:29', '2025-10-09 03:27:29'),
(13, 'Rafael Villar Bahamondes', 'rvillar1995@gmail.com', '991621564', 'Quisiera hacer un quincho implementando la ultima tecnologia', 24, 1, NULL, NULL, NULL, '2025-10-09 03:30:31', '2025-10-09 03:30:31'),
(14, 'adfasdas', 'b@c.cl', '991621564', 'adas asdasd asdasdas asdasdasd', 25, 1, NULL, NULL, NULL, '2025-10-09 03:32:51', '2025-10-09 03:32:51'),
(15, 'Rafael Villar', 'rvillar1995@gmail.com', '991621564', 'Test final porfa envialo', 25, 1, NULL, NULL, NULL, '2025-10-09 03:34:26', '2025-10-09 03:34:26'),
(16, 'asdasdas', 'asdasd@gmail.com', '991621564', 'asdasdqwasdas', 24, 1, NULL, NULL, NULL, '2025-10-09 03:37:23', '2025-10-09 03:37:23'),
(18, 'asdasdasd', 'asdasd@gmail.com', '991621564', 'asdasd asdasd', 25, 3, NULL, NULL, NULL, '2025-10-09 03:42:46', '2025-10-09 03:54:50'),
(19, 'ASDASD', 'asdasd@gmail.com', '991621564', 'Este es un ejemplo de mensaje enviado', 21, 4, NULL, NULL, NULL, '2025-10-09 00:58:51', '2025-10-09 00:59:22'),
(20, 'Rafael Villar Bahamondes', 'rvillar1995@gmail.com', '+56991621564', 'Quisiera ver que tal es la web y el modulo de contacto', 28, 1, NULL, NULL, NULL, '2025-10-09 01:39:31', '2025-10-09 01:39:31'),
(21, 'Rafael ', 'rvillar1995@gmail.com', '991621564', 'ASDASDasdasdasd', 24, 1, NULL, NULL, NULL, '2025-10-09 01:48:26', '2025-10-09 01:48:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lead_estado`
--

DROP TABLE IF EXISTS `lead_estado`;
CREATE TABLE IF NOT EXISTS `lead_estado` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lead_estado`
--

INSERT INTO `lead_estado` (`id`, `nombre`) VALUES
(1, 'Nuevo'),
(2, 'En gestión'),
(3, 'Contactado'),
(4, 'Cerrado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-08-17-000001', 'App\\Database\\Migrations\\ConvertMyISAMToInnoDB', 'default', 'App', 1755406406, 1),
(2, '2025-08-17-000002', 'App\\Database\\Migrations\\AddSecurityColumnsToUsuario', 'default', 'App', 1755406406, 1),
(3, '2025-08-20-000001', 'App\\Database\\Migrations\\CreateLeads', 'default', 'App', 1758955576, 2),
(4, '2025-08-20-000002', 'App\\Database\\Migrations\\CreateCmsBasics', 'default', 'App', 1758955576, 2),
(5, '2025-08-20-000003', 'App\\Database\\Migrations\\CreateProfesional', 'default', 'App', 1758955577, 2);

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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(14, 'Galeria', 'Modulo web para gestionar galerias.', '/dashboard/galeria', 'A', 'S', 'N', '2024-10-12 18:26:18', '2024-10-12 18:26:18', '0000-00-00 00:00:00'),
(16, 'Categorías de Galería', 'Gestión de categorías para proyectos de galería', '/dashboard/galeria-categoria', 'A', 'S', 'N', '0000-00-00 00:00:00', '2025-09-28 23:14:39', '0000-00-00 00:00:00'),
(17, 'Categorías de Servicios', 'Gestión de categorías para servicios', '/dashboard/servicio-categoria', 'A', 'S', 'N', '0000-00-00 00:00:00', '2025-09-28 23:14:34', '0000-00-00 00:00:00'),
(18, 'Proyectos', 'Gestión completa de proyectos de construcción', '/dashboard/proyecto', 'A', 'S', 'N', '0000-00-00 00:00:00', '2025-09-28 23:14:18', '0000-00-00 00:00:00'),
(19, 'Contactos', 'Consulta/Contacto web', '/dashboard/leads', 'A', 'S', 'N', '2025-09-27 06:46:20', '2025-09-27 18:30:44', '0000-00-00 00:00:00'),
(26, 'Testimonios', 'Gestión de testimonios de clientes', '/dashboard/testimonio', 'A', 'S', 'N', '0000-00-00 00:00:00', '2025-09-28 23:16:03', '0000-00-00 00:00:00'),
(28, 'Clientes', 'Gestión de clientes particulares, empresas y organizaciones', '/dashboard/cliente', 'A', 'S', 'N', '0000-00-00 00:00:00', '2025-09-28 23:14:27', '0000-00-00 00:00:00'),
(29, 'Cotizaciones', 'Gestión de cotizaciones y presupuestos', '/dashboard/cotizacion', 'A', 'S', 'N', '0000-00-00 00:00:00', '2025-09-28 23:14:45', '0000-00-00 00:00:00'),
(30, 'Ubicacion', 'Modulo para Regiones y Comunas', '/dashboard/ubicacion', 'A', 'N', 'N', '2025-09-28 23:13:54', '2025-09-28 23:36:39', '0000-00-00 00:00:00'),
(31, 'Empresa', 'Gestión de datos de la empresa para encabezados, logos y datos corporativos', 'dashboard/empresa', 'A', 'S', 'N', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

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
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(48, 8, 'Gestionar Servicios', '/lista', 'ver', 'A', 'S', 2, '2024-10-12 04:44:51', '2025-09-30 04:54:51', '0000-00-00 00:00:00'),
(49, 8, 'Registrar servicio', '/registro', 'ver', 'A', 'N', 1, '2024-10-12 04:46:50', '2025-09-28 00:40:26', '0000-00-00 00:00:00'),
(50, 8, 'Acción Obtener servicios para datatable', '/getServicio', 'ver', 'A', 'N', 0, '2024-10-12 04:47:24', '2024-10-12 06:08:05', '0000-00-00 00:00:00'),
(52, 8, 'Acción Editar servicio', '/update', 'editar', 'A', 'N', 0, '2024-10-12 04:49:56', '2024-10-12 04:49:56', '0000-00-00 00:00:00'),
(53, 8, 'Editar servicio', '/editar', 'ver', 'A', 'N', 0, '2024-10-12 06:12:18', '2024-10-12 06:12:18', '0000-00-00 00:00:00'),
(54, 14, 'Acción Registrar Galeria', '/registrar', 'registrar', 'A', 'N', 0, '2024-10-12 18:27:06', '2024-10-12 18:27:06', '0000-00-00 00:00:00'),
(55, 14, 'Acción eliminar galeria', '/eliminar', 'eliminar', 'A', 'N', 0, '2024-10-12 18:27:29', '2024-10-12 18:29:30', '0000-00-00 00:00:00'),
(56, 14, 'Acción Obtener galeria para datatable', '/getGaleria', 'ver', 'A', 'N', 0, '2024-10-12 18:28:03', '2024-10-12 18:28:03', '0000-00-00 00:00:00'),
(57, 14, 'Acción editar galería', '/update', 'editar', 'A', 'N', 0, '2024-10-12 18:28:31', '2024-10-12 18:28:31', '0000-00-00 00:00:00'),
(58, 14, 'Editar galeria', '/editar', 'ver', 'A', 'N', 0, '2024-10-12 18:28:52', '2024-10-12 18:29:46', '0000-00-00 00:00:00'),
(59, 14, 'Registrar Galeria', '/registro', 'ver', 'A', 'N', 1, '2024-10-12 18:30:28', '2025-09-28 00:39:06', '0000-00-00 00:00:00'),
(60, 14, 'Gestionar Galeria', '/lista ', 'ver', 'A', 'S', 2, '2024-10-12 18:30:47', '2025-09-28 00:39:54', '0000-00-00 00:00:00'),
(63, 16, 'Acción Registrar Categoría', ' /registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(64, 16, 'Acción eliminar categoría', '/eliminar', 'eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, 16, 'Acción Obtener categorías para datatable', '/getGaleriaCategoria', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 16, 'Acción Editar categoría', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, 16, 'Acción Editar categoría', '/editar', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(68, 16, 'Registrar categoría', '/registro', 'ver', 'A', 'N', 1, '0000-00-00 00:00:00', '2025-09-28 00:07:36', '0000-00-00 00:00:00'),
(69, 16, 'Gestionar Categorías', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '2025-09-28 00:08:06', '0000-00-00 00:00:00'),
(70, 17, 'Acción Registrar Categoría', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, 17, 'Acción eliminar categoría', '/eliminar', 'eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, 17, 'Acción Obtener categorías para datatable', '/getServicioCategoria', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(73, 17, 'Acción Editar categoría', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(74, 17, 'Acción Editar categoría', '/editar', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(75, 17, 'Registrar categoría', '/registro', 'ver', 'A', 'N', 1, '0000-00-00 00:00:00', '2025-09-28 00:08:58', '0000-00-00 00:00:00'),
(76, 17, 'Gestionar Categorías', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '2025-09-28 00:08:36', '0000-00-00 00:00:00'),
(77, 18, 'Acción Registrar Proyecto', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(78, 18, 'Acción eliminar proyecto', '/eliminar', 'eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(79, 18, 'Acción Obtener proyectos para datatable', '/getProyecto', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(80, 18, 'Acción Editar proyecto', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, 18, 'Acción Editar proyecto', '/editar', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, 18, 'Acción Establecer imagen portada', '/setPortada', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(83, 18, 'Acción Eliminar imagen', '/eliminarImagen', 'eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(84, 18, 'Registrar proyecto', '/registro', 'ver', 'A', 'N', 1, '0000-00-00 00:00:00', '2025-09-28 01:39:29', '0000-00-00 00:00:00'),
(85, 18, 'Gestionar Proyectos', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '2025-09-28 01:39:34', '0000-00-00 00:00:00'),
(87, 19, 'Listar contactos', '/lista', 'ver', 'A', 'S', 2, '2025-09-27 06:46:20', '2025-09-27 18:32:50', '0000-00-00 00:00:00'),
(88, 19, 'Editar leads', '/editar', 'ver', 'A', 'N', 3, '2025-09-27 06:46:20', '2025-09-27 06:46:20', '0000-00-00 00:00:00'),
(90, 19, 'Accion eliminar leads', '/eliminar', 'eliminar', 'A', 'N', 0, '2025-09-27 06:46:20', '2025-09-27 06:46:20', '0000-00-00 00:00:00'),
(91, 19, 'Acción Obtener leads para datatable', '/getLeads', 'ver', 'A', 'N', 0, '2025-09-27 06:46:20', '2025-09-27 07:33:21', '0000-00-00 00:00:00'),
(92, 19, 'Acción Editar leads', '/update', 'editar', 'A', 'N', 0, '2025-09-27 06:46:20', '2025-09-27 06:46:20', '0000-00-00 00:00:00'),
(123, 19, 'Accion de Cambiar Estado', '/cambiarEstado', 'editar', 'A', 'N', 0, '2025-09-27 07:41:21', '2025-09-27 07:44:26', '0000-00-00 00:00:00'),
(124, 19, 'Obtener los servicios de los Leads', '/getServicios', 'ver', 'A', 'N', 0, '2025-09-27 07:49:41', '2025-09-27 07:53:42', '0000-00-00 00:00:00'),
(132, 26, 'Acción Registrar Testimonio', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(133, 26, 'Acción Eliminar Testimonio', '/eliminar', 'eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(134, 26, 'Acción Obtener Testimonios', '/getTestimonios', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(135, 26, 'Acción Actualizar Testimonio', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(136, 26, 'Editar Testimonio', '/editar', 'ver', 'A', 'N', 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(137, 26, 'Registro de Testimonio', '/registro', 'ver', 'A', 'N', 1, '0000-00-00 00:00:00', '2025-09-28 22:52:12', '0000-00-00 00:00:00'),
(138, 26, 'Gestionar Testimonios', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '2025-09-28 22:52:24', '0000-00-00 00:00:00'),
(146, 28, 'Acción Registrar Cliente', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(147, 28, 'Acción Eliminar Cliente', '/eliminar', 'eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(148, 28, 'Acción Obtener Clientes', '/getClientes', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(149, 28, 'Acción Actualizar Cliente', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(150, 28, 'Acción Obtener Clientes Select', '/getClientesSelect', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(151, 28, 'Editar Cliente', '/editar', 'ver', 'A', 'N', 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(152, 28, 'Detalle Cliente', '/detalle', 'ver', 'A', 'N', 4, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(153, 28, 'Registro de Cliente', '/registro', 'ver', 'A', 'N', 1, '0000-00-00 00:00:00', '2025-09-29 00:17:14', '0000-00-00 00:00:00'),
(154, 28, 'Gestionar Clientes', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '2025-09-29 00:17:10', '0000-00-00 00:00:00'),
(155, 29, 'Acción Registrar Cotización', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(156, 29, 'Acción Eliminar Cotización', '/eliminar', 'eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(157, 29, 'Acción Obtener Cotizaciones', '/getCotizaciones', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(158, 29, 'Acción Actualizar Cotización', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(159, 29, 'Acción Obtener Clientes Select', '/getClientesSelect', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(160, 29, 'Acción Generar PDF', '/generarPDF', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(161, 29, 'Editar Cotización', '/editar', 'ver', 'A', 'N', 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(162, 29, 'Detalle Cotización', '/detalle', 'ver', 'A', 'N', 4, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(163, 29, 'Registro de Cotización', '/registro', 'ver', 'A', 'N', 1, '0000-00-00 00:00:00', '2025-09-29 03:20:39', '0000-00-00 00:00:00'),
(164, 29, 'Gestionar Cotizaciones', '/lista', 'ver', 'A', 'S', 2, '0000-00-00 00:00:00', '2025-09-29 03:20:46', '0000-00-00 00:00:00'),
(165, 30, 'Accion de obtener Regiones', '/regiones', 'ver', 'A', 'N', 0, '2025-09-28 23:15:45', '2025-09-28 23:15:45', '0000-00-00 00:00:00'),
(167, 30, 'Acciones de Obtener Comunas', '/comunas', 'ver', 'A', 'N', 1, '2025-09-28 23:30:58', '2025-09-28 23:30:58', '0000-00-00 00:00:00'),
(168, 28, 'Accion de activar cliente', '/activar', 'editar', 'A', 'N', 1, '2025-09-29 00:03:01', '2025-09-29 00:03:01', '0000-00-00 00:00:00'),
(169, 31, 'Acción Registrar Empresa', '/registrar', 'registrar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(170, 31, 'Acción Eliminar Empresa', '/eliminar', 'eliminar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(171, 31, 'Acción Obtener Empresas para datatable', '/getEmpresa', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(172, 31, 'Acción Actualizar Empresa', '/update', 'editar', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(173, 31, 'Acción Editar Empresa', '/editar', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(174, 31, 'Acción Detalle Empresa', '/detalle', 'ver', 'A', 'N', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(175, 31, 'Gestionar Empresa', '/registro', 'ver', 'A', 'S', 1, '0000-00-00 00:00:00', '2025-09-29 19:25:35', '0000-00-00 00:00:00'),
(176, 31, 'Listar Empresas', '/lista', 'ver', 'A', 'N', 2, '0000-00-00 00:00:00', '2025-09-29 19:25:26', '0000-00-00 00:00:00'),
(177, 29, 'Generar proyecto a partir de cotizaciones', '/convertir-proyecto', 'editar', 'A', 'N', 1, '2025-09-29 21:00:20', '2025-09-29 21:00:20', '0000-00-00 00:00:00'),
(178, 19, 'Ver detalle de contacto', '/getDetalle', 'ver', 'A', 'N', 1, '2025-10-09 03:56:50', '2025-10-09 03:56:50', '0000-00-00 00:00:00'),
(179, 18, 'Obtener clientes para hacer filtro', '/getClientesSelect', 'ver', 'A', 'N', 0, '2025-10-11 20:42:45', '2025-10-11 20:42:45', '0000-00-00 00:00:00'),
(180, 6, 'Editar sobre la datatable', '/updateOrden', 'editar', 'A', 'N', 0, '2025-10-11 20:50:14', '2025-10-11 20:50:14', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagina`
--

DROP TABLE IF EXISTS `pagina`;
CREATE TABLE IF NOT EXISTS `pagina` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `titulo` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `contenido` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'A',
  `fcreacion` datetime NOT NULL,
  `factualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, 'Administrador', 2, 'I', '0000-00-00 00:00:00', '2025-10-11 20:52:40', '0000-00-00 00:00:00'),
(4, 'Trabajador Portero', 0, 'A', '2024-08-10 04:42:14', '2024-08-16 03:00:46', '2024-08-16 03:00:46'),
(7, 'Administrador Web', 0, 'A', '2024-09-24 23:03:21', '2024-09-24 23:03:21', '0000-00-00 00:00:00'),
(8, 'Administrador', 0, 'A', '2024-10-12 04:50:17', '2025-10-11 20:52:48', '0000-00-00 00:00:00');

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
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `perfil_modulo`
--

INSERT INTO `perfil_modulo` (`id`, `perfil_id`, `modulo_id`, `ver`, `registrar`, `editar`, `eliminar`, `analizar`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 1, 1, 1, 1, 1, 1, 0, 'A', 2, '0000-00-00 00:00:00', '2024-08-11 05:56:33', '0000-00-00 00:00:00'),
(2, 1, 2, 1, 1, 1, 1, 0, 'A', 1, '0000-00-00 00:00:00', '2024-08-16 01:26:24', '2024-08-16 01:26:24'),
(4, 1, 4, 1, 1, 1, 1, 0, 'A', 4, '0000-00-00 00:00:00', '2024-08-16 03:35:52', '0000-00-00 00:00:00'),
(5, 1, 6, 1, 1, 1, 1, 0, 'A', 5, '2024-08-10 07:13:19', '2024-08-10 07:13:19', '2024-08-10 07:13:19'),
(8, 1, 7, 1, 1, 1, 1, 0, 'A', 6, '2024-08-11 04:14:07', '2024-08-11 04:14:07', '0000-00-00 00:00:00'),
(16, 3, 8, 1, 1, 1, 0, 0, 'A', 4, '2024-08-16 01:33:08', '2024-08-16 03:59:23', '0000-00-00 00:00:00'),
(17, 3, 6, 1, 1, 0, 0, 0, 'A', 3, '2024-08-16 01:33:46', '2024-08-16 03:59:16', '0000-00-00 00:00:00'),
(18, 3, 2, 1, 0, 0, 0, 0, 'A', 1, '2024-08-16 01:39:26', '2024-08-16 03:59:58', '2024-08-16 03:06:10'),
(19, 1, 3, 1, 1, 1, 1, 0, 'A', 3, '2024-08-16 02:55:10', '2024-08-16 02:55:10', '0000-00-00 00:00:00'),
(20, 3, 1, 1, 1, 1, 1, 0, 'A', 2, '2024-08-16 03:58:57', '2024-09-24 23:07:02', '0000-00-00 00:00:00'),
(23, 3, 3, 1, 1, 1, 1, 0, 'A', 4, '2024-09-24 23:03:00', '2024-09-24 23:03:00', '0000-00-00 00:00:00'),
(24, 7, 2, 1, 1, 1, 1, 0, 'A', 1, '2024-09-24 23:05:11', '2024-09-24 23:05:11', '0000-00-00 00:00:00'),
(25, 7, 8, 1, 1, 1, 0, 0, 'A', 2, '2024-09-24 23:07:37', '2024-10-12 06:42:06', '0000-00-00 00:00:00'),
(26, 8, 2, 1, 1, 1, 1, 0, 'A', 1, '2024-10-12 04:50:50', '2024-10-12 04:50:50', '0000-00-00 00:00:00'),
(28, 8, 14, 1, 1, 1, 1, 0, 'A', 4, '2024-10-12 18:31:05', '2025-09-27 04:43:37', '0000-00-00 00:00:00'),
(30, 3, 16, 1, 1, 1, 0, 0, 'A', 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, 7, 16, 1, 1, 1, 1, 0, 'A', 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 8, 16, 1, 1, 1, 1, 0, 'A', 5, '0000-00-00 00:00:00', '2025-09-27 04:44:07', '0000-00-00 00:00:00'),
(34, 3, 17, 1, 1, 1, 0, 0, 'A', 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 7, 17, 1, 1, 1, 1, 0, 'A', 7, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 8, 17, 1, 1, 1, 1, 0, 'A', 3, '0000-00-00 00:00:00', '2025-09-27 04:43:54', '0000-00-00 00:00:00'),
(38, 3, 18, 1, 1, 1, 0, 0, 'A', 7, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 7, 18, 1, 1, 1, 1, 0, 'A', 8, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 7, 19, 1, 0, 0, 1, 0, 'A', 5, '2025-09-27 06:46:20', '2025-09-27 06:46:20', '0000-00-00 00:00:00'),
(43, 8, NULL, 1, 1, 1, 1, 0, 'A', 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 8, 18, 1, 1, 1, 1, 0, 'A', 7, '2025-09-27 07:21:35', '2025-09-27 07:21:35', '0000-00-00 00:00:00'),
(45, 8, 19, 1, 1, 1, 1, 0, 'A', 8, '2025-09-27 07:21:42', '2025-09-27 07:21:42', '0000-00-00 00:00:00'),
(48, 2, 26, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(49, 3, 26, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(50, 4, 26, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(51, 7, 26, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 8, 26, 1, 1, 1, 1, 0, 'A', 9, '2025-09-27 18:32:14', '2025-09-27 18:32:14', '0000-00-00 00:00:00'),
(64, 2, 28, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, 3, 28, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 4, 28, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, 7, 28, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, 2, 29, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, 3, 29, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(73, 4, 29, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(74, 7, 29, 1, 1, 1, 1, 0, 'A', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(77, 8, 28, 1, 1, 1, 1, 0, 'A', 10, '2025-09-27 22:45:52', '2025-09-27 22:45:52', '0000-00-00 00:00:00'),
(78, 8, 29, 1, 1, 1, 1, 0, 'A', 11, '2025-09-27 22:46:00', '2025-09-27 22:46:00', '0000-00-00 00:00:00'),
(81, 8, 8, 1, 1, 1, 1, 0, 'A', 2, '2025-09-27 22:57:09', '2025-09-27 22:57:09', '0000-00-00 00:00:00'),
(82, 8, 30, 1, 1, 1, 1, 0, 'A', 1, '2025-09-28 23:16:49', '2025-09-28 23:16:49', '0000-00-00 00:00:00'),
(83, 8, 31, 1, 1, 1, 1, 0, 'A', 1, '0000-00-00 00:00:00', '2025-09-29 19:26:12', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesional`
--

DROP TABLE IF EXISTS `profesional`;
CREATE TABLE IF NOT EXISTS `profesional` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cargo` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bio` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `foto` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'A',
  `fcreacion` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesional_especialidad`
--

DROP TABLE IF EXISTS `profesional_especialidad`;
CREATE TABLE IF NOT EXISTS `profesional_especialidad` (
  `profesional_id` int NOT NULL,
  `especialidad_id` int NOT NULL,
  PRIMARY KEY (`profesional_id`,`especialidad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `cliente` varchar(255) DEFAULT NULL,
  `tipo_proyecto` enum('residencial','comercial','industrial','institucional','otro') NOT NULL DEFAULT 'residencial',
  `ubicacion` varchar(255) DEFAULT NULL,
  `direccion` varchar(500) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `presupuesto` decimal(15,2) DEFAULT NULL,
  `mostrar_presupuesto` tinyint(1) DEFAULT '0',
  `estado` enum('en_progreso','completado','en_pausa','cancelado') NOT NULL DEFAULT 'en_progreso',
  `descripcion_corta` varchar(500) DEFAULT NULL,
  `descripcion_detallada` text,
  `caracteristicas_tecnicas` text,
  `area_construida` decimal(10,2) DEFAULT NULL,
  `materiales_principales` varchar(500) DEFAULT NULL,
  `testimonio_cliente` text,
  `nombre_cliente` varchar(255) DEFAULT NULL,
  `destacado` tinyint(1) DEFAULT '0',
  `meta_titulo` varchar(255) DEFAULT NULL,
  `meta_descripcion` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `estado_publico` char(1) DEFAULT 'A',
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_estado` (`estado`),
  KEY `idx_destacado` (`destacado`),
  KEY `idx_tipo` (`tipo_proyecto`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `slug`, `cliente`, `tipo_proyecto`, `ubicacion`, `direccion`, `fecha_inicio`, `fecha_finalizacion`, `presupuesto`, `mostrar_presupuesto`, `estado`, `descripcion_corta`, `descripcion_detallada`, `caracteristicas_tecnicas`, `area_construida`, `materiales_principales`, `testimonio_cliente`, `nombre_cliente`, `destacado`, `meta_titulo`, `meta_descripcion`, `meta_keywords`, `estado_publico`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 'Casa Familiar Los Robles', 'casa-familiar-los-robles', 'Familia González', 'residencial', 'Santiago', NULL, '2024-01-15', '2024-06-30', '85000000.00', 0, 'completado', NULL, 'Proyecto completo de construcción de casa familiar de 180m² con diseño contemporáneo. Incluye 3 dormitorios, 2 baños, living comedor integrado, cocina moderna y terraza. Construcción con materiales de primera calidad y acabados de lujo.', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'A', '2025-09-27 02:10:25', '2025-09-30 03:10:03', NULL),
(2, 'Edificio Comercial Centro', 'edificio-comercial-centro', 'Inmobiliaria Central', 'comercial', 'Santiago', NULL, '2024-03-01', '2024-12-15', '250000000.00', 0, 'en_progreso', NULL, 'Construcción de edificio comercial de 8 pisos con locales comerciales en los primeros 2 pisos y oficinas en los pisos superiores. Incluye estacionamientos subterráneos y sistemas modernos de climatización.', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'A', '2025-09-27 02:10:25', '2025-09-30 03:10:09', NULL),
(3, 'Ampliación Residencial', 'ampliacion-residencial', 'Familia Martínez', 'residencial', 'Providencia', 'Calle Providencia 890, Providencia', '2024-02-10', '2024-05-20', '35000000.00', 0, 'completado', 'Ampliación de casa existente con nuevo dormitorio y baño', 'Ampliación de casa existente agregando un dormitorio principal con baño en suite y walk-in closet. Manteniendo la armonía arquitectónica con el diseño original de la casa.', 'Ampliación de 1 piso, estructura de hormigón armado, techumbre a dos aguas, ventanas de aluminio', '45.00', 'Hormigón armado, Tejas cerámicas, Aluminio, Porcelanato', NULL, NULL, 0, NULL, NULL, NULL, 'A', '2025-09-27 02:10:25', '2025-09-27 05:37:19', '2025-09-27 05:37:19'),
(4, 'Ampliación Residencial con Patio Cubierto', 'ampliaci-n-residencial-con-patio-cubierto', 'Rafael Villar', 'residencial', 'Linares', NULL, '2025-04-21', '2025-06-30', '10000000.00', 0, 'completado', 'Ampliación residencial que incluye la construcción de un patio cubierto funcional con iluminación LED integrada, área de lavandería exterior y zona de almacenamiento.', 'Proyecto de ampliación que incluye la construcción de un patio cubierto adyacente a la vivienda principal. El espacio cuenta con iluminación LED integrada en el techo, área de lavandería exterior con lavadora y secadora, y zona de almacenamiento. La estructura combina elementos de madera en el techo con paredes de concreto, creando un ambiente funcional y estéticamente agradable. Se incluye conexión de servicios básicos como agua, gas y electricidad.', 'Estructura de concreto armado con techo de madera tratada. Iluminación LED integrada con sistema de control automático. Instalaciones eléctricas empotradas con protección diferencial. Sistema de drenaje perimetral con conexión a red municipal. Ventilación natural mediante ventanas corredizas de aluminio.', '45.50', 'Concreto armado, Madera tratada de pino radiata, Aluminio anodizado, Láminas de policarbonato, Iluminación LED, Pintura anticorrosiva, Ladrillos cerámicos, Mortero de cemento', 'Excelente trabajo realizado por el equipo de MANSANCHEZ. La ampliación quedó exactamente como la habíamos planeado. El patio cubierto es perfecto para nuestras necesidades familiares y la calidad de los materiales es superior. Recomiendo totalmente sus servicios.', 'Rafael Villar', 1, NULL, NULL, NULL, 'A', '2025-09-27 05:33:01', '2025-09-30 00:30:21', NULL),
(5, 'Casa Familiar Las Condes', 'casa-familiar-las-condes', 'Familia González', 'residencial', 'Las Condes, Santiago', NULL, '2024-01-15', '2024-08-30', '45000000.00', 1, 'completado', NULL, 'Proyecto de construcción de casa familiar de 180m² en Las Condes. Incluye 3 dormitorios, 2 baños, living comedor integrado, cocina moderna, jardín con piscina y quincho. Construcción con materiales de primera calidad y terminaciones de lujo.', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'A', '2025-09-27 02:44:55', '2025-09-30 03:09:44', NULL),
(6, 'Edificio Comercial Providencia', 'edificio-comercial-providencia', 'Inmobiliaria Central', 'comercial', 'Providencia, Santiago', NULL, '2023-06-01', '2024-03-15', '120000000.00', 1, 'completado', NULL, 'Construcción de edificio comercial de 4 pisos con 8 locales comerciales en planta baja y 12 oficinas en pisos superiores. Incluye estacionamientos subterráneos y sistema de climatización central.', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'A', '2025-09-27 02:44:55', '2025-09-30 03:09:57', NULL),
(7, 'Ampliación Casa Ñuñoa', 'ampliacion-casa-nunoa', 'Familia Silva', 'residencial', 'Ñuñoa, Santiago', 'Av. Irarrázaval 9012', '2024-03-01', '2024-07-15', '25000000.00', 1, 'completado', 'Ampliación de casa existente con nuevo dormitorio y baño.', 'Ampliación de casa existente agregando un dormitorio principal con baño en suite, walk-in closet y terraza privada. Manteniendo la arquitectura original de la casa.', 'Estructura de hormigón, muros de ladrillo, techumbre de zinc, baño con porcelanato, closet empotrado.', '45.00', 'Hormigón, Ladrillos, Zinc, Porcelanato, Madera', 'La ampliación quedó perfecta, se integró muy bien con la casa original. Muy contentos con el resultado.', 'Ana Silva', 0, 'Ampliación Casa Ñuñoa - NextLine Constructor', 'Ampliación de casa en Ñuñoa con nuevo dormitorio y baño. Proyecto completado exitosamente.', 'ampliación casa, Ñuñoa, dormitorio, baño, construcción residencial', 'A', '2025-09-27 02:44:55', '2025-09-27 02:44:55', NULL),
(8, 'Bodega Industrial Maipú', 'bodega-industrial-maipu', 'Logística del Sur', 'industrial', 'Maipú, Santiago', 'Camino a Melipilla 3456', '2023-09-01', '2024-01-30', '85000000.00', 1, 'completado', 'Bodega industrial de 2000m² para almacenamiento y distribución.', 'Construcción de bodega industrial de 2000m² con oficinas administrativas, estacionamientos para camiones y sistema de seguridad. Diseñada para almacenamiento y distribución de productos.', 'Estructura metálica, muros de panel sandwich, piso de hormigón pulido, sistema de seguridad, oficinas administrativas.', '2000.00', 'Estructura metálica, Panel sandwich, Hormigón pulido, Acero', 'Excelente construcción, cumplió con todos los requerimientos técnicos y de seguridad. Muy recomendable.', 'Roberto Vargas', 0, 'Bodega Industrial Maipú - NextLine Constructor', 'Bodega industrial de 2000m² en Maipú para almacenamiento y distribución. Proyecto completado.', 'bodega industrial, Maipú, almacenamiento, distribución, construcción industrial', 'A', '2025-09-27 02:44:55', '2025-09-27 02:44:55', NULL),
(9, 'Remodelación Restaurante Bellavista', 'remodelacion-restaurante-bellavista', 'Restaurante El Buen Sabor', 'comercial', 'Bellavista, Santiago', 'Pío Nono 7890', '2024-02-01', '2024-05-15', '18000000.00', 1, 'completado', 'Remodelación completa de restaurante con nueva cocina y comedor.', 'Remodelación completa de restaurante incluyendo nueva cocina industrial, comedor ampliado, barra de tragos y terraza exterior. Diseño moderno y funcional.', 'Cocina industrial, sistema de ventilación, piso antideslizante, iluminación LED, terraza con techo retráctil.', '120.00', 'Acero inoxidable, Cerámica antideslizante, LED, Madera, Vidrio', 'La remodelación transformó completamente el restaurante. Los clientes están encantados con el nuevo ambiente.', 'Patricia Morales', 0, 'Remodelación Restaurante Bellavista - NextLine Constructor', 'Remodelación completa de restaurante en Bellavista con nueva cocina y comedor. Proyecto completado.', 'remodelación restaurante, Bellavista, cocina industrial, comedor, construcción comercial', 'A', '2025-09-27 02:44:55', '2025-09-27 02:44:55', NULL),
(10, 'Test', 'test', 'asd', 'comercial', 'aaa', NULL, '2025-09-02', '2025-09-27', '123123123.00', 1, 'en_progreso', NULL, 'asdasdasd', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, 'A', '2025-09-28 01:41:48', '2025-09-28 01:41:48', NULL),
(11, 'Cotizacion cobertizo 2', 'cotizacion-cobertizo-2', 'Don Rafael Villar', 'residencial', '', '', '2025-09-29', NULL, '5027120.00', 0, 'en_progreso', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico en', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico entrada ( portón )\r\n• Instalación tubería gris sanitaria (futuro baño segundo nivel)\r\n• instalación eléctrica 12 focos a elección más 2 enchufes.', '', NULL, '', NULL, 'Don Rafael Villar', 0, 'Cotizacion cobertizo 2 - MANSANCHEZ', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico en', 'proyecto, construcción, residencial', 'A', '2025-09-29 21:02:19', '2025-09-29 21:04:53', '2025-09-29 21:04:53'),
(12, 'Cotizacion cobertizo 2', 'cotizacion-cobertizo-2-1', 'Don Rafael Villar', 'residencial', '', '', '2025-09-29', NULL, '5027120.00', 0, 'en_progreso', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico en', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico entrada ( portón )\r\n• Instalación tubería gris sanitaria (futuro baño segundo nivel)\r\n• instalación eléctrica 12 focos a elección más 2 enchufes.', '', NULL, '', NULL, 'Don Rafael Villar', 0, 'Cotizacion cobertizo 2 - MANSANCHEZ', 'Puntos a tratar,más detalles.\r\n• _ Fabricación Radier allanado\r\nde piso con acma para loza\r\n• Fabricación de cobertizo (stilo mediterráneo) más portón 2 hojas interior, pintura. Pilares de Fierro y madera.\r\n• Sacar calefón interior e instalar afuera,sellar muro.\r\n• Llevar( sacar ) agua fría y caliente para la logía según lo conversado.\r\n• Eliminar llaves actuales lavadero interior cocina y sellar muro.\r\n• Eliminar llave y desagüe actual patio\r\n• Correr Pilar metálico en', 'proyecto, construcción, residencial', 'A', '2025-09-29 21:04:14', '2025-09-29 21:04:14', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regiones`
--

DROP TABLE IF EXISTS `regiones`;
CREATE TABLE IF NOT EXISTS `regiones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  UNIQUE KEY `numero` (`numero`),
  KEY `idx_regiones_activo` (`activo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `regiones`
--

INSERT INTO `regiones` (`id`, `codigo`, `nombre`, `numero`, `activo`) VALUES
(1, 'XV', 'Arica y Parinacota', 15, 1),
(2, 'I', 'Tarapacá', 1, 1),
(3, 'II', 'Antofagasta', 2, 1),
(4, 'III', 'Atacama', 3, 1),
(5, 'IV', 'Coquimbo', 4, 1),
(6, 'V', 'Valparaíso', 5, 1),
(7, 'RM', 'Metropolitana', 13, 1),
(8, 'VI', 'O\'Higgins', 6, 1),
(9, 'VII', 'Maule', 7, 1),
(10, 'XVI', 'Ñuble', 16, 1),
(11, 'VIII', 'Biobío', 8, 1),
(12, 'IX', 'La Araucanía', 9, 1),
(13, 'XIV', 'Los Ríos', 14, 1),
(14, 'X', 'Los Lagos', 10, 1),
(15, 'XI', 'Aysén', 11, 1),
(16, 'XII', 'Magallanes', 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seo_meta`
--

DROP TABLE IF EXISTS `seo_meta`;
CREATE TABLE IF NOT EXISTS `seo_meta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `entity_id` int NOT NULL,
  `meta_title` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_desc` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `og_image` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_type_entity_id` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

DROP TABLE IF EXISTS `servicio`;
CREATE TABLE IF NOT EXISTS `servicio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `descripcionCorta` varchar(500) DEFAULT NULL,
  `descripcionLarga` varchar(2000) DEFAULT NULL,
  `caracteristicas` text,
  `beneficios` text,
  `tiempo_estimado` varchar(50) DEFAULT NULL,
  `garantia` varchar(100) DEFAULT NULL,
  `orden` int DEFAULT '0',
  `destacado` char(1) DEFAULT 'N',
  `valor` int DEFAULT NULL,
  `precio_desde` decimal(10,2) DEFAULT NULL,
  `precio_hasta` decimal(10,2) DEFAULT NULL,
  `mostrar_precio` char(1) DEFAULT 'S',
  `slug` varchar(150) DEFAULT NULL,
  `foto` varchar(1000) DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `fcreacion` datetime DEFAULT NULL,
  `factualizacion` datetime DEFAULT NULL,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_servicio_categoria` (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id`, `nombre`, `categoria_id`, `icono`, `descripcionCorta`, `descripcionLarga`, `caracteristicas`, `beneficios`, `tiempo_estimado`, `garantia`, `orden`, `destacado`, `valor`, `precio_desde`, `precio_hasta`, `mostrar_precio`, `slug`, `foto`, `estado`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(12, 'Quincho', 4, NULL, 'Construcción de quinchos personalizados con diseño moderno, perfectos para disfrutar al aire libre con familia y amigos', 'Construimos quinchos completamente personalizados que se adaptan a tu espacio y estilo de vida. Nuestros quinchos incluyen estructura sólida, techado resistente, instalaciones eléctricas y sanitarias, y acabados de primera calidad. Desde quinchos simples hasta complejos con parrillas integradas, barras, y áreas de entretenimiento. Cada proyecto incluye diseño arquitectónico, permisos municipales, construcción y entrega llave en mano. Perfectos para celebraciones familiares, reuniones sociales y disfrutar de la vida al aire libre en cualquier época del año.', 'Estructura de hormigón armado resistente\r\nTechado con materiales de primera calidad\r\nInstalaciones eléctricas completas\r\nParrilla integrada de acero inoxidable\r\nBarra de servicio con mesón\r\nIluminación LED decorativa\r\nPisos antideslizantes\r\nInstalaciones sanitarias (opcional)\r\nSistema de ventilación natural\r\nAcabados en madera o materiales sintéticos', 'Aumenta el valor de tu propiedad\r\nEspacio perfecto para entretenimiento\r\nDiseño personalizado según tus necesidades\r\nGarantía de 2 años en estructura\r\nSupervisión técnica constante\r\nMateriales resistentes a la intemperie\r\nInstalación profesional garantizada\r\nCumplimiento estricto de plazos\r\nMantenimiento mínimo requerido', '4 - 6 semanas', '2 años', 1, 'S', NULL, '800000.00', '2500000.00', 'N', 'quincho', 'lib/img/27092025/1758942737_f4a96947ec391a7a1f83.jpg', 'A', '2025-09-27 02:22:36', '2025-09-30 04:51:50', NULL),
(15, 'Casas Unifamiliares', 7, NULL, 'Construcción de viviendas unifamiliares desde cero con diseño personalizado', 'Construimos casas unifamiliares completamente personalizadas según tus necesidades. Desde el diseño arquitectónico hasta la entrega llave en mano, nos encargamos de todo el proceso constructivo con materiales de primera calidad y mano de obra especializada.', 'Estructura de hormigón armado\nTechado con materiales de primera calidad\nInstalaciones eléctricas y sanitarias completas\nTerminaciones de alta calidad\nAislamiento térmico y acústico', 'Vivienda propia personalizada\nInversión segura a largo plazo\nValorización de la propiedad\nComodidad y funcionalidad\nGarantía de calidad', '6 - 8 meses', '2 años', 1, 'S', NULL, '15000000.00', '25000000.00', 'S', 'casas-unifamiliares', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(16, 'Edificios de Apartamentos', 7, NULL, 'Construcción de edificios residenciales multifamiliares', 'Desarrollamos edificios de apartamentos modernos y funcionales, optimizando espacios y maximizando la eficiencia energética. Incluye diseño arquitectónico, permisos municipales y construcción completa.', 'Estructura antisísmica\nSistemas de seguridad modernos\nAscensores y escaleras\nEstacionamientos subterráneos\nÁreas verdes comunes', 'Inversión inmobiliaria rentable\nEspacios optimizados\nTecnología moderna\nSeguridad y comodidad\nValorización garantizada', '12 - 18 meses', '3 años', 2, 'S', NULL, '50000000.00', '99999999.99', 'S', 'edificios-apartamentos', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(17, 'Locales Comerciales', 2, NULL, 'Construcción y remodelación de locales comerciales modernos', 'Construimos locales comerciales adaptados a las necesidades específicas de tu negocio. Desde tiendas hasta restaurantes, creamos espacios funcionales y atractivos que potencian tu actividad comercial.', 'Diseño comercial funcional\nInstalaciones especializadas\nFachadas atractivas\nSistemas de ventilación\nIluminación comercial', 'Espacio comercial optimizado\nMayor atracción de clientes\nFuncionalidad empresarial\nValorización del local\nImagen profesional', '3 - 4 meses', '1 año', 3, 'N', NULL, '8000000.00', '20000000.00', 'S', 'locales-comerciales', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(18, 'Edificios de Oficinas', 2, NULL, 'Construcción de edificios corporativos y torres de oficinas', 'Desarrollamos edificios de oficinas modernos con tecnología de punta, sistemas eficientes y diseño corporativo que proyecta profesionalismo y funcionalidad empresarial.', 'Estructura corporativa moderna\nSistemas de climatización\nAscensores de alta velocidad\nEstacionamientos amplios\nTecnología integrada', 'Imagen corporativa profesional\nEspacios de trabajo eficientes\nTecnología de punta\nValorización empresarial\nPrestigio comercial', '18 - 24 meses', '5 años', 4, 'S', NULL, '80000000.00', '99999999.99', 'S', 'edificios-oficinas', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(19, 'Remodelación de Cocinas', 3, NULL, 'Renovación completa de cocinas con diseño moderno y funcional', 'Transformamos tu cocina en un espacio moderno, funcional y hermoso. Incluye diseño, demolición, instalaciones nuevas y terminaciones de alta calidad con los mejores materiales del mercado.', 'Diseño de cocinas modernas\nInstalaciones eléctricas y de gas\nMuebles de cocina personalizados\nEncimeras de granito o cuarzo\nIluminación especializada', 'Cocina moderna y funcional\nMayor valor de la propiedad\nComodidad en la cocina\nDiseño personalizado\nCalidad premium', '4 - 6 semanas', '1 año', 5, 'N', NULL, '2500000.00', '8000000.00', 'S', 'remodelacion-cocinas', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(20, 'Remodelación de Baños', 3, NULL, 'Modernización completa de baños con diseño contemporáneo', 'Renovamos tus baños con diseño contemporáneo, instalaciones modernas y materiales de primera calidad. Creamos espacios de relajación y funcionalidad en tu hogar.', 'Diseño de baños modernos\nInstalaciones sanitarias nuevas\nPisos y paredes premium\nGrifería de alta gama\nVentilación mejorada', 'Baño moderno y elegante\nMayor comodidad diaria\nValorización del hogar\nHigiene mejorada\nDiseño contemporáneo', '3 - 4 semanas', '1 año', 6, 'N', NULL, '1800000.00', '5000000.00', 'S', 'remodelacion-banos', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(21, 'Ampliación de Casas', 4, NULL, 'Extensión de viviendas agregando habitaciones o pisos', 'Ampliamos tu casa agregando habitaciones, pisos completos o espacios específicos. Mantenemos la armonía arquitectónica y optimizamos el uso del espacio disponible.', 'Ampliaciones estructurales\nIntegración arquitectónica\nInstalaciones nuevas\nTerminaciones consistentes\nPermisos municipales', 'Mayor espacio habitable\nValorización de la propiedad\nComodidad familiar\nInversión inteligente\nDiseño integrado', '2 - 4 meses', '2 años', 7, 'N', NULL, '5000000.00', '15000000.00', 'S', 'ampliacion-casas', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(22, 'Ampliación de Oficinas', 4, NULL, 'Extensión de espacios de trabajo y oficinas', 'Ampliamos tus oficinas agregando salas de reuniones, espacios de trabajo o pisos completos. Optimizamos la funcionalidad y productividad de tu empresa.', 'Ampliaciones corporativas\nEspacios de trabajo optimizados\nInstalaciones empresariales\nDiseño funcional\nTecnología integrada', 'Mayor capacidad de trabajo\nMejor productividad\nImagen empresarial\nValorización del inmueble\nEspacios modernos', '3 - 5 meses', '2 años', 8, 'N', NULL, '8000000.00', '25000000.00', 'S', 'ampliacion-oficinas', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(23, 'Piscinas', 8, NULL, 'Construcción de piscinas residenciales y comerciales', 'Construimos piscinas de todos los tamaños y estilos. Desde piscinas familiares hasta complejos acuáticos comerciales, con tecnología moderna y sistemas de mantenimiento eficientes.', 'Piscinas de concreto armado\nSistemas de filtración modernos\nIluminación LED\nCalefacción solar\nSistemas de limpieza automática', 'Entretenimiento familiar\nValorización de la propiedad\nEjercicio y relajación\nDiseño personalizado\nTecnología moderna', '6 - 8 semanas', '2 años', 9, 'S', NULL, '3500000.00', '12000000.00', 'S', 'piscinas', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(24, 'Quinchos y Parrillas', 8, NULL, 'Construcción de espacios de entretenimiento al aire libre', 'Construimos quinchos y parrillas personalizados para disfrutar al aire libre. Incluye diseño, construcción, instalaciones eléctricas y terminaciones de alta calidad.', 'Estructura resistente al clima\nParrilla integrada de acero inoxidable\nInstalaciones eléctricas\nMesones de trabajo\nIluminación especializada', 'Entretenimiento al aire libre\nValorización de la propiedad\nEspacio social familiar\nDiseño personalizado\nCalidad premium', '4 - 6 semanas', '2 años', 10, 'S', NULL, '2800000.00', '8000000.00', 'S', 'quinchos-parrillas', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(25, 'Pavimentación', 8, NULL, 'Construcción de pavimentos en asfalto, concreto y adoquines', 'Realizamos pavimentación de estacionamientos, patios, caminos y accesos vehiculares. Trabajamos con asfalto, concreto estampado y adoquines según tus necesidades.', 'Pavimentos de asfalto\nConcreto estampado\nAdoquines decorativos\nDrenaje integrado\nAcabados especiales', 'Acceso vehicular seguro\nValorización de la propiedad\nDurabilidad a largo plazo\nDiseño personalizado\nMantenimiento mínimo', '2 - 3 semanas', '1 año', 11, 'N', NULL, '1500000.00', '5000000.00', 'S', 'pavimentacion', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(26, 'Instalaciones Eléctricas', 9, NULL, 'Sistemas eléctricos completos para viviendas y comercios', 'Instalamos sistemas eléctricos completos, modernos y seguros. Incluye cableado, tableros, iluminación especializada y sistemas de automatización para hogares inteligentes.', 'Cableado eléctrico moderno\nTableros de distribución\nIluminación LED\nSistemas de automatización\nInstalaciones especializadas', 'Seguridad eléctrica\nEficiencia energética\nTecnología moderna\nComodidad diaria\nAhorro en consumo', '1 - 2 semanas', '1 año', 12, 'N', NULL, '800000.00', '3000000.00', 'S', 'instalaciones-electricas', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(27, 'Asesoría en Proyectos', 5, NULL, 'Consultoría técnica y de diseño para proyectos constructivos', 'Brindamos asesoría técnica especializada para proyectos de construcción. Incluye evaluación de factibilidad, diseño preliminar, cálculo de costos y gestión de permisos.', 'Evaluación técnica\nDiseño preliminar\nCálculo de costos\nGestión de permisos\nSupervisión técnica', 'Proyecto bien planificado\nAhorro en costos\nCumplimiento normativo\nCalidad garantizada\nAsesoría especializada', '1 - 2 semanas', '6 meses', 13, 'N', NULL, '500000.00', '2000000.00', 'S', 'asesoria-proyectos', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(28, 'Permisos Municipales', 11, NULL, 'Gestión completa de trámites y permisos municipales', 'Nos encargamos de todos los trámites y permisos municipales necesarios para tu proyecto. Incluye planos arquitectónicos, estudios técnicos y seguimiento del proceso.', 'Gestión de trámites\nPlanos arquitectónicos\nEstudios técnicos\nSeguimiento municipal\nDocumentación completa', 'Tramitación eficiente\nCumplimiento normativo\nAhorro de tiempo\nDocumentación correcta\nProceso garantizado', '2 - 4 semanas', '3 meses', 14, 'N', NULL, '300000.00', '1500000.00', 'S', 'permisos-municipales', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL),
(29, 'Diseño Arquitectónico', 10, NULL, 'Servicios de diseño arquitectónico y planos técnicos', 'Desarrollamos diseños arquitectónicos personalizados y planos técnicos detallados. Incluye diseño 3D, planos de construcción y especificaciones técnicas completas.', 'Diseño arquitectónico\nPlanos técnicos detallados\nModelado 3D\nEspecificaciones técnicas\nDiseño personalizado', 'Diseño profesional\nPlanos técnicos precisos\nVisualización 3D\nPersonalización completa\nCalidad arquitectónica', '2 - 3 semanas', '6 meses', 15, 'N', NULL, '800000.00', '3000000.00', 'S', 'diseno-arquitectonico', 'lib/images/default-service.svg', 'A', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio_categoria`
--

DROP TABLE IF EXISTS `servicio_categoria`;
CREATE TABLE IF NOT EXISTS `servicio_categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `meta_titulo` varchar(255) DEFAULT NULL,
  `meta_descripcion` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `descripcion` varchar(500) DEFAULT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `estado` char(1) DEFAULT 'A',
  `orden` int DEFAULT '0',
  `fcreacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `servicio_categoria`
--

INSERT INTO `servicio_categoria` (`id`, `nombre`, `slug`, `meta_titulo`, `meta_descripcion`, `meta_keywords`, `descripcion`, `icono`, `color`, `estado`, `orden`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(2, 'Construcción Comercial', 'construccion-comercial', 'Construcción Comercial - NextLine Constructor', 'Edificios de oficinas y locales comerciales Servicios profesionales de construcción en Linares, Maule, Chile.', 'construcción comercial, construcción, constructor, Linares, Maule, Chile, servicios, profesional, edificios, oficinas, locales', 'Edificios de oficinas y locales comerciales', 'fas fa-building', '#e74c3c', 'A', 2, '2025-09-26 23:02:07', '2025-09-29 22:32:53', NULL),
(3, 'Remodelaciones', 'remodelaciones', NULL, NULL, NULL, 'Transformación de espacios existentes', 'icofont-tools', '#f39c12', 'A', 3, '2025-09-26 23:02:07', '2025-09-27 02:45:35', NULL),
(4, 'Ampliaciones', 'ampliaciones', 'Ampliaciones - NextLine Constructor', 'Extensión de construcciones existentes Servicios profesionales de construcción en Linares, Maule, Chile.', 'ampliaciones, construcción, constructor, Linares, Maule, Chile, servicios, profesional, extensión, agregar, crecer', 'Extensión de construcciones existentes', 'fas fa-expand-arrows-alt', '#27ae60', 'A', 4, '2025-09-26 23:02:07', '2025-09-29 22:33:52', NULL),
(5, 'Consultoría', 'consultoria', 'Consultoría - NextLine Constructor', 'Asesoría técnica y de proyectos Servicios profesionales de construcción en Linares, Maule, Chile.', 'consultoría, construcción, constructor, Linares, Maule, Chile, servicios, profesional', 'Asesoría técnica y de proyectos', 'fas fa-user-tie', '#9b59b6', 'A', 5, '2025-09-26 23:02:07', '2025-09-29 22:33:01', NULL),
(7, 'Construcción Residencial', 'construccion-residencial', 'Construcción Residencial - NextLine Constructor', 'Servicios especializados en construcción de casas familiares Servicios profesionales de construcción en Santiago, Chile.', 'construcción residencial, construcción, constructor, Santiago, Chile, servicios, profesional, casas, hogar, familia', 'Servicios especializados en construcción de casas familiares', 'fas fa-home', '#007bff', 'A', 1, '2025-09-27 06:00:40', '2025-09-27 06:04:32', NULL),
(8, 'Servicios Especializados', 'servicios-especializados', NULL, NULL, NULL, 'Servicios técnicos especializados como piscinas, quinchos, pavimentación e instalaciones', 'fas fa-cogs', '#17a2b8', 'A', 6, '2025-09-29 19:29:08', '2025-09-29 19:29:08', NULL),
(9, 'Instalaciones', 'instalaciones', NULL, NULL, NULL, 'Servicios de instalaciones eléctricas, sanitarias, de gas y sistemas especializados', 'fas fa-plug', '#6f42c1', 'A', 7, '2025-09-29 19:29:08', '2025-09-29 19:29:08', NULL),
(10, 'Diseño y Arquitectura', 'diseno-arquitectura', NULL, NULL, NULL, 'Servicios de diseño arquitectónico, planos técnicos y asesoría de proyectos', 'fas fa-drafting-compass', '#fd7e14', 'A', 8, '2025-09-29 19:29:08', '2025-09-29 19:29:08', NULL),
(11, 'Permisos y Trámites', 'permisos-tramites', NULL, NULL, NULL, 'Gestión de permisos municipales, trámites legales y documentación técnica', 'fas fa-file-alt', '#20c997', 'A', 9, '2025-09-29 19:29:08', '2025-09-29 19:29:08', NULL),
(12, 'Mantenimiento', 'mantenimiento', NULL, NULL, NULL, 'Servicios de mantenimiento, reparaciones y mejoras de construcciones existentes', 'fas fa-tools', '#dc3545', 'A', 10, '2025-09-29 19:29:08', '2025-09-29 19:29:08', NULL),
(13, 'Proyectos Industriales', 'proyectos-industriales', NULL, NULL, NULL, 'Construcción y desarrollo de proyectos industriales, bodegas y complejos comerciales', 'fas fa-industry', '#6c757d', 'A', 11, '2025-09-29 19:29:08', '2025-09-29 19:29:08', NULL),
(14, 'Infraestructura', 'infraestructura', NULL, NULL, NULL, 'Proyectos de infraestructura urbana, pavimentación y obras públicas', 'fas fa-road', '#28a745', 'A', 12, '2025-09-29 19:29:08', '2025-09-29 19:29:08', NULL),
(15, 'Sustentabilidad', 'sustentabilidad', NULL, NULL, NULL, 'Servicios de construcción sustentable, eficiencia energética y tecnologías verdes', 'fas fa-leaf', '#198754', 'A', 13, '2025-09-29 19:29:08', '2025-09-29 19:29:08', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `testimonios`
--

DROP TABLE IF EXISTS `testimonios`;
CREATE TABLE IF NOT EXISTS `testimonios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `testimonio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `calificacion` tinyint(1) NOT NULL DEFAULT '5' COMMENT '1-5 estrellas',
  `proyecto_id` int DEFAULT NULL COMMENT 'ID del proyecto relacionado (opcional)',
  `servicio_id` int DEFAULT NULL COMMENT 'ID del servicio relacionado (opcional)',
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Foto del cliente',
  `estado` enum('A','I') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `destacado` enum('S','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'S=Destacado, N=Normal',
  `fecha_proyecto` date DEFAULT NULL COMMENT 'Fecha cuando se realizó el proyecto',
  `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_titulo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_descripcion` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_keywords` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `factualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `feliminacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_destacado` (`destacado`),
  KEY `idx_proyecto` (`proyecto_id`),
  KEY `idx_servicio` (`servicio_id`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `testimonios`
--

INSERT INTO `testimonios` (`id`, `nombre`, `cargo`, `empresa`, `testimonio`, `calificacion`, `proyecto_id`, `servicio_id`, `imagen`, `estado`, `destacado`, `fecha_proyecto`, `slug`, `meta_titulo`, `meta_descripcion`, `meta_keywords`, `fcreacion`, `factualizacion`, `feliminacion`) VALUES
(1, 'María González', 'Propietaria', 'Casa Residencial', 'Excelente trabajo en la construcción de nuestra casa. El equipo fue muy profesional y cumplieron con todos los plazos establecidos. La calidad de los materiales y la atención al detalle fueron excepcionales.', 5, 5, NULL, NULL, 'A', 'S', '2024-08-15', 'maria-gonzalez', 'Testimonio de María González - Casa Residencial', 'Excelente trabajo en la construcción de nuestra casa. El equipo fue muy profesional y cumplieron con todos los plazos establecidos. La calidad de los...', 'testimonio, maría gonzález, casa residencial, construcción, cliente satisfecho', '2025-09-27 06:59:50', '2025-09-29 01:53:59', NULL),
(2, 'Carlos Rodríguez', 'Director', 'Empresa Comercial S.A.', 'Contratamos a MANSANCHEZ para la ampliación de nuestras oficinas y quedamos muy satisfechos. El proyecto se completó a tiempo y dentro del presupuesto. Altamente recomendados.', 5, 1, NULL, NULL, 'A', 'S', '2024-07-20', 'carlos-rodriguez', 'Testimonio de Carlos Rodríguez - Empresa Comercial S.A.', 'Contratamos a NextLine para la ampliación de nuestras oficinas y quedamos muy satisfechos. El proyecto se completó a tiempo y dentro del presupuesto...', 'testimonio, carlos rodríguez, empresa comercial s.a., construcción, cliente satisfecho', '2025-09-27 06:59:50', '2025-09-29 22:07:03', NULL),
(5, 'Rafael Villar', 'Propietario', 'Casa Residencial', 'Excelente profesional, muy serio don Manuel, siempre muy presente en la obra.', 5, 1, 21, NULL, 'A', 'N', '2025-09-27', 'rafael-villar', 'Testimonio de Rafael Villar - Casa Residencial', 'Excelente profesional, muy serio don Manuel, siempre muy presente en la obra.', 'testimonio, rafael villar, casa residencial, construcción, cliente satisfecho', '2025-09-27 21:17:46', '2025-10-01 06:36:22', NULL);

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
(17, 'Manuel', 'Sanchez', 'manuel@gmail.com', '991621564', '$2y$10$.DFZHHzMbKplgYnHfPPcOOLdusgdNEjgoha8DLT1rxlMazqvweZT2', 8, 'A', '2024-10-12 04:50:38', '2025-10-11 20:53:04', NULL, NULL, 0, NULL, NULL, NULL, NULL);

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
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_clientes_comuna` FOREIGN KEY (`comuna_id`) REFERENCES `comunas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_clientes_region` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `comunas`
--
ALTER TABLE `comunas`
  ADD CONSTRAINT `comunas_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regiones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `cotizaciones`
--
ALTER TABLE `cotizaciones`
  ADD CONSTRAINT `cotizaciones_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE RESTRICT;

--
-- Filtros para la tabla `cotizacion_archivos`
--
ALTER TABLE `cotizacion_archivos`
  ADD CONSTRAINT `cotizacion_archivos_ibfk_1` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cotizacion_items`
--
ALTER TABLE `cotizacion_items`
  ADD CONSTRAINT `cotizacion_items_ibfk_1` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cotizacion_seguimiento`
--
ALTER TABLE `cotizacion_seguimiento`
  ADD CONSTRAINT `cotizacion_seguimiento_ibfk_1` FOREIGN KEY (`cotizacion_id`) REFERENCES `cotizaciones` (`id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `galeria`
--
ALTER TABLE `galeria`
  ADD CONSTRAINT `fk_galeria_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `galeria_categoria` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `galeria_detalle`
--
ALTER TABLE `galeria_detalle`
  ADD CONSTRAINT `galeria_detalle_ibfk_1` FOREIGN KEY (`galeria_id`) REFERENCES `galeria` (`id`);

--
-- Filtros para la tabla `modulo_detalle`
--
ALTER TABLE `modulo_detalle`
  ADD CONSTRAINT `modulo_detalle_ibfk_1` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `perfil_modulo`
--
ALTER TABLE `perfil_modulo`
  ADD CONSTRAINT `perfil_modulo_ibfk_1` FOREIGN KEY (`perfil_id`) REFERENCES `perfil` (`id`),
  ADD CONSTRAINT `perfil_modulo_ibfk_2` FOREIGN KEY (`modulo_id`) REFERENCES `modulo` (`id`);

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `fk_servicio_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `servicio_categoria` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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
