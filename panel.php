<?php
session_start();
if (!isset($_SESSION['usuario_rut'])) {
    header("Location: index.php");
    exit;
}

require 'conexion.php';

$registros_por_pagina = 10;

$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_actual < 1) {
    $pagina_actual = 1;
}

$offset = ($pagina_actual - 1) * $registros_por_pagina;

$sql_total = "SELECT COUNT(*) FROM registro";
$stmt_total = $pdo->query($sql_total);
$total_registros = $stmt_total->fetchColumn();
$total_paginas = ceil($total_registros / $registros_por_pagina);

$sql = "SELECT tipo, detalle, usuario, fecha_hora, IP FROM registro ORDER BY fecha_hora DESC LIMIT :limite OFFSET :offset";
$stmt = $pdo->prepare($sql);

$stmt->bindValue(':limite', $registros_por_pagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

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

<?php if ($total_paginas > 1): ?>
<div style="text-align: center; margin-top: 20px; padding-bottom: 20px;">
    
    <?php if ($pagina_actual > 1): ?>
        <a href="?pagina=<?= $pagina_actual - 1 ?>" class="btn-submit" style="padding: 8px 15px; text-decoration: none; font-size: 14px;">&laquo; Anterior</a>
    <?php endif; ?>

    <span style="margin: 0 20px; font-weight: bold; color: #333;">
        Página <?= $pagina_actual ?> de <?= $total_paginas ?>
    </span>

    <?php if ($pagina_actual < $total_paginas): ?>
        <a href="?pagina=<?= $pagina_actual + 1 ?>" class="btn-submit" style="padding: 8px 15px; text-decoration: none; font-size: 14px;">Siguiente &raquo;</a>
    <?php endif; ?>

</div>
<?php endif; ?>

<?php include 'layout/footer.php'; ?>