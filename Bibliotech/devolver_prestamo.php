<?php
session_start();
header('Content-Type: application/json');

// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'Debes iniciar sesión para devolver un libro.']);
    exit;
}

// Verifica si se recibió el ID del libro
if (!isset($_POST['libro_id'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de libro no proporcionado.']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$libro_id = (int) $_POST['libro_id'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=bibliotech", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar el préstamo activo más reciente.
    $stmt = $pdo->prepare("SELECT id, estado FROM prestamo WHERE usuario_id = ? AND libro_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$usuario_id, $libro_id]);
    $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prestamo) {
        echo json_encode(['exito' => false, 'mensaje' => 'No se encontró ningún préstamo para este libro.']);
        exit;
    }

    // Si ya está devuelto
    if (strtolower($prestamo['estado']) === 'devuelto') {
        echo json_encode(['exito' => false, 'mensaje' => 'Este préstamo ya fue devuelto anteriormente.']);
        exit;
    }

    // Inicia transacción para devolver el libro
    $pdo->beginTransaction();

    // Actualizar estado del préstamo
    $stmtUpdate = $pdo->prepare("UPDATE prestamo SET estado = 'devuelto', fecha_devolucion = NOW() WHERE id = ?");
    $stmtUpdate->execute([$prestamo['id']]);

    // Actualizar estado del libro
    $stmtLibro = $pdo->prepare("UPDATE libros SET estado = 'disponible' WHERE id = ?");
    $stmtLibro->execute([$libro_id]);

    $pdo->commit();

    echo json_encode(['exito' => true, 'mensaje' => 'Libro devuelto correctamente.']);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['exito' => false, 'mensaje' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
