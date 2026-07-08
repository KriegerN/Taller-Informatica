<?php
session_start();
require 'conexion.php'; 

$tipo_evento = "Cierre de sesión"; 
$detalle_evento = "Cierre de sesión exitoso del administrador.";
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip_cliente = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
} else {
    $ip_cliente = $_SERVER['REMOTE_ADDR'];
}
$usuario_responsable = $_SESSION['usuario_nombre'];
$sql_log = "INSERT INTO registro (tipo, detalle, usuario, IP) VALUES (?, ?, ?, ?)";
$stmt_log = $pdo->prepare($sql_log);
$stmt_log->execute([$tipo_evento, $detalle_evento, $usuario_responsable, trim($ip_cliente)]);


session_destroy();
header("Location: index.php");
exit;
?>