<?php
$host = "localhost:3307"; // Puerto de MySQL que configuraste
$usuario = "root";
$contrasena = "root"; // Contraseña predeterminada en MAMP
$base_datos = "bienestar_buap";

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>