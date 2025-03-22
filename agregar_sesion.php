<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fecha = $_POST['fecha'];
    $turno = $_POST['turno'];
    $tipo_actividad = $_POST['tipo_actividad'];
    $duracion = $_POST['duracion'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conexion->prepare("INSERT INTO sesiones (fecha, turno, tipo_actividad, duracion, descripcion) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssds", $fecha, $turno, $tipo_actividad, $duracion, $descripcion);

    if ($stmt->execute()) {
        echo "Sesión agregada con éxito";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conexion->close();
?>