<?php
$host = 'localhost';
$dbname = 'universidad_db';
$usuario = 'postgres';
$contrasena = 'admin'; 
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $usuario, $contrasena);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>