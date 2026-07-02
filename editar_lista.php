<?php
require 'conexion.php';

$sql = "SELECT rut, nombre FROM usuario";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'layout/header.php';
?>

<h2>Seleccionar Usuario para Editar</h2>
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
                <a href="editar.php?rut=<?= urlencode($u['rut']) ?>" class="btn-submit" style="padding: 6px 12px; font-size: 14px;">
                    <img src="media/editar.svg" class="icono-accion" alt="Editar"> Seleccionar
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'layout/footer.php'; ?>