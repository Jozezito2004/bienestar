<?php
// Iniciar el buffer de salida para evitar que se envíe contenido al navegador
ob_start();

// Incluir las librerías necesarias
require_once 'conexion.php'; // Conexión a la base de datos
require_once 'tcpdf/tcpdf.php'; // Cargar TCPDF manualmente
require_once 'phpqrcode/qrlib.php'; // Librería para generar códigos QR
require_once 'config.php'; // Incluir el archivo de configuración para la clave de DeepSeek

// Crear una clase personalizada que extienda TCPDF para definir un encabezado y pie de página
class MYPDF extends TCPDF {
    // Encabezado personalizado
    public function Header() {
        // Logo izquierdo (logo_bibes.jpeg)
        $this->Image('logo_bibes.jpeg', 10, 10, 30, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Logo derecho (buap_.png)
        $this->Image('buap_.png', 170, 10, 30, 15, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Título centrado
        $this->SetFont('helvetica', 'B', 16);
        $this->SetY(10);
        $this->Cell(0, 10, 'Reporte de Colaboradores - Bienestar BUAP', 0, 1, 'C');
        // Barra de color debajo del título
        $this->SetFillColor(0, 102, 204); // Color azul (RGB: 0, 102, 204)
        $this->Rect(10, 25, 190, 2, 'F'); // Barra de 190 mm de ancho y 2 mm de alto
    }

    // Pie de página personalizado
    public function Footer() {
        // Posición a 15 mm del borde inferior
        $this->SetY(-15);
        // Fuente
        $this->SetFont('helvetica', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Consulta para obtener todos los datos de los colaboradores, incluyendo pruebas de actividad física
$sql = "
    SELECT 
        c.id_colaborador, c.numero_identificacion, c.nombre, c.apellido, c.fecha_nacimiento, c.genero, c.unidad_trabajo, c.puesto, c.anos_servicio, c.email,
        TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) AS edad,
        a.promedio_pasos_diarios,
        p.fecha_evaluacion, p.estres, p.depresion, p.burnout,
        db.peso, (db.talla / 100) AS talla, db.perimetro_cintura, db.porcentaje_grasa, db.masa_muscular,
        db.presion_arterial_sistolica, db.presion_arterial_diastolica, db.frecuencia_cardiaca,
        db.glucosa_ayuno, db.colesterol_total, db.trigliceridos,
        pa.fecha_evaluacion AS fecha_evaluacion_actividad,
        pa.flexiones_pecho, pa.flexiones_pecho_hr,
        pa.sentadillas_peso_corporal, pa.sentadillas_peso_corporal_hr,
        pa.plancha_isometrica, pa.plancha_isometrica_hr,
        pa.remo_mancuerna, pa.remo_mancuerna_hr,
        pa.test_1rm, pa.test_1rm_hr,
        pa.test_rockport, pa.test_rockport_hr, pa.test_rockport_tiempo,
        pa.test_recuperacion_hr, pa.test_recuperacion_hr_peak, pa.test_recuperacion_hrv
    FROM colaboradores c
    LEFT JOIN actividad_fisica a ON c.id_colaborador = a.id_colaborador
    LEFT JOIN pruebas_psicometricas p ON c.id_colaborador = p.id_colaborador
    LEFT JOIN datos_biometricos db ON c.id_colaborador = db.id_colaborador
    LEFT JOIN pruebas_actividad_fisica pa ON c.id_colaborador = pa.id_colaborador
";
$resultado = $conexion->query($sql);

// Verificar si la consulta falló
if (!$resultado) {
    die("Error en la consulta SQL: " . $conexion->error);
}

// Crear el directorio para archivos temporales si no existe
$temp_dir = __DIR__ . '/temp_qr/';
if (!is_dir($temp_dir)) {
    mkdir($temp_dir, 0777, true);
}

// Generar códigos QR para cada colaborador y almacenar las rutas
$qr_files = [];
if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $id_colaborador = $fila['id_colaborador'];
        // URL del formulario de Google Forms con el ID del colaborador prellenado
        $qr_data = "https://docs.google.com/forms/d/e/1FAIpQLSeE4dY0z1vI2DS0uDFFm91mXurirLCpms8xGIskTcae05lozg/viewform?entry.1045142956=" . $id_colaborador;
        $qr_file = $temp_dir . "temp_qr_{$id_colaborador}.png";

        // Generar el código QR
        QRcode::png($qr_data, $qr_file, QR_ECLEVEL_L, 3);

        // Guardar la ruta del archivo para usarla en el PDF
        $qr_files[$id_colaborador] = $qr_file;
    }
}

// Crear el PDF con la clase personalizada MYPDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar metadatos del PDF
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Bienestar BUAP');
$pdf->SetTitle('Reporte de Colaboradores');
$pdf->SetSubject('Reporte de Colaboradores de Bienestar BUAP');
$pdf->SetKeywords('colaboradores, bienestar, BUAP');

// Configurar márgenes
$pdf->SetMargins(15, 35, 15); // Ajustamos el margen superior para dejar espacio al encabezado
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Configurar auto salto de página
$pdf->SetAutoPageBreak(TRUE, 15);

// Configurar fuente
$pdf->SetFont('helvetica', '', 10);

// Añadir una página
$pdf->AddPage();

// Generar el contenido del PDF
$resultado->data_seek(0); // Reiniciar el puntero del resultado
while ($fila = $resultado->fetch_assoc()) {
    $id_colaborador = $fila['id_colaborador'];

    // Sección: Información del colaborador
    $pdf->SetFillColor(230, 240, 255); // Fondo azul claro para el título de sección
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Colaborador ID: " . $id_colaborador, 0, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245); // Fondo gris claro para el contenido
    $pdf->Cell(0, 6, "Número de Identificación: " . ($fila['numero_identificacion'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Nombre: " . ($fila['nombre'] ?? 'N/A') . " " . ($fila['apellido'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Edad: " . ($fila['edad'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Género: " . ($fila['genero'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Unidad de Trabajo: " . ($fila['unidad_trabajo'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Puesto: " . ($fila['puesto'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Años de Servicio: " . ($fila['anos_servicio'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Email: " . ($fila['email'] ?? 'N/A'), 0, 1, 'L', 1);

    // Barra decorativa entre secciones
    $pdf->SetFillColor(0, 102, 204); // Color azul
    $pdf->Rect(15, $pdf->GetY(), 180, 1, 'F'); // Barra de 180 mm de ancho y 1 mm de alto
    $pdf->Ln(5);

    // Sección: Actividad física (tabla antigua)
    $pdf->SetFillColor(230, 240, 255); // Fondo azul claro para el título de sección
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Actividad Física", 0, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245); // Fondo gris claro para el contenido
    $pdf->Cell(0, 6, "Promedio de Pasos Diarios: " . ($fila['promedio_pasos_diarios'] ?? 'N/A'), 0, 1, 'L', 1);

    // Barra decorativa entre secciones
    $pdf->SetFillColor(0, 102, 204); // Color azul
    $pdf->Rect(15, $pdf->GetY(), 180, 1, 'F'); // Barra de 180 mm de ancho y 1 mm de alto
    $pdf->Ln(5);

    // Sección: Pruebas de Actividad Física (nueva tabla)
    $pdf->SetFillColor(230, 240, 255); // Fondo azul claro para el título de sección
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Pruebas de Actividad Física", 0, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245); // Fondo gris claro para el contenido
    $pdf->Cell(0, 6, "Fecha de Evaluación: " . ($fila['fecha_evaluacion_actividad'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Test de Flexiones de Pecho: " . ($fila['flexiones_pecho'] ? $fila['flexiones_pecho'] . " repeticiones" : 'N/A') . ($fila['flexiones_pecho_hr'] ? " (HR: " . $fila['flexiones_pecho_hr'] . " bpm)" : ''), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Test de Sentadillas con Peso Corporal: " . ($fila['sentadillas_peso_corporal'] ? $fila['sentadillas_peso_corporal'] . " repeticiones" : 'N/A') . ($fila['sentadillas_peso_corporal_hr'] ? " (HR: " . $fila['sentadillas_peso_corporal_hr'] . " bpm)" : ''), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Test de Plancha Isométrica: " . ($fila['plancha_isometrica'] ? $fila['plancha_isometrica'] . " segundos" : 'N/A') . ($fila['plancha_isometrica_hr'] ? " (HR: " . $fila['plancha_isometrica_hr'] . " bpm)" : ''), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Test de Remo con Mancuerna: " . ($fila['remo_mancuerna'] ? $fila['remo_mancuerna'] . " repeticiones (promedio)" : 'N/A') . ($fila['remo_mancuerna_hr'] ? " (HR: " . $fila['remo_mancuerna_hr'] . " bpm)" : ''), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Test de 1RM (Press de Banca): " . ($fila['test_1rm'] ? $fila['test_1rm'] . " kg" : 'N/A') . ($fila['test_1rm_hr'] ? " (HR: " . $fila['test_1rm_hr'] . " bpm)" : ''), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Test de Rockport: " . ($fila['test_rockport'] ? $fila['test_rockport'] . " ml/kg/min (VO2 máx)" : 'N/A') . ($fila['test_rockport_hr'] ? " (HR al final: " . $fila['test_rockport_hr'] . " bpm)" : '') . ($fila['test_rockport_tiempo'] ? " (Tiempo: " . $fila['test_rockport_tiempo'] . " min)" : ''), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Test de Recuperación de Frecuencia Cardíaca: " . ($fila['test_recuperacion_hr'] ? $fila['test_recuperacion_hr'] . " bpm (diferencia a 1 min)" : 'N/A') . ($fila['test_recuperacion_hr_peak'] ? " (HR pico: " . $fila['test_recuperacion_hr_peak'] . " bpm)" : '') . ($fila['test_recuperacion_hrv'] ? " (HRV: " . $fila['test_recuperacion_hrv'] . " ms)" : ''), 0, 1, 'L', 1);

    // Barra decorativa entre secciones
    $pdf->SetFillColor(0, 102, 204); // Color azul
    $pdf->Rect(15, $pdf->GetY(), 180, 1, 'F'); // Barra de 180 mm de ancho y 1 mm de alto
    $pdf->Ln(5);

    // Sección: Pruebas psicométricas
    $pdf->SetFillColor(230, 240, 255); // Fondo azul claro para el título de sección
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Pruebas Psicométricas", 0, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245); // Fondo gris claro para el contenido
    $pdf->Cell(0, 6, "Fecha de Evaluación: " . ($fila['fecha_evaluacion'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Nivel de Estrés (1-10): " . ($fila['estres'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Nivel de Depresión (1-10): " . ($fila['depresion'] ?? 'N/A'), 0, 1, 'L', 1);
    $pdf->Cell(0, 6, "Nivel de Burnout (1-10): " . ($fila['burnout'] ?? 'N/A'), 0, 1, 'L', 1);

    // Barra decorativa entre secciones
    $pdf->SetFillColor(0, 102, 204); // Color azul
    $pdf->Rect(15, $pdf->GetY(), 180, 1, 'F'); // Barra de 180 mm de ancho y 1 mm de alto
    $pdf->Ln(5);

    // Sección: Datos Biométricos
    $pdf->SetFillColor(230, 240, 255); // Fondo azul claro para el título de sección
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Datos Biométricos", 0, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245); // Fondo gris claro para el contenido

    // Obtener datos biométricos de la base de datos
    $peso = $fila['peso'];
    $talla = $fila['talla'];
    $perimetro_cintura = $fila['perimetro_cintura'];
    $porcentaje_grasa = $fila['porcentaje_grasa'];
    $masa_muscular = $fila['masa_muscular'];
    $presion_arterial_sistolica = $fila['presion_arterial_sistolica'];
    $presion_arterial_diastolica = $fila['presion_arterial_diastolica'];
    $frecuencia_cardiaca = $fila['frecuencia_cardiaca'];
    $glucosa_ayuno = $fila['glucosa_ayuno'];
    $colesterol_total = $fila['colesterol_total'];
    $trigliceridos = $fila['trigliceridos'];

    // Inicializar variables para DeepSeek
    $imc = 0;
    $tmb = 0;
    $calorias_sedentario = 0;
    $peso_objetivo = 0;
    $calorias_a_quemar_total = 0;
    $calorias_a_quemar_diarias = 0;
    $recomendaciones = "No se pudieron obtener recomendaciones debido a datos faltantes.";

    // Verificar si los datos de peso y talla están disponibles y son válidos
    if (is_null($peso) || is_null($talla) || $peso <= 30 || $peso >= 200 || $talla <= 1.0 || $talla >= 2.5) {
        $pdf->Cell(0, 6, "Peso: No disponible", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Talla: No disponible", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "IMC: No se puede calcular (falta peso o talla)", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Metabolismo Basal (TMB): No se puede calcular (falta peso o talla)", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Calorías Diarias (Sedentario): No se puede calcular (falta peso o talla)", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Peso Objetivo (IMC 24.9): No se puede calcular (falta talla)", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Calorías a Quemar para Bajar IMC: No se puede calcular (falta peso o talla)", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Calorías a Quemar por Día (90 días): No se puede calcular (falta peso o talla)", 0, 1, 'L', 1);
    } else {
        // Calcular el IMC directamente
        $imc = $peso / ($talla * $talla);
        $imc = round($imc, 1); // Redondear a 1 decimal

        // Calcular la TMB (Tasa Metabólica Basal) usando la fórmula de Harris-Benedict
        $edad = $fila['edad'] ?? 30;
        $genero = $fila['genero'] ?? 'M';
        $talla_cm = $talla * 100; // Convertir talla a centímetros para la fórmula
        if ($genero === 'M') {
            $tmb = 88.362 + (13.397 * $peso) + (4.799 * $talla_cm) - (5.677 * $edad);
        } else {
            $tmb = 447.593 + (9.247 * $peso) + (3.098 * $talla_cm) - (4.330 * $edad);
        }
        $tmb = round($tmb); // Redondear a número entero

        // Calcular calorías diarias para una persona sedentaria (factor 1.2)
        $calorias_sedentario = $tmb * 1.2;
        $calorias_sedentario = round($calorias_sedentario);

        // Calcular peso objetivo para un IMC de 24.9 (límite superior del rango "bueno")
        $peso_objetivo = 24.9 * ($talla * $talla);
        $peso_objetivo = round($peso_objetivo, 1);

        // Calcular calorías a quemar para alcanzar el peso objetivo
        $kilos_a_perder = $peso - $peso_objetivo;
        $calorias_a_quemar_total = $kilos_a_perder * 7700; // 7700 kcal por kg
        $calorias_a_quemar_total = round($calorias_a_quemar_total);

        // Calcular calorías a quemar por día (suponiendo un plan de 90 días)
        $dias = 90;
        $calorias_a_quemar_diarias = $calorias_a_quemar_total / $dias;
        $calorias_a_quemar_diarias = round($calorias_a_quemar_diarias);

        // Mostrar los datos biométricos en el PDF
        $pdf->Cell(0, 6, "Peso: " . $peso . " kg", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Talla: " . $talla . " m", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "IMC: " . $imc, 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Metabolismo Basal (TMB): " . $tmb . " kcal", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Calorías Diarias (Sedentario): " . $calorias_sedentario . " kcal", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Peso Objetivo (IMC 24.9): " . $peso_objetivo . " kg", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Calorías a Quemar para Bajar IMC: " . $calorias_a_quemar_total . " kcal", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Calorías a Quemar por Día (90 días): " . $calorias_a_quemar_diarias . " kcal", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Perímetro de Cintura: " . ($perimetro_cintura ?? 'N/A') . " cm", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Porcentaje de Grasa: " . ($porcentaje_grasa ?? 'N/A') . " %", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Masa Muscular: " . ($masa_muscular ?? 'N/A') . " kg", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Presión Arterial: " . ($presion_arterial_sistolica ?? 'N/A') . "/" . ($presion_arterial_diastolica ?? 'N/A') . " mmHg", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Frecuencia Cardíaca: " . ($frecuencia_cardiaca ?? 'N/A') . " lpm", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Glucosa en Ayunas: " . ($glucosa_ayuno ?? 'N/A') . " mg/dL", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Colesterol Total: " . ($colesterol_total ?? 'N/A') . " mg/dL", 0, 1, 'L', 1);
        $pdf->Cell(0, 6, "Triglicéridos: " . ($trigliceridos ?? 'N/A') . " mg/dL", 0, 1, 'L', 1);

        // Obtener recomendaciones de DeepSeek
        $estres = $fila['estres'] ?? 5;
        $promedio_pasos_diarios = $fila['promedio_pasos_diarios'] ?? 5000;

        // Configurar la API de DeepSeek
        $api_key = DEEPSEEK_API_KEY; // Usar la constante definida en config.php
        $prompt = "
        Eres un experto en salud y bienestar. Proporciona recomendaciones personalizadas para un colaborador con las siguientes características:
        - Edad: $edad años
        - Género: $genero
        - Peso: $peso kg
        - Talla: $talla m
        - IMC: $imc
        - Tasa Metabólica Basal (TMB): $tmb kcal
        - Calorías diarias (sedentario): $calorias_sedentario kcal
        - Nivel de estrés (1-10): $estres
        - Promedio de pasos diarios: $promedio_pasos_diarios
        - Peso objetivo para un IMC de 24.9: $peso_objetivo kg
        - Calorías a quemar para alcanzar el peso objetivo: $calorias_a_quemar_total kcal
        - Calorías a quemar por día (en 90 días): $calorias_a_quemar_diarias kcal
        - Perímetro de cintura: " . ($perimetro_cintura ?? 'N/A') . " cm
        - Porcentaje de grasa: " . ($porcentaje_grasa ?? 'N/A') . " %
        - Masa muscular: " . ($masa_muscular ?? 'N/A') . " kg
        - Presión arterial: " . ($presion_arterial_sistolica ?? 'N/A') . "/" . ($presion_arterial_diastolica ?? 'N/A') . " mmHg
        - Frecuencia cardíaca: " . ($frecuencia_cardiaca ?? 'N/A') . " lpm
        - Glucosa en ayunas: " . ($glucosa_ayuno ?? 'N/A') . " mg/dL
        - Colesterol total: " . ($colesterol_total ?? 'N/A') . " mg/dL
        - Triglicéridos: " . ($trigliceridos ?? 'N/A') . " mg/dL

        Proporciona recomendaciones detalladas en los siguientes aspectos:
        1. Alimentación: ¿Qué tipo de dieta debería seguir para mantener o reducir su IMC, porcentaje de grasa, colesterol y triglicéridos? ¿Qué alimentos priorizar o evitar?
        2. Manejo del estrés: ¿Qué técnicas o actividades recomiendas para reducir el estrés y mejorar la presión arterial?
        3. Quema de calorías: ¿Qué actividades recomiendas para quemar $calorias_a_quemar_diarias kcal diarias y alcanzar el peso objetivo?
        4. Zona de frecuencia cardíaca para entrenamientos: ¿En qué zona de frecuencia cardíaca debería entrenar para optimizar su salud y quema de calorías, considerando su frecuencia cardíaca actual?

        Responde en texto plano con secciones claras para cada aspecto, usando títulos con '###' (por ejemplo, ### Alimentación).
        ";

        // Hacer la solicitud a la API de DeepSeek
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.deepseek.com/v1/chat/completions"); // Endpoint de DeepSeek
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_key",
            "Content-Type: application/json"
        ]);
        $data = [
            "model" => "deepseek-r1", // Usar el modelo DeepSeek R1
            "messages" => [
                ["role" => "system", "content" => "Eres un experto en salud y bienestar."],
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0.7
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);

        // Manejar errores de cURL
        $recomendaciones = "No se pudieron obtener recomendaciones.";
        if ($response === false) {
            $recomendaciones = "Error al conectar con la API de DeepSeek: " . curl_error($ch);
        } else {
            $response_data = json_decode($response, true);
            if (isset($response_data['choices'][0]['message']['content'])) {
                $recomendaciones = $response_data['choices'][0]['message']['content'];
            } else {
                $recomendaciones = "Error al obtener recomendaciones: " . (isset($response_data['error']['message']) ? $response_data['error']['message'] : 'Respuesta inválida de la API');
            }
        }
        curl_close($ch);
    }

    // Barra decorativa entre secciones
    $pdf->SetFillColor(0, 102, 204); // Color azul
    $pdf->Rect(15, $pdf->GetY(), 180, 1, 'F'); // Barra de 180 mm de ancho y 1 mm de alto
    $pdf->Ln(5);

    // Sección: Recomendaciones personalizadas
    $pdf->SetFillColor(230, 240, 255); // Fondo azul claro para el título de sección
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Recomendaciones Personalizadas", 0, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 10);

    // Dividir las recomendaciones en líneas y agregarlas al PDF
    $recomendaciones_lineas = explode("\n", $recomendaciones);
    foreach ($recomendaciones_lineas as $linea) {
        if (trim($linea) !== '') {
            if (strpos($linea, '###') === 0) {
                // Títulos de sección
                $pdf->SetFillColor(200, 220, 255); // Fondo azul más claro para subtítulos
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6, str_replace('### ', '', $linea), 0, 1, 'L', 1);
                $pdf->SetFont('helvetica', '', 10);
            } else {
                // Texto normal
                $pdf->SetFillColor(245, 245, 245); // Fondo gris claro para el contenido
                $pdf->MultiCell(0, 6, $linea, 0, 'L', 1);
            }
        }
    }

    // Barra decorativa entre secciones
    $pdf->SetFillColor(0, 102, 204); // Color azul
    $pdf->Rect(15, $pdf->GetY(), 180, 1, 'F'); // Barra de 180 mm de ancho y 1 mm de alto
    $pdf->Ln(5);

    // Sección: Código QR
    $pdf->SetFillColor(230, 240, 255); // Fondo azul claro para el título de sección
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Código QR para Registrar Alimentación Diaria", 0, 1, 'L', 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetFillColor(245, 245, 245); // Fondo gris claro para el contenido
    $pdf->Cell(0, 6, "Escanea el código QR para registrar tu alimentación diaria (desayuno, comida y cena).", 0, 1, 'L', 1);
    if (isset($qr_files[$id_colaborador]) && file_exists($qr_files[$id_colaborador])) {
        $pdf->Image($qr_files[$id_colaborador], 15, $pdf->GetY(), 30, 30, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
    }

    $pdf->Ln(35); // Espacio después del QR
    // Añadir un salto de página si hay más colaboradores
    if ($resultado->num_rows > 1) {
        $pdf->AddPage();
    }
}

// Cerrar la conexión a la base de datos
$conexion->close();

// Limpiar el buffer de salida
ob_end_clean();

// Generar el PDF y enviarlo al navegador
$pdf->Output('reporte_colaboradores.pdf', 'I');

// Eliminar los archivos temporales después de generar el PDF
foreach ($qr_files as $qr_file) {
    if (file_exists($qr_file)) {
        unlink($qr_file);
    }
}