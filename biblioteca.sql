-- ============================================================
-- BiblioTec - Base de Datos
-- Práctica Unidad 4: Programación del Lado del Servidor
-- ============================================================

CREATE DATABASE IF NOT EXISTS bibliotec
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bibliotec;

-- ------------------------------------------------------------
-- Tabla de Usuarios (Paso 2: roles, cifrado de contraseñas)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50)  NOT NULL UNIQUE,
    email          VARCHAR(100) NOT NULL UNIQUE,
    password_hash  VARCHAR(255) NOT NULL,           -- bcrypt
    rol            ENUM('admin','editor','lector') NOT NULL DEFAULT 'lector',
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    fecha_registro TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Tabla de Libros / Productos (Paso 1)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS libros (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    isbn           VARCHAR(20)  NOT NULL UNIQUE,
    titulo         VARCHAR(200) NOT NULL,
    autor          VARCHAR(150) NOT NULL,
    precio         DECIMAL(10,2) NOT NULL,
    stock          INT          NOT NULL DEFAULT 0,
    activo         TINYINT(1)   NOT NULL DEFAULT 1,
    fecha_registro TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- Datos iniciales
-- Contraseña del admin: Admin123!
-- Hash generado con password_hash('Admin123!', PASSWORD_BCRYPT)
-- ------------------------------------------------------------
INSERT INTO usuarios (nombre_usuario, email, password_hash, rol) VALUES
('admin', 'admin@bibliotec.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Libros de muestra
INSERT INTO libros (isbn, titulo, autor, precio, stock) VALUES
('978-0-14-200967-5', 'Cuento de Navidad',  'Charles Dickens',  199.00, 10),
('978-0-14-310793-1', 'Tierra de Lágrimas', 'Kathleen Grissom', 249.00,  8),
('978-0-14-143951-8', 'Orgullo y Prejuicio','Jane Austen',       179.00, 15);
