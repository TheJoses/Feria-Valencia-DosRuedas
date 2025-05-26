CREATE DATABASE participacion;
USE sorteos;

CREATE TABLE participaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    edad INT NOT NULL CHECK (edad >= 18),
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(15) NOT NULL,
    codigo_postal VARCHAR(6) NOT NULL,
    fecha_participacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);