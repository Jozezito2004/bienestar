<?php
$page_title = "Seguimiento de Actividad Física - Bienestar BUAP";
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
    $stmt = $conexion->prepare("SELECT fecha_evaluacion, flexiones_pecho, sentadillas_peso_corporal, plancha_isometrica, remo_mancuerna, test_1rm, test_rockport, test_recuperacion_hr FROM pruebas_actividad_fisica WHERE id_colaborador = ?");
    $stmt->bind_param("i", $selected_colaborador);
    $stmt->execute();
    $test_results = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Seguimiento de Actividad Física</h1>
            <p class="card-text">Selecciona un colaborador y registra los resultados de sus pruebas de actividad física.</p>

            <!-- Formulario para seleccionar colaborador y mostrar resultados -->
            <div class="import-container">
                <form method="POST" action="seguimiento_actividad_fisica.php">
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
                    <?php if ($test_results['flexiones_pecho']): ?><p>Flexiones de Pecho: <?php echo $test_results['flexiones_pecho']; ?> repeticiones</p><?php endif; ?>
                    <?php if ($test_results['sentadillas_peso_corporal']): ?><p>Sentadillas con Peso Corporal: <?php echo $test_results['sentadillas_peso_corporal']; ?> repeticiones</p><?php endif; ?>
                    <?php if ($test_results['plancha_isometrica']): ?><p>Plancha Isométrica: <?php echo $test_results['plancha_isometrica']; ?> segundos</p><?php endif; ?>
                    <?php if ($test_results['remo_mancuerna']): ?><p>Remo con Mancuerna: <?php echo $test_results['remo_mancuerna']; ?> repeticiones</p><?php endif; ?>
                    <?php if ($test_results['test_1rm']): ?><p>Test de 1RM (Press de Banca): <?php echo $test_results['test_1rm']; ?> kg</p><?php endif; ?>
                    <?php if ($test_results['test_rockport']): ?><p>Test de Rockport (VO2 Máx): <?php echo $test_results['test_rockport']; ?> ml/kg/min</p><?php endif; ?>
                    <?php if ($test_results['test_recuperacion_hr']): ?><p>Recuperación de Frecuencia Cardíaca: <?php echo $test_results['test_recuperacion_hr']; ?> bpm a 1 min</p><?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Test de Flexiones de Pecho -->
            <div class="import-container">
                <h2>Test de Flexiones de Pecho (ACSM)</h2>
                <p>Evalúa la resistencia del tren superior (pecho, tríceps, hombros), clave para movimientos de empuje y deportes como natación.</p>
                <h4>Cómo Realizarlo:</h4>
                <ul>
                    <li><strong>Materiales:</strong> Una colchoneta (opcional) para mayor comodidad, un cronómetro o reloj, y un dispositivo de monitoreo (WHOOP, Apple Watch o Huawei Band) para registrar frecuencia cardíaca.</li>
                    <li><strong>Paso 1:</strong> Coloca al colaborador en posición de flexión: manos separadas al ancho de los hombros, cuerpo en línea recta desde la cabeza hasta los talones. Si es principiante, puede apoyar las rodillas (versión modificada).</li>
                    <li><strong>Paso 2:</strong> Indica que realice tantas flexiones como pueda con buena técnica: bajar hasta que el pecho esté a 5-10 cm del suelo y subir hasta extender completamente los brazos.</li>
                    <li><strong>Paso 3:</strong> Detén la prueba cuando el colaborador no pueda mantener la técnica o llegue al fallo muscular. Registra el número de repeticiones completadas.</li>
                    <li><strong>Paso 4:</strong> Usa el dispositivo para monitorear la frecuencia cardíaca durante el esfuerzo y la recuperación post-ejercicio (por ejemplo, cuánto tarda en bajar a 100 bpm).</li>
                </ul>
                <form method="GET" action="test_flexiones_pecho.php" class="import-container">
                    <label for="id_colaborador_flexiones">Colaborador:</label>
                    <select id="id_colaborador_flexiones" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Flexiones</button>
                </form>
            </div>

            <!-- Test de Sentadillas con Peso Corporal -->
            <div class="import-container">
                <h2>Test de Sentadillas con Peso Corporal (NSCA)</h2>
                <p>Mide la resistencia del tren inferior (cuádriceps, glúteos), esencial para actividades como correr o danza.</p>
                <h4>Cómo Realizarlo:</h4>
                <ul>
                    <li><strong>Materiales:</strong> Un cronómetro para medir 1 minuto, un espacio plano y seguro, y un dispositivo de monitoreo para registrar frecuencia cardíaca.</li>
                    <li><strong>Paso 1:</strong> Indica al colaborador que se pare con los pies al ancho de los hombros, manos en las caderas o extendidas al frente para mantener el equilibrio.</li>
                    <li><strong>Paso 2:</strong> Pide que realice sentadillas con buena técnica: bajar hasta que los muslos estén paralelos al suelo, manteniendo la espalda recta y las rodillas alineadas con los pies.</li>
                    <li><strong>Paso 3:</strong> Durante 1 minuto, cuenta las repeticiones completadas a un ritmo constante (aproximadamente 1-2 segundos por repetición).</li>
                    <li><strong>Paso 4:</strong> Registra el número de repeticiones y usa el dispositivo para monitorear la frecuencia cardíaca y la oxigenación (SpO2, si está disponible).</li>
                </ul>
                <form method="GET" action="test_sentadillas.php" class="import-container">
                    <label for="id_colaborador_sentadillas">Colaborador:</label>
                    <select id="id_colaborador_sentadillas" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Sentadillas</button>
                </form>
            </div>

            <!-- Test de Plancha Isométrica -->
            <div class="import-container">
                <h2>Test de Plancha Isométrica (ACSM)</h2>
                <p>Evalúa la resistencia del core, fundamental para estabilidad y prevención de lesiones.</p>
                <h4>Cómo Realizarlo:</h4>
                <ul>
                    <li><strong>Materiales:</strong> Una colchoneta para mayor comodidad, un cronómetro para medir el tiempo, y un dispositivo de monitoreo para registrar frecuencia cardíaca.</li>
                    <li><strong>Paso 1:</strong> Coloca al colaborador en posición de plancha: antebrazos y puntas de los pies apoyados, cuerpo en línea recta desde la cabeza hasta los talones.</li>
                    <li><strong>Paso 2:</strong> Indica que mantenga la posición con buena técnica, sin que las caderas se hundan o se eleven.</li>
                    <li><strong>Paso 3:</strong> Mide el tiempo (en segundos) hasta que el colaborador llegue al fallo muscular o no pueda mantener la técnica.</li>
                    <li><strong>Paso 4:</strong> Registra el tiempo y usa el dispositivo para monitorear la frecuencia cardíaca durante el esfuerzo y la recuperación.</li>
                </ul>
                <form method="GET" action="test_plancha.php" class="import-container">
                    <label for="id_colaborador_plancha">Colaborador:</label>
                    <select id="id_colaborador_plancha" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Plancha</button>
                </form>
            </div>

            <!-- Test de Remo con Mancuerna -->
            <div class="import-container">
                <h2>Test de Remo con Mancuerna (NSCA)</h2>
                <p>Mide la resistencia de la espalda y brazos, importante para movimientos de tracción y deportes como remo o escalada.</p>
                <h4>Cómo Realizarlo:</h4>
                <ul>
                    <li><strong>Materiales:</strong> Una mancuerna ligera (5-10 kg, ajustada al nivel del colaborador), un banco plano, y un dispositivo de monitoreo para registrar frecuencia cardíaca.</li>
                    <li><strong>Paso 1:</strong> Coloca al colaborador en posición: una rodilla y una mano apoyadas en el banco, espalda recta, y la mancuerna en la otra mano.</li>
                    <li><strong>Paso 2:</strong> Indica que realice repeticiones de remo con buena técnica: llevar la mancuerna hacia la cadera, manteniendo el codo cerca del cuerpo.</li>
                    <li><strong>Paso 3:</strong> Registra el número de repeticiones hasta el fallo muscular para cada brazo, asegurándote de que la técnica sea correcta.</li>
                    <li><strong>Paso 4:</strong> Usa el dispositivo para monitorear la frecuencia cardíaca y la oxigenación (SpO2, si está disponible) durante y después del esfuerzo.</li>
                </ul>
                <form method="GET" action="test_remo_mancuerna.php" class="import-container">
                    <label for="id_colaborador_remo">Colaborador:</label>
                    <select id="id_colaborador_remo" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Remo</button>
                </form>
            </div>

            <!-- Test de 1RM -->
            <div class="import-container">
                <h2>Test de 1RM (NSCA)</h2>
                <p>Mide tu fuerza máxima (por ejemplo, press de banca) y usa tu dispositivo para monitorear HR y recuperación.</p>
                <h4>Cómo Realizarlo:</h4>
                <ul>
                    <li><strong>Materiales:</strong> Una barra con discos de peso, un banco plano (para press de banca), un rack de seguridad, y un dispositivo de monitoreo para registrar frecuencia cardíaca.</li>
                    <li><strong>Paso 1:</strong> Calienta al colaborador: 5-10 repeticiones con un peso ligero (40-50% de su 1RM estimado), descansa 1 minuto. Luego, 3-5 repeticiones al 60-70%, descansa 2 minutos.</li>
                    <li><strong>Paso 2:</strong> Aumenta el peso progresivamente (por ejemplo, 5-10 kg) e intenta una repetición máxima con buena técnica. Descansa 3-4 minutos entre intentos.</li>
                    <li><strong>Paso 3:</strong> Repite hasta encontrar el peso máximo que el colaborador pueda levantar una vez (1RM). Registra el peso (por ejemplo, 80 kg).</li>
                    <li><strong>Paso 4:</strong> Usa el dispositivo para monitorear la frecuencia cardíaca durante el esfuerzo y la recuperación post-ejercicio.</li>
                </ul>
                <form method="GET" action="test_1rm.php" class="import-container">
                    <label for="id_colaborador_1rm">Colaborador:</label>
                    <select id="id_colaborador_1rm" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de 1RM</button>
                </form>
            </div>

            <!-- Test de Rockport -->
            <div class="import-container">
                <h2>Test de Rockport (ACSM)</h2>
                <p>Evalúa tu capacidad aeróbica (VO2 máx) caminando 1 milla, con monitoreo de HR, tiempo y SpO2.</p>
                <h4>Cómo Realizarlo:</h4>
                <ul>
                    <li><strong>Materiales:</strong> Una pista plana o cinta de correr (1 milla = 1.6 km), un cronómetro, y un dispositivo de monitoreo con GPS (Apple Watch, Huawei Band) para medir distancia y frecuencia cardíaca.</li>
                    <li><strong>Paso 1:</strong> Asegúrate de que el colaborador use un dispositivo con GPS activado para medir la distancia. Si usas WHOOP, necesitarás el GPS del teléfono.</li>
                    <li><strong>Paso 2:</strong> Indica que camine 1 milla (1.6 km) lo más rápido posible sin correr. Registra el tiempo que tarda (en minutos y segundos).</li>
                    <li><strong>Paso 3:</strong> Inmediatamente al finalizar, registra la frecuencia cardíaca del colaborador usando el dispositivo.</li>
                    <li><strong>Paso 4:</strong> Calcula el VO2 máx con la fórmula de Rockport: VO2 máx = 132.853 - (0.0769 × Peso en libras) - (0.3877 × Edad) + (6.315 × Género: 1 para hombres, 0 para mujeres) - (3.2649 × Tiempo en minutos) - (0.1565 × HR al final). Registra el resultado.</li>
                </ul>
                <form method="GET" action="test_rockport.php" class="import-container">
                    <label for="id_colaborador_rockport">Colaborador:</label>
                    <select id="id_colaborador_rockport" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Rockport</button>
                </form>
            </div>

            <!-- Test de Recuperación de Frecuencia Cardíaca -->
            <div class="import-container">
                <h2>Test de Recuperación de Frecuencia Cardíaca (ACSM)</h2>
                <p>Mide cómo tu HR se recupera después de un esfuerzo intenso, con apoyo de HRV y SpO2.</p>
                <h4>Cómo Realizarlo:</h4>
                <ul>
                    <li><strong>Materiales:</strong> Una pista o cinta de correr para el esfuerzo, un cronómetro, y un dispositivo de monitoreo para registrar frecuencia cardíaca y HRV (WHOOP, Apple Watch, Huawei Band).</li>
                    <li><strong>Paso 1:</strong> Calienta al colaborador durante 5-10 minutos con una caminata rápida o trote suave.</li>
                    <li><strong>Paso 2:</strong> Indica que corra a un ritmo intenso (85-90% de su HR máx) durante 2 minutos. Puede ser un sprint o intervalos en una cinta de correr.</li>
                    <li><strong>Paso 3:</strong> Para inmediatamente y quédate quieto (de pie o sentado). Registra la HR pico al momento de parar y luego a los 1 y 2 minutos después usando el dispositivo.</li>
                    <li><strong>Paso 4:</strong> Calcula la recuperación: HR pico - HR a 1 min (por ejemplo, 160 bpm - 120 bpm = 40 bpm). Registra el valor a 1 minuto y, opcionalmente, mide HRV y SpO2 post-ejercicio.</li>
                </ul>
                <form method="GET" action="test_recuperacion_hr.php" class="import-container">
                    <label for="id_colaborador_recuperacion">Colaborador:</label>
                    <select id="id_colaborador_recuperacion" name="id" required>
                        <option value="">Selecciona un colaborador</option>
                        <?php foreach ($colaboradores as $colaborador): ?>
                            <option value="<?php echo $colaborador['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($colaborador['apellido'] . " " . $colaborador['nombre'] . " (" . $colaborador['numero_identificacion'] . ")"); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn">Realizar Test de Recuperación</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>