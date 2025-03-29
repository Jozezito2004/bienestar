<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numero_identificacion = $_POST['numero_identificacion'];
    $fecha_evaluacion = $_POST['fecha_evaluacion'];
    $estres = $_POST['estres'];
    $depresion = $_POST['depresion'];
    $burnout = $_POST['burnout'];

    // Obtener id_colaborador
    $stmt = $conexion->prepare("SELECT id_colaborador FROM colaboradores WHERE numero_identificacion = ?");
    $stmt->bind_param("s", $numero_identificacion);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $colaborador = $resultado->fetch_assoc();
    $stmt->close();

    if ($colaborador) {
        $id_colaborador = $colaborador['id_colaborador'];
        $stmt = $conexion->prepare("INSERT INTO pruebas_psicometricas (id_colaborador, fecha_evaluacion, estres, depresion, burnout) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiii", $id_colaborador, $fecha_evaluacion, $estres, $depresion, $burnout);
        $stmt->execute();
        $stmt->close();
        echo "Evaluación guardada con éxito. <a href='ver_colaboradores.php'>Volver</a>";
    } else {
        echo "Colaborador no encontrado.";
    }
}
$conexion->close();
?>