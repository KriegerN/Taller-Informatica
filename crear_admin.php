<?php
require 'conexion.php';
// archivo usado para crear el administrador, tambien se puede usando consola y obteniendo el hash de la contraseña que queramos 
// pero mas facil asi:
$rut = '21469267-8';
$nombre = 'Nicolás Guerrero';
$correo = 'admin@ulagos.cl';
$password_plana = 'admin';

$id_rol = 3; 
$id_departamento = 1; 

// Generamos el hash 
$password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO usuario (rut, nombre, correo, password_hash, id_rol, id_departamento) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rut, $nombre, $correo, $password_hash, $id_rol, $id_departamento]);
    
    echo "<h2>Administrador creado con éxito.</h2>";
    echo "<p>Correo: admin@ulagos.cl</p>";
    echo "<p>Contraseña:admin</p>";

} catch (PDOException $e) {
    if ($e->getCode() == 23505) { 
        echo "<h2>El administrador ya existe en la base de datos.</h2>";
    } else {
        echo "<h2>Error de base de datos:</h2><p>" . $e->getMessage() . "</p>";
    }
}
?>