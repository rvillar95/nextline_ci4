-- Script para crear tabla empresa
USE nextline_ci4;

CREATE TABLE IF NOT EXISTS empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    nombre_comercial VARCHAR(255),
    rut VARCHAR(20),
    direccion TEXT,
    telefono VARCHAR(50),
    email VARCHAR(255),
    sitio_web VARCHAR(255),
    logo_path VARCHAR(500),
    descripcion TEXT,
    mision TEXT,
    vision TEXT,
    valores TEXT,
    estado ENUM('A', 'I') DEFAULT 'A',
    fcreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fmodificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar datos iniciales de MANSANCHEZ
INSERT INTO empresa (
    nombre, 
    nombre_comercial, 
    rut, 
    direccion, 
    telefono, 
    email, 
    sitio_web, 
    logo_path, 
    descripcion,
    mision,
    vision,
    valores
) VALUES (
    'MANSANCHEZ',
    'MANSANCHEZ Construcciones',
    '12.345.678-9',
    'Santiago, Chile',
    '+56 9 1234 5678',
    'info@mansanchez.cl',
    'www.mansanchez.cl',
    'lib/images/logo-min.jpg',
    'Empresa especializada en construcciones y remodelaciones residenciales y comerciales.',
    'Proporcionar servicios de construcción de alta calidad, cumpliendo con los más altos estándares de seguridad y excelencia.',
    'Ser la empresa líder en construcciones y remodelaciones, reconocida por nuestra calidad, innovación y compromiso con el cliente.',
    'Calidad, Honestidad, Compromiso, Innovación, Trabajo en equipo'
);

-- Verificar la inserción
SELECT * FROM empresa;
