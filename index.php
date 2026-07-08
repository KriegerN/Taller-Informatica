<?php
session_start();

// Si el usuario ya tiene sesión iniciada, el sistema no lo deja ver el login y lo manda al panel
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
        // Buscamos al usuario por su correo
        $sql = "SELECT rut, nombre, id_rol, password_hash FROM usuario WHERE correo = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();

        // Validamos si el usuario existe y si la contraseña coincide con el hash guardado
        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            
            // Credenciales correctas: Creamos la sesión
            $_SESSION['usuario_rut'] = $usuario['rut'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_rol'] = $usuario['id_rol'];
            
            header("Location: panel.php");
            exit;
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
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        .login-box input { width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .login-box button { width: 100%; padding: 10px; background-color: #0056b3; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .login-box button:hover { background-color: #004494; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Iniciar Sesión</h2>
    
    <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <input type="email" name="correo" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>
</div>

</body>
</html>