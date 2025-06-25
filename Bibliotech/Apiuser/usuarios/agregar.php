<?php
include 'C:\laragon\www\Bibliotech\Apiuser\conexion.php';

$data = json_decode(file_get_contents("php://input"));


$nombre = $conn->real_escape_string($data->nombre);
$correo = $conn->real_escape_string($data->email);
$rol = $conn->real_escape_string($data->rol);


$sql = "INSERT INTO usuarios (nombre_usuario, correo, rol) VALUES ('$nombre', '$correo', '$rol')";


if ($conn->query($sql) === TRUE) {
    echo json_encode(["mensaje" => "Usuario agregado correctamente"]);
} else {
    echo json_encode(["error" => "Error al agregar usuario: " . $conn->error]);
}

$conn->close();
?>
