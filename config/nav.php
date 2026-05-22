<?php
require_once __DIR__ . '/sesion.php';


$total_carrito = 0;

if (isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total_carrito += (int)($item['cantidad'] ?? 0);
    }
}

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
    <a href="<?= $base_url ?>carrito.php">
        Carrito 🛒
        <?php if ($total_carrito > 0): ?>
            <span class="carrito-badge"><?= $total_carrito ?></span>
        <?php endif; ?>
    </a>

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