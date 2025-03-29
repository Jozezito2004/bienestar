<?php
$page_title = "Recomendaciones Personalizadas - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'config.php'; // Incluir el archivo de configuración

// Obtener el ID del colaborador desde la URL
$id_colaborador = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Si no se proporciona un ID, mostrar un formulario para seleccionar un colaborador
if ($id_colaborador === 0) {
    $sql = "SELECT id_colaborador, nombre, apellido FROM colaboradores";
    $resultado = $conexion->query($sql);
?>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Seleccionar Colaborador</h1>
                <form action="recomendaciones_ia.php" method="GET">
                    <label for="id">Colaborador:</label>
                    <select name="id" id="id" required>
                        <option value="">Seleccione un colaborador</option>
                        <?php while ($fila = $resultado->fetch_assoc()) { ?>
                            <option value="<?php echo $fila['id_colaborador']; ?>">
                                <?php echo htmlspecialchars($fila['nombre'] . ' ' . $fila['apellido']); ?>
                            </option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="btn">Ver Recomendaciones</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
    $conexion->close();
    exit;
}

// Consulta para obtener los datos del colaborador
$sql = "
    SELECT 
        c.id_colaborador, c.nombre, c.apellido, c.genero, c.fecha_nacimiento,
        TIMESTAMPDIFF(YEAR, c.fecha_nacimiento, CURDATE()) AS edad,
        a.promedio_pasos_diarios, -- Eliminamos tiempo_actividad
        p.estres, p.depresion, p.burnout,
        al.peso, al.talla, al.imc, al.metabolismo_basal, al.calorias_diarias
    FROM colaboradores c
    LEFT JOIN actividad_fisica a ON c.id_colaborador = a.id_colaborador
    LEFT JOIN pruebas_psicometricas p ON c.id_colaborador = p.id_colaborador
    LEFT JOIN alimentacion al ON c.id_colaborador = al.id_colaborador
    WHERE c.id_colaborador = ?
";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_colaborador);
$stmt->execute();
$resultado = $stmt->get_result();
$colaborador = $resultado->fetch_assoc();
$stmt->close();

// Verificar si el colaborador existe
if (!$colaborador) {
    echo "<div class='container mt-4'><h1>Colaborador no encontrado</h1></div>";
    exit;
}

// Preparar los datos para la API de OpenAI
$edad = $colaborador['edad'] ?? 30;
$genero = $colaborador['genero'] ?? 'M';
$peso = $colaborador['peso'] ?? 70;
$talla = $colaborador['talla'] ?? 1.70;
$imc = $colaborador['imc'] ?? 24;
$calorias_diarias = $colaborador['calorias_diarias'] ?? 2000;
$estres = $colaborador['estres'] ?? 5;
$promedio_pasos_diarios = $colaborador['promedio_pasos_diarios'] ?? 5000;

// Configurar la API de OpenAI
$api_key = OPENAI_API_KEY; // Usar la constante definida en config.php
$prompt = "
Eres un experto en salud y bienestar. Proporciona recomendaciones personalizadas para un colaborador con las siguientes características:
- Edad: $edad años
- Género: $genero
- Peso: $peso kg
- Talla: $talla m
- IMC: $imc
- Calorías diarias necesarias: $calorias_diarias kcal
- Nivel de estrés (1-10): $estres
- Promedio de pasos diarios: $promedio_pasos_diarios

Proporciona recomendaciones detalladas en los siguientes aspectos:
1. Alimentación: ¿Qué tipo de dieta debería seguir? ¿Qué alimentos priorizar o evitar?
2. Manejo del estrés: ¿Qué técnicas o actividades recomiendas para reducir el estrés?
3. Quema de calorías: ¿Cuántas calorías debería quemar diariamente y qué actividades recomiendas?
4. Zona de frecuencia cardíaca para entrenamientos: ¿En qué zona de frecuencia cardíaca debería entrenar para optimizar su salud y quema de calorías?

Responde en formato HTML con secciones claras para cada aspecto.
";

// Hacer la solicitud a la API de OpenAI
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key",
    "Content-Type: application/json"
]);
$data = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "Eres un experto en salud y bienestar."],
        ["role" => "user", "content" => $prompt]
    ],
    "temperature" => 0.7
];
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);

// Manejar errores de cURL
if ($response === false) {
    $recomendaciones = "<p>Error al conectar con la API de OpenAI: " . curl_error($ch) . "</p>";
} else {
    $response_data = json_decode($response, true);
    if (isset($response_data['choices'][0]['message']['content'])) {
        $recomendaciones = $response_data['choices'][0]['message']['content'];
    } else {
        $recomendaciones = "<p>Error al obtener recomendaciones: " . (isset($response_data['error']['message']) ? $response_data['error']['message'] : 'Respuesta inválida de la API') . "</p>";
    }
}
curl_close($ch);
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Recomendaciones Personalizadas para <?php echo htmlspecialchars($colaborador['nombre'] . ' ' . $colaborador['apellido']); ?></h1>
            <div class="section">
                <?php echo $recomendaciones; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<?php
$conexion->close();
?>