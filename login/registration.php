<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$mensaje = "";

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "bibliotech");

    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $nombre = trim($_POST["nombre"]);
    $correo = trim($_POST["correo"]);
    $clave = trim($_POST["clave"]);

    if (empty($nombre) || empty($correo) || empty($clave)) {
        $mensaje = "Por favor, rellena todos los campos.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Correo no válido.";
    } elseif (strlen($clave) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt_check->bind_param("s", $correo);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $mensaje = "El correo ya está registrado.";
        } else {
            $clave_hashed = password_hash($clave, PASSWORD_DEFAULT);
            $tipo_usuario = 'usuario';
            $rol = 'user';

            $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, correo, contraseña, tipo_usuario, fecha_registro, rol)
                                    VALUES (?, ?, ?, ?, NOW(), ?)");
            $stmt->bind_param("sssss", $nombre, $correo, $clave_hashed, $tipo_usuario, $rol);

            if ($stmt->execute()) {
                $mensaje = "¡Registrado correctamente!";
            } else {
                $mensaje = "Error al registrar: " . $stmt->error;
            }

            $stmt->close();
        }

        $stmt_check->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bibliotech - Registrarse</title>
 <link rel="stylesheet" href="estilos.css">
  <style>
 body {
            background: url('https://i.pinimg.com/originals/84/d9/9b/84d99badf7284b3d4547f0fb49ca4aa1.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: 'Exo', Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
            overflow-x: hidden;
        }

        #particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            pointer-events: none;
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
            text-shadow:
                2px 2px 5px rgba(0, 0, 0, 0.5),
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
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 15px;
            transition: 0.3s;
        }

        nav ul li a:hover {
            background: red;
            border-radius: 5px;
        }

        .form-container {
            width: 80%;
            max-width: 600px;
            margin: 120px auto;
            padding: 40px 20px;
            border-radius: 10px;
            text-align: center;
            background: rgba(0, 0, 0, 0.6); /* <- Capa semitransparente negra */
        }

        .form-container h2 {
            font-size: 2.5em;
            color: #ffffff;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .form-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid #ff2d00;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            background: #ffffff;
            color: black;
            outline: none;
        }

        .form-container input::placeholder {
            color: black;
        }

        .form-container input:focus {
            border-color: #ff2d00;
            box-shadow: 0 0 8px #ff2d00;
        }

        .form-container button {
            padding: 12px;
            background-color: #ff2d00;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #d02400;
        }

        footer {
            background: rgba(0, 0, 0, 0.8);
            padding: 10px;
            margin-top: 20px;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 0.9em;
        }
  </style>
</head>
<body>
<canvas id="particles"></canvas>
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
  <div class="form-container">
    <h2>Registro de usuario</h2>
    <form action="registration.php" method="POST">
      <input type="text" name="nombre" placeholder="Nombre completo" required />
      <input type="email" name="correo" placeholder="Correo electrónico" required />
      <input type="password" name="clave" placeholder="Contraseña" required />
      <button type="submit">Registrarse</button>
    </form>
    <?php if (!empty($mensaje)) echo "<p style='margin-top:15px;color:#ff4d4d;'>$mensaje</p>"; ?>
  </div>
</main>

<footer>
  <p>&copy; 2025 Bibliotech. Todos los derechos reservados.</p>
</footer>

<script>
  const canvas = document.getElementById('particles');
  const ctx = canvas.getContext('2d');
  let width, height, particlesArray = [];

  function initCanvas() {
    width = window.innerWidth;
    height = window.innerHeight;
    canvas.width = width;
    canvas.height = height;
  }

  class Particle {
    constructor() {
      this.x = Math.random() * width;
      this.y = Math.random() * height;
      this.size = Math.random() * 3 + 1;
      this.speedX = (Math.random() - 0.5) * 1.5;
      this.speedY = (Math.random() - 0.5) * 1.5;
      this.color = 'rgba(255, 0, 0, 0.7)';
    }
    update() {
      this.x += this.speedX;
      this.y += this.speedY;
      if (this.x < 0 || this.x > width) this.speedX *= -1;
      if (this.y < 0 || this.y > height) this.speedY *= -1;
    }
    draw() {
      ctx.beginPath();
      ctx.fillStyle = this.color;
      ctx.shadowColor = 'rgba(255, 0, 0, 0.8)';
      ctx.shadowBlur = 10;
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fill();
      ctx.closePath();
    }
  }

  function createParticles(num) {
    particlesArray = [];
    for (let i = 0; i < num; i++) particlesArray.push(new Particle());
  }

  function animate() {
    ctx.clearRect(0, 0, width, height);
    particlesArray.forEach(p => { p.update(); p.draw(); });
    requestAnimationFrame(animate);
  }

  window.addEventListener('resize', () => {
    initCanvas();
    createParticles(100);
  });

  initCanvas();
  createParticles(100);
  animate();
</script>
</body>
</html>
