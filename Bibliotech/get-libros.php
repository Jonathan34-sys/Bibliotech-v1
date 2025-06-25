<?php

header("Access-Control-Allow-Origin: *");



header('Content-Type: application/json');

$baseDir = __DIR__ . '/libros';
$libros = [];

foreach (scandir($baseDir) as $folder) {
    if ($folder === '.' || $folder === '..') continue;

    $libroPath = "$baseDir/$folder";
    if (is_dir($libroPath)) {
        $paginas = array_filter(scandir($libroPath), function ($file) use ($libroPath) {
            return is_file("$libroPath/$file") && preg_match('/\.jpg$/', $file);
        });

        sort($paginas); 
        $libros[$folder] = array_values($paginas); 
    }
}

echo json_encode($libros);
