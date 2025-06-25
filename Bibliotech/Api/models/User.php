<?php
class User {
    private $conn;
    private $table = "usuarios";

    public $id;
    public $correo;     // Reemplaza username por correo
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar usuario
    public function register() {
        $query = "INSERT INTO {$this->table} (correo, password) VALUES (:correo, :password)";
        $stmt = $this->conn->prepare($query);

        // Encriptar contraseña
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":correo", $this->correo);
        $stmt->bindParam(":password", $hashedPassword);

        return $stmt->execute();
    }

    // Iniciar sesión
    public function login() {
        $query = "SELECT * FROM {$this->table} WHERE correo = :correo LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($this->password, $user['password'])) {
            // Puedes asignar más datos si los necesitas
            $this->id = $user['id'];
            return true;
        }

        return false;
    }
}
?>
