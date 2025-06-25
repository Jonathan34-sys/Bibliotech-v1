<?php
header("Content-Type: application/json");

// Incluir archivos necesarios
include_once "C://laragon/www/Bibliotech/Api/config/database.php";
include_once "C://laragon/www/Bibliotech/Api/models/User.php";

// Obtener datos JSON enviados por el cliente
$data = json_decode(file_get_contents("php://input"));

// Validar que los campos requeridos estén presentes
if (!empty($data->correo) && !empty($data->password)) {
    // Conectar a la base de datos
    $database = new Database();
    $db = $database->connect();

    // Crear nuevo usuario
    $user = new User($db);
    $user->correo = $data->correo;
    $user->password = $data->password;

    // Intentar registrar
    if ($user->register()) {
        http_response_code(201); // Código 201: creado
        echo json_encode(["message" => "Registro exitoso"]);
    } else {
        http_response_code(503); // Código 503: servicio no disponible
        echo json_encode(["message" => "Error al registrar"]);
    }
} else {
    http_response_code(400); // Código 400: solicitud incorrecta
    echo json_encode(["message" => "Datos incompletos"]);
}
?>
