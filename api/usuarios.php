<?php
// api/usuarios.php
// ============================================================
// API AJAX para Usuarios
// ============================================================

header('Content-Type: application/json; charset=utf-8');

require_once '../config/conexion.php';
require_once '../config/sesion.php';

$pdo    = Conexion::obtenerInstancia()->getPDO();
$metodo = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? '';

try {
    // ---- Verificar disponibilidad de nombre de usuario----
    if ($metodo === 'GET' && $accion === 'verificar_nombre') {
        $nombre = trim($_GET['nombre_usuario'] ?? '');
        if (strlen($nombre) < 3) {
            echo json_encode(['disponible' => false, 'mensaje' => 'Mínimo 3 caracteres.']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nombre_usuario = :n LIMIT 1");
        $stmt->execute([':n' => $nombre]);
        $existe = (bool)$stmt->fetch();
        echo json_encode([
            'disponible' => !$existe,
            'mensaje'    => $existe ? 'Ese nombre de usuario ya está en uso.' : 'Nombre disponible ✓',
        ]);
        exit;
    }

    // ---- Solo admins pueden gestionar usuarios a partir de aquí ----
    requiereRol('../index.php', 'admin');

    switch ($metodo) {
        // Listar todos los usuarios
        case 'GET':
            $stmt = $pdo->query(
                "SELECT id, nombre_usuario, email, rol, activo, fecha_registro
                 FROM usuarios ORDER BY fecha_registro DESC"
            );
            echo json_encode(['exito' => true, 'datos' => $stmt->fetchAll()]);
            break;

        // Cambiar rol de usuario
        case 'PUT':
            $datos = json_decode(file_get_contents('php://input'), true);
            $id  = (int)($datos['id']  ?? 0);
            $rol = trim($datos['rol'] ?? '');
            $rolesValidos = ['admin', 'editor', 'lector'];

            if (!$id || !in_array($rol, $rolesValidos, true)) {
                http_response_code(400);
                echo json_encode(['exito' => false, 'mensaje' => 'Datos inválidos.']);
                break;
            }
            $stmt = $pdo->prepare("UPDATE usuarios SET rol=:rol WHERE id=:id");
            $stmt->execute([':rol' => $rol, ':id' => $id]);
            echo json_encode(['exito' => true, 'mensaje' => 'Rol actualizado.']);
            break;

        // Baja lógica de usuario
        case 'DELETE':
            $datos = json_decode(file_get_contents('php://input'), true);
            $id = (int)($datos['id'] ?? 0);
            if (!$id || $id === $_SESSION['usuario_id']) {
                http_response_code(400);
                echo json_encode(['exito' => false, 'mensaje' => 'No puedes eliminar tu propia cuenta.']);
                break;
            }
            $stmt = $pdo->prepare("UPDATE usuarios SET activo=0 WHERE id=:id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['exito' => true, 'mensaje' => 'Usuario dado de baja.']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido.']);
    }

} catch (PDOException $e) {
    error_log('API usuarios - Error BD: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['exito' => false, 'mensaje' => 'Error interno del servidor.']);
}