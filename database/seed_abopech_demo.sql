-- Seed demo ABOPECH: admin, comunas RM, tribunales ejemplo
-- Ejecutar después de schema_red_abogado_perfil_gcp.sql
USE `red_profesionales_judiciales`;

-- Admin (clave: AdminAbopech2026!)
INSERT INTO `rj_usuario` (`correo`, `clave`, `nombre`, `apellido`, `telefono`, `tipo_cuenta`, `estado`, `consentimiento_contacto`, `terminos_aceptados_en`)
VALUES (
  'admin@abopech.cl',
  '$2y$10$IHvDOp1f2zeQkOsV20ZIvOlnaF1idj9QcYEz6yQmBhcTEXr2xpq9C',
  'Admin',
  'ABOPECH',
  '+56912345678',
  'administrador',
  'A',
  1,
  NOW()
) ON DUPLICATE KEY UPDATE `tipo_cuenta` = 'administrador';

-- Comunas Región Metropolitana (region_id=7 según seed norte-sur)
INSERT IGNORE INTO `rj_comuna` (`region_id`, `nombre`, `activo`) VALUES
(7, 'Santiago', 1),
(7, 'Providencia', 1),
(7, 'Las Condes', 1),
(7, 'Maipú', 1),
(9, 'Linares', 1);

-- Tribunales ejemplo (ajustar region_id según comuna real de Linares = Maule reg 9 en seed geográfico)
INSERT INTO `rj_tribunal` (`nombre`, `descripcion`, `region_id`, `comuna_id`, `tipo_codigo`, `estado`) VALUES
('Juzgado de Garantía de Linares', 'Tribunal penal Linares', 9, NULL, 'garantia', 'activo'),
('Tribunal de Juicio Oral en lo Penal de Linares', 'TOP Linares', 9, NULL, 'juicio_oral_penal', 'activo'),
('Juzgado de Garantía de Santiago', 'Ejemplo RM', 7, NULL, 'garantia', 'activo');
