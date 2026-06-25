<?php
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut = $_POST['rut'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $id_rol = $_POST['id_rol'];
    $id_departamento = $_POST['id_departamento'];

    $sql = "INSERT INTO usuario (rut, nombre, correo, id_rol, id_departamento) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$rut, $nombre, $correo, $id_rol, $id_departamento]);
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $error = "Error al guardar: " . $e->getMessage();
    }
}

include 'layout/header.php';
?>

<div class="panel-header">
    <h2>Añadir Nuevo Usuario</h2>
</div>

<?php if(isset($error)) echo "<p style='color:red; font-weight:bold;'>$error</p>"; ?>

<form method="POST" action="">
    <label>RUT (ej: 12345678-9):</label>
    <input type="text" name="rut" maxlength=10  required>

    <label>Nombre Completo:</label>
    <input type="text" name="nombre" maxlength=100 required>

    <label>Correo:</label>
    <input type="email" name="correo" maxlength=100 required>

    <label>Rol:</label>
    <select name="id_rol">
        <option value="1">Estudiante</option>
        <option value="2">Profesor</option>
    </select>

    <label>Departamento:</label>
    <select name="id_departamento">
        <option value="1">Informática</option>
        <option value="2">Derecho</option>
        <option value="3">Obstetricia</option>
    </select><br>

    <button type="submit" class="btn-submit">Guardar Usuario</button>
    <a href="index.php" class="btn-cancel">Cancelar</a>
</form>

<?php include 'layout/footer.php'; ?>