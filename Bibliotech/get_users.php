<?php
header('Content-Type: application/json; charset=utf-8');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bibliotech";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit();
}

// Establecer codificse
$conn->set_charset("utf8");

$sql = "SELECT id, nombre_usuario, correo, rol FROM usuarios";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la consulta: " . $conn->error]);
    $conn->close();
    exit();
}

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        "id" => (int)$row["id"],
        "nombre" => $row["nombre_usuario"],
        "email" => $row["correo"],
        "rol" => $row["rol"]
    ];
}

echo json_encode($usuarios);
$conn->close();
?>
