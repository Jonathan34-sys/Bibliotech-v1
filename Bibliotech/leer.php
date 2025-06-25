<?php
session_start();
$usuario_id = $_SESSION['usuario_id'] ?? null;

if (!$usuario_id) {
    die("Acceso denegado");
}

if (!isset($_GET['libro_id'])) {
    die("Libro no especificado");
}

$libro_id = intval($_GET['libro_id']);

$db = new PDO("mysql:host=localhost;dbname=bibliotech", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Verificar si el libro está prestado al usuario y aún no ha sido devuelto
$stmt = $db->prepare("
    SELECT l.titulo, l.ubicacion, p.fecha_prestamo
    FROM libros l
    JOIN prestamos p ON l.id = p.libro_id
    WHERE p.usuario_id = ? AND p.libro_id = ? AND p.fecha_devolucion IS NULL
");
$stmt->execute([$usuario_id, $libro_id]);
$libro = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$libro) {
    die("No tienes acceso a este libro o no está prestado.");
}

// Ruta local y URL base
$carpeta = $libro['ubicacion'];
$baseLocal = "C:/laragon/www/Bibliotech/react-app/";
$baseUrl = "http://localhost/Bibliotech/react-app/";

$urlCarpeta = str_replace($baseLocal, $baseUrl, $carpeta);

// Escanea la carpeta para encontrar páginas del libro
$imagenes = glob($carpeta . "/pagina_*.jpg");
sort($imagenes);

// Genera URL
$paginas = array_map(function($img) use ($baseLocal, $baseUrl) {
    return str_replace($baseLocal, $baseUrl, $img);
}, $imagenes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Leer <?= htmlspecialchars($libro['titulo']) ?></title>
    <style>
        body {
            background: #0d0d0d;
            color: white;
            font-family: 'Exo', sans-serif;
            text-align: center;
        }
        img {
            max-width: 90vw;
            max-height: 80vh;
            margin: 1rem auto;
            display: block;
            border-radius: 12px;
            box-shadow: 0 0 10px red;
        }
        a {
            color: #ff2d00;
            text-decoration: none;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($libro['titulo']) ?></h1>

    <?php foreach ($paginas as $pagina): ?>
        <img src="<?= htmlspecialchars($pagina) ?>" alt="Página del libro" />
    <?php endforeach; ?>

    <a href="dashboard.php">⬅ Volver al Dashboard</a>
</body>
</html>
