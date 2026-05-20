<?php
// config/conexion.php
// ============================================================
// Clase de conexión — lee credenciales de variables de entorno
// Compatible con XAMPP local y cualquier nube pública
// ============================================================

class Conexion {

    private static ?Conexion $instancia = null;
    private PDO $pdo;

    private function __construct() {
        // Lee de variables de entorno (nube) o usa defaults locales
        $host     = getenv('DB_HOST') ?: 'localhost';
        $dbname   = getenv('DB_NAME') ?: 'bibliotec';
        $usuario  = getenv('DB_USER') ?: 'root';
        $contrasena = getenv('DB_PASS') ?: '';

        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $usuario, $contrasena, $opciones);
        } catch (PDOException $e) {
            error_log('Error de conexión BD: ' . $e->getMessage());
            http_response_code(500);
            die(json_encode(['exito' => false, 'mensaje' => 'Error interno del servidor.']));
        }
    }

    public static function obtenerInstancia(): Conexion {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    public function getPDO(): PDO {
        return $this->pdo;
    }

    private function __clone() {}
    public function __wakeup() { throw new \Exception("No se puede deserializar."); }
}
