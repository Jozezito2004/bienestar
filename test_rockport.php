<?php
$page_title = "Test de Rockport - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php'; // Verificar acceso por IP

// Obtener el ID del colaborador desde la URL
$id_colaborador = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener el nombre del colaborador
$colaborador = $conexion->query("SELECT nombre, apellido, edad, peso, genero FROM colaboradores WHERE id_colaborador = $id_colaborador")->fetch_assoc();
if (!$colaborador) {
    die("Colaborador no encontrado.");
}

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tiempo = isset($_POST['tiempo']) ? (float)$_POST['tiempo'] : 0;
    $hr = isset($_POST['hr']) ? (int)$_POST['hr'] : 0;
    $fecha = date('Y-m-d');

    // Validar datos
    if ($tiempo <= 0) {
        $error = "El tiempo debe ser mayor a 0 minutos.";
    } elseif ($hr < 30 || $hr > 220) {
        $error = "La frecuencia cardíaca debe estar entre 30 y 220 bpm.";
    } else {
        // Calcular VO2 máx usando la fórmula de Rockport
        $peso_libras = $colaborador['peso'] * 2.2; // Convertir peso a libras
        $genero = ($colaborador['genero'] == 'M') ? 1 : 0; // 1 para hombres, 0 para mujeres
        $vo2_max = 132.853 - (0.0769 * $peso_libras) - (0.3877 * $colaborador['edad']) + (6.315 * $genero) - (3.2649 * $tiempo) - (0.1565 * $hr);

        // Insertar o actualizar los datos
        $stmt = $conexion->prepare("INSERT INTO pruebas_actividad_fisica (id_colaborador, fecha_evaluacion, test_rockport, test_rockport_hr, test_rockport_tiempo) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE test_rockport = ?, test_rockport_hr = ?, test_rockport_tiempo = ?");
        $stmt->bind_param("isdiidid", $id_colaborador, $fecha, $vo2_max, $hr, $tiempo, $vo2_max, $hr, $tiempo);
        if ($stmt->execute()) {
            $success = "Resultados guardados correctamente. VO2 Máx estimado: " . round($vo2_max, 2) . " ml/kg/min.";
        } else {
            $error = "Error al guardar los resultados: " . $conexion->error;
        }
        $stmt->close();
    }
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Test de Rockport (ACSM)</h1>
            <p class="card-text">Colaborador: <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre']); ?></p>
            <p>Evalúa tu capacidad aeróbica (VO2 máx) caminando 1 milla, con monitoreo de HR, tiempo y SpO2.</p>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="test_rockport.php?id=<?php echo $id_colaborador; ?>">
                <div class="form-group">
                    <label for="tiempo">Tiempo de Caminata (minutos, formato decimal, ej. 15.5 para 15 min 30 seg):</label>
                    <input type="number" step="0.01" class="form-control" id="tiempo" name="tiempo" min="0" required>
                </div>
                <div class="form-group">
                    <label for="hr">Frecuencia Cardíaca al Final del Test (bpm):</label>
                    <input type="number" class="form-control" id="hr" name="hr" min="30" max="220" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Resultados</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>