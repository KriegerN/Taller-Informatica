<?php
session_start();
if (!isset($_SESSION['usuario_rut'])) {
    header("Location: index.php");
    exit;
}

require 'conexion.php';

$sql = "SELECT tipo, detalle, usuario, fecha_hora, IP FROM registro ORDER BY fecha_hora DESC";
$stmt = $pdo->query($sql);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'layout/header.php';
?>

<div class="panel-header-crear-editar">
    <h2>Bitácora de Eventos del Sistema</h2>
</div>

<table>
    <thead>
        <tr>
            <th>FECHA Y HORA</th>
            <th>USUARIO</th>
            <th>TIPO DE EVENTO</th>
            <th>DETALLE</th>
            <th>IP CLIENTE</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $r): ?>
        <tr>
            <td><?= date('d/m/Y, H:i:s', strtotime($r['fecha_hora'])) ?></td>
            <td><?= htmlspecialchars($r['usuario']) ?></td>
            <td><?= htmlspecialchars($r['tipo']) ?></td>
            <td><?= htmlspecialchars($r['detalle']) ?></td>
            <td><?= htmlspecialchars($r['ip']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($registros)): ?>
        <tr>
            <td colspan="5" style="text-align: center;">No hay eventos registrados en la bitácora aún.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'layout/footer.php'; ?>