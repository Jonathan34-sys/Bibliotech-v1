<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['archivo_pdf'])) {
        echo "❌ Archivo PDF no cargado.";
        exit;
    }

    $archivo = $_FILES['archivo_pdf'];
    if ($archivo['type'] !== 'application/pdf') {
        echo "❌ Solo se permiten archivos PDF.";
        exit;
    }

    // Recoger datos 
    $titulo = $_POST['titulo'] ?? '';
    $autor = $_POST['autor'] ?? '';
    $anio = $_POST['anio_publicacion'] ?? '';
    $portada = $_POST['portada'] ?? '';

    if ($titulo === '' || $autor === '' || $anio === '' || $portada === '') {
        echo "❌ Faltan campos obligatorios.";
        exit;
    }

    // Crear carpeta única
    $rutaBase = "C:/laragon/www/Bibliotech/react-app/src/Pages";
    if (!file_exists($rutaBase)) {
        mkdir($rutaBase, 0777, true);
    }

    $indice = 1;
    while (is_dir("$rutaBase/libro_$indice")) {
        $indice++;
    }
    $nombreCarpeta = "$rutaBase/libro_$indice";
    mkdir($nombreCarpeta);

    // Guardar PDF
    $rutaPDF = "$nombreCarpeta/libro.pdf";
    move_uploaded_file($archivo['tmp_name'], $rutaPDF);

    // Convertir PDF a imágenes
    $rutaSalida = "$nombreCarpeta/pagina";
    $comando = "pdftoppm -jpeg \"$rutaPDF\" \"$rutaSalida\"";
    exec($comando);

    $archivos = glob("$nombreCarpeta/pagina-*.jpg");
    natsort($archivos);
    $pagina = 1;
    foreach ($archivos as $img) {
        rename($img, "$nombreCarpeta/pagina_$pagina.jpg");
        $pagina++;
    }

    // Conexión a BD
    $conexion = new mysqli("localhost", "root", "", "bibliotech");
    if ($conexion->connect_error) {
        die("❌ Error al conectar: " . $conexion->connect_error);
    }

    $ubicacion = $conexion->real_escape_string($nombreCarpeta);
    $titulo = $conexion->real_escape_string($titulo);
    $autor = $conexion->real_escape_string($autor);
    $portada = $conexion->real_escape_string($portada);
    $anio = intval($anio); 

    
    $sql = "INSERT INTO libros (titulo, autor, anio_publicacion, portada, ubicacion)
            VALUES ('$titulo', '$autor', $anio, '$portada', '$ubicacion')";

    if ($conexion->query($sql) === TRUE) {
        header("Location: http://localhost/Bibliotech/Dasboard%20admin.php");
        exit;
    } else {
        echo "❌ Error SQL: " . $conexion->error;
    }

    $conexion->close();
}
?>
