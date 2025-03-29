<?php
$page_title = "Test de Ansiedad Laboral - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php'; // Verificar acceso por IP

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_colaborador = isset($_POST['id_colaborador']) ? (int)$_POST['id_colaborador'] : 0;
    $respuestas = [];

    // Obtener las respuestas del formulario
    for ($i = 1; $i <= 20; $i++) {
        $respuestas[$i] = isset($_POST["pregunta_$i"]) ? (int)$_POST["pregunta_$i"] : 1; // 1 es el valor mínimo
    }

    // Calcular la puntuación total
    $total_score = array_sum($respuestas);

    // Determinar el nivel de ansiedad (1-10)
    // Normalizamos la puntuación total (20-80) a una escala de 1-10
    // Fórmula: ((puntuación - mínimo) / (máximo - mínimo)) * (nuevo_máximo - nuevo_mínimo) + nuevo_mínimo
    $ansiedad_level = round((($total_score - 20) / (80 - 20)) * (10 - 1) + 1);
    $ansiedad_level = max(1, min(10, $ansiedad_level)); // Aseguramos que esté entre 1 y 10

    // Guardar los resultados en la base de datos
    $sql = "INSERT INTO pruebas_psicometricas (id_colaborador, fecha_evaluacion, estres, depresion, burnout, ansiedad) 
            VALUES (?, CURDATE(), NULL, NULL, NULL, ?)
            ON DUPLICATE KEY UPDATE fecha_evaluacion = CURDATE(), ansiedad = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $id_colaborador, $ansiedad_level, $ansiedad_level);
    $stmt->execute();
    $stmt->close();

    // Mostrar los resultados
    $resultado = [
        'total_score' => $total_score,
        'ansiedad_level' => $ansiedad_level
    ];
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Test de Ansiedad Laboral</h1>
            <p class="card-text">A continuación, encontrarás una serie de enunciados acerca de cómo te sientes en relación con tu trabajo. No existen respuestas malas ni buenas. El objetivo de este cuestionario es conocer tu nivel de ansiedad laboral. A cada una de las frases debes responder expresando la frecuencia con que experimentas ese sentimiento.</p>

            <?php if (isset($resultado)): ?>
                <!-- Mostrar resultados -->
                <div class="section">
                    <h2>Resultados del Test de Ansiedad Laboral</h2>
                    <p><strong>Puntuación Total:</strong> <?php echo $resultado['total_score']; ?> / 80 
                        (<?php 
                            if ($resultado['total_score'] <= 35) echo "Bajo";
                            elseif ($resultado['total_score'] <= 50) echo "Moderado";
                            else echo "Alto";
                        ?>)
                    </p>
                    <p><strong>Nivel de Ansiedad (1-10):</strong> <?php echo $resultado['ansiedad_level']; ?></p>
                    <a href="ver_colaboradores.php" class="btn">Volver a Colaboradores</a>
                </div>
            <?php else: ?>
                <!-- Formulario del test -->
                <form method="POST" action="test_ansiedad.php" class="import-container">
                    <input type="hidden" name="id_colaborador" value="<?php echo isset($_GET['id']) ? (int)$_GET['id'] : 0; ?>">
                    <table id="ansiedad-table">
                        <tr>
                            <th>Enunciado</th>
                            <th>Nunca (1)</th>
                            <th>A veces (2)</th>
                            <th>Frecuentemente (3)</th>
                            <th>Siempre (4)</th>
                        </tr>
                        <?php
                        $enunciados = [
                            1 => "Me siento nervioso o ansioso antes de ir al trabajo.",
                            2 => "Tengo dificultades para concentrarme en mis tareas debido a preocupaciones laborales.",
                            3 => "Siento que mi trabajo me abruma frecuentemente.",
                            4 => "Me preocupo constantemente por cometer errores en mi trabajo.",
                            5 => "Siento tensión o presión en el pecho cuando pienso en mis responsabilidades laborales.",
                            6 => "Tengo problemas para dormir porque pienso en mi trabajo.",
                            7 => "Me siento irritable o impaciente con mis compañeros de trabajo.",
                            8 => "Siento que no puedo cumplir con las expectativas de mi jefe o equipo.",
                            9 => "Experimentó palpitaciones o taquicardia cuando estoy en el trabajo.",
                            10 => "Me siento agotado emocionalmente por las demandas de mi trabajo.",
                            11 => "Tengo miedo de no poder manejar situaciones difíciles en el trabajo.",
                            12 => "Siento que mi trabajo afecta negativamente mi vida personal.",
                            13 => "Me preocupo por mi desempeño laboral incluso fuera del horario de trabajo.",
                            14 => "Siento que no tengo control sobre las exigencias de mi trabajo.",
                            15 => "Tengo pensamientos recurrentes sobre problemas laborales que no puedo controlar.",
                            16 => "Siento una sensación de inquietud o nerviosismo durante mi jornada laboral.",
                            17 => "Me siento inseguro sobre mi capacidad para realizar mis tareas laborales.",
                            18 => "Siento que mi trabajo me genera estrés constante.",
                            19 => "Tengo síntomas físicos (como dolores de cabeza o estómago) relacionados con mi trabajo.",
                            20 => "Me siento ansioso por el futuro de mi carrera o estabilidad laboral."
                        ];

                        $opciones = [
                            1 => "Nunca (1)",
                            2 => "A veces (2)",
                            3 => "Frecuentemente (3)",
                            4 => "Siempre (4)"
                        ];

                        foreach ($enunciados as $num => $enunciado): ?>
                            <tr>
                                <td data-label="Enunciado"><?php echo $num . ". " . $enunciado; ?></td>
                                <?php foreach ($opciones as $valor => $etiqueta): ?>
                                    <td data-label="<?php echo $etiqueta; ?>">
                                        <input type="radio" name="pregunta_<?php echo $num; ?>" value="<?php echo $valor; ?>" required>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <button type="submit" class="btn">Enviar Test</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>