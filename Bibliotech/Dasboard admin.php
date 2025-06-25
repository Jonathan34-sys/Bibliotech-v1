<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "bibliotech";
$port = 3306;

$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName, $port);
if (!$conn) {
    die("Error en la conexión a la base de datos: " . mysqli_connect_error());
}


// Agregar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($nombre && $email && $password) {
        $passHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, correo, contraseña, tipo_usuario, fecha_registro, rol) VALUES (?, ?, ?, 'usuario', NOW(), 'user')");
        if ($stmt === false) {
            die("Error al preparar la consulta: " . $conn->error);
        }
        $stmt->bind_param('sss', $nombre, $email, $passHash);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "Por favor completa todos los campos para agregar usuario.";
    }
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $idEliminar = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $idEliminar);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Agregar libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_libro'])) {
    $titulo = trim($_POST['titulo']);
    $autor = trim($_POST['autor']);
    $anio = intval($_POST['anio']);
    $portada = trim($_POST['portada']);

    if ($titulo && $autor && $anio && $portada) {
        $stmt = $conn->prepare("INSERT INTO libros (titulo, autor, anio_publicacion, portada, fecha_agregado) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssis", $titulo, $autor, $anio, $portada);
        $stmt->execute();
        $stmt->close();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $errorLibro = "Por favor completa todos los campos para agregar libro.";
    }
}

// Eliminar libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_libro'])) {
    $idLibro = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM libros WHERE id = ?");
    $stmt->bind_param("i", $idLibro);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = $conn->query("SELECT id, nombre_usuario, correo, tipo_usuario, fecha_registro, rol FROM usuarios ORDER BY id DESC");
$resultLibros = $conn->query("SELECT id, titulo, autor, anio_publicacion, portada, fecha_agregado FROM libros ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Panel de Administración - Bibliotech</title>
  <link href="https://fonts.googleapis.com/css2?family=Exo:wght@400;700&display=swap" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Exo', sans-serif;
      background-color: #121212;
      color: #eee;
      display: flex;
      height: 100vh;
      overflow: hidden;
    }
    nav.sidebar {
      width: 220px;
      background: #1c1c1c;
      border-right: 3px solid #ff2d00;
      display: flex;
      flex-direction: column;
      padding-top: 20px;
    }
    nav.sidebar h2 {
      color: #ff2d00;
      text-align: center;
      margin-bottom: 30px;
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
    nav.sidebar button:hover, nav.sidebar button.active {
      background: #ff2d00;
      color: #121212;
      font-weight: bold;
      border-left: 4px solid #eee;
    }
    main.content {
      flex-grow: 1;
      padding: 30px;
      overflow-y: auto;
    }
    h2 { color: #ff2d00; }
    input, button {
      margin: 5px;
      padding: 10px;
      border-radius: 5px;
      border: none;
      font-size: 15px;
    }
    input { width: 200px; }
    button {
      cursor: pointer;
      background-color: #d32f2f;
      color: #fff;
    }
    button:hover { background-color: #b71c1c; }
    table {
      width: 100%;
      margin-top: 20px;
      background: #1e1e1e;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      border: 1px solid #444;
    }
    th {
      background-color: #ff2d00;
      color: #121212;
    }
    tr:hover { background-color: #333; }

    @media (max-width: 700px) {
      body { flex-direction: column; }
      nav.sidebar {
        width: 100%;
        flex-direction: row;
        border-bottom: 3px solid #ff2d00;
      }
      nav.sidebar button {
        flex: 1;
        text-align: center;
        border-left: none;
        border-bottom: 4px solid transparent;
      }
      nav.sidebar button:hover, nav.sidebar button.active {
        border-bottom: 4px solid #eee;
      }
    }
  </style>
  <script>
    function mostrarSeccion(seccion) {
      document.getElementById("usuarios").style.display = "none";
      document.getElementById("libros").style.display = "none";
      document.getElementById(seccion).style.display = "block";

      let botones = document.querySelectorAll("nav.sidebar button");
      botones.forEach(b => b.classList.remove("active"));
      if (seccion === "usuarios") botones[0].classList.add("active");
      else if (seccion === "libros") botones[1].classList.add("active");
    }
    function confirmarEliminar(nombre) {
      return confirm("¿Seguro que quieres eliminar a " + nombre + "?");
    }
 
 </script>
</head>
<body>
  <nav class="sidebar">
    <h2>Bibliotech</h2>
    <button class="active" onclick="mostrarSeccion('usuarios')">Usuarios</button>
    <button onclick="mostrarSeccion('libros')">Libros</button>
 
<button
    onclick="window.location.href='http://localhost/login/logout.php'">
    Cerrar sesión
</button>


 </nav>
  <main class="content">
    <section id="usuarios" style="display: block;">
      <h2>Agregar nuevo usuario</h2>
      <?php if (!empty($error)) echo "<p style='color:red; font-weight:bold;'>$error</p>"; ?>
      <form method="post">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" name="agregar">Agregar</button>
      </form>
      <table>
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Tipo</th>
            <th>Fecha Registro</th>
            <th>Rol</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($u = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
            <td><?= htmlspecialchars($u['correo']) ?></td>
            <td><?= htmlspecialchars($u['tipo_usuario']) ?></td>
            <td><?= htmlspecialchars($u['fecha_registro']) ?></td>
            <td><?= htmlspecialchars($u['rol']) ?></td>
            <td>
              <form method="post" onsubmit="return confirmarEliminar('<?= htmlspecialchars($u['nombre_usuario']) ?>');">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" name="eliminar">Eliminar</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
	<section id="libros" style="display: none;">
  <h2>Agregar nuevo libro</h2>
  <?php if (!empty($errorLibro)) echo "<p style='color:red; font-weight:bold;'>$errorLibro</p>"; ?>

<form action="http://localhost/Bibliotech/convertir_pdf.php" method="POST" enctype="multipart/form-data">
  <input type="text" name="titulo" placeholder="Título" required>
  <input type="text" name="autor" placeholder="Autor" required>
  <input type="number" name="anio_publicacion" placeholder="Año" required>
  <input type="text" name="portada" placeholder="URL de portada" required>
  <label for="archivo_pdf">📄 Selecciona PDF:</label>
  <input type="file" name="archivo_pdf" accept="application/pdf" required>
  <button type="submit">Guardar libro</button>
</form>


</section>

      <table>
        <thead>
          <tr>
            <th>Portada</th>
            <th>Título</th>
            <th>Autor</th>
            <th>Año</th>
            <th>Fecha</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($l = $resultLibros->fetch_assoc()): ?>
          <tr>
            <td><img src="<?= htmlspecialchars($l['portada']) ?>" alt="Portada" width="50"></td>
            <td><?= htmlspecialchars($l['titulo']) ?></td>
            <td><?= htmlspecialchars($l['autor']) ?></td>
            <td><?= htmlspecialchars($l['anio_publicacion']) ?></td>
            <td><?= htmlspecialchars($l['fecha_agregado']) ?></td>
            <td>
              <form method="post" onsubmit="return confirmarEliminar('<?= htmlspecialchars($l['titulo']) ?>');">
                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                <button type="submit" name="eliminar_libro">Eliminar</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>
