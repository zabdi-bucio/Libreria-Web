<?php require_once 'config/nav.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="unsafe-url">
    <title>Misión</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .texto-destacado {
            font-size: 1.2em; line-height: 1.8; color: #2c3e50;
            padding: 30px; background: white; border-left: 6px solid #1abc9c;
            border-radius: 8px; margin: 40px auto; max-width: 700px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: left;
        }
    </style>
</head>
<body>
<header>
    <h1>Misión</h1>
    <img src="img/logo.png" width="200" alt="Logo BiblioTec">
</header>
<main>
    <div id="contenido-mision" style="opacity:0;">
        <div class="texto-destacado">
            <p>Nuestra misión en BiblioTec es satisfacer las necesidades de estudiantes, docentes y lectores, ofreciendo una amplia variedad de títulos, asesoría especializada y productos educativos a precios accesibles.</p>
        </div>
    </div>
</main>
<?php require_once 'config/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function(){
        $("#contenido-mision").animate({opacity: 1}, 1000);
    });
</script>
</body>
</html>
