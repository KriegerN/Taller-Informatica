<?php
session_start();
if (!isset($_SESSION['usuario_rut'])) {
    header("Location: index.php");
    exit;
}

require 'conexion.php';

if (!isset($_GET['rut'])) {
    header("Location: editar_lista.php");
    exit;
}

$rut_actual = $_GET['rut'];

// Evitar auto edicion
if ($rut_actual === $_SESSION['usuario_rut']) {
    header("Location: editar_lista.php");
    exit;
}

$sql_select = "SELECT * FROM usuario WHERE rut = ? AND id_rol!=3";
$stmt_select = $pdo->prepare($sql_select);
$stmt_select->execute([$rut_actual]);
$usuario = $stmt_select->fetch(PDO::FETCH_ASSOC);
if (!$usuario) {
    header("Location: editar_lista.php");
    exit;
}
$error = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $id_rol = $_POST['id_rol'];
    $id_departamento = $_POST['id_departamento'];

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    }

    if (empty($error)) {
        $sql_check = "SELECT rut FROM usuario WHERE correo = ? AND rut != ?";
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->execute([$correo, $rut_actual]);
        
        if ($stmt_check->fetch()) {
            $error = "El correo electrónico ya está siendo utilizado por otro usuario.";
        }
    }

    if (empty($error)) {
        $nombres_roles = [1 => 'Estudiante', 2 => 'Profesor', 3 => 'Administrador'];
        $nombres_deptos = [1 => 'Informática', 2 => 'Derecho', 3 => 'Obstetricia'];

        $cambios = [];
        if ($usuario['nombre'] != $nombre) {
            $cambios[] = "Nombre ({$usuario['nombre']} -> $nombre)";
        }
        if ($usuario['correo'] != $correo) {
            $cambios[] = "Correo ({$usuario['correo']} -> $correo)";
        }
        if ($usuario['id_rol'] != $id_rol) {
            $rol_antiguo = $nombres_roles[$usuario['id_rol']];
            $rol_nuevo = $nombres_roles[$id_rol];
            $cambios[] = "Rol ($rol_antiguo -> $rol_nuevo)";
        }
        if ($usuario['id_departamento'] != $id_departamento) {
            $depto_antiguo = $nombres_deptos[$usuario['id_departamento']];
            $depto_nuevo = $nombres_deptos[$id_departamento];
            $cambios[] = "Depto ($depto_antiguo -> $depto_nuevo)";
        }

        $texto_cambios = empty($cambios) ? "Sin cambios" : implode(", ", $cambios);
        
        $sql_update = "UPDATE usuario SET nombre = ?, correo = ?, id_rol = ?, id_departamento = ? WHERE rut = ?";
        $stmt_update = $pdo->prepare($sql_update);
        
        try {
            $stmt_update->execute([$nombre, $correo, $id_rol, $id_departamento, $rut_actual]);
            
            $tipo_evento = "Modificar registro"; 
            $detalle_evento = "Tabla usuario (RUT: $rut_actual). Cambios: " . $texto_cambios;
            
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
            
            header("Location: editar_lista.php");
            exit;
        } catch (Exception $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    }
}

include 'layout/header.php'; 
?>

<div class="panel-header-crear-editar">
    <h2>Editar Usuario: <?=$rut_actual?></h2>
</div>

<?php if(!empty($error)) echo "<p style='color:red; font-weight:bold; text-align:center;'>$error</p>"; ?>

<form method="POST" action="">
    <label>Nombre Completo:</label>
    <input type="text" name="nombre" value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : htmlspecialchars($usuario['nombre']) ?>" maxlength="100" required>

    <label>Correo:</label>
    <input type="email" name="correo" value="<?= isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : htmlspecialchars($usuario['correo']) ?>" maxlength="100" required>

    <label>Rol:</label>
    <?php $rol_seleccionado = isset($_POST['id_rol']) ? $_POST['id_rol'] : $usuario['id_rol']; ?>
    <select name="id_rol">
        <option value="1" <?= $rol_seleccionado == 1 ? 'selected' : '' ?>>Estudiante</option>
        <option value="2" <?= $rol_seleccionado == 2 ? 'selected' : '' ?>>Profesor</option>
        <option value="3" <?= $rol_seleccionado == 3 ? 'selected' : '' ?>>Administrador</option>
    </select>

    <label>Departamento:</label>
    <?php $depto_seleccionado = isset($_POST['id_departamento']) ? $_POST['id_departamento'] : $usuario['id_departamento']; ?>
    <select name="id_departamento">
        <option value="1" <?= $depto_seleccionado == 1 ? 'selected' : '' ?>>Informática</option>
        <option value="2" <?= $depto_seleccionado == 2 ? 'selected' : '' ?>>Derecho</option>
        <option value="3" <?= $depto_seleccionado == 3 ? 'selected' : '' ?>>Obstetricia</option>
    </select>
    <br><br>
    
    <button type="submit" class="btn-submit">Actualizar Datos</button>
    <a href="editar_lista.php" class="btn-cancel">Cancelar</a>
</form>

<?php include 'layout/footer.php'; ?>