<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numero_identificacion = $_POST['numero_identificacion'];
    $peso = $_POST['peso'];
    $talla = $_POST['talla'] / 100; // Convertir a metros
    $genero = $_POST['genero'];
    $edad = $_POST['edad'];
    $nivel_actividad = $_POST['nivel_actividad'];

    // Calcular IMC
    $imc = $peso / ($talla * $talla);

    // Calcular Metabolismo Basal (Fórmula de Mifflin-St Jeor)
    if ($genero == 'M') {
        $mb = 10 * $peso + 6.25 * ($talla * 100) - 5 * $edad + 5;
    } else {
        $mb = 10 * $peso + 6.25 * ($talla * 100) - 5 * $edad - 161;
    }

    // Calcular calorías diarias
    $calorias_diarias = $mb * $nivel_actividad;

    // Guardar en la base de datos
    $stmt = $conexion->prepare("SELECT id_colaborador FROM colaboradores WHERE numero_identificacion = ?");
    $stmt->bind_param("s", $numero_identificacion);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $colaborador = $resultado->fetch_assoc();
    $stmt->close();

    if ($colaborador) {
        $id_colaborador = $colaborador['id_colaborador'];
        $stmt = $conexion->prepare("INSERT INTO alimentacion (id_colaborador, fecha_registro, peso, talla, imc, metabolismo_basal, calorias_diarias) VALUES (?, NOW(), ?, ?, ?, ?, ?)");
        $stmt->bind_param("iddddd", $id_colaborador, $peso, $talla, $imc, $mb, $calorias_diarias);
        $stmt->execute();
        $stmt->close();

        echo "IMC: " . round($imc, 2) . "<br>";
        echo "Metabolismo Basal: " . round($mb) . " kcal<br>";
        echo "Calorías Diarias: " . round($calorias_diarias) . " kcal<br>";
        echo "<a href='recomendaciones_ia.php?id_colaborador=$id_colaborador'>Ver Recomendaciones</a>";
    } else {
        echo "Colaborador no encontrado.";
    }
}
$conexion->close();
?>