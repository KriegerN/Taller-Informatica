<?php
require 'conexion.php';

if (!isset($_GET['rut'])) {
    header("Location: index.php");
    exit;
}

$rut_actual = $_GET['rut'];

$sql_select = "SELECT * FROM usuario WHERE rut = ?";
$stmt_select = $pdo->prepare($sql_select);
$stmt_select->execute([$rut_actual]);
$usuario = $stmt_select->fetch(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $id_rol = $_POST['id_rol'];
    $id_departamento = $_POST['id_departamento'];

    $sql_update = "UPDATE usuario SET nombre = ?, correo = ?, id_rol = ?, id_departamento = ? WHERE rut = ?";
    $stmt_update = $pdo->prepare($sql_update);
    
    try {
        $stmt_update->execute([$nombre, $correo, $id_rol, $id_departamento, $rut_actual]);
        header("Location: editar_lista.php");
        exit;
    } catch (Exception $e) {
        $error = "Error al actualizar: " . $e->getMessage();
    }
}

include 'layout/header.php'; ?>

<div class="panel-header-crear-editar">
    <h2>Editar Usuario: <?=$rut_actual?></h2>
</div>

<?php if(isset($error)) echo "<p style='color:red; font-weight:bold;'>$error</p>"; ?>

<form method="POST" action="">
    <label>Nombre Completo:</label>
    <input type="text" name="nombre" value="<?=$usuario['nombre']?>" maxlength=100 required>

    <label>Correo:</label>
    <input type="email" name="correo" value="<?=$usuario['correo']?>" maxlength=100 required>

    <label>Rol:</label>
    <select name="id_rol">
        <option value="1" <?= $usuario['id_rol'] == 1 ? 'selected' : '' ?>>Estudiante</option>
        <option value="2" <?= $usuario['id_rol'] == 2 ? 'selected' : '' ?>>Profesor</option>
    </select>

    <label>Departamento:</label>
    <select name="id_departamento">
        <option value="1" <?= $usuario['id_departamento'] == 1 ? 'selected' : '' ?>>Informática</option>
        <option value="2" <?= $usuario['id_departamento'] == 2 ? 'selected' : '' ?>>Derecho</option>
        <option value="3" <?= $usuario['id_departamento'] == 3 ? 'selected' : '' ?>>Obstetricia</option>
    </select>
    <br>
    <br>
    <button type="submit" class="btn-submit">Actualizar Datos</button>

    <a href="index.php" class="btn-cancel">Cancelar</a>
</form>

<?php include 'layout/footer.php'; ?>