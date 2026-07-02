<?php
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