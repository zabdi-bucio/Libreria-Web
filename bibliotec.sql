CREATE DATABASE IF NOT EXISTS bibliotec;
USE bibliotec;

DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS libros;

CREATE TABLE libros (
  id int(11) NOT NULL AUTO_INCREMENT,
  titulo varchar(200) NOT NULL,
  descripcion text DEFAULT NULL,
  autor varchar(150) NOT NULL,
  precio decimal(10,2) NOT NULL,
  stock int(11) NOT NULL DEFAULT 0,
  activo tinyint(1) NOT NULL DEFAULT 1,
  fecha_registro timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO libros (id, titulo, descripcion, autor, precio, stock, activo, fecha_registro) VALUES
(1, 'Cuento de Navidad', NULL, 'Charles Dickens', 299.00, 5, 1, '2026-05-07 04:55:04'),
(2, 'Tierra de Lágrimas', NULL, 'Kathleen Grissom', 249.00, 8, 1, '2026-05-07 04:55:04'),
(3, 'Orgullo y Prejuicio', 'descripcion', 'Jane Austennnn', 179.00, 10, 1, '2026-05-07 04:55:04'),
(4, 'Cuento de Navidad', NULL, 'Charles Dickens', 299.00, 5, 0, '2026-05-07 08:34:34'),
(13, 'El Secreto de El Futuro de la Humanidad', NULL, 'Holaa', 264.99, 5, 1, '2026-05-07 09:50:43'),
(14, 'Crónicas de Steve Jobbs', NULL, 'Charles Dickens', 249.99, 4, 1, '2026-05-07 09:51:16'),
(16, 'Cuento de Navidad', NULL, 'Holaa', 500.00, 4, 1, '2026-05-07 09:59:46'),
(17, 'holiiii', 'libro narrativo', '', 284.99, 3, 1, '2026-05-07 10:13:50'),
(18, 'hninon', 'ijkmnjmnjjnjk', '', 220.00, 10, 1, '2026-05-07 11:14:37');

CREATE TABLE usuarios (
  id int(11) NOT NULL AUTO_INCREMENT,
  nombre_usuario varchar(50) NOT NULL,
  email varchar(100) NOT NULL,
  password_hash varchar(255) NOT NULL,
  rol enum('admin','editor','lector') NOT NULL DEFAULT 'lector',
  activo tinyint(1) NOT NULL DEFAULT 1,
  fecha_registro timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY nombre_usuario (nombre_usuario),
  UNIQUE KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO usuarios (id, nombre_usuario, email, password_hash, rol, activo, fecha_registro) VALUES
(1, 'admin', 'admin@bibliotec.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2026-05-07 04:55:04'),
(2, 'zabdi', 'zabdibucio74@gmail.com', '$2y$10$NnddTptewnDNQ.vlAAakU.L7Vsl.KqSkim83d.dWC7GzaNhxBI9KG', 'editor', 0, '2026-05-07 08:35:22'),
(3, 'zabdii', 'zabdibucio75@gmail.com', '$2y$10$cpHDiYJQkb08slgylOayKe78Vx2GeY9vcag173a6RsA8hTUTB6TDG', 'lector', 1, '2026-05-07 08:36:16'),
(4, 'hola', 'zabdibucio@gmail.com', '$2y$10$ZuzazqHe3ZAwH1.oj0Ub5uDISGylEx9KN5fwSclUWMze9n6MiLrW2', 'admin', 1, '2026-05-07 09:41:00'),
(5, 'holaaa', 'zabdibucio00@gmail.com', '$2y$10$Jmg.s5Wu2b4mV0Xl1lE27O6rIZJGyci85eWcZxyt2UMZYx.5nr4Ta', 'lector', 1, '2026-05-07 11:13:13'),
(6, 'usuario', 'zabdibucio11@gmail.com', '$2y$10$RlYSNIMD7Ljb0Hsdf29Hde96sgzq/pBQ/cb5Sr5EJY1tlzGX2Y3dO', 'lector', 1, '2026-05-07 11:41:38');

ALTER TABLE libros AUTO_INCREMENT = 19;
ALTER TABLE usuarios AUTO_INCREMENT = 7;