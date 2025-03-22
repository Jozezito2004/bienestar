<?php include 'navbar.php'; ?>
<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sesiones Registradas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Sesiones Registradas</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Turno</th>
            <th>Tipo de Actividad</th>
            <th>Duración (minutos)</th>
            <th>Descripción</th>
        </tr>
        <?php
        $sql = "SELECT * FROM sesiones";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $fila['id_sesion'] . "</td>";
                echo "<td>" . $fila['fecha'] . "</td>";
                echo "<td>" . $fila['turno'] . "</td>";
                echo "<td>" . $fila['tipo_actividad'] . "</td>";
                echo "<td>" . $fila['duracion'] . "</td>";
                echo "<td>" . $fila['descripcion'] ?: 'N/A' . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No hay sesiones registradas</td></tr>";
        }
        $conexion->close();
        ?>
    </table>
</body>
</html>