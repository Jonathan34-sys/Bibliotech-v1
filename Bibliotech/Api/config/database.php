<?php
class Database {
    private $host = "127.0.0.1";
    private $port = 3306;
    private $db_name = "bibliotech";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo fetch por defecto
                PDO::ATTR_EMULATE_PREPARES => false, // Usar sentencias preparadas nativas
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $exception) {
            // Mejor lanzar excepción para que la capa superior la maneje
            throw new Exception("Conexión fallida: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
