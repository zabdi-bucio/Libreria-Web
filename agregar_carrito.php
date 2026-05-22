<?php
require_once 'config/conexion.php';
require_once 'config/sesion.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    header('Location: catalogo.php');
    exit;
}

$pdo = Conexion::obtenerInstancia()->getPDO();

$stmt = $pdo->prepare("
    SELECT id, titulo, precio
    FROM libros
    WHERE id = :id
    AND activo = 1
");

$stmt->execute([':id' => $id]);

$libro = $stmt->fetch();

if (!$libro) {
    header('Location: catalogo.php');
    exit;
}

$encontrado = false;

foreach ($_SESSION['carrito'] as &$item) {

    if ($item['id'] == $id) {
        $item['cantidad']++;
        $encontrado = true;
        break;
    }
}

if (!$encontrado) {

    $_SESSION['carrito'][] = [
        'id' => $libro['id'],
        'titulo' => $libro['titulo'],
        'precio' => $libro['precio'],
        'cantidad' => 1
    ];
}

header('Location: carrito.php');
exit;
