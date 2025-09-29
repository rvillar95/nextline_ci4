-- Tabla de clientes (genérica para particulares y empresas)
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo_cliente ENUM('particular','empresa','organizacion') NOT NULL DEFAULT 'particular',
    nombre_razon_social VARCHAR(200) NOT NULL,
    rut_dni VARCHAR(20),
    contacto_nombre VARCHAR(100), -- Para empresas: persona de contacto
    contacto_cargo VARCHAR(100), -- Para empresas: cargo del contacto
    telefono VARCHAR(50),
    email VARCHAR(150),
    direccion VARCHAR(300),
    comuna VARCHAR(100),
    region VARCHAR(100),
    sitio_web VARCHAR(200),
    observaciones TEXT,
    estado ENUM('A','I') DEFAULT 'A',
    fcreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    factualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo_cliente (tipo_cliente),
    INDEX idx_nombre (nombre_razon_social),
    INDEX idx_rut (rut_dni)
);

-- Tabla de cotizaciones
CREATE TABLE cotizaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    numero_cotizacion VARCHAR(50) UNIQUE,
    proyecto_nombre VARCHAR(200) NOT NULL,
    proyecto_descripcion TEXT,
    proyecto_tipo ENUM('residencial','comercial','industrial','mantenimiento','otro') DEFAULT 'residencial',
    proyecto_area DECIMAL(10,2),
    proyecto_ubicacion VARCHAR(300),
    proyecto_direccion VARCHAR(300),
    fecha_cotizacion DATE NOT NULL,
    fecha_validez DATE,
    vigencia_dias INT DEFAULT 30,
    estado ENUM('borrador','enviada','revisada','aprobada','rechazada','expirada') DEFAULT 'borrador',
    prioridad ENUM('baja','media','alta','urgente') DEFAULT 'media',
    
    -- Totales calculados
    subtotal_materiales DECIMAL(12,2) DEFAULT 0,
    subtotal_mano_obra DECIMAL(12,2) DEFAULT 0,
    subtotal_servicios DECIMAL(12,2) DEFAULT 0,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    descuento_monto DECIMAL(12,2) DEFAULT 0,
    iva_porcentaje DECIMAL(5,2) DEFAULT 19,
    iva_monto DECIMAL(12,2) DEFAULT 0,
    total_general DECIMAL(12,2) DEFAULT 0,
    
    -- Condiciones comerciales
    forma_pago ENUM('contado','credito','mixto') DEFAULT 'contado',
    plazo_pago_dias INT DEFAULT 0,
    anticipo_porcentaje DECIMAL(5,2) DEFAULT 0,
    anticipo_monto DECIMAL(12,2) DEFAULT 0,
    
    -- Tiempos de ejecución
    tiempo_ejecucion_dias INT,
    fecha_inicio_estimada DATE,
    fecha_fin_estimada DATE,
    
    -- Observaciones y condiciones
    condiciones_generales TEXT,
    observaciones_especiales TEXT,
    garantia_meses INT DEFAULT 12,
    
    -- SEO y slug
    slug VARCHAR(200),
    meta_titulo VARCHAR(200),
    meta_descripcion VARCHAR(300),
    
    -- Auditoría
    creado_por INT, -- ID del usuario que creó la cotización
    fcreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    factualizacion TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    feliminacion TIMESTAMP NULL,
    
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    INDEX idx_cliente (cliente_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_cotizacion (fecha_cotizacion),
    INDEX idx_numero (numero_cotizacion),
    INDEX idx_slug (slug)
);

-- Tabla de items de cotización (flexible para materiales, mano de obra, servicios)
CREATE TABLE cotizacion_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cotizacion_id INT NOT NULL,
    categoria ENUM('material','mano_obra','servicio','equipo','transporte','otros') NOT NULL,
    subcategoria VARCHAR(100), -- Ej: "Estructura", "Acabados", "Instalaciones"
    codigo_item VARCHAR(50), -- Código interno del item
    descripcion TEXT NOT NULL,
    especificaciones TEXT, -- Detalles técnicos adicionales
    cantidad DECIMAL(10,3) NOT NULL DEFAULT 1,
    unidad VARCHAR(50) NOT NULL DEFAULT 'unidad', -- m², m³, kg, horas, etc.
    precio_unitario DECIMAL(10,2) NOT NULL DEFAULT 0,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0,
    descuento_monto DECIMAL(10,2) DEFAULT 0,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
    orden INT DEFAULT 0,
    es_opcional BOOLEAN DEFAULT FALSE, -- Para items opcionales
    observaciones TEXT,
    fcreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id) ON DELETE CASCADE,
    INDEX idx_cotizacion (cotizacion_id),
    INDEX idx_categoria (categoria),
    INDEX idx_orden (orden)
);

-- Tabla de archivos adjuntos (planos, fotos, documentos)
CREATE TABLE cotizacion_archivos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cotizacion_id INT NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    nombre_original VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(500) NOT NULL,
    tipo_archivo VARCHAR(100), -- pdf, jpg, dwg, etc.
    tamaño_bytes BIGINT,
    descripcion TEXT,
    es_principal BOOLEAN DEFAULT FALSE, -- Archivo principal de la cotización
    orden INT DEFAULT 0,
    fcreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id) ON DELETE CASCADE,
    INDEX idx_cotizacion (cotizacion_id),
    INDEX idx_tipo (tipo_archivo)
);

-- Tabla de seguimiento de cotizaciones
CREATE TABLE cotizacion_seguimiento (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cotizacion_id INT NOT NULL,
    usuario_id INT, -- Usuario que realizó la acción
    accion ENUM('creada','enviada','revisada','aprobada','rechazada','modificada','recordatorio') NOT NULL,
    descripcion TEXT,
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    proxima_accion TEXT, -- Próxima acción a realizar
    fecha_proxima_accion DATE,
    
    FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id) ON DELETE CASCADE,
    INDEX idx_cotizacion (cotizacion_id),
    INDEX idx_fecha (fecha_accion)
);

-- Insertar algunos tipos de cliente de ejemplo
INSERT INTO clientes (tipo_cliente, nombre_razon_social, contacto_nombre, telefono, email, direccion) VALUES
('particular', 'Don Rafael Villar', 'Rafael Villar', '+56912345678', 'rafael@email.com', 'Av. Principal 123, Santiago'),
('empresa', 'Constructora ABC S.A.', 'María González', '+56987654321', 'maria@constructoraabc.cl', 'Av. Empresarial 456, Las Condes'),
('organizacion', 'Municipalidad de Santiago', 'Carlos Rodríguez', '+56911223344', 'carlos@munistgo.cl', 'Plaza de Armas 1, Santiago');
