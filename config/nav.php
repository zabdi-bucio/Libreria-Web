<?php
require_once __DIR__ . '/sesion.php';

$base_url = '/';
?>

<?php if (estaAutenticado()): ?>
<div style="background:#1abc9c;color:white;padding:8px 20px;font-size:0.85em;
            display:flex;justify-content:space-between;align-items:center;">
    <span>
         <strong><?= htmlspecialchars($_SESSION['nombre_usuario']) ?></strong>
        &nbsp;|&nbsp;
        Rol: <strong><?= htmlspecialchars(ucfirst($_SESSION['rol'])) ?></strong>
    </span>
    <a href="<?= $base_url ?>logout.php"
       style="color:white;font-weight:600;text-decoration:none;">
        Cerrar sesión
    </a>
</div>
<?php endif; ?>

<nav>
    <a href="<?= $base_url ?>index.php">Inicio</a>
    <a href="<?= $base_url ?>mision.php">Misión</a>
    <a href="<?= $base_url ?>vision.php">Visión</a>
    <a href="<?= $base_url ?>catalogo.php">Catálogo</a>
    <a href="<?= $base_url ?>contacto.php">Contacto</a>

    <?php if (tieneRol('admin', 'editor')): ?>
        <a href="<?= $base_url ?>gestion.php">Gestión de Productos</a>
    <?php endif; ?>

    <?php if (tieneRol('admin')): ?>
        <a href="<?= $base_url ?>gestion_usuarios.php">Usuarios</a>
    <?php endif; ?>

    <?php if (estaAutenticado()): ?>
        <a href="<?= $base_url ?>logout.php">Cerrar Sesión</a>
    <?php else: ?>
        <a href="<?= $base_url ?>login.php">Iniciar Sesión</a>
    <?php endif; ?>
</nav>