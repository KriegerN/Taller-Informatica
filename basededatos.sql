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
    id_rol INT NOT NULL,
    id_departamento INT NOT NULL,
    
    FOREIGN KEY (id_rol) REFERENCES rol(id) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_departamento) REFERENCES departamento(id) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

CREATE TABLE registro (
    id SERIAL PRIMARY KEY,
    accion VARCHAR(50) NOT NULL, -- Ej: 'ELIMINAR', 'CREAR', 'MODIFICAR'
    descripcion TEXT NOT NULL,   -- Ej: 'Se eliminó al usuario RUT 1234567-8'
    usuario_responsable VARCHAR(100),
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Roles
INSERT INTO rol (nombre) VALUES 
('Estudiante'), 
('Profesor');

-- Departamentos
INSERT INTO departamento (nombre) VALUES 
('Informática'), 
('Derecho'), 
('Obstetricia');