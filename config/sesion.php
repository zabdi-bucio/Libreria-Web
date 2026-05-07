<?php
// config/sesion.php
// ============================================================
// Helper para manejo de Sesiones del Navegador
// Paso 3 - Práctica Unidad 4
// ============================================================

// Configuración de seguridad de sesión (antes de session_start)
ini_set('session.cookie_httponly', 1);   // Evita acceso JS a la cookie
ini_set('session.use_strict_mode', 1);   // Solo IDs generados por el servidor
ini_set('session.cookie_samesite', 'Strict');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------------------------------------
// Funciones de utilidad para sesiones
// ------------------------------------------------------------

/**
 * Inicia sesión para un usuario autenticado.
 */
function iniciarSesionUsuario(array $usuario): void {
    session_regenerate_id(true); // Previene fijación de sesión
    $_SESSION['usuario_id']     = $usuario['id'];
    $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
    $_SESSION['rol']            = $usuario['rol'];
    $_SESSION['inicio']         = time();
}

/**
 * Verifica si hay una sesión activa.
 */
function estaAutenticado(): bool {
    return isset($_SESSION['usuario_id']);
}

/**
 * Verifica si el usuario tiene un rol específico.
 */
function tieneRol(string ...$roles): bool {
    return estaAutenticado() && in_array($_SESSION['rol'], $roles, true);
}

/**
 * Redirige al login si no está autenticado.
 */
function requiereAutenticacion(string $redireccion = '../login.php'): void {
    if (!estaAutenticado()) {
        header("Location: {$redireccion}");
        exit;
    }
}

/**
 * Redirige si no tiene el rol requerido.
 */
function requiereRol(string $redireccion = '../index.html', string ...$roles): void {
    requiereAutenticacion();
    if (!tieneRol(...$roles)) {
        header("Location: {$redireccion}");
        exit;
    }
}

/**
 * Cierra la sesión completamente.
 */
function cerrarSesion(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}
