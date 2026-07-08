<?php
session_start();
if (!isset($_SESSION['usuario_rut'])) {
    header("Location: index.php");
    exit;
}

require 'conexion.php';

if (isset($_GET['rut'])) {
    $rut = $_GET['rut'];
    // Evitar auto eliminacion
    if ($rut === $_SESSION['usuario_rut']) {
        header("Location: eliminar_lista.php");
        exit;
    }

    $sql_select = "SELECT nombre, correo, id_rol, id_departamento FROM usuario WHERE rut = ?";
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->execute([$rut]);
    $usuario_borrado = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if ($usuario_borrado) {
        $nombre = $usuario_borrado['nombre'];
        $correo = $usuario_borrado['correo'];
        $id_rol = $usuario_borrado['id_rol'];
        $id_departamento = $usuario_borrado['id_departamento'];

        $sql = "DELETE FROM usuario WHERE rut = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$rut]);
   
        $nombres_roles = [1 => 'Estudiante', 2 => 'Profesor', 3 => 'Administrador'];
        $nombres_deptos = [1 => 'Informática', 2 => 'Derecho', 3 => 'Obstetricia'];
        
        $rol_texto = $nombres_roles[$id_rol];
        $depto_texto = $nombres_deptos[$id_departamento];


        $tipo_evento = "Eliminar registro"; 
        $detalle_evento = "Tabla usuario. Eliminado: $nombre (RUT: $rut, Correo: $correo, Rol: $rol_texto, Depto: $depto_texto)";
        
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
    }
}

header("Location: eliminar_lista.php");
exit;
?>