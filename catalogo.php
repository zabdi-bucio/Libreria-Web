<?php
require_once 'config/conexion.php';
require_once 'config/sesion.php';

$pdo = Conexion::obtenerInstancia()->getPDO();
$libros = $pdo->query(
    "SELECT id, titulo, descripcion, precio, stock FROM libros WHERE activo=1 ORDER BY id ASC"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - BiblioTec</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        #galeria-libros {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .libro-item {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            text-align: center;
        }
        .libro-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.1);
        }
        .libro-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-bottom: 2px solid #f0f4f8;
        }
        .libro-info {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .libro-info h3 { font-size:1.3em; color:#2c3e50; margin:0 0 10px 0; }
        .libro-info p.desc { color:#7f8c8d; font-size:0.9em; flex-grow:1; text-align:justify; margin-bottom:15px; }
        .precio { color:#1abc9c; font-weight:bold; font-size: 1.4em; margin:10px 0; }
        .stock { font-size:0.9em; font-weight:bold; margin-bottom:15px; }
        .stock.ok { color: #27ae60; }
        .stock.agotado { color: #e74c3c; }
        .btn-comprar {
            background:#1abc9c; color:white; border:none; padding:10px; 
            border-radius:8px; cursor:pointer; font-weight:bold; width:100%;
            transition: background 0.3s;
        }
        .btn-comprar:hover { background:#16a085; }
    </style>
</head>
<body>
    <header>
        <h1>Catálogo de Libros</h1>
        <img src="img/logo.png" width="200" alt="Logo BiblioTec">
    </header>

    <?php require_once 'config/nav.php'; ?>

    <main>
        <div id="galeria-libros" style="display:none;">
            <?php if (empty($libros)): ?>
                <p style="text-align:center; width:100%;">No hay libros disponibles en este momento.</p>
            <?php else: ?>
                <?php foreach ($libros as $libro): ?>
                    <div class="libro-item">
                        <img src="img/libro<?= $libro['id'] ?>.jpeg" 
                             alt="Portada de <?= htmlspecialchars($libro['titulo']) ?>"
                             onerror="this.src='img/logo.png'">
                        
                        <div class="libro-info">
                            <span style="font-size:0.8em; color:#bdc3c7;">ID: <?= $libro['id'] ?></span>
                            <h3><?= htmlspecialchars($libro['titulo']) ?></h3>
                            <p class="desc"><?= nl2br(htmlspecialchars($libro['descripcion'] ?? 'Sin descripción.')) ?></p>
                            
                            <p class="precio">$<?= number_format($libro['precio'], 2) ?></p>
                            
                            <?php if ($libro['stock'] > 0): ?>
                                <p class="stock ok">Disponibles: <?= $libro['stock'] ?></p>
                                <form method="POST" action="agregar_carrito.php">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= $libro['id'] ?>"
                                    >

                                    <button class="btn-comprar">
                                        Agregar al carrito
                                    </button>

                                </form>
                            <?php else: ?>
                                <p class="stock agotado">AGOTADO</p>
                                <button class="btn-comprar" style="background:#bdc3c7; cursor:not-allowed;" disabled>No disponible</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 BiblioTec.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#galeria-libros").fadeIn(1000);
        });
    </script>
</body>
</html>