<?php include 'navbar.php'; ?>
<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asistencia Registrada</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Asistencia Registrada</h1>
    <table>
        <tr>
            <th>ID Asistencia</th>
            <th>Colaborador</th>
            <th>Sesión</th>
            <th>Evaluación</th>
            <th>Estado de Salud</th>
        </tr>
        <?php
        $sql = "SELECT a.id_asistencia, c.nombre, c.apellido, s.fecha, s.tipo_actividad, a.evaluacion, a.estado_salud 
                FROM asistencia a 
                JOIN colaboradores c ON a.id_colaborador = c.id_colaborador 
                JOIN sesiones s ON a.id_sesion = s.id_sesion";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $fila['id_asistencia'] . "</td>";
                echo "<td>" . $fila['nombre'] . " " . $fila['apellido'] . "</td>";
                echo "<td>" . $fila['fecha'] . " - " . $fila['tipo_actividad'] . "</td>";
                echo "<td>" . ($fila['evaluacion'] ?: 'N/A') . "</td>";
                echo "<td>" . ($fila['estado_salud'] ?: 'N/A') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No hay asistencias registradas</td></tr>";
        }
        $conexion->close();
        ?>
    </table>
</body>
</html>