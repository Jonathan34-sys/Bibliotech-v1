session_start();
$usuario_id = $_SESSION['usuario_id'];
$libro_id = $_POST['libro_id'];

$db = new PDO("mysql:host=localhost;dbname=bibliotech", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Eliminar préstamo
$stmt = $db->prepare("DELETE FROM prestamos WHERE usuario_id = ? AND libro_id = ?");
$stmt->execute([$usuario_id, $libro_id]);

header("Location: dashboard_usuario.php");
exit;
