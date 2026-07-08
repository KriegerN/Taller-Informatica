<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_rut'])) {
    header("Location: index.php");
    exit;
}

$error = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut = trim($_POST['rut']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $id_rol = $_POST['id_rol'];
    $id_departamento = $_POST['id_departamento'];

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    }

    if (empty($error)) {
        $sql_check = "SELECT rut, correo FROM usuario WHERE rut = ? OR correo = ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$rut, $correo]);
        $duplicado = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($duplicado) {
            if ($duplicado['rut'] === $rut) {
                $error = "El RUT ingresado ya está registrado en el sistema.";
            } elseif ($duplicado['correo'] === $correo) {
                $error = "El correo electrónico ya está siendo utilizado por otro usuario.";
            }
        }
    }


    if (empty($error)) {
        $password_plana = $rut; //  temporal, tal como lo hace idelfos con los nuevos estudiantes
        $password_hash = password_hash($password_plana, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuario (rut, nombre, correo, password_hash, id_rol, id_departamento) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        try {
            $stmt->execute([$rut, $nombre, $correo, $password_hash, $id_rol, $id_departamento]);
            
            $nombres_roles = [1 => 'Estudiante', 2 => 'Profesor', 3 => 'Administrador'];
            $nombres_deptos = [1 => 'Informática', 2 => 'Derecho', 3 => 'Obstetricia'];
            $rol_texto = $nombres_roles[$id_rol];
            $depto_texto = $nombres_deptos[$id_departamento];
            
            $tipo_evento = "Creación de usuario"; 
            $detalle_evento = "Tabla usuario. Creado: $nombre (RUT: $rut, Correo: $correo, Rol: $rol_texto, Depto: $depto_texto)";
            
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
            
            header("Location: ver.php");
            exit;
        } catch (Exception $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    }
}

include 'layout/header.php';
?>

<div class="panel-header-crear-editar">
    <h2>Añadir Nuevo Usuario</h2>
</div>

<?php if(!empty($error)) echo "<p style='color:red; font-weight:bold; text-align:center;'>$error</p>"; ?>

<form method="POST" action="">
    <label>RUT (ej: 12345678-9):</label>
    <input type="text" name="rut" maxlength="10" value="<?= isset($_POST['rut']) ? htmlspecialchars($_POST['rut']) : '' ?>" required>

    <label>Nombre Completo:</label>
    <input type="text" name="nombre" maxlength="100" value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>" required>

    <label>Correo:</label>
    <input type="email" name="correo" maxlength="100" value="<?= isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : '' ?>" required>

    <label>Rol:</label>
    <select name="id_rol">
        <option value="1" <?= (isset($_POST['id_rol']) && $_POST['id_rol'] == 1) ? 'selected' : '' ?>>Estudiante</option>
        <option value="2" <?= (isset($_POST['id_rol']) && $_POST['id_rol'] == 2) ? 'selected' : '' ?>>Profesor</option>
        <option value="3" <?= (isset($_POST['id_rol']) && $_POST['id_rol'] == 3) ? 'selected' : '' ?>>Administrador</option>
    </select>

    <label>Departamento:</label>
    <select name="id_departamento">
        <option value="1" <?= (isset($_POST['id_departamento']) && $_POST['id_departamento'] == 1) ? 'selected' : '' ?>>Informática</option>
        <option value="2" <?= (isset($_POST['id_departamento']) && $_POST['id_departamento'] == 2) ? 'selected' : '' ?>>Derecho</option>
        <option value="3" <?= (isset($_POST['id_departamento']) && $_POST['id_departamento'] == 3) ? 'selected' : '' ?>>Obstetricia</option>
    </select>
    <br><br>
    
    <button type="submit" class="btn-submit">Guardar Usuario</button>
    <a href="panel.php" class="btn-cancel">Cancelar</a>
</form>

<?php include 'layout/footer.php'; ?>