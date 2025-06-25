<?php
require 'C:\laragon\www\Bibliotech\Apiuser\conexion.php';

$sql = "SELECT id, nombre_usuario AS nombre, correo AS email FROM usuarios"; // Corregí nombres según usualmente son las columnas
$result = $conn->query($sql);

$usuarios = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    echo json_encode($usuarios);
} else {
    echo json_encode(["error" => "Error en la consulta: " . $conn->error]);
}

$conn->close();
?>