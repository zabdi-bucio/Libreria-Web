<?php
// login.php
// ============================================================
// Inicio de Sesión - Paso 2 & 3
// ============================================================
require_once 'config/conexion.php';
require_once 'config/sesion.php';

// Si ya está autenticado, redirigir al inicio
if (estaAutenticado()) {
    header('Location: index.html');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = trim($_POST['nombre_usuario'] ?? '');
    $contrasena    = $_POST['contrasena'] ?? '';

    if ($nombreUsuario && $contrasena) {
        $pdo  = Conexion::obtenerInstancia()->getPDO();
        $stmt = $pdo->prepare(
            "SELECT id, nombre_usuario, password_hash, rol, activo
             FROM usuarios WHERE nombre_usuario = :n LIMIT 1"
        );
        $stmt->execute([':n' => $nombreUsuario]);
        $usuario = $stmt->fetch();

        if ($usuario && $usuario['activo'] && password_verify($contrasena, $usuario['password_hash'])) {
            iniciarSesionUsuario($usuario);
            header('Location: index.html');
            exit;
        }
    }
    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="unsafe-url">
    <title>Iniciar Sesión - BiblioTec</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="formularios.css">
</head>
<body>
<header>
    <h1>Iniciar Sesión</h1>
    <img src="img/logo.png" width="200" alt="Logo BiblioTec">
</header>
<nav>
    <a href="index.php">Inicio</a>
    <a href="mision.php">Misión</a>
    <a href="vision.php">Visión</a>
    <a href="catalogo.php">Catálogo</a>
    <a href="contacto.php">Contacto</a>
    <a href="registro.php">Registrarse</a>
    <a href="gestion.php">Gestión de Productos</a> 
</nav>
<main>
    <?php if ($error): ?>
        <p style="color:#e74c3c;font-weight:bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form id="formLogin" action="login.php" method="POST">
        <fieldset>
            <legend>Acceso al Sistema</legend>

            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario"
                   required minlength="3" autocomplete="username">

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena"
                   required autocomplete="current-password">

            <button type="submit">Entrar</button>
        </fieldset>
    </form>
    <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
</main>
<footer>
    <p>&copy; 2026 BiblioTec.</p>
    <a href="https://validator.w3.org/nu/?doc=https://zabdi-bucio.github.io/Libreria-Web/login.php" target="_blank">
        <img src="img/valid_HTML5.png" alt="¡HTML 5 Válido!" height="31" width="88" style="border:0;">
    </a>
    <a href="https://jigsaw.w3.org/css-validator/validator?uri=https://zabdi-bucio.github.io/Libreria-Web/login.php" target="_blank">
        <img src="img/valid_CSS.png" alt="¡CSS Válido!" height="31" width="88" style="border:0;">
    </a>
</footer>
</body>
</html>
