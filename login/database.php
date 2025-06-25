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
?>
