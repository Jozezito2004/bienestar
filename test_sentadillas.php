<?php
$page_title = "Test de Sentadillas con Peso Corporal - Bienestar BUAP";
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
    $repeticiones = isset($_POST['repeticiones']) ? (int)$_POST['repeticiones'] : 0;
    $hr = isset($_POST['hr']) ? (int)$_POST['hr'] : null;
    $fecha = date('Y-m-d');

    // Validar datos
    if ($repeticiones <= 0) {
        $error = "El número de repeticiones debe ser mayor a 0.";
    } elseif ($hr !== null && ($hr < 30 || $hr > 220)) {
        $error = "La frecuencia cardíaca debe estar entre 30 y 220 bpm.";
    } else {
        // Insertar o actualizar los datos
        $stmt = $conexion->prepare("INSERT INTO pruebas_actividad_fisica (id_colaborador, fecha_evaluacion, sentadillas_peso_corporal, sentadillas_peso_corporal_hr) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE sentadillas_peso_corporal = ?, sentadillas_peso_corporal_hr = ?");
        $stmt->bind_param("isiiii", $id_colaborador, $fecha, $repeticiones, $hr, $repeticiones, $hr);
        if ($stmt->execute()) {
            $success = "Resultados guardados correctamente.";
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
            <h1 class="card-title">Test de Sentadillas con Peso Corporal (NSCA)</h1>
            <p class="card-text">Colaborador: <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre']); ?></p>
            <p>Mide la resistencia del tren inferior (cuádriceps, glúteos), esencial para actividades como correr o danza.</p>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="test_sentadillas.php?id=<?php echo $id_colaborador; ?>">
                <div class="form-group">
                    <label for="repeticiones">Número de Repeticiones (en 1 minuto):</label>
                    <input type="number" class="form-control" id="repeticiones" name="repeticiones" min="0" required>
                </div>
                <div class="form-group">
                    <label for="hr">Frecuencia Cardíaca durante el Test (bpm, opcional):</label>
                    <input type="number" class="form-control" id="hr" name="hr" min="30" max="220">
                </div>
                <button type="submit" class="btn btn-primary">Guardar Resultados</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>