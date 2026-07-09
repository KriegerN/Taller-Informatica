<?php
session_start();
require 'conexion.php';

// Si ya tiene sesión, lo mandamos al panel
if (isset($_SESSION['usuario_rut'])) {
    header("Location: panel.php");
    exit;
}

$error = '';
$exito = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut = trim($_POST['rut']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password_plana = trim($_POST['password']);
    $clave_secreta = trim($_POST['clave_secreta']);
   
    $id_rol = 3; 
    $id_departamento = $_POST['id_departamento'];

    if ($clave_secreta !== 'Ulagos2026$') {
        $error = "La clave de autorización es incorrecta. Acceso denegado.";
    }

    if (empty($error) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    }

    if (empty($error)) {
        $sql_check = "SELECT rut, correo FROM usuario WHERE rut = ? OR correo = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$rut, $correo]);
        $duplicado = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($duplicado) {
            if ($duplicado['rut'] === $rut) {
                $error = "Este RUT ya está registrado. Inicia sesión.";
            } elseif ($duplicado['correo'] === $correo) {
                $error = "El correo electrónico ya está en uso.";
            }
        }
    }

    if (empty($error)) {
        $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (rut, nombre, correo, password_hash, id_rol, id_departamento) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$rut, $nombre, $correo, $password_hash, $id_rol, $id_departamento]);
            
            $tipo_evento = "Creación de usuario"; 
            $detalle_evento = "Auto-registro autorizado. Creado: $nombre (RUT: $rut, Rol: Administrador)";
            
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_cliente = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip_cliente = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip_cliente = $_SERVER['REMOTE_ADDR'];
            }

            $usuario_responsable = "SISTEMA";
            
            $sql_log = "INSERT INTO registro (tipo, detalle, usuario, IP) VALUES (?, ?, ?, ?)";
            $stmt_log = $pdo->prepare($sql_log);
            $stmt_log->execute([$tipo_evento, $detalle_evento, $usuario_responsable, trim($ip_cliente)]);
            $exito = "Cuenta de Administrador creada exitosamente. Ya puedes iniciar sesión.";
            
            $_POST = array();
            
        } catch (Exception $e) {
            $error = "Error al registrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Universidad de Los Lagos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-wrapper">
    <form method="POST" action="registro.php">
        
        <div class="login-header">
            <img src="media/ulagos.svg" alt="Logo Universidad de Los Lagos" class="login-logo">
            <h2>Crear Cuenta</h2>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-msg" style="color: red; text-align: center; margin-bottom: 15px; font-weight: bold;"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($exito)): ?>
            <div class="success-msg" style="color: green; text-align: center; margin-bottom: 15px; font-weight: bold;"><?php echo $exito; ?></div>
        <?php endif; ?>

        <label>RUT (ej: 12345678-9):</label>
        <input type="text" name="rut" maxlength="10" value="<?= isset($_POST['rut']) ? htmlspecialchars($_POST['rut']) : '' ?>" required>

        <label>Nombre Completo:</label>
        <input type="text" name="nombre" maxlength="100" value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>" required>

        <label>Correo Electrónico:</label>
        <input type="email" name="correo" maxlength="100" value="<?= isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : '' ?>" required>

        <label>Contraseña:</label>
        <input type="password" name="password" required>

        <label>Departamento:</label>
        <select name="id_departamento">
            <option value="1">Informática</option>
            <option value="2">Derecho</option>
            <option value="3">Obstetricia</option>
        </select>
        
        <div style="margin: 15px 0; padding: 10px; background-color: #e2e3e5; border-radius: 5px; border: 1px solid #d6d8db;">
            <input type="checkbox" name="confirmacion_admin" id="confirmacion_admin" required onchange="document.getElementById('caja_clave').style.display = this.checked ? 'block' : 'none';">
            <label for="confirmacion_admin" style="margin: 0; font-weight: bold; cursor: pointer; color: #383d41;">
                Confirmo que soy personal autorizado para registrar una cuenta de Administrador.
            </label>
            
            <div id="caja_clave" style="display: <?= isset($_POST['confirmacion_admin']) ? 'block' : 'none' ?>; margin-top: 15px; border-top: 1px solid #c8cbcf; padding-top: 10px;">
                <label style="color: #495057; margin-bottom: 5px; font-weight: bold; font-size: 0.9em;">Clave de Autorización Institucional:</label>
                <input type="password" name="clave_secreta" placeholder="Ingresa la clave del equipo" style="margin-bottom: 0;">
            </div>
        </div>
        
        <button type="submit" class="btn-submit" style="width: 100%;">Registrarme</button>
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="index.php" style="color: #666; text-decoration: none;">Volver al inicio de sesión</a>
        </div>
    </form>
</div>

</body>
</html>