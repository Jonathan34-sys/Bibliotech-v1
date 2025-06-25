<?php
require 'C:\laragon\www\Bibliotech\Apiuser\conexion.php';


$data = json_decode(file_get_contents("php://input"));

$id = intval($data->id); 
$nueva = password_hash($data->nueva_contrasena, PASSWORD_DEFAULT); 


$sql = "UPDATE usuarios SET contrasena = '$nueva' WHERE id = $id";


if ($conn->query($sql) === TRUE) {
    echo json_encode(["mensaje" => "Contraseña actualizada correctamente."]);
} else {
    echo json_encode(["error" => "Error al actualizar la contraseña: " . $conn->error]);
}

$conn->close();
?>
