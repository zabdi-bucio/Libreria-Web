<?php
require_once 'config/conexion.php';
require_once 'config/sesion.php';

$exito = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre_usuario'] ?? '');
    $email     = trim($_POST['email']          ?? '');
    $pass      = $_POST['contrasena']          ?? '';
    $passConf  = $_POST['confirmar_contrasena'] ?? '';
    $rol       = 'lector'; // Por defecto todos se registran como lectores

    // Validaciones
    if (!$nombre || !$email || !$pass) {
        $error = 'Todos los campos son obligatorios.';
    } elseif (strlen($nombre) < 3) {
        $error = 'El nombre de usuario debe tener al menos 3 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido.';
    } elseif (strlen($pass) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($pass !== $passConf) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $pdo = Conexion::obtenerInstancia()->getPDO();

        // Verificar si el nombre o email ya existen
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_usuario=:n OR email=:e LIMIT 1");
        $stmt->execute([':n' => $nombre, ':e' => $email]);

        if ($stmt->fetch()) {
            $error = 'El nombre de usuario o correo ya está registrado.';
        } else {
            // Cifrar contraseña con bcrypt
            $hash = password_hash($pass, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre_usuario, email, password_hash, rol)
                 VALUES (:n, :e, :h, :r)"
            );
            $stmt->execute([':n' => $nombre, ':e' => $email, ':h' => $hash, ':r' => $rol]);
            $exito = '¡Cuenta creada correctamente! Ya puedes iniciar sesión.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="unsafe-url">
    <title>Registro - BiblioTec</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="formularios.css">
    <style>
        #estado-nombre { font-size:0.85em; margin-top:4px; }
        .disponible  { color:#1abc9c; font-weight:600; }
        .no-disponible { color:#e74c3c; font-weight:600; }
    </style>
</head>
<body>
<header>
    <h1>Registro de Usuario</h1>
    <img src="img/logo.png" width="200" alt="Logo BiblioTec">
</header>
<?php require_once 'config/nav.php'; ?>
<main>

    <?php if ($exito): ?>
        <p style="color:#1abc9c;font-weight:bold;font-size:1.1em;"><?= htmlspecialchars($exito) ?></p>
        <p><a href="login.php">Ir al inicio de sesión</a></p>
    <?php else: ?>

        <?php if ($error): ?>
            <p style="color:#e74c3c;font-weight:bold;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form id="formRegistro" action="registro.php" method="POST">
            <fieldset>
                <legend>Crear Cuenta</legend>

                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario"
                       required minlength="3" autocomplete="username"
                       value="<?= htmlspecialchars($_POST['nombre_usuario'] ?? '') ?>">
                <!-- Resultado de verificación AJAX (Paso 3) -->
                <span id="estado-nombre"></span>

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email"
                       required autocomplete="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

                <label for="contrasena">Contraseña (mín. 8 caracteres):</label>
                <input type="password" id="contrasena" name="contrasena"
                       required minlength="8" autocomplete="new-password">

                <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena"
                       required autocomplete="new-password">

                <button type="submit">Crear Cuenta</button>
            </fieldset>
        </form>
        <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>

    <?php endif; ?>
</main>
<?php require_once 'config/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function(){

    let timerNombre;
    $("#nombre_usuario").on("input", function(){
        clearTimeout(timerNombre);
        const nombre = $(this).val().trim();
        const $estado = $("#estado-nombre");

        if (nombre.length < 3) {
            $estado.text('').removeClass('disponible no-disponible');
            return;
        }

        timerNombre = setTimeout(function(){
            $.ajax({
                url: 'api/usuarios.php',
                method: 'GET',
                data: { accion: 'verificar_nombre', nombre_usuario: nombre },
                success: function(resp){
                    if (resp.disponible) {
                        $estado.text(resp.mensaje)
                               .removeClass('no-disponible').addClass('disponible');
                    } else {
                        $estado.text(resp.mensaje)
                               .removeClass('disponible').addClass('no-disponible');
                    }
                },
                dataType: 'json'
            });
        }, 500);
    });
});
</script>
</body>
</html>
