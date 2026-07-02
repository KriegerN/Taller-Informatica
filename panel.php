<?php
// llamamos al archivo que tiene la conexion a postgres
require 'conexion.php';

// armamos la consulta 
// usamos join para traer el texto del rol y el departamento 
$sql = "SELECT u.rut, u.nombre, u.correo, r.nombre AS rol, d.nombre AS departamento 
        FROM usuario u
        JOIN rol r ON u.id_rol = r.id
        JOIN departamento d ON u.id_departamento = d.id";

// mandamos la consulta a la base de datos
$stmt = $pdo->query($sql);
// sacamos todas las filas y las guardamos en este arreglo para usarlas mas abajo
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'layout/header.php'; ?>

<div class="panel-header">
    <h2>Panel de Control</h2>
    <a href="crear.php" class="btn-add">+ Añadir Nuevo Usuario</a>
</div>

<table>
    <thead>
        <tr>
            <th>RUT</th>
            <th>NOMBRE COMPLETO</th>
            <th>CORREO</th>
            <th>ROL</th>
            <th>DEPARTAMENTO</th>
            <th>ACCIONES</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
        <tr>
            <td><?= $u['rut'] ?></td>
            <td><?= $u['nombre'] ?></td>
            <td><?= $u['correo'] ?></td>
            <td><?= $u['rol'] ?></td>
            <td><?= $u['departamento'] ?></td>
            <td class="acciones">
                
                <a href="editar.php?rut=<?= urlencode($u['rut']) ?>" title="Editar">
                    <img  src="media/editar.svg" alt="Editar" class="icono-accion">
                </a>
                <a href="eliminar.php?rut=<?= urlencode($u['rut']) ?>" onclick="return confirm('¿Seguro que deseas eliminar a este usuario?');" title="Eliminar">
                    <img  src="media/eliminar.svg" alt="Eliminar" class="icono-accion">
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php include 'layout/footer.php'; ?>