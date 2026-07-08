<?php

session_start();
if (!isset($_SESSION['usuario_rut'])) {
    header("Location: index.php");
    exit;
}

require 'conexion.php';
# simplemente recibimos el rut y borramos de la base de datos al usuario
if (isset($_GET['rut'])) {
    $rut = $_GET['rut'];
    $sql = "DELETE FROM usuario WHERE rut = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$rut]);
}

header("Location: eliminar_lista.php");
exit;
?>