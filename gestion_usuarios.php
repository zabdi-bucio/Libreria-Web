<?php
require_once 'config/conexion.php';
require_once 'config/sesion.php';

requiereRol('index.php', 'admin');

$pdo      = Conexion::obtenerInstancia()->getPDO();
$mensaje  = '';
$tipoMsg  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'alta') {
    $nombre   = trim($_POST['nombre_usuario'] ?? '');
    $email    = trim($_POST['email']          ?? '');
    $pass     = $_POST['contrasena']          ?? '';
    $rol      = $_POST['rol']                 ?? 'lector';
    $rolesVal = ['admin', 'editor', 'lector'];

    if (!$nombre || !$email || !$pass || !in_array($rol, $rolesVal, true)) {
        $mensaje = 'Todos los campos son obligatorios y el rol debe ser válido.';
        $tipoMsg = 'error';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_usuario=:n OR email=:e LIMIT 1");
        $stmt->execute([':n' => $nombre, ':e' => $email]);
        if ($stmt->fetch()) {
            $mensaje = 'El nombre de usuario o correo ya existe.';
            $tipoMsg = 'error';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (nombre_usuario, email, password_hash, rol)
                 VALUES (:n, :e, :h, :r)"
            );
            $stmt->execute([':n'=>$nombre,':e'=>$email,':h'=>$hash,':r'=>$rol]);
            $mensaje = "Usuario '{$nombre}' registrado correctamente.";
            $tipoMsg = 'exito';
        }
    }
}

$usuarios = $pdo->query(
    "SELECT id, nombre_usuario, email, rol, activo, fecha_registro FROM usuarios ORDER BY id"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="referrer" content="unsafe-url">
    <title>Gestión de Usuarios - BiblioTec</title>
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="formularios.css">
    <style>
        table { width:100%; max-width:900px; margin:20px auto; border-collapse:collapse;
                background:white; border-radius:10px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
        th { background:#2c3e50; color:white; padding:12px; }
        td { padding:10px; border-bottom:1px solid #eee; text-align:center; }
        tr:hover td { background:#f0f4f8; }
        .badge { padding:4px 10px; border-radius:12px; font-size:0.8em; font-weight:600; }
        .badge-admin  { background:#e74c3c; color:white; }
        .badge-editor { background:#f39c12; color:white; }
        .badge-lector { background:#1abc9c; color:white; }
        .badge-inactivo { background:#bdc3c7; color:white; }
        .msg-exito { color:#1abc9c; font-weight:bold; }
        .msg-error { color:#e74c3c; font-weight:bold; }
        select.rol-select { padding:5px; border-radius:6px; border:2px solid #e0e6ed; font-family:'Poppins',sans-serif; width:auto; }
        .btn-baja { background:#e74c3c; color:white; border:none; padding:6px 12px; border-radius:8px; cursor:pointer; font-size:0.85em; }
        .btn-baja:hover { background:#c0392b; }
    </style>
</head>
<body>
<header>
    <h1>Gestión de Usuarios</h1>
    <img src="img/logo.png" width="200" alt="Logo BiblioTec">
</header>
<nav>
    <a href="index.php">Inicio</a>
    <a href="catalogo.php">Catálogo</a>
    <a href="gestion.php">Gestión Inventario</a>
    <a href="logout.php">Cerrar Sesión (<?= htmlspecialchars($_SESSION['nombre_usuario']) ?>)</a>
</nav>
<main>
    <h2>Administración de Cuentas</h2>

    <?php if ($mensaje): ?>
        <p class="msg-<?= $tipoMsg ?>"><?= htmlspecialchars($mensaje) ?></p>
    <?php endif; ?>

    <form action="gestion_usuarios.php" method="POST">
        <input type="hidden" name="accion" value="alta">
        <fieldset>
            <legend>Registrar Nuevo Usuario</legend>

            <label for="nombre_usuario">Nombre de Usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required minlength="3">

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="contrasena">Contraseña Inicial:</label>
            <input type="password" id="contrasena" name="contrasena" required minlength="8">

            <label for="rol">Rol:</label>
            <select id="rol" name="rol">
                <option value="lector">Lector</option>
                <option value="editor">Editor</option>
                <option value="admin">Administrador</option>
            </select>

            <button type="submit">Dar de Alta</button>
        </fieldset>
    </form>

    <h3>Usuarios Registrados</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Usuario</th><th>Email</th>
                <th>Rol</th><th>Estado</th><th>Registro</th><th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaUsuarios">
        <?php foreach ($usuarios as $u): ?>
            <tr id="fila-<?= $u['id'] ?>">
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                    <select class="rol-select" data-id="<?= $u['id'] ?>">
                        <?php foreach (['admin','editor','lector'] as $r): ?>
                            <option value="<?= $r ?>" <?= $u['rol']===$r?'selected':'' ?>>
                                <?= ucfirst($r) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <span class="badge badge-<?= $u['activo'] ? $u['rol'] : 'inactivo' ?>">
                        <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                    </span>
                </td>
                <td><?= htmlspecialchars(substr($u['fecha_registro'],0,10)) ?></td>
                <td>
                    <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                        <button class="btn-baja" data-id="<?= $u['id'] ?>">Dar de Baja</button>
                    <?php else: ?>
                        <em>(tu cuenta)</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
<footer>
    <p>&copy; 2026 BiblioTec.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function(){

    $(document).on('change', '.rol-select', function(){
        const id  = $(this).data('id');
        const rol = $(this).val();
        $.ajax({
            url: 'api/usuarios.php',
            method: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify({ id, rol }),
            success: function(resp){ alert(resp.mensaje); },
            error: function(xhr){ alert(xhr.responseJSON ? xhr.responseJSON.mensaje : 'Error al cambiar rol.'); },
            dataType: 'json'
        });
    });

    $(document).on('click', '.btn-baja', function(){
        const id = $(this).data('id');
        if (!confirm('¿Confirmas dar de baja a este usuario?')) return;
        $.ajax({
            url: 'api/usuarios.php',
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({ id }),
            success: function(resp){
                if (resp.exito) {
                    $(`#fila-${id}`).fadeOut(400, function(){ $(this).remove(); });
                } else {
                    alert(resp.mensaje);
                }
            },
            error: function(xhr){ alert(xhr.responseJSON ? xhr.responseJSON.mensaje : 'Error al dar de baja.'); },
            dataType: 'json'
        });
    });
});
</script>
</body>
</html>