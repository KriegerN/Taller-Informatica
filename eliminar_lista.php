<?php

session_start();
if (!isset($_SESSION['usuario_rut'])) {
    header("Location: index.php");
    exit;
}

require 'conexion.php';

$mi_rut = $_SESSION['usuario_rut'];

$sql = "SELECT * FROM usuario WHERE rut != ? AND id_rol!=3";
$stmt = $pdo->prepare($sql);
$stmt->execute([$mi_rut]);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'layout/header.php';
?>

<h2>Seleccionar Usuario para Eliminar</h2>
<table>
    <thead>
        <tr>
            <th>RUT</th>
            <th>NOMBRE COMPLETO</th>
            <th>ACCIÓN</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['rut']) ?></td>
            <td><?= htmlspecialchars($u['nombre']) ?></td>
            <td>
                <a href="eliminar.php?rut=<?= urlencode($u['rut']) ?>" onclick="return confirm('¿Seguro que deseas eliminar a este usuario?');" class="btn-submit" style="background-color: #d9534f; padding: 6px 12px; font-size: 14px;">
                    <img src="media/eliminar.svg" class="icono-accion" alt="Eliminar"> Eliminar
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'layout/footer.php'; ?>