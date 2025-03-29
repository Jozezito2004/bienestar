<?php
include 'conexion.php';

// Obtener la IP del cliente
$ip_cliente = $_SERVER['REMOTE_ADDR'];

// Verificar si la IP está autorizada
$sql = "SELECT COUNT(*) FROM ip_autorizadas WHERE direccion_ip = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $ip_cliente);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count == 0) {
    // IP no autorizada: Mostrar mensaje de acceso denegado
    http_response_code(403);
    echo "<div style='text-align: center; padding: 50px;'>";
    echo "<h1>Acceso Denegado</h1>";
    echo "<p>Tu dirección IP ($ip_cliente) no está autorizada para acceder a este sistema.</p>";
    echo "<p>Contacta al administrador para solicitar acceso.</p>";
    echo "</div>";
    exit();
}
?>