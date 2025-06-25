<?php
header("Content-Type: application/json");

// Incluir clases necesarias
include_once "C://laragon/www/Bibliotech/Api/config/database.php";
include_once "C://laragon/www/Bibliotech/Api/models/User.php";

// Obtener los datos del cuerpo de la solicitud
$data = json_decode(file_get_contents("php://input"));

// Verificar que los campos estén completos
if (!empty($data->correo) && !empty($data->password)) {
    // Conectar a la base de datos
    $database = new Database();
    $db = $database->connect();

    // Crear objeto usuario
    $user = new User($db);
    $user->correo = $data->correo;
    $user->password = $data->password;

    // Intentar iniciar sesión
    if ($user->login()) {
        http_response_code(200); // OK
        echo json_encode(["message" => "Autenticación satisfactoria"]);
    } else {
        http_response_code(401); // No autorizado
        echo json_encode(["message" => "Error en la autenticación"]);
    }
} else {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(["message" => "Datos incompletos"]);
}
?>
