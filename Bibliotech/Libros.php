<?php
$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "bibliotech";
$port = 3306;

try {
    $db = new PDO("mysql:host=$hostName;port=$port;dbname=$dbName;charset=utf8", $dbUser, $dbPassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $libros = $db->query("SELECT titulo, anio_publicacion, portada FROM libros")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bibliotech - Libros</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        body {
            background: url('https://i.pinimg.com/originals/84/d9/9b/84d99badf7284b3d4547f0fb49ca4aa1.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: 'Exo', sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            overflow-x: hidden;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
        }
		
       .logo h1 {
    font-family: Arial, sans-serif;
    font-weight: bold;
    font-size: 3em;
    text-transform: uppercase;
    color: #ff2d00;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5), 
                 0 0 25px rgba(255, 0, 0, 0.5), 
                 0 0 5px rgba(255, 0, 0, 0.5);
    margin: 0;
}

        nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 15px;
            transition: background 0.3s;
            border-radius: 5px;
            display: inline-block;
        }

        nav ul li a:hover {
            background: red;
        }

        main {
            margin: 100px auto 80px;
            background-color: rgba(0, 0, 0, 0.7);
            width: 90%;
            max-width: 900px;
            padding: 40px 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px #ff2d00;
            text-align: center;
        }

        main h2 {
            font-size: 2.5em;
            color: #ff2d00;
            margin-bottom: 20px;
        }

        .libros-lista {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 20px;
            justify-items: center;
        }

        .libro {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 15px;
            width: 140px;
            box-shadow: 0 0 10px #ff2d00;
            transition: transform 0.3s;
        }

        .libro:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px #ff4a00;
        }

        .libro img {
            width: 120px;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .libro h3 {
            font-size: 1em;
            margin: 0 0 5px 0;
            color: #ff2d00;
            word-break: break-word;
        }

        .libro p {
            font-size: 0.9em;
            margin: 0;
        }

        footer {
            background: rgba(0, 0, 0, 0.8);
            padding: 10px;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 0.9em;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
         <div class="logo">
         <h1>Bibliotech</h1>
         </div>
        <nav>
             <ul>
              <li><a href="http://localhost/bibliotech/index.html">Inicio</a></li>
              <li><a href="http://localhost/bibliotech/Libros.php">Libros</a></li>
              <li><a href="http://localhost/bibliotech/Catalogo.html">Catálogo</a></li>
              <li><a href="http://localhost/login/login.php">Ingresar</a></li>
              <li><a href="http://localhost/login/registration.php">Registrarse</a></li>
              <li><a href="http://localhost/bibliotech/Preguntas PQR.HTML">Preguntas (PQR)</a></li>
              <li><a href="http://localhost/bibliotech/Contacto.html">Contacto</a></li>
           </ul>
        </nav>
    </header>

    <main>
        <h2>Explora nuestros libros</h2>
        <div class="libros-lista">
            <?php foreach ($libros as $libro): ?>
                <div class="libro">
                    <img src="<?= htmlspecialchars($libro['portada']) ?>" alt="Portada <?= htmlspecialchars($libro['titulo']) ?>">
                    <h3><?= htmlspecialchars($libro['titulo']) ?></h3>
                  <p>Año: <?= htmlspecialchars($libro['anio_publicacion']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        &copy; 2025 Bibliotech. Todos los derechos reservados.
    </footer>
</body>
</html>
