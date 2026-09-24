-- Base de datos DEMO reconstruida para el portfolio.
-- No es una recuperación ni una copia de la base de datos original.
-- Ejecutar únicamente en un entorno local de demostración.

CREATE DATABASE IF NOT EXISTS bienesraices_demo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bienesraices_demo;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vendedores (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS propiedades (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    precio DECIMAL(12,2) NOT NULL,
    imagen VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    habitaciones TINYINT UNSIGNED NOT NULL,
    wc TINYINT UNSIGNED NOT NULL,
    estacionamientos TINYINT UNSIGNED NOT NULL,
    creado DATE NOT NULL,
    vendedores_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY idx_propiedades_vendedores_id (vendedores_id),
    CONSTRAINT fk_propiedades_vendedores
        FOREIGN KEY (vendedores_id)
        REFERENCES vendedores (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Usuario exclusivo de demostración. La contraseña no se almacena en texto plano.
INSERT INTO usuarios (email, password)
VALUES (
    'admin@demo.inmobiliaria.test',
    '$2y$12$OyoFoU.O4JF5NnSaOzkop.1cDuZVI1Qd8VT5CNH/xpn2Z01pHhdt6'
);

-- Vendedores ficticios para probar el CRUD.
INSERT INTO vendedores (nombre, apellido, telefono) VALUES
    ('Agente', 'Demo Norte', '1111111111'),
    ('Agente', 'Demo Centro', '2222222222'),
    ('Agente', 'Demo Sur', '3333333333');

-- Propiedades ficticias para probar el CRUD y los tres anuncios del inicio.
-- Solo se utilizan imágenes cuya coincidencia con assets fuente fue confirmada:
-- anuncio1.jpg y anuncio2.jpg.
INSERT INTO propiedades (
    titulo,
    precio,
    imagen,
    descripcion,
    habitaciones,
    wc,
    estacionamientos,
    creado,
    vendedores_id
) VALUES
    (
        'Casa Modelo Horizonte',
        185000.00,
        '938fbd004e900949af2b40729d041a53.jpg',
        'Propiedad ficticia para demostración, con ambientes luminosos, patio privado y una distribución pensada para una familia pequeña.',
        3,
        2,
        1,
        '2026-01-15',
        1
    ),
    (
        'Departamento Demo Parque',
        128500.00,
        'da8caeada70bb49cc45346a4164bdee8.jpg',
        'Departamento ficticio de demostración ubicado cerca de espacios verdes, con balcón, buena ventilación y terminaciones contemporáneas.',
        2,
        1,
        1,
        '2026-02-10',
        2
    ),
    (
        'Residencia Demo Mirador',
        242000.00,
        '938fbd004e900949af2b40729d041a53.jpg',
        'Residencia ficticia para portfolio con áreas sociales amplias, jardín de muestra y una distribución adaptable a distintos estilos de vida.',
        4,
        3,
        2,
        '2026-03-05',
        3
    );
