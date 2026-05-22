<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Iniciando prueba BD<br>";

echo "DB_HOST: " . getenv('DB_HOST') . "<br>";
echo "DB_NAME: " . getenv('DB_NAME') . "<br>";
echo "DB_USER: " . getenv('DB_USER') . "<br>";

require_once 'config/conexion.php';

try {
    $pdo = Conexion::obtenerInstancia()->getPDO();
    echo "Conexión a BD correcta<br>";

    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM libros");
    $resultado = $stmt->fetch();

    echo "Total de libros: " . $resultado['total'];
} catch (Throwable $e) {
    echo "<pre>Error real: " . $e->getMessage() . "</pre>";
}
?>