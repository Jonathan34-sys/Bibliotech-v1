<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autenticado']);
    exit;
}

if (!isset($_POST['libro_id'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de libro no proporcionado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$libro_id = (int) $_POST['libro_id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=bibliotech", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Validar límite de 5 préstamos activos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM prestamo WHERE usuario_id = ? AND estado = 'prestado'");
    $stmt->execute([$usuario_id]);
    $prestamos_activos = $stmt->fetchColumn();

    if ($prestamos_activos >= 5) {
        echo json_encode(['exito' => false, 'mensaje' => 'Límite de 5 préstamos alcanzado']);
        exit;
    }

    // Validar estado del libro
    $stmt = $pdo->prepare("SELECT estado FROM libros WHERE id = ?");
    $stmt->execute([$libro_id]);
    $estado = $stmt->fetchColumn();

    if (!$estado || strtolower($estado) !== 'disponible') {
        echo json_encode(['exito' => false, 'mensaje' => 'El libro no está disponible']);
        exit;
    }

    // Registrar préstamo y actualizar estado del libro 
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO prestamo (usuario_id, libro_id, fecha_prestamo, estado) VALUES (?, ?, NOW(), 'prestado')");
    $stmt->execute([$usuario_id, $libro_id]);

    $stmt = $pdo->prepare("UPDATE libros SET estado = 'prestado' WHERE id = ?");
    $stmt->execute([$libro_id]);

    $pdo->commit();

    echo json_encode(['exito' => true, 'mensaje' => 'Libro prestado correctamente']);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['exito' => false, 'mensaje' => 'Error en el servidor: ' . $e->getMessage()]);
}
?>
