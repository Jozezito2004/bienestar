<?php
$page_title = "Cargar Pruebas Psicométricas - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php'; // Verificar acceso por IP

// Obtener la lista de colaboradores para el formulario
$colaboradores = $conexion->query("SELECT id_colaborador, numero_identificacion, nombre, apellido FROM colaboradores ORDER BY apellido, nombre")->fetch_all(MYSQLI_ASSOC);

// Verificar si se seleccionó un colaborador en el primer formulario
$selected_colaborador = isset($_POST['id_colaborador']) ? (int)$_POST['id_colaborador'] : null;
$test_results = null;
if ($selected_colaborador) {
    $stmt = $conexion->prepare("SELECT fecha_evaluacion, estres, depresion, burnout, ansiedad FROM pruebas_psicometricas WHERE id_colaborador = ?");
    $stmt->bind_param("i", $selected_colaborador);
    $stmt->execute();
    $test_results = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Cargar Pruebas Psicométricas</h1>
            <p class="card-text">Selecciona un colaborador y registra sus niveles o realiza tests psicométricos.</p>

            <!-- Formulario para seleccionar colaborador y mostrar resultados -->
            <div class="import-container">
                <form method="POST" action="cargar_pruebas_psicometricas.php">
                    <label for="id_colaborador">Colaborador:</label>
                    <select id="id_colaborador" name="id_colaborador" onchange="this.form.submit()" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>" <?php echo $selected_colaborador == $colaborador['id_colaborador'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

                <?php if ($selected_colaborador && $test_results): ?>
                    <h3>Resultados Registrados</h3>
                    <p>Fecha de evaluación: <?php echo $test_results['fecha_evaluacion']; ?></p>
                    <?php if ($test_results['estres']): ?><p>Estrés: <?php echo $test_results['estres']; ?>/10</p><?php endif; ?>
                    <?php if ($test_results['depresion']): ?><p>Depresión: <?php echo $test_results['depresion']; ?>/10</p><?php endif; ?>
                    <?php if ($test_results['burnout']): ?><p>Burnout: <?php echo $test_results['burnout']; ?></p><?php endif; ?>
                    <?php if ($test_results['ansiedad']): ?><p>Ansiedad: <?php echo $test_results['ansiedad']; ?></p><?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Test de Estrés Laboral -->
            <div class="import-container">
                <h2>Realizar Test de Estrés Laboral (OMS)</h2>
                <form method="GET" action="test_estres.php" class="import-container">
                    <label for="id_colaborador_estres">Colaborador:</label>
                    <select id="id_colaborador_estres" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Estrés</button>
                </form>
            </div>

            <!-- Test de Burnout -->
            <div class="import-container">
                <h2>Realizar Test de Burnout de Maslach</h2>
                <form method="GET" action="test_burnout.php" class="import-container">
                    <label for="id_colaborador_burnout">Colaborador:</label>
                    <select id="id_colaborador_burnout" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Burnout</button>
                </form>
            </div>

            <!-- Test de Ansiedad Laboral -->
            <div class="import-container">
                <h2>Realizar Test de Ansiedad Laboral</h2>
                <form method="GET" action="test_ansiedad.php" class="import-container">
                    <label for="id_colaborador_ansiedad">Colaborador:</label>
                    <select id="id_colaborador_ansiedad" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Ansiedad</button>
                </form>
            </div>

            <!-- Test de Depresión (PHQ-9) -->
            <div class="import-container">
                <h2>Realizar Test de Depresión (PHQ-9)</h2>
                <form method="GET" action="test_depresion.php" class="import-container">
                    <label for="id_colaborador_depresion">Colaborador:</label>
                    <select id="id_colaborador_depresion" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Depresión</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>