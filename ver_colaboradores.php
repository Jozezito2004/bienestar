<?php
$page_title = "Colaboradores Registrados - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php'; // Verificar acceso por IP

// Obtener valores de los filtros (si existen)
$id = isset($_GET['id']) ? trim($_GET['id']) : '';
$numero_identificacion = isset($_GET['numero_identificacion']) ? trim($_GET['numero_identificacion']) : '';
$edad_min = isset($_GET['edad_min']) && $_GET['edad_min'] !== '' ? (int)$_GET['edad_min'] : null;
$edad_max = isset($_GET['edad_max']) && $_GET['edad_max'] !== '' ? (int)$_GET['edad_max'] : null;
$genero = isset($_GET['genero']) ? trim($_GET['genero']) : '';
$unidad_trabajo = isset($_GET['unidad_trabajo']) ? trim($_GET['unidad_trabajo']) : '';
$puesto = isset($_GET['puesto']) ? trim($_GET['puesto']) : '';

// Construir la consulta SQL con los filtros
$sql = "SELECT id_colaborador, numero_identificacion, apellido, nombre, fecha_nacimiento, genero, unidad_trabajo, puesto, TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad FROM colaboradores WHERE 1=1";

$params = [];
$types = '';

if ($id !== '') {
    $sql .= " AND id_colaborador = ?";
    $params[] = $id;
    $types .= 'i';
}

if ($numero_identificacion !== '') {
    $sql .= " AND LOWER(numero_identificacion) LIKE LOWER(?)";
    $params[] = "%$numero_identificacion%";
    $types .= 's';
}

if ($edad_min !== null && $edad_max !== null) {
    $sql .= " AND TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) BETWEEN ? AND ?";
    $params[] = $edad_min;
    $params[] = $edad_max;
    $types .= 'ii';
} elseif ($edad_min !== null) {
    $sql .= " AND TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= ?";
    $params[] = $edad_min;
    $types .= 'i';
} elseif ($edad_max !== null) {
    $sql .= " AND TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) <= ?";
    $params[] = $edad_max;
    $types .= 'i';
}

if ($genero !== '' && $genero !== 'todos') {
    $sql .= " AND genero = ?";
    $params[] = $genero;
    $types .= 's';
}

if ($unidad_trabajo !== '' && $unidad_trabajo !== 'todos') {
    $sql .= " AND LOWER(unidad_trabajo) = LOWER(?)";
    $params[] = $unidad_trabajo;
    $types .= 's';
}

if ($puesto !== '' && $puesto !== 'todos') {
    $sql .= " AND LOWER(puesto) = LOWER(?)";
    $params[] = $puesto;
    $types .= 's';
}

// Preparar y ejecutar la consulta
$stmt = $conexion->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Obtener listas únicas para los filtros desplegables
$unidades_trabajo = $conexion->query("SELECT DISTINCT unidad_trabajo FROM colaboradores WHERE unidad_trabajo IS NOT NULL ORDER BY unidad_trabajo")->fetch_all(MYSQLI_ASSOC);
$puestos = $conexion->query("SELECT DISTINCT puesto FROM colaboradores WHERE puesto IS NOT NULL ORDER BY puesto")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Colaboradores Registrados</h1>

            <!-- Formulario de filtros -->
            <form method="GET" action="ver_colaboradores.php" class="import-container">
                <h2>Filtros de Búsqueda</h2>
                <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                    <div style="flex: 1; min-width: 200px;">
                        <label for="id">ID:</label>
                        <input type="number" id="id" name="id" value="<?php echo htmlspecialchars($id); ?>" placeholder="Ej. 4">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label for="numero_identificacion">Número de Identificación:</label>
                        <input type="text" id="numero_identificacion" name="numero_identificacion" value="<?php echo htmlspecialchars($numero_identificacion); ?>" placeholder="Ej. 12345">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label for="edad_min">Edad (Mínima):</label>
                        <input type="number" id="edad_min" name="edad_min" value="<?php echo htmlspecialchars($edad_min ?? ''); ?>" placeholder="Ej. 20">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label for="edad_max">Edad (Máxima):</label>
                        <input type="number" id="edad_max" name="edad_max" value="<?php echo htmlspecialchars($edad_max ?? ''); ?>" placeholder="Ej. 40">
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label for="genero">Género:</label>
                        <select id="genero" name="genero">
                            <option value="todos" <?php echo $genero === 'todos' || $genero === '' ? 'selected' : ''; ?>>Todos</option>
                            <option value="M" <?php echo $genero === 'M' ? 'selected' : ''; ?>>Masculino</option>
                            <option value="F" <?php echo $genero === 'F' ? 'selected' : ''; ?>>Femenino</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label for="unidad_trabajo">Unidad de Trabajo:</label>
                        <select id="unidad_trabajo" name="unidad_trabajo">
                            <option value="todos" <?php echo $unidad_trabajo === 'todos' || $unidad_trabajo === '' ? 'selected' : ''; ?>>Todos</option>
                            <?php foreach ($unidades_trabajo as $ut): ?>
                                <option value="<?php echo htmlspecialchars($ut['unidad_trabajo']); ?>" <?php echo $unidad_trabajo === $ut['unidad_trabajo'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ut['unidad_trabajo']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label for="puesto">Puesto:</label>
                        <select id="puesto" name="puesto">
                            <option value="todos" <?php echo $puesto === 'todos' || $puesto === '' ? 'selected' : ''; ?>>Todos</option>
                            <?php foreach ($puestos as $p): ?>
                                <option value="<?php echo htmlspecialchars($p['puesto']); ?>" <?php echo $puesto === $p['puesto'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['puesto']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn">Filtrar</button>
                <a href="ver_colaboradores.php" class="btn">Limpiar Filtros</a>
            </form>

            <!-- Tabla de colaboradores -->
            <table id="colaboradores-table">
                <tr>
                    <th>ID</th>
                    <th>Número Identificación</th>
                    <th>Apellido</th>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Género</th>
                    <th>Unidad Trabajo</th>
                    <th>Puesto</th>
                    <th>Acciones</th>
                </tr>
                <?php
                if ($resultado->num_rows > 0) {
                    while ($fila = $resultado->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td data-label='ID'>" . $fila['id_colaborador'] . "</td>";
                        echo "<td data-label='Número Identificación'>" . $fila['numero_identificacion'] . "</td>";
                        echo "<td data-label='Apellido'>" . $fila['apellido'] . "</td>";
                        echo "<td data-label='Nombre'>" . $fila['nombre'] . "</td>";
                        echo "<td data-label='Edad'>" . $fila['edad'] . "</td>";
                        echo "<td data-label='Género'>" . $fila['genero'] . "</td>";
                        echo "<td data-label='Unidad Trabajo'>" . $fila['unidad_trabajo'] . "</td>";
                        echo "<td data-label='Puesto'>" . $fila['puesto'] . "</td>";
                        echo "<td data-label='Acciones'><a href='detalle_colaborador.php?id=" . $fila['id_colaborador'] . "'>Ver Detalles</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No hay colaboradores que coincidan con los filtros seleccionados.</td></tr>";
                }
                $stmt->close();
                $conexion->close();
                ?>
            </table>
        </div>
    </div>
</div>

</body>
</html>