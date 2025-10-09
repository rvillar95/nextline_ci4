-- Insertar categorías de galería
INSERT INTO `galeria_categoria` (`nombre`, `slug`, `descripcion`, `icono`, `color`, `estado`, `orden`, `meta_titulo`, `meta_descripcion`, `meta_keywords`) VALUES
('Casas Residenciales', 'casas-residenciales', 'Construcción de casas familiares y residenciales', 'fas fa-home', '#f0841a', 'A', 1, 'Casas Residenciales - NextLine Constructor', 'Construcción de casas familiares y residenciales en Linares, Maule. Constructor con más de 15 años de experiencia.', 'casas, residenciales, construcción, hogar, familia, Linares, Maule, Chile'),
('Edificios Comerciales', 'edificios-comerciales', 'Construcción de edificios comerciales y oficinas', 'fas fa-building', '#1d2844', 'A', 2, 'Edificios Comerciales - NextLine Constructor', 'Construcción de edificios comerciales y oficinas en Linares, Maule. Proyectos comerciales de calidad.', 'edificios, comerciales, oficinas, construcción, Linares, Maule, Chile'),
('Quinchos y Parrillas', 'quinchos-parrillas', 'Construcción de quinchos y áreas de parrilla', 'fas fa-fire', '#e74c3c', 'A', 3, 'Quinchos y Parrillas - NextLine Constructor', 'Construcción de quinchos y áreas de parrilla para disfrutar en familia. Diseños modernos y funcionales.', 'quinchos, parrillas, jardín, recreación, construcción, Linares, Maule, Chile'),
('Ampliaciones', 'ampliaciones', 'Ampliaciones y extensiones de viviendas', 'fas fa-expand-arrows-alt', '#27ae60', 'A', 4, 'Ampliaciones - NextLine Constructor', 'Ampliaciones y extensiones de viviendas existentes. Más espacio para tu familia.', 'ampliaciones, extensiones, viviendas, construcción, Linares, Maule, Chile'),
('Remodelaciones', 'remodelaciones', 'Remodelaciones y renovaciones de espacios', 'fas fa-tools', '#8e44ad', 'A', 5, 'Remodelaciones - NextLine Constructor', 'Remodelaciones y renovaciones de espacios existentes. Transformamos tu hogar.', 'remodelaciones, renovaciones, reformas, construcción, Linares, Maule, Chile');

-- Insertar imágenes de ejemplo en la galería
INSERT INTO `galeria` (`nombre`, `categoria_id`, `descripcion`, `portada`, `foto`, `estado`) VALUES
('Casa Familiar Moderna', 1, 'Hermosa casa familiar de 3 dormitorios con diseño moderno y amplios espacios. Construida con materiales de primera calidad y terminaciones de lujo.', 'casa-familiar-moderna.jpg', 'casa-familiar-moderna.jpg', 'A'),
('Edificio de Oficinas', 2, 'Edificio comercial de 4 pisos con oficinas modernas y estacionamientos. Diseño funcional y eficiente para empresas.', 'edificio-oficinas.jpg', 'edificio-oficinas.jpg', 'A'),
('Quincho con Parrilla', 3, 'Quincho familiar con parrilla integrada y área de estar. Perfecto para reuniones familiares y asados.', 'quincho-parrilla.jpg', 'quincho-parrilla.jpg', 'A'),
('Ampliación de Cocina', 4, 'Ampliación de cocina con isla central y más espacio de almacenamiento. Diseño moderno y funcional.', 'ampliacion-cocina.jpg', 'ampliacion-cocina.jpg', 'A'),
('Remodelación de Baño', 5, 'Remodelación completa de baño principal con diseño moderno, nuevos azulejos y accesorios de lujo.', 'remodelacion-bano.jpg', 'remodelacion-bano.jpg', 'A'),
('Casa de Dos Pisos', 1, 'Casa de dos pisos con 4 dormitorios, sala de estar amplia y jardín. Ideal para familias grandes.', 'casa-dos-pisos.jpg', 'casa-dos-pisos.jpg', 'A'),
('Local Comercial', 2, 'Local comercial de 200m² con fachada moderna y espacios amplios para negocios.', 'local-comercial.jpg', 'local-comercial.jpg', 'A'),
('Quincho Rústico', 3, 'Quincho rústico con chimenea y área de estar al aire libre. Ambiente acogedor y familiar.', 'quincho-rustico.jpg', 'quincho-rustico.jpg', 'A'),
('Ampliación de Dormitorio', 4, 'Ampliación de dormitorio principal con walk-in closet y baño privado.', 'ampliacion-dormitorio.jpg', 'ampliacion-dormitorio.jpg', 'A'),
('Remodelación de Sala', 5, 'Remodelación completa de sala de estar con diseño abierto y iluminación natural.', 'remodelacion-sala.jpg', 'remodelacion-sala.jpg', 'A');

