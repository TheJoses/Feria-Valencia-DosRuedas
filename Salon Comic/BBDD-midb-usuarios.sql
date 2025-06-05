CREATE DATABASE midb;
USE midb;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido1 VARCHAR(100) NOT NULL,
    apellido2 VARCHAR(100),
    correo_electronico VARCHAR(255) NOT NULL UNIQUE,
    idioma VARCHAR(50) DEFAULT 'es'
);