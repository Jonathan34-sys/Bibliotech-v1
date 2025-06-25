<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$accion = $_GET['accion'] ?? '';
$libro_id = intval($_GET['id'] ?? 0);

try {
    $db = new PDO("mysql:host=localhost;dbname=bibliotech", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($accion === 'prestar') {
        $check = $db->prepare("SELECT * FROM prestamo WHERE libro_id = ? AND usuario_id = ? AND estado = 'prestado'");
        $check->execute([$libro_id, $usuario_id]);

        if ($check->rowCount() > 0) {
            echo json_encode(['success' => false, 'message' => 'Ya tienes este libro en préstamo']);
        } else {
            $stmt = $db->prepare("INSERT INTO prestamo (libro_id, usuario_id, fecha_prestamo, estado) VALUES (?, ?, NOW(), 'prestado')");
            $stmt->execute([$libro_id, $usuario_id]);
            echo json_encode(['success' => true]);
        }
    } elseif ($accion === 'devolver') {
        $stmt = $db->prepare("UPDATE prestamo SET estado = 'devuelto' WHERE libro_id = ? AND usuario_id = ? AND estado = 'prestado'");
        $stmt->execute([$libro_id, $usuario_id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
