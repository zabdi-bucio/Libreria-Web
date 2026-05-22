<?php
require_once 'config/sesion.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$carrito = $_SESSION['carrito'];
$total = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito - BiblioTec</title>
    <link rel="stylesheet" href="estilos.css">

    <style>
        .carrito-container{
            max-width:1000px;
            margin:40px auto;
            padding:20px;
        }

        .carrito-item{
            background:white;
            padding:20px;
            margin-bottom:20px;
            border-radius:12px;
            box-shadow:0 4px 10px rgba(0,0,0,.08);

            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:20px;
            flex-wrap:wrap;
        }

        .carrito-item h3{
            margin:0;
            color:#2c3e50;
        }

        .precio{
            color:#1abc9c;
            font-size:1.2em;
            font-weight:bold;
        }

        .btn-eliminar{
            background:#e74c3c;
            color:white;
            border:none;
            padding:10px 16px;
            border-radius:8px;
            cursor:pointer;
        }

        .btn-vaciar{
            background:#34495e;
            color:white;
            border:none;
            padding:12px 18px;
            border-radius:10px;
            cursor:pointer;
            margin-top:20px;
        }

        .total{
            margin-top:30px;
            font-size:1.5em;
            font-weight:bold;
            color:#2c3e50;
            text-align:right;
        }
    </style>
</head>
<body>

<header>
    <h1>Carrito de Compras</h1>
</header>

<?php require_once 'config/nav.php'; ?>

<main class="carrito-container">

<?php if (empty($carrito)): ?>

    <div class="empty-state">
    <h2>Tu carrito está vacío 🛒</h2>
    <p>Explora el catálogo y agrega libros para continuar.</p>
    <a class="btn-link" href="catalogo.php">Ver catálogo</a>
</div>

<?php else: ?>

    <?php foreach ($carrito as $index => $item): ?>

        <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
        <?php $total += $subtotal; ?>

        <div class="carrito-item">

            <div>
                <h3><?= htmlspecialchars($item['titulo']) ?></h3>

                <p>
                    Cantidad: <?= $item['cantidad'] ?>
                </p>

                <p class="precio">
                    $<?= number_format($subtotal, 2) ?>
                </p>
            </div>

            <form method="POST" action="quitar_carrito.php">
                <input type="hidden" name="index" value="<?= $index ?>">
                <button class="btn-eliminar">
                    Eliminar
                </button>
            </form>

        </div>

    <?php endforeach; ?>

    <div class="total">
        Total: $<?= number_format($total, 2) ?>
    </div>

    <form method="POST" action="vaciar_carrito.php">
        <button class="btn-vaciar">
            Vaciar carrito
        </button>
    </form>

<?php endif; ?>

</main>

</body>
</html>
