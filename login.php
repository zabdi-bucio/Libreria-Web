<?php
require_once 'config/conexion.php';
require_once 'config/sesion.php';

// Si ya tiene sesión, mandarlo al inicio
if (estaAutenticado()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['nombre_usuario'] ?? '');
    $pass = $_POST['contrasena'] ?? '';

    if ($user && $pass) {
        $pdo = Conexion::obtenerInstancia()->getPDO();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ? AND activo = 1 LIMIT 1");
        $stmt->execute([$user]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($pass, $usuario['password_hash'])) {
            iniciarSesionUsuario($usuario);
            header('Location: index.php');
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Por favor ingresa todos los datos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - BiblioTec</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="formularios.css">
</head>
<body>
    <header>
        <h1>Iniciar Sesión</h1>
        <img src="img/logo.png" width="200" alt="Logo BiblioTec">
    </header>
    
    <?php require_once 'config/nav.php'; ?>
    
    <main>
        <?php if ($error): ?>
            <p style="color:#e74c3c; font-weight:bold; text-align:center; background:#fadbd8; padding:10px; border-radius:5px;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form action="login.php" method="POST" style="max-width: 400px; margin: 20px auto;">
            <fieldset>
                <legend>Tus Credenciales</legend>
                <label for="nombre_usuario">Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
                
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
                
                <button type="submit">Entrar</button>
            </fieldset>
        </form>
    </main>
    <footer>
        <p>&copy; 2026 BiblioTec.</p>
    </footer>
</body>
</html>