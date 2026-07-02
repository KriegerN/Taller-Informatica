<?php
require 'conexion.php';

$sql = "SELECT rut, nombre FROM usuario";
$stmt = $pdo->query($sql);
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