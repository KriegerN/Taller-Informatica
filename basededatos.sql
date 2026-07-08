-- base de datos: universidad_db
-- usuario: postgres
-- contraseña: admin
-- comando para la base de datos en consola: psql -h localhost -U postgres -d universidad_db

-- Tabla rol
CREATE TABLE rol (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla departamento
CREATE TABLE departamento (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

-- Tabla usuario
CREATE TABLE usuario (
    rut VARCHAR(10) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    id_rol INT NOT NULL,
    id_departamento INT NOT NULL,
    
    FOREIGN KEY (id_rol) REFERENCES rol(id) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_departamento) REFERENCES departamento(id) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

-- Tabla registro (Bitácora)
CREATE TABLE registro (
    id SERIAL PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL,
    detalle TEXT NOT NULL,  
    usuario VARCHAR(100) DEFAULT 'SISTEMA',
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    IP VARCHAR(20) NOT NULL
);

-- Roles
INSERT INTO rol (nombre) VALUES 
('Estudiante'), 
('Profesor'),
('Administrador');

-- Departamentos
INSERT INTO departamento (nombre) VALUES 
('Informática'), 
('Derecho'), 
('Obstetricia');

-- Usuario Administrador por defecto
-- (La contraseña encriptada equivale a la palabra: admin)
INSERT INTO usuario (rut, nombre, correo, password_hash, id_rol, id_departamento) VALUES 
('1111111-1', 'Administrador Principal', 'admin@ulagos.cl', '$2y$10$/4JD6SW4o/gMC0mNmih4Het.y9fTWAhJpwRrUB7d1mdOoDUvFqXeq', 3, 1);