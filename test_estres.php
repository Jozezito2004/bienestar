<?php
$page_title = "Test de Estrés Laboral - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php';

$id_colaborador = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuestas = [
        'carga_trabajo' => (int)$_POST['carga_trabajo'],
        'control' => (int)$_POST['control'],
        'apoyo' => (int)$_POST['apoyo'],
        'relaciones' => (int)$_POST['relaciones'],
        'rol' => (int)$_POST['rol'],
        'cambio' => (int)$_POST['cambio']
    ];
    
    // Calcular puntaje total (0-24, mayor puntaje = mayor estrés)
    $estres_total = array_sum($respuestas);
    
    $sql = "INSERT INTO pruebas_psicometricas (id_colaborador, fecha_evaluacion, estres) 
            VALUES (?, CURDATE(), ?) 
            ON DUPLICATE KEY UPDATE fecha_evaluacion = CURDATE(), estres = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("iii", $id_colaborador, $estres_total, $estres_total);
    $stmt->execute();
    $stmt->close();
    
    echo "<div class='container mt-4'><div class='card'><div class='card-body'>";
    echo "<h1 class='card-title'>Resultados Guardados</h1>";
    echo "<p>El test de estrés laboral ha sido guardado exitosamente. Puntaje: $estres_total/24</p>";
    echo "<a href='cargar_pruebas_psicometricas.php' class='btn'>Volver</a>";
    echo "</div></div></div>";
    include 'footer.php';
    exit();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Test de Estrés Laboral (OMS)</h1>
            <p>Evalúa los siguientes aspectos en una escala de 0 (Nada) a 4 (Mucho):</p>
            
            <form method="POST" action="test_estres.php?id=<?php echo $id_colaborador; ?>">
                <div class="form-group">
                    <label>1. ¿Sientes que tu carga de trabajo es excesiva?</label>
                    <select name="carga_trabajo" required>
                        <option value="0">0 - Nada</option>
                        <option value="1">1 - Poco</option>
                        <option value="2">2 - Moderado</option>
                        <option value="3">3 - Bastante</option>
                        <option value="4">4 - Mucho</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>2. ¿Tienes control sobre cómo realizas tu trabajo?</label>
                    <select name="control" required>
                        <option value="4">0 - Mucho</option>
                        <option value="3">1 - Bastante</option>
                        <option value="2">2 - Moderado</option>
                        <option value="1">3 - Poco</option>
                        <option value="0">4 - Nada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>3. ¿Recibes apoyo suficiente de tus superiores y compañeros?</label>
                    <select name="apoyo" required>
                        <option value="4">0 - Mucho</option>
                        <option value="3">1 - Bastante</option>
                        <option value="2">2 - Moderado</option>
                        <option value="1">3 - Poco</option>
                        <option value="0">4 - Nada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>4. ¿Cómo son tus relaciones interpersonales en el trabajo?</label>
                    <select name="relaciones" required>
                        <option value="4">0 - Muy buenas</option>
                        <option value="3">1 - Buenas</option>
                        <option value="2">2 - Regulares</option>
                        <option value="1">3 - Malas</option>
                        <option value="0">4 - Muy malas</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>5. ¿Está claro tu rol y responsabilidades en el trabajo?</label>
                    <select name="rol" required>
                        <option value="4">0 - Muy claro</option>
                        <option value="3">1 - Claro</option>
                        <option value="2">2 - Algo claro</option>
                        <option value="1">3 - Poco claro</option>
                        <option value="0">4 - Nada claro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>6. ¿Cómo manejas los cambios en tu entorno laboral?</label>
                    <select name="cambio" required>
                        <option value="4">0 - Muy bien</option>
                        <option value="3">1 - Bien</option>
                        <option value="2">2 - Regular</option>
                        <option value="1">3 - Mal</option>
                        <option value="0">4 - Muy mal</option>
                    </select>
                </div>

                <button type="submit" class="btn">Guardar Resultados</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php $conexion->close(); ?>