<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_colaborador = $_POST['id_colaborador'];
    $id_sesion = $_POST['id_sesion'];
    $evaluacion = $_POST['evaluacion'] ?: null;
    $estado_salud = $_POST['estado_salud'] ?: null;

    $stmt = $conexion->prepare("INSERT INTO asistencia (id_colaborador, id_sesion, evaluacion, estado_salud) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_colaborador, $id_sesion, $evaluacion, $estado_salud);

    if ($stmt->execute()) {
        echo "Asistencia registrada con éxito";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conexion->close();
?>