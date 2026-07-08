<?php
session_start();

// Si el usuario ya tiene sesion iniciada se le envia al panel principal
if (isset($_SESSION['usuario_rut'])) {
    header("Location: panel.php");
    exit;
}

require 'conexion.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    try {
        $sql = "SELECT rut, nombre, id_rol, password_hash FROM usuario WHERE correo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            if ($usuario['id_rol'] == 3) {
                $_SESSION['usuario_rut'] = $usuario['rut'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['id_rol'];
                $tipo_evento = "Inicio de sesión"; 
                $detalle_evento = "El administrador accedió al sistema.";
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
                header("Location: panel.php");
                exit;
                
            } else {
                $error = "Correo o contraseña incorrectos.";
            }

        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $error = "Error de base de datos: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Universidad de Los Lagos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-wrapper">
    <form method="POST" action="index.php">
        
        <div class="login-header">
            <img src="media/ulagos.svg" alt="Logo Universidad de Los Lagos" class="login-logo">
            <h2>Iniciar Sesión</h2>
        </div>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <label>Correo Electrónico:</label>
        <input type="email" name="correo" placeholder="ejemplo@ulagos.cl" required>
        
        <label>Contraseña:</label>
        <input type="password" name="password" placeholder="••••••••" required>
        
        <br><br>
        <button type="submit" class="btn-submit" style="width: 100%;">Ingresar</button>
    </form>
</div>

</body>
</html>