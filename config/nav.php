<?php
require_once __DIR__ . '/sesion.php';

$base_url = '/';
?>

<?php if (estaAutenticado()): ?>
<div class="topbar">
    <span>
        Sesión: <strong><?= htmlspecialchars($_SESSION['nombre_usuario']) ?></strong>
        &nbsp;|&nbsp;
        Rol: <strong><?= htmlspecialchars(ucfirst($_SESSION['rol'])) ?></strong>
    </span>
    <a href="<?= $base_url ?>logout.php">Cerrar sesión</a>
</div>
<?php endif; ?>

<nav class="navbar">
    <a href="<?= $base_url ?>index.php">Inicio</a>
    <a href="<?= $base_url ?>mision.php">Misión</a>
    <a href="<?= $base_url ?>vision.php">Visión</a>
    <a href="<?= $base_url ?>catalogo.php">Catálogo</a>
    <a href="<?= $base_url ?>contacto.php">Contacto</a>
    <a href="<?= $base_url ?>carrito.php">Carrito</a>

    <?php if (tieneRol('admin', 'editor')): ?>
        <a href="<?= $base_url ?>gestion.php">Inventario</a>
    <?php endif; ?>

    <?php if (tieneRol('admin')): ?>
        <a href="<?= $base_url ?>gestion_usuarios.php">Usuarios</a>
    <?php endif; ?>

    <?php if (!estaAutenticado()): ?>
        <a href="<?= $base_url ?>login.php">Iniciar Sesión</a>
        <a href="<?= $base_url ?>registro.php">Registro</a>
    <?php endif; ?>
</nav>