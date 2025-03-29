<?php
include 'conexion.php';

// Array de alimentos (abreviado aquí, incluye todos los ítems del script anterior)
$alimentos = [
    [1, "Avena en hojuelas", "1/3 taza", 70, 15.00, 0.00, 2.00],
    [1, "Arroz blanco o integral cocido", "1/2 taza", 70, 15.00, 0.00, 2.00],
    [2, "Pan dulce (concha, cuernito)", "1 pieza", 115, 15.00, 5.00, 2.00],
    [3, "Lechuga", "3 tazas", 25, 4.00, 0.00, 2.00],
    [17, "Agua mineral", "1 taza", 0, 0.00, 0.00, 0.00],
    [18, "Hamburguesa con queso", "1 pieza", 300, 30.00, 15.00, 15.00],
    // ... Agrega aquí TODOS los ítems del array $alimentos del script anterior
];

// Preparar la consulta de inserción
$stmt = $conexion->prepare("INSERT INTO alimentos (grupo_id, nombre, racion, energia, carbohidratos, grasa, proteina) VALUES (?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$success_count = 0;
foreach ($alimentos as $alimento) {
    [$grupo_id, $nombre, $racion, $energia, $carbohidratos, $grasa, $proteina] = $alimento;
    $stmt->bind_param("issdddd", $grupo_id, $nombre, $racion, $energia, $carbohidratos, $grasa, $proteina);
    if ($stmt->execute()) {
        $success_count++;
    } else {
        echo "Error al insertar '$nombre': " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conexion->close();

echo "Se insertaron $success_count alimentos exitosamente.";
?>