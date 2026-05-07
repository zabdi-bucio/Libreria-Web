<?php
// catalogo.php
// ============================================================
// Catálogo de Libros - Datos desde la Base de Datos (Paso 1)
// ============================================================
require_once 'config/conexion.php';
require_once 'config/sesion.php';

$pdo    = Conexion::obtenerInstancia()->getPDO();
$libros = $pdo->query(
    "SELECT isbn, titulo, autor, precio FROM libros WHERE activo=1 ORDER BY titulo"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="unsafe-url">
    <title>Catálogo</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        #galeria-libros {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1000px;
            margin: 0 auto;
            text-align: center;
        }
        .libro-item {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }
        .libro-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.1);
        }
        .libro-item img {
            border-radius: 8px;
            max-width: 100%;
            height: auto;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            margin-bottom: 15px;
        }
        .libro-item h2 { font-size:1.2em; color:#2c3e50; margin-bottom:5px; }
        .libro-item p  { font-style:italic; color:#7f8c8d; font-size:0.9em; margin:0; }
        .precio { color:#1abc9c; font-weight:600; font-style:normal !important; margin-top:8px !important; }
    </style>
</head>
<body>
<header>
    <h1>Catálogo de Libros</h1>
    <img src="img/logo.png" width="200" alt="Logo BiblioTec">
</header>
<nav>
    <a href="index.php">Inicio</a>
    <a href="mision.php">Misión</a>
    <a href="vision.php">Visión</a>
    <a href="catalogo.php">Catálogo</a>
    <a href="contacto.php">Contacto</a>
    <?php if (estaAutenticado()): ?>
        <a href="gestion.php">Gestión de Productos</a>
        <a href="logout.php">Cerrar Sesión</a>
    <?php else: ?>
        <a href="login.php">Iniciar Sesión</a>
    <?php endif; ?>
</nav>
<main>
    <div id="galeria-libros" style="display:none;">
        <?php if (empty($libros)): ?>
            <p>No hay libros disponibles en este momento.</p>
        <?php else: ?>
            <?php foreach ($libros as $i => $libro): ?>
                <div class="libro-item">
                    <h2>Libro <?= $i + 1 ?></h2>
                    <img src="img/libro<?= $i + 1 ?>.jpeg" width="200"
                         alt="Portada de <?= htmlspecialchars($libro['titulo']) ?>">
                    <p>"<?= htmlspecialchars($libro['titulo']) ?>" -
                       <?= htmlspecialchars($libro['autor']) ?></p>
                    <p class="precio">$<?= number_format($libro['precio'], 2) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
<footer>
    <p>&copy; 2026 BiblioTec.</p>
    <a href="https://validator.w3.org/nu/?doc=https://zabdi-bucio.github.io/Libreria-Web/catalogo.php" target="_blank">
        <img src="img/valid_HTML5.png" alt="¡HTML 5 Válido!" height="31" width="88" style="border:0;">
    </a>
    <a href="https://jigsaw.w3.org/css-validator/validator?uri=https://zabdi-bucio.github.io/Libreria-Web/catalogo.php" target="_blank">
        <img src="img/valid_CSS.png" alt="¡CSS Válido!" height="31" width="88" style="border:0;">
    </a>
</footer>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function(){
        $("#galeria-libros").fadeIn(1200);
    });
</script>
</body>
</html>
