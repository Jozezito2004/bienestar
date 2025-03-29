<?php
$page_title = "Test de Recuperación de Frecuencia Cardíaca - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php'; // Verificar acceso por IP

// Obtener el ID del colaborador desde la URL
$id_colaborador = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Obtener el nombre del colaborador
$colaborador = $conexion->query("SELECT nombre, apellido FROM colaboradores WHERE id_colaborador = $id_colaborador")->fetch_assoc();
if (!$colaborador) {
    die("Colaborador no encontrado.");
}

// Procesar el formulario cuando se envíe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hr_peak = isset($_POST['hr_peak']) ? (int)$_POST['hr_peak'] : 0;
    $hr_1min = isset($_POST['hr_1min']) ? (int)$_POST['hr_1min'] : 0;
    $hrv = isset($_POST['hrv']) ? (int)$_POST['hrv'] : null;
    $fecha = date('Y-m-d');

    // Calcular la diferencia de recuperación
    $recuperacion = $hr_peak - $hr_1min;

    // Validar datos
    if ($hr_peak < 30 || $hr_peak > 220 || $hr_1min < 30 || $hr_1min > 220) {
        $error = "Las frecuencias cardíacas deben estar entre 30 y 220 bpm.";
    } elseif ($hr_1min > $hr_peak) {
        $error = "La frecuencia cardíaca a 1 minuto no puede ser mayor que la HR pico.";
    } elseif ($hrv !== null && ($hrv < 10 || $hrv > 150)) {
        $error = "La HRV debe estar entre 10 y 150 ms.";
    } else {
        // Insertar o actualizar los datos
        $stmt = $conexion->prepare("INSERT INTO pruebas_actividad_fisica (id_colaborador, fecha_evaluacion, test_recuperacion_hr, test_recuperacion_hr_peak, test_recuperacion_hrv) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE test_recuperacion_hr = ?, test_recuperacion_hr_peak = ?, test_recuperacion_hrv = ?");
        $stmt->bind_param("isiiiii", $id_colaborador, $fecha, $recuperacion, $hr_peak, $hrv, $recuperacion, $hr_peak, $hrv);
        if ($stmt->execute()) {
            $success = "Resultados guardados correctamente. Recuperación a 1 minuto: " . $recuperacion . " bpm.";
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
            <h1 class="card-title">Test de Recuperación de Frecuencia Cardíaca (ACSM)</h1>
            <p class="card-text">Colaborador: <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre']); ?></p>
            <p>Mide cómo tu HR se recupera después de un esfuerzo intenso, con apoyo de HRV y SpO2.</p>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="test_recuperacion_hr.php?id=<?php echo $id_colaborador; ?>">
                <div class="form-group">
                    <label for="hr_peak">Frecuencia Cardíaca Pico al Final del Esfuerzo (bpm):</label>
                    <input type="number" class="form-control" id="hr_peak" name="hr_peak" min="30" max="220" required>
                </div>
                <div class="form-group">
                    <label for="hr_1min">Frecuencia Cardíaca a 1 Minuto Post-Esfuerzo (bpm):</label>
                    <input type="number" class="form-control" id="hr_1min" name="hr_1min" min="30" max="220" required>
                </div>
                <div class="form-group">
                    <label for="hrv">HRV Post-Ejercicio (ms, opcional):</label>
                    <input type="number" class="form-control" id="hrv" name="hrv" min="10" max="150">
                </div>
                <button type="submit" class="btn btn-primary">Guardar Resultados</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>