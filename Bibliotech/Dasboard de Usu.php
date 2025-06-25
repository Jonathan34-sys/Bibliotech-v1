<?php
session_start();

try {
    $db = new PDO("mysql:host=localhost;dbname=bibliotech", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

$nombre_usuario = "Invitado";
$libros = [];
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : "";
$prestamosActuales = 0;

if (isset($_SESSION['usuario_id'])) {
    $usuario_id = $_SESSION['usuario_id'];

    // Obtener nombre del usuario
    $stmtUsuario = $db->prepare("SELECT nombre_usuario FROM usuarios WHERE id = ?");
    $stmtUsuario->execute([$usuario_id]);
    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
    $nombre_usuario = $usuario ? $usuario['nombre_usuario'] : "Invitado";

    // Obtener cantidad de préstamos activos
    $stmtPrestamos = $db->prepare("SELECT COUNT(*) AS total FROM prestamo WHERE usuario_id = ? AND estado = 'prestado'");
    $stmtPrestamos->execute([$usuario_id]);
    $resultadoPrestamos = $stmtPrestamos->fetch(PDO::FETCH_ASSOC);
    $prestamosActuales = $resultadoPrestamos ? (int)$resultadoPrestamos['total'] : 0;

    // Consulta de libros 
    $sql = "
       SELECT 
    l.id, l.titulo, l.autor, l.anio_publicacion, l.portada, l.fecha_agregado, l.ubicacion,
    p.id AS prestamo_id, p.fecha_prestamo, p.estado
FROM libros l
LEFT JOIN prestamo p 
    ON l.id = p.libro_id AND p.usuario_id = :usuario_id AND p.estado = 'prestado'
        WHERE (:busqueda = '' OR l.titulo LIKE :busquedaLike)
        ORDER BY l.titulo
    ";

    $stmtLibros = $db->prepare($sql);
    $stmtLibros->execute([
        'usuario_id' => $usuario_id,
        'busqueda' => $busqueda,
        'busquedaLike' => "%$busqueda%"
    ]);
    $libros = $stmtLibros->fetchAll(PDO::FETCH_ASSOC);
} else {
   
    $sql = "SELECT id, titulo, autor, anio_publicacion, portada FROM libros";
    if ($busqueda !== "") {
        $sql .= " WHERE titulo LIKE ?";
        $stmtLibros = $db->prepare($sql . " ORDER BY titulo");
        $stmtLibros->execute(["%$busqueda%"]);
    } else {
        $stmtLibros = $db->query($sql . " ORDER BY titulo");
    }
    $libros = $stmtLibros->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Usuario - Bibliotech</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Exo:wght@400;700&display=swap" rel="stylesheet">
    <script src="js/particles.min.js"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Exo', sans-serif;
            background-color: #0d0d0d;
            color: #fff;
            overflow-x: hidden;
        }
        nav.sidebar {
            width: 220px;
            background: #1c1c1c;
            border-right: 3px solid #ff2d00;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
        }
        nav.sidebar button {
            background: none;
            border: none;
            color: #eee;
            padding: 15px 20px;
            text-align: left;
            cursor: pointer;
            border-left: 4px solid transparent;
            transition: 0.3s;
        }
        nav.sidebar button:hover,
        nav.sidebar button.active {
            background: #ff2d00;
            color: #121212;
            font-weight: bold;
            border-left: 4px solid #eee;
        }
        main.contenido-dashboard {
            margin-left: 240px;
            padding: 2rem;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 12px;
            box-shadow: 0 0 20px #ff2d00;
            max-width: calc(100% - 260px);
        }
        .libros-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
            max-width: 700px;
        }
        .libro {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(255, 45, 0, 0.3);
        }
        .libro img {
            width: 100px;
            height: auto;
            border-radius: 5px;
            margin-bottom: 0.8rem;
        }
        .libro h3 {
            margin: 0.5rem 0 0.3rem 0;
        }
        .libro em {
            font-size: 0.9rem;
            color: #bbb;
            margin-bottom: 0.6rem;
        }
        .libro button {
            margin: 0.3rem 0.2rem;
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            color: #121212;
            background-color: #ff2d00;
            transition: background-color 0.3s;
            width: 100%;
            max-width: 120px;
        }
        .libro button:hover {
            background-color: #e32600;
        }
        form.busqueda-form {
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            max-width: 500px;
        }
        form.busqueda-form input[type="text"] {
            flex-grow: 1;
            padding: 0.5rem;
            border-radius: 6px;
            border: none;
            font-size: 1rem;
        }
        form.busqueda-form button {
            background-color: #ff2d00;
            border: none;
            color: #fff;
            padding: 0 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        form.busqueda-form button:hover {
            background-color: #e32600;
        }
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div id="particles-js"></div>

    <nav class="sidebar">
        <h1 style="color: #ff2d00; margin: 0 0 20px 20px;">BIBLIOTECH</h1>
        <p style="color: white; margin-left: 20px;">Bienvenido, <strong><?= htmlspecialchars($nombre_usuario) ?></strong></p>
        <button class="active">Libros</button>
        <button>Mi Perfil</button>
        <button onclick="window.location.href='http://localhost/login/logout.php'">Cerrar sesión</button>
    </nav>
<main class="contenido-dashboard">
    <div id="contadorPrestamos" style="margin-bottom: 1rem; font-weight: bold; color: #ff2d00;">
        Préstamos realizados: <?= $prestamosActuales ?> / 5
    </div>

    <h2 style="border-left: 6px solid #ff2d00; padding-left: 10px;">Libros disponibles</h2>

    <form class="busqueda-form" method="get" action="">
        <input type="text" name="busqueda" placeholder="Buscar libros por título..." value="<?= htmlspecialchars($busqueda ?? '') ?>">
        <button type="submit">Buscar</button>
    </form>

    <div class="libros-grid">
        <?php if (!empty($libros)): ?>
            <?php foreach ($libros as $libro): ?>
                <div class="libro" data-libroid="<?= htmlspecialchars($libro['id']) ?>">
                    <img src="<?= htmlspecialchars($libro['portada'] ?? 'portada_default.jpg') ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
                    <h3><?= htmlspecialchars($libro['titulo']) ?></h3>

                    <?php if (($libro['estado'] ?? '') === 'prestado'): ?>
                        <em>Estado: prestado</em><br>
                        <button type="button" id="btnLeer-<?= $libro['id'] ?>" onclick="leerLibro(<?= $libro['id'] ?>)">Leer</button>
                        <button type="button" id="btnDevolver-<?= $libro['id'] ?>" onclick="devolverLibro(<?= $libro['id'] ?>)">Devolver</button>
                    <?php else: ?>
                        <em>Estado: disponible</em><br>
                        <button type="button" id="btnPedir-<?= $libro['id'] ?>" onclick="pedirPrestado(<?= $libro['id'] ?>)">Pedir prestado</button>
                        <button type="button" id="btnLeer-<?= $libro['id'] ?>" onclick="leerLibro(<?= $libro['id'] ?>)" style="display:none;">Leer</button>
                        <button type="button" id="btnDevolver-<?= $libro['id'] ?>" onclick="devolverLibro(<?= $libro['id'] ?>)" style="display:none;">Devolver</button>
                    <?php endif; ?>

                    <p id="mensaje-<?= $libro['id'] ?>"></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron libros con ese título.</p>
        <?php endif; ?>
    </div>
</main>
<script>
let enEspera = false;

function actualizarContador(cambio) {
    const contadorElem = document.getElementById('contadorPrestamos');
    const texto = contadorElem.textContent; 
    const match = texto.match(/(\d+) \/ 5/);
    let actuales = match ? parseInt(match[1], 10) : 0;

    actuales += cambio;

    if (actuales < 0) actuales = 0;
    if (actuales > 5) actuales = 5;

    contadorElem.textContent = `Préstamos realizados: ${actuales} / 5`;
}

function pedirPrestado(libroId) {
    if (enEspera) return;

    // Verificar si el usuario ya tiene 5 préstamos activos
    const contadorElem = document.getElementById('contadorPrestamos');
    const texto = contadorElem.textContent;
    const match = texto.match(/(\d+) \/ 5/);
    const actuales = match ? parseInt(match[1], 10) : 0;

    if (actuales >= 5) {
        alert('Has alcanzado el máximo de 5 préstamos simultáneos.');
        return;
    }

    enEspera = true;

    const btnPedir = document.getElementById(`btnPedir-${libroId}`);
    btnPedir.disabled = true;

    const mensaje = document.getElementById(`mensaje-${libroId}`);
    mensaje.textContent = "Solicitando préstamo…";

    fetch('pedir_prestado.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `libro_id=${encodeURIComponent(libroId)}`
    })
    .then(response => response.json())
    .then(data => {
        mensaje.textContent = data.mensaje;

        if (data.exito) {
            btnPedir.style.display = "none";
            document.getElementById(`btnLeer-${libroId}`).style.display = "inline-block";
            document.getElementById(`btnDevolver-${libroId}`).style.display = "inline-block";

            const tarjeta = document.querySelector(`[data-libroid="${libroId}"]`);
            const estadoElem = tarjeta.querySelector('em');
            if (estadoElem) estadoElem.textContent = 'Estado: prestado';

            actualizarContador(1);
        }
    })
    .catch(error => {
        mensaje.textContent = 'Error en la solicitud';
        console.error("Error en pedirPrestado:", error);
    })
    .finally(() => {
        enEspera = false;
        btnPedir.disabled = false;
    });
}

function devolverLibro(libroId) {
    if (enEspera) return;

    enEspera = true;

    const btnDevolver = document.getElementById(`btnDevolver-${libroId}`);
    btnDevolver.disabled = true;

    const mensaje = document.getElementById(`mensaje-${libroId}`);
    mensaje.textContent = "Procesando devolución…";

    fetch('devolver_prestamo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `libro_id=${encodeURIComponent(libroId)}`
    })
    .then(response => response.json())
    .then(data => {
        mensaje.textContent = data.mensaje;

        if (data.exito) {
            // Mostrar botón pedir y ocultar leer y devolver
            document.getElementById(`btnPedir-${libroId}`).style.display = "inline-block";
            document.getElementById(`btnLeer-${libroId}`).style.display = "none";
            btnDevolver.style.display = "none";

            const tarjeta = document.querySelector(`[data-libroid="${libroId}"]`);
            const estadoElem = tarjeta.querySelector('em');
            if (estadoElem) estadoElem.textContent = 'Estado: disponible';

            actualizarContador(-1);
        }
    })
    .catch(error => {
        mensaje.textContent = 'Error en la devolución';
        console.error("Error en devolverLibro:", error);
    })
    .finally(() => {
        enEspera = false;
        btnDevolver.disabled = false;
    });
}



function leerLibro(libroId) {
    window.location.href = `http://localhost:3000/Bibliotech/react-app?libroId=${libroId}`;
}

// Cargar partículas'particles-js'
if (document.getElementById('particles-js')) {
    particlesJS.load('particles-js', 'particles.json', function() {
        console.log('Particles.js cargado correctamente.');
    });
}
</script>

</body>
</html>
