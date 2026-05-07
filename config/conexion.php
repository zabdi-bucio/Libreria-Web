<?php
// config/conexion.php
// ============================================================
// Clase de conexión a la Base de Datos (Patrón Singleton)
// Paso 1 - Práctica Unidad 4
// ============================================================

class Conexion {

    private static ?Conexion $instancia = null;
    private PDO $pdo;

    // --- Credenciales (ajusta según tu servidor) -------------
    private string $host     = 'localhost';
    private string $dbname   = 'bibliotec';
    private string $usuario  = 'root';
    private string $contrasena = '';
    // ---------------------------------------------------------

    /**
     * Constructor privado: impide instancias externas (Singleton).
     */
    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->usuario, $this->contrasena, $opciones);
        } catch (PDOException $e) {
            // En producción nunca exponer detalles del error
            error_log('Error de conexión BD: ' . $e->getMessage());
            http_response_code(500);
            die(json_encode(['exito' => false, 'mensaje' => 'Error interno del servidor.']));
        }
    }

    /**
     * Devuelve la única instancia de la clase.
     */
    public static function obtenerInstancia(): Conexion {
        if (self::$instancia === null) {
            self::$instancia = new Conexion();
        }
        return self::$instancia;
    }

    /**
     * Expone el objeto PDO para realizar consultas.
     */
    public function getPDO(): PDO {
        return $this->pdo;
    }

    // Evitar clonación y deserialización (Singleton estricto)
    private function __clone() {}
    public function __wakeup() { throw new \Exception("No se puede deserializar el Singleton."); }
}
