<?php
require 'C:\laragon\www\Bibliotech\Apiuser\conexion.php';


$data = json_decode(file_get_contents("php://input"));
$id = isset($data->id) ? intval($data->id) : 0;


if ($id <= 0) {
    echo json_encode(["error" => "ID inválido o no recibido."]);
    exit;
}


$sql = "DELETE FROM usuarios WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["mensaje" => "Usuario eliminado correctamente."]);
} else {
    echo json_encode(["error" => "Error al eliminar usuario: " . $conn->error]);
}

$conn->close();
?>
