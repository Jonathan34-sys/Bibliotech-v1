<?php
session_start();

if (isset($_SESSION["user"]) && isset($_SESSION["rol"])) {
    $dashboard = ($_SESSION["rol"] === "admin") ? "Dasboard%20admin.php" : "Dasboard%20de%20Usu.php";
    header("Location: http://localhost/Bibliotech/$dashboard");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    require_once "database.php";

    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user["contraseña"])) {
        $_SESSION["user"] = $user["nombre_usuario"];
        $_SESSION["rol"] = $user["rol"];
        $_SESSION["usuario_id"] = $user["id"];

        $dashboard = ($user["rol"] === "admin") ? "Dasboard%20admin.php" : "Dasboard%20de%20Usu.php";
        header("Location: http://localhost/Bibliotech/$dashboard");
        exit;
    } else {
        $error = $user ? "La contraseña no coincide" : "El correo no está registrado";
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Bibliotech - Iniciar sesión</title>
  <link rel="stylesheet" href="estilos.css">
  <style>
    body {
      background: url('https://i.pinimg.com/originals/84/d9/9b/84d99badf7284b3d4547f0fb49ca4aa1.jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      font-family: Arial, sans-serif;
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
      padding: 50px;
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      width: 300px;
      margin: 100px auto;
      border-radius: 10px;
      text-align: center;
    }

    .form-container h2 {
      font-size: 2em;
      margin-bottom: 20px;
    }

    .form-container input {
      padding: 10px;
      margin-bottom: 20px;
      border: 2px solid #ff2d00;
      border-radius: 5px;
      font-size: 16px;
      width: 100%;
      box-sizing: border-box;
      display: block;
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
    }

    .error-msg {
      color: red;
      font-weight: bold;
      margin-top: 10px;
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
      <h2>Iniciar sesión</h2>
      <?php if (isset($error)) echo "<p class='error-msg'>" . htmlspecialchars($error) . "</p>"; ?>
      <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Correo" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" name="login">Iniciar Sesión</button>
      </form>
      <p>¿No tienes cuenta? <a href="http://localhost/login/registration.php" style="color: #ff2d00;">Regístrate aquí</a></p>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 Bibliotech. Todos los derechos reservados.</p>
  </footer>

  <script>
    const canvas = document.getElementById('particles');
    const ctx = canvas.getContext('2d');
    let width, height;
    let particlesArray = [];

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
      for (let i = 0; i < num; i++) {
        particlesArray.push(new Particle());
      }
    }

    function animate() {
      ctx.clearRect(0, 0, width, height);
      particlesArray.forEach(p => {
        p.update();
        p.draw();
      });
      requestAnimationFrame(animate);
    }

    window.addEventListener('resize', () => {
      initCanvas();
      createParticles(100);
    });

    window.onload = () => {
      initCanvas();
      createParticles(100);
      animate();
    };
  </script>
</body>
</html>
