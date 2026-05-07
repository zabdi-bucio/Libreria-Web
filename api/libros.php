<?php
// api/libros.php
// ============================================================
// API REST (AJAX) para Libros/Productos
// Paso 3 - Práctica Unidad 4
// ============================================================

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once '../config/conexion.php';
require_once '../config/sesion.php';

// Solo usuarios autenticados pueden usar la API
requiereAutenticacion('../login.php');

$pdo    = Conexion::obtenerInstancia()->getPDO();
$metodo = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? '';

try {
    switch ($metodo) {

        // ---- GET: Buscar / Listar libros ----------------
        case 'GET':
            if ($accion === 'buscar') {
                // Búsqueda AJAX por título o ISBN (Paso 3)
                $termino = '%' . trim($_GET['q'] ?? '') . '%';
                $stmt = $pdo->prepare(
                    "SELECT id, isbn, titulo, autor, precio, stock
                     FROM libros
                     WHERE activo = 1
                       AND (titulo LIKE :t OR isbn LIKE :i)
                     LIMIT 10"
                );
                $stmt->execute([':t' => $termino, ':i' => $termino]);
                echo json_encode(['exito' => true, 'datos' => $stmt->fetchAll()]);

            } elseif ($accion === 'obtener' && isset($_GET['id'])) {
                $stmt = $pdo->prepare(
                    "SELECT id, isbn, titulo, autor, precio, stock
                     FROM libros WHERE id = :id AND activo = 1"
                );
                $stmt->execute([':id' => (int)$_GET['id']]);
                $libro = $stmt->fetch();
                if ($libro) {
                    echo json_encode(['exito' => true, 'datos' => $libro]);
                } else {
                    http_response_code(404);
                    echo json_encode(['exito' => false, 'mensaje' => 'Libro no encontrado.']);
                }

            } else {
                // Listar todos los libros activos
                $stmt = $pdo->query(
                    "SELECT id, isbn, titulo, autor, precio, stock
                     FROM libros WHERE activo = 1 ORDER BY titulo"
                );
                echo json_encode(['exito' => true, 'datos' => $stmt->fetchAll()]);
            }
            break;

        // ---- POST: Dar de alta un libro -----------------
        case 'POST':
            requiereRol('../index.html', 'admin', 'editor');
            $datos = json_decode(file_get_contents('php://input'), true)
                   ?? $_POST;

            $isbn   = trim($datos['isbn']   ?? '');
            $titulo = trim($datos['titulo'] ?? '');
            $autor  = trim($datos['autor']  ?? '');
            $precio = (float)($datos['precio'] ?? 0);
            $stock  = (int)($datos['stock']  ?? 0);

            if (!$isbn || !$titulo || !$autor || $precio <= 0) {
                http_response_code(400);
                echo json_encode(['exito' => false, 'mensaje' => 'Datos incompletos o inválidos.']);
                break;
            }

            $stmt = $pdo->prepare(
                "INSERT INTO libros (isbn, titulo, autor, precio, stock)
                 VALUES (:isbn, :titulo, :autor, :precio, :stock)"
            );
            $stmt->execute([
                ':isbn'   => $isbn,
                ':titulo' => $titulo,
                ':autor'  => $autor,
                ':precio' => $precio,
                ':stock'  => $stock,
            ]);
            echo json_encode([
                'exito'   => true,
                'mensaje' => 'Libro dado de alta correctamente.',
                'id'      => (int)$pdo->lastInsertId(),
            ]);
            break;

        // ---- PUT: Modificar libro -----------------------
        case 'PUT':
            requiereRol('../index.html', 'admin', 'editor');
            $datos = json_decode(file_get_contents('php://input'), true);
            $id     = (int)($datos['id']     ?? 0);
            $titulo = trim($datos['titulo']  ?? '');
            $autor  = trim($datos['autor']   ?? '');
            $precio = (float)($datos['precio'] ?? 0);
            $stock  = (int)($datos['stock']  ?? 0);

            if (!$id || !$titulo || !$autor || $precio <= 0) {
                http_response_code(400);
                echo json_encode(['exito' => false, 'mensaje' => 'Datos incompletos.']);
                break;
            }

            $stmt = $pdo->prepare(
                "UPDATE libros
                 SET titulo=:titulo, autor=:autor, precio=:precio, stock=:stock
                 WHERE id=:id AND activo=1"
            );
            $stmt->execute([
                ':titulo' => $titulo, ':autor'  => $autor,
                ':precio' => $precio, ':stock'  => $stock, ':id' => $id,
            ]);
            echo json_encode(['exito' => true, 'mensaje' => 'Libro actualizado.']);
            break;

        // ---- DELETE: Baja lógica de libro ---------------
        case 'DELETE':
            requiereRol('../index.html', 'admin');
            $datos = json_decode(file_get_contents('php://input'), true);
            $id = (int)($datos['id'] ?? 0);

            if (!$id) {
                http_response_code(400);
                echo json_encode(['exito' => false, 'mensaje' => 'ID inválido.']);
                break;
            }

            $stmt = $pdo->prepare("UPDATE libros SET activo=0 WHERE id=:id");
            $stmt->execute([':id' => $id]);
            echo json_encode(['exito' => true, 'mensaje' => 'Libro dado de baja.']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido.']);
    }
} catch (PDOException $e) {
    error_log('API libros - Error BD: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['exito' => false, 'mensaje' => 'Error interno del servidor.']);
}
