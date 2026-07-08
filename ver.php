<?php
session_start();
if (!isset($_SESSION['usuario_rut'])) {
    header("Location: index.php");
    exit;
}

require 'conexion.php';

$sql = "SELECT u.rut, u.nombre, u.correo, r.nombre AS rol, d.nombre AS departamento 
        FROM usuario u
        JOIN rol r ON u.id_rol = r.id
        JOIN departamento d ON u.id_departamento = d.id";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tipo_evento = "Consultar registro"; 
        $detalle_evento = "Tabla usuario, lectura general de lista.";
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
        $stmt_log->execute([$tipo_evento, $detalle_evento, $usuario_responsable, $ip_cliente]);

include 'layout/header.php';
?>

<h2>Lista de Usuarios</h2>
<table>
    <thead>
        <tr>
            <th>RUT</th>
            <th>NOMBRE COMPLETO</th>
            <th>CORREO</th>
            <th>ROL</th>
            <th>DEPARTAMENTO</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['rut']) ?></td>
            <td><?= htmlspecialchars($u['nombre']) ?></td>
            <td><?= htmlspecialchars($u['correo']) ?></td>
            <td><?= htmlspecialchars($u['rol']) ?></td>
            <td><?= htmlspecialchars($u['departamento']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'layout/footer.php'; ?>