<?php
$page_title = "Test de Depresión - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php'; // Verificar acceso por IP

// Verificar si se ha enviado el ID del colaborador
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-4'><div class='card'><div class='card-body'>";
    echo "<h1 class='card-title'>Error</h1>";
    echo "<p>No se ha seleccionado un colaborador válido.</p>";
    echo "<a href='cargar_pruebas_psicometricas.php' class='btn'>Volver</a>";
    echo "</div></div></div>";
    include 'footer.php';
    exit();
}

$id_colaborador = (int)$_GET['id'];

// Obtener el nombre del colaborador
$stmt = $conexion->prepare("SELECT nombre, apellido FROM colaboradores WHERE id_colaborador = ?");
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$result = $stmt->get_result();
$colaborador = $result->fetch_assoc();
$stmt->close();

if (!$colaborador) {
    echo "<div class='container mt-4'><div class='card'><div class='card-body'>";
    echo "<h1 class='card-title'>Error</h1>";
    echo "<p>No se encontró el colaborador con ID: $id_colaborador.</p>";
    echo "<a href='cargar_pruebas_psicometricas.php' class='btn'>Volver</a>";
    echo "</div></div></div>";
    include 'footer.php';
    exit();
}

// Procesar el formulario del test si se ha enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuestas = [];
    $puntaje_total = 0;

    // Calcular el puntaje total sumando las respuestas
    for ($i = 1; $i <= 9; $i++) {
        $respuesta = isset($_POST["pregunta_$i"]) ? (int)$_POST["pregunta_$i"] : 0;
        $respuestas[] = $respuesta;
        $puntaje_total += $respuesta;
    }

    // Determinar el nivel de depresión según el puntaje
    if ($puntaje_total <= 4) {
        $nivel_depresion = "Depresión mínima";
    } elseif ($puntaje_total <= 9) {
        $nivel_depresion = "Depresión leve";
    } elseif ($puntaje_total <= 14) {
        $nivel_depresion = "Depresión moderada";
    } elseif ($puntaje_total <= 19) {
        $nivel_depresion = "Depresión moderadamente severa";
    } else {
        $nivel_depresion = "Depresión severa";
    }

    // Guardar el resultado en la tabla pruebas_psicometricas
    $sql = "INSERT INTO pruebas_psicometricas (id_colaborador, fecha_evaluacion, estres, depresion, burnout, ansiedad) 
            VALUES (?, CURDATE(), NULL, ?, NULL, NULL) 
            ON DUPLICATE KEY UPDATE fecha_evaluacion = CURDATE(), depresion = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $id_colaborador, $puntaje_total, $puntaje_total);
    $stmt->execute();
    $stmt->close();

    // Mostrar los resultados
    echo "<div class='container mt-4'><div class='card'><div class='card-body'>";
    echo "<h1 class='card-title'>Resultados del Test de Depresión</h1>";
    echo "<p><strong>Colaborador:</strong> " . htmlspecialchars($colaborador['nombre'] . " " . $colaborador['apellido']) . "</p>";
    echo "<p><strong>Puntaje Total:</strong> $puntaje_total</p>";
    echo "<p><strong>Nivel de Depresión:</strong> $nivel_depresion</p>";
    echo "<a href='cargar_pruebas_psicometricas.php' class='btn'>Volver</a>";
    echo "</div></div></div>";
    include 'footer.php';
    exit();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Test de Depresión (PHQ-9)</h1>
            <p class="card-text">A continuación, se presenta el Test de Depresión PHQ-9 para el colaborador: <strong><?php echo htmlspecialchars($colaborador['nombre'] . " " . $colaborador['apellido']); ?></strong>. Por favor, responde cada pregunta según cómo te has sentido en las últimas dos semanas.</p>

            <form method="POST" action="test_depresion.php?id=<?php echo $id_colaborador; ?>" class="import-container">
                <p><strong>Instrucciones:</strong> Para cada pregunta, selecciona la opción que mejor describa cómo te has sentido en las últimas dos semanas.</p>

                <!-- Pregunta 1 -->
                <label>1. Poco interés o placer en hacer cosas:</label>
                <select name="pregunta_1" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <!-- Pregunta 2 -->
                <label>2. Sentirse deprimido, triste o sin esperanzas:</label>
                <select name="pregunta_2" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <!-- Pregunta 3 -->
                <label>3. Dificultad para conciliar o mantener el sueño, o dormir demasiado:</label>
                <select name="pregunta_3" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <!-- Pregunta 4 -->
                <label>4. Sentirse cansado o con poca energía:</label>
                <select name="pregunta_4" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <!-- Pregunta 5 -->
                <label>5. Poco apetito o comer en exceso:</label>
                <select name="pregunta_5" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <!-- Pregunta 6 -->
                <label>6. Sentirse mal consigo mismo, o que es un fracaso, o que ha decepcionado a su familia o a sí mismo:</label>
                <select name="pregunta_6" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <!-- Pregunta 7 -->
                <label>7. Dificultad para concentrarse en cosas, como leer el periódico o ver televisión:</label>
                <select name="pregunta_7" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <!-- Pregunta 8 -->
                <label>8. Moverse o hablar tan despacio que otras personas lo han notado, o lo contrario, estar tan inquieto o agitado que ha estado moviéndose más de lo habitual:</label>
                <select name="pregunta_8" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <!-- Pregunta 9 -->
                <label>9. Pensamientos de que estaría mejor muerto o de hacerse daño de alguna manera:</label>
                <select name="pregunta_9" required>
                    <option value="0">Nada (0)</option>
                    <option value="1">Varios días (1)</option>
                    <option value="2">Más de la mitad de los días (2)</option>
                    <option value="3">Casi todos los días (3)</option>
                </select>

                <button type="submit" class="btn">Enviar Test</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>