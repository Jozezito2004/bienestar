<?php
ob_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
    header("Location: cargar_alimentacion.php?error=No se subió ningún archivo CSV.");
    exit();
}

$csv_file = $_FILES['archivo_csv']['tmp_name'];
$sobrescribir = isset($_POST['sobrescribir']);

if (($handle = fopen($csv_file, "r")) === FALSE) {
    header("Location: cargar_alimentacion.php?error=Error al abrir el CSV.");
    exit();
}

$headers = fgetcsv($handle, 1000, ",");
if ($headers === FALSE) {
    fclose($handle);
    header("Location: cargar_alimentacion.php?error=CSV vacío o ilegible.");
    exit();
}

$expected_headers = ['Timestamp', 'Número de Identificación', 'Desayuno', 'Comida', 'Cena'];
$missing_headers = array_diff($expected_headers, $headers);
if (!empty($missing_headers)) {
    fclose($handle);
    header("Location: cargar_alimentacion.php?error=Faltan columnas: " . implode(", ", $missing_headers));
    exit();
}

$indices = array_map(fn($header) => array_search($header, $headers), $expected_headers);

$success_count = 0;
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if (count($data) < count($expected_headers)) continue;

    $timestamp = $data[$indices[0]] ?? '';
    $numero_identificacion = trim($data[$indices[1]] ?? '');
    $desayuno = trim($data[$indices[2]] ?? '');
    $comida = trim($data[$indices[3]] ?? '');
    $cena = trim($data[$indices[4]] ?? '');

    if (empty($numero_identificacion) || empty($desayuno) || empty($comida) || empty($cena) || empty($timestamp)) continue;

    $stmt = $conexion->prepare("SELECT id_colaborador FROM colaboradores WHERE numero_identificacion = ?");
    $stmt->bind_param("s", $numero_identificacion);
    $stmt->execute();
    $colaborador = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$colaborador) continue;

    $id_colaborador = $colaborador['id_colaborador'];
    $fecha_registro = date('Y-m-d H:i:s', strtotime($timestamp));
    if ($fecha_registro === false) continue;

    $desayuno_ids = array_filter(explode(',', $desayuno), 'is_numeric');
    $comida_ids = array_filter(explode(',', $comida), 'is_numeric');
    $cena_ids = array_filter(explode(',', $cena), 'is_numeric');

    $stmt = $conexion->prepare("SELECT id_alimentacion FROM alimentacion_diaria WHERE id_colaborador = ? AND DATE(fecha_registro) = DATE(?)");
    $stmt->bind_param("is", $id_colaborador, $fecha_registro);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exists && $sobrescribir) {
        $desayuno_json = json_encode($desayuno_ids);
        $comida_json = json_encode($comida_ids);
        $cena_json = json_encode($cena_ids);
        $stmt = $conexion->prepare("UPDATE alimentacion_diaria SET desayuno = ?, comida = ?, cena = ?, fecha_registro = ? WHERE id_colaborador = ? AND DATE(fecha_registro) = DATE(?)");
        $stmt->bind_param("ssssis", $desayuno_json, $comida_json, $cena_json, $fecha_registro, $id_colaborador, $fecha_registro);
        if ($stmt->execute()) $success_count++;
        $stmt->close();
    } elseif (!$exists) {
        $desayuno_json = json_encode($desayuno_ids);
        $comida_json = json_encode($comida_ids);
        $cena_json = json_encode($cena_ids);
        $stmt = $conexion->prepare("INSERT INTO alimentacion_diaria (id_colaborador, numero_identificacion, desayuno, comida, cena, fecha_registro) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $id_colaborador, $numero_identificacion, $desayuno_json, $comida_json, $cena_json, $fecha_registro);
        if ($stmt->execute()) $success_count++;
        $stmt->close();
    }
}

fclose($handle);
$conexion->close();

ob_end_clean();
header("Location: cargar_alimentacion.php?success=Se procesaron $success_count registros desde el CSV.");
exit();
?>