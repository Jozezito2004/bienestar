<?php
$page_title = "Cargar Alimentación - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';

// Probar la consulta
$alimentos = $conexion->query("SELECT id, nombre, racion FROM alimentos ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
if (!$alimentos) {
    die("Error en la consulta: " . $conexion->error);
}

echo "<pre>";
print_r($alimentos);
echo "</pre>";

$conexion->close();
?>