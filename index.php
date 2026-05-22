<?php require_once 'config/sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="unsafe-url">
    <title>Librería BiblioTec</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .bienvenida-caja {
            background-color: #1abc9c;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 600px;
            display: none;
            box-shadow: 0 4px 10px rgba(26, 188, 156, 0.3);
        }
        #btn-magia {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            margin-top: 15px;
            font-size: 16px;
            font-weight: bold;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s;
        }
        #btn-magia:hover { background-color: #c0392b; }
    </style>
</head>
<body>
    <header>
        <h1>Librería BiblioTec</h1>
        <img src="img/logo.png" width="200" alt="Logo BiblioTec">
    </header>

    <?php require_once 'config/nav.php'; ?>

    <main id="contenido-principal">
        <h2>Tu librería de confianza</h2>
        <p>Bienvenido al sitio web de BiblioTec. Explora nuestro catálogo y descubre mundos increíbles.</p>
    </main>

    <?php require_once 'config/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function(){
            let boton = '<button id="btn-magia">Descubre nuestra oferta de hoy</button>';
            let caja  = '<div class="bienvenida-caja">¡Hoy tenemos 20% de descuento en todos los libros de programación!</div>';
            $("#contenido-principal").append(boton).append(caja);
            $("#btn-magia").click(function(){
                $(".bienvenida-caja").slideToggle("fast");
            });
        });
    </script>
</body>
</html>
