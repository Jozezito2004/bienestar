<?php
$page_title = "Detalles del Colaborador - Bienestar BUAP";
include 'header.php'; // Incluye Bootstrap y estilos
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php'; // Verificar acceso por IP
?>

<?php
if (!isset($_GET['id'])) {
    die("ID de colaborador no proporcionado");
}

$id_colaborador = $_GET['id'];

// Obtener datos generales
$sql_colaborador = "SELECT *, TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad FROM colaboradores WHERE id_colaborador = ?";
$stmt = $conexion->prepare($sql_colaborador);
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$colaborador = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener datos biométricos (última medición)
$sql_biometricos = "SELECT * FROM datos_biometricos WHERE id_colaborador = ? ORDER BY fecha_medicion DESC LIMIT 1";
$stmt = $conexion->prepare($sql_biometricos);
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$biometricos = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Calcular IMC, Relación Cintura-Estatura, TMB y Calorías Diarias
$imc = $biometricos ? round($biometricos['peso'] / (($biometricos['talla'] / 100) ** 2), 2) : 'N/A';
$relacion_cintura_estatura = $biometricos && $biometricos['perimetro_cintura'] ? round($biometricos['perimetro_cintura'] / $biometricos['talla'], 2) : 'N/A';

// Calcular TMB (Tasa Metabólica Basal) usando la fórmula de Harris-Benedict
$tmb = 'N/A';
$calorias_sedentario = 'N/A';
if ($biometricos && $colaborador) {
    $peso = $biometricos['peso'];
    $talla = $biometricos['talla'] / 100; // Convertir talla a metros
    $edad = $colaborador['edad'];
    $genero = $colaborador['genero'];
    $talla_cm = $biometricos['talla']; // Talla en centímetros para la fórmula

    if ($peso && $talla && $edad && $genero) {
        if ($genero === 'M') {
            $tmb = 88.362 + (13.397 * $peso) + (4.799 * $talla_cm) - (5.677 * $edad);
        } else {
            $tmb = 447.593 + (9.247 * $peso) + (3.098 * $talla_cm) - (4.330 * $edad);
        }
        $tmb = round($tmb);
        $calorias_sedentario = round($tmb * 1.2);
    }
}

// Obtener historial de salud
$sql_historial = "SELECT * FROM historial_salud WHERE id_colaborador = ? LIMIT 1";
$stmt = $conexion->prepare($sql_historial);
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$historial = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener pruebas psicométricas (última medición)
$sql_psicometricas = "SELECT * FROM pruebas_psicometricas WHERE id_colaborador = ? ORDER BY fecha_evaluacion DESC LIMIT 1";
$stmt = $conexion->prepare($sql_psicometricas);
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$psicometricas = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Interpretar el nivel de estrés laboral (basado en el test OMS: 0-24)
$nivel_estres = 'N/A';
if ($psicometricas && $psicometricas['estres'] !== null) {
    $estres_score = $psicometricas['estres'];
    if ($estres_score <= 8) {
        $nivel_estres = "Bajo ($estres_score/24)";
    } elseif ($estres_score <= 16) {
        $nivel_estres = "Moderado ($estres_score/24)";
    } else {
        $nivel_estres = "Alto ($estres_score/24)";
    }
}

// Obtener actividad física (última medición) - Tabla antigua
$sql_actividad = "SELECT * FROM actividad_fisica WHERE id_colaborador = ? ORDER BY fecha_registro DESC LIMIT 1";
$stmt = $conexion->prepare($sql_actividad);
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$actividad = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener pruebas de actividad física (última medición) - Nueva tabla
$sql_pruebas_actividad = "SELECT * FROM pruebas_actividad_fisica WHERE id_colaborador = ? ORDER BY fecha_evaluacion DESC LIMIT 1";
$stmt = $conexion->prepare($sql_pruebas_actividad);
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$pruebas_actividad = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Obtener exámenes médicos (última medición)
$sql_examenes = "SELECT * FROM examenes_medicos WHERE id_colaborador = ? ORDER BY fecha_examen DESC LIMIT 1";
$stmt = $conexion->prepare($sql_examenes);
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$examenes = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Detalles del Colaborador: <?php echo $colaborador['nombre'] . " " . $colaborador['apellido']; ?></h1>

            <!-- Datos Generales -->
            <div class="section">
                <h2>Datos Generales</h2>
                <p><strong>Número de Identificación:</strong> <?php echo $colaborador['numero_identificacion']; ?></p>
                <p><strong>Nombre:</strong> <?php echo $colaborador['nombre'] . " " . $colaborador['apellido']; ?></p>
                <p><strong>Edad:</strong> <?php echo $colaborador['edad']; ?> años</p>
                <p><strong>Género:</strong> <?php echo $colaborador['genero']; ?></p>
                <p><strong>Unidad de Trabajo:</strong> <?php echo $colaborador['unidad_trabajo']; ?></p>
                <p><strong>Puesto:</strong> <?php echo $colaborador['puesto']; ?></p>
                <p><strong>Años de Servicio:</strong> <?php echo $colaborador['anos_servicio']; ?></p>
                <p><strong>Email:</strong> <?php echo $colaborador['email'] ?: 'N/A'; ?></p>
                <p><strong>Fecha de Registro:</strong> <?php echo $colaborador['fecha_registro']; ?></p>
            </div>

            <!-- Datos Biométricos -->
            <div class="section">
                <h2>Datos Biométricos (Última Medición)</h2>
                <?php if ($biometricos): ?>
                    <p><strong>Fecha de Medición:</strong> <?php echo $biometricos['fecha_medicion']; ?></p>
                    <p><strong>Peso:</strong> <?php echo $biometricos['peso']; ?> kg</p>
                    <p><strong>Talla:</strong> <?php echo $biometricos['talla']; ?> cm</p>
                    <p><strong>IMC:</strong> <?php echo $imc; ?></p>
                    <p><strong>Tasa Metabólica Basal (TMB):</strong> <?php echo $tmb; ?> kcal</p>
                    <p><strong>Calorías Diarias (Sedentario):</strong> <?php echo $calorias_sedentario; ?> kcal</p>
                    <p><strong>Perímetro de Cintura:</strong> <?php echo $biometricos['perimetro_cintura'] ?: 'N/A'; ?> cm</p>
                    <p><strong>Relación Cintura-Estatura:</strong> <?php echo $relacion_cintura_estatura; ?></p>
                    <p><strong>Porcentaje de Grasa Corporal:</strong> <?php echo $biometricos['porcentaje_grasa'] ?: 'N/A'; ?> %</p>
                    <p><strong>Masa Muscular:</strong> <?php echo $biometricos['masa_muscular'] ?: 'N/A'; ?> kg</p>
                    <p><strong>Presión Arterial:</strong> <?php echo ($biometricos['presion_arterial_sistolica'] && $biometricos['presion_arterial_diastolica']) ? $biometricos['presion_arterial_sistolica'] . "/" . $biometricos['presion_arterial_diastolica'] : 'N/A'; ?> mmHg</p>
                    <p><strong>Frecuencia Cardíaca:</strong> <?php echo $biometricos['frecuencia_cardiaca'] ?: 'N/A'; ?> lpm</p>
                    <p><strong>Glucosa en Ayuno:</strong> <?php echo $biometricos['glucosa_ayuno'] ?: 'N/A'; ?> mg/dL</p>
                    <p><strong>Colesterol Total:</strong> <?php echo $biometricos['colesterol_total'] ?: 'N/A'; ?> mg/dL</p>
                    <p><strong>Triglicéridos:</strong> <?php echo $biometricos['trigliceridos'] ?: 'N/A'; ?> mg/dL</p>
                <?php else: ?>
                    <p>No hay datos biométricos registrados.</p>
                <?php endif; ?>
            </div>

            <!-- Historial de Salud -->
            <div class="section">
                <h2>Historial de Salud y Bienestar</h2>
                <?php if ($historial): ?>
                    <p><strong>Enfermedades Diagnosticadas:</strong> <?php echo $historial['enfermedades_diagnosticadas'] ?: 'Ninguna'; ?></p>
                    <p><strong>Historial de Medicamentos:</strong> <?php echo $historial['historial_medicamentos'] ?: 'Ninguno'; ?></p>
                    <p><strong>Alergias:</strong> <?php echo $historial['alergias'] ?: 'Ninguna'; ?></p>
                    <p><strong>Cirugías Previas:</strong> <?php echo $historial['cirugias_previas'] ?: 'Ninguna'; ?></p>
                    <p><strong>Historial Familiar:</strong> <?php echo $historial['historial_familiar'] ?: 'Ninguno'; ?></p>
                    <p><strong>Nivel de Estrés:</strong> <?php echo $nivel_estres; ?></p>
                    <p><strong>Ansiedad:</strong> <?php echo $psicometricas['ansiedad'] ? $psicometricas['ansiedad'] : 'N/A'; ?> (Escala variable)</p>
                    <p><strong>Depresión:</strong> <?php echo $psicometricas['depresion'] ? $psicometricas['depresion'] . "/10" : 'N/A'; ?></p>
                    <p><strong>Burnout:</strong> <?php echo $psicometricas['burnout'] ?: 'N/A'; ?> (Escala variable)</p>
                    <p><strong>Calidad del Sueño:</strong> <?php echo $historial['calidad_sueno_horas'] ? $historial['calidad_sueno_horas'] . " horas, " . $historial['calidad_sueno_nivel'] : 'N/A'; ?></p>
                    <p><strong>Recuperación Física:</strong> <?php echo $historial['recuperacion_fisica'] ?: 'N/A'; ?></p>
                <?php else: ?>
                    <p>No hay historial de salud registrado.</p>
                <?php endif; ?>
            </div>

            <!-- Actividad Física (Tabla Antigua) -->
            <div class="section">
                <h2>Seguimiento de Actividad Física (Última Medición - Tabla Antigua)</h2>
                <?php if ($actividad): ?>
                    <p><strong>Fecha de Registro:</strong> <?php echo $actividad['fecha_registro']; ?></p>
                    <p><strong>Promedio de Pasos Diarios:</strong> <?php echo $actividad['promedio_pasos_diarios'] ?: 'N/A'; ?></p>
                    <p><strong>Minutos de Actividad Moderada/Vigorosa:</strong> <?php echo $actividad['minutos_actividad_moderada'] ?: 'N/A'; ?> minutos</p>
                    <p><strong>Frecuencia de Entrenamiento:</strong> <?php echo $actividad['frecuencia_entrenamiento'] ?: 'N/A'; ?> días/semana</p>
                    <p><strong>Deportes Practicados:</strong> <?php echo $actividad['deportes_practicados'] ?: 'Ninguno'; ?></p>
                <?php else: ?>
                    <p>No hay datos de actividad física registrados en la tabla antigua.</p>
                <?php endif; ?>
            </div>

            <!-- Pruebas de Actividad Física (Nueva Tabla) -->
            <div class="section">
                <h2>Pruebas de Actividad Física (Última Evaluación)</h2>
                <?php if ($pruebas_actividad): ?>
                    <p><strong>Fecha de Evaluación:</strong> <?php echo $pruebas_actividad['fecha_evaluacion']; ?></p>
                    <p><strong>Test de Flexiones de Pecho (ACSM):</strong> 
                        <?php echo $pruebas_actividad['flexiones_pecho'] ? $pruebas_actividad['flexiones_pecho'] . " repeticiones" : 'N/A'; ?>
                        <?php echo $pruebas_actividad['flexiones_pecho_hr'] ? " (HR: " . $pruebas_actividad['flexiones_pecho_hr'] . " bpm)" : ''; ?>
                    </p>
                    <p><strong>Test de Sentadillas con Peso Corporal (NSCA):</strong> 
                        <?php echo $pruebas_actividad['sentadillas_peso_corporal'] ? $pruebas_actividad['sentadillas_peso_corporal'] . " repeticiones" : 'N/A'; ?>
                        <?php echo $pruebas_actividad['sentadillas_peso_corporal_hr'] ? " (HR: " . $pruebas_actividad['sentadillas_peso_corporal_hr'] . " bpm)" : ''; ?>
                    </p>
                    <p><strong>Test de Plancha Isométrica (ACSM):</strong> 
                        <?php echo $pruebas_actividad['plancha_isometrica'] ? $pruebas_actividad['plancha_isometrica'] . " segundos" : 'N/A'; ?>
                        <?php echo $pruebas_actividad['plancha_isometrica_hr'] ? " (HR: " . $pruebas_actividad['plancha_isometrica_hr'] . " bpm)" : ''; ?>
                    </p>
                    <p><strong>Test de Remo con Mancuerna (NSCA):</strong> 
                        <?php echo $pruebas_actividad['remo_mancuerna'] ? $pruebas_actividad['remo_mancuerna'] . " repeticiones (promedio)" : 'N/A'; ?>
                        <?php echo $pruebas_actividad['remo_mancuerna_hr'] ? " (HR: " . $pruebas_actividad['remo_mancuerna_hr'] . " bpm)" : ''; ?>
                    </p>
                    <p><strong>Test de 1RM (NSCA):</strong> 
                        <?php echo $pruebas_actividad['test_1rm'] ? $pruebas_actividad['test_1rm'] . " kg (press de banca)" : 'N/A'; ?>
                        <?php echo $pruebas_actividad['test_1rm_hr'] ? " (HR: " . $pruebas_actividad['test_1rm_hr'] . " bpm)" : ''; ?>
                    </p>
                    <p><strong>Test de Rockport (ACSM):</strong> 
                        <?php echo $pruebas_actividad['test_rockport'] ? $pruebas_actividad['test_rockport'] . " ml/kg/min (VO2 máx)" : 'N/A'; ?>
                        <?php echo $pruebas_actividad['test_rockport_hr'] ? " (HR al final: " . $pruebas_actividad['test_rockport_hr'] . " bpm)" : ''; ?>
                        <?php echo $pruebas_actividad['test_rockport_tiempo'] ? " (Tiempo: " . $pruebas_actividad['test_rockport_tiempo'] . " min)" : ''; ?>
                    </p>
                    <p><strong>Test de Recuperación de Frecuencia Cardíaca (ACSM):</strong> 
                        <?php echo $pruebas_actividad['test_recuperacion_hr'] ? $pruebas_actividad['test_recuperacion_hr'] . " bpm (diferencia a 1 min)" : 'N/A'; ?>
                        <?php echo $pruebas_actividad['test_recuperacion_hr_peak'] ? " (HR pico: " . $pruebas_actividad['test_recuperacion_hr_peak'] . " bpm)" : ''; ?>
                        <?php echo $pruebas_actividad['test_recuperacion_hrv'] ? " (HRV post-ejercicio: " . $pruebas_actividad['test_recuperacion_hrv'] . " ms)" : ''; ?>
                    </p>
                <?php else: ?>
                    <p>No hay pruebas de actividad física registradas.</p>
                <?php endif; ?>
            </div>

            <!-- Exámenes Médicos -->
            <div class="section">
                <h2>Exámenes Médicos (Última Medición)</h2>
                <?php if ($examenes): ?>
                    <p><strong>Fecha de Examen:</strong> <?php echo $examenes['fecha_examen']; ?></p>
                    <p><strong>Electrocardiograma:</strong> <?php echo $examenes['electrocardiograma'] ?: 'N/A'; ?></p>
                    <p><strong>Prueba de Esfuerzo:</strong> <?php echo $examenes['prueba_esfuerzo'] ?: 'N/A'; ?></p>
                    <p><strong>Perfil Lipídico:</strong> <?php echo $examenes['perfil_lipidico'] ?: 'N/A'; ?></p>
                    <p><strong>Hemoglobina Glicosilada:</strong> <?php echo $examenes['hemoglobina_glicosilada'] ?: 'N/A'; ?> %</p>
                    <p><strong>Creatinina:</strong> <?php echo $examenes['creatinina'] ?: 'N/A'; ?> mg/dL</p>
                    <p><strong>Urea:</strong> <?php echo $examenes['urea'] ?: 'N/A'; ?> mg/dL</p>
                    <p><strong>TGO:</strong> <?php echo $examenes['tgo'] ?: 'N/A'; ?> U/L</p>
                    <p><strong>TGP:</strong> <?php echo $examenes['tgp'] ?: 'N/A'; ?> U/L</p>
                    <p><strong>Densitometría Ósea:</strong> <?php echo $examenes['densitometria_osea'] ?: 'N/A'; ?></p>
                    <p><strong>Vitamina D:</strong> <?php echo $examenes['vitamina_d'] ?: 'N/A'; ?> ng/mL</p>
                    <p><strong>Evaluación de Postura:</strong> <?php echo $examenes['evaluacion_postura'] ?: 'N/A'; ?></p>
                <?php else: ?>
                    <p>No hay exámenes médicos registrados.</p>
                <?php endif; ?>
            </div>

            <!-- Pruebas Psicométricas -->
            <div class="section">
                <h2>Pruebas Psicométricas (Última Evaluación)</h2>
                <?php if ($psicometricas): ?>
                    <p><strong>Fecha de Evaluación:</strong> <?php echo $psicometricas['fecha_evaluacion']; ?></p>
                    <p><strong>Estrés Laboral (OMS):</strong> <?php echo $psicometricas['estres'] ? $psicometricas['estres'] . "/24" : 'N/A'; ?></p>
                    <p><strong>Burnout (Maslach):</strong> <?php echo $psicometricas['burnout'] ?: 'N/A'; ?></p>
                    <p><strong>Ansiedad Laboral:</strong> <?php echo $psicometricas['ansiedad'] ?: 'N/A'; ?></p>
                    <p><strong>Depresión (PHQ-9):</strong> <?php echo $psicometricas['depresion'] ? $psicometricas['depresion'] . "/10" : 'N/A'; ?></p>
                <?php else: ?>
                    <p>No hay pruebas psicométricas registradas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>