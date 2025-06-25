<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conexion = new mysqli("localhost", "root", "", "bibliotech");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$sql = "SELECT id, nombre_usuario, correo, rol FROM usuarios";
$resultado = $conexion->query($sql);

$users = [];

if ($resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $users[] = [
            "id" => $row["id"],
            "nombre" => $row["nombre_usuario"],
            "email" => $row["correo"],
            "rol" => $row["rol"]
        ];
    }
}

echo json_encode($users);
$conexion->close();
?>
