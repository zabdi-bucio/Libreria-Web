<?php
// session.php a prueba de fallos

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function iniciarSesionUsuario($usuario) {
    $_SESSION['usuario_id']     = $usuario['id'];
    $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
    $_SESSION['rol']            = $usuario['rol'];
}

function estaAutenticado() {
    return isset($_SESSION['usuario_id']);
}

function tieneRol(...$roles) {
    return estaAutenticado() && in_array($_SESSION['rol'], $roles);
}

function requiereAutenticacion($redireccion = '../login.php') {
    if (!estaAutenticado()) {
        // Si es AJAX, avisamos del error
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            http_response_code(401);
            echo json_encode(['exito' => false, 'mensaje' => 'Tu sesión expiró. Inicia sesión de nuevo.']);
            exit;
        }
        header("Location: $redireccion");
        exit;
    }
}

function requiereRol($redireccion = '../index.php', ...$roles) {
    requiereAutenticacion($redireccion);
    if (!tieneRol(...$roles)) {
        // Si es AJAX y no tiene permiso
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            http_response_code(403);
            echo json_encode(['exito' => false, 'mensaje' => 'No tienes permisos para hacer esto.']);
            exit;
        }
        header("Location: $redireccion");
        exit;
    }
}

function cerrarSesion() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();
}
?>