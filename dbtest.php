<?php
require_once 'config/conexion.php';

try {
    $pdo = Conexion::obtenerInstancia()->getPDO();
    echo "Conexión a BD correcta<br>";

    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM libros");
    $resultado = $stmt->fetch();

    echo "Total de libros: " . $resultado['total'];
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage();
}
?>