<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - CRUD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="top-header">
        <div class="container flex-header">
            <div class="logo-title">
                <img src="media/ulagos.svg" class="logo-img" alt="Logo">
                <span>Universidad de Los Lagos</span>
            </div>
            
            <?php if(isset($_SESSION['usuario_nombre'])): ?>
                <div class="header-user">
                    <span style="font-weight: bold; margin-right: 15px;">
                        Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
                    </span>
                    <a href="logout.php" style="color: #ffffff; text-decoration: none; font-weight: bold; border: 1px solid #ffffff; padding: 6px 12px; border-radius: 4px; background-color: rgba(255,255,255,0.1);">
                        Cerrar Sesión
                    </a>
                </div>
            <?php endif; ?>
            
        </div>
    </header>
    
    <?php if(isset($_SESSION['usuario_nombre'])): ?>
        <nav class="nav-menu">
            <div class="container flex-nav">
                <a href="panel.php">Bitácora</a>
                <a href="crear.php">Crear Usuario</a>
                <a href="ver.php">Ver Usuarios</a>
                <a href="editar_lista.php">Editar Usuario</a>
                <a href="eliminar_lista.php">Eliminar Usuario</a>
            </div>
        </nav>
    <?php endif; ?>

    <div class="container">