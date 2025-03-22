<?php include 'navbar.php'; ?>
<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Estadísticas</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Estadísticas Generales</h1>
    <div class="section">
        <h2>Promedio de Pasos Diarios por Unidad de Trabajo</h2>
        <table>
            <tr>
                <th>Unidad de Trabajo</th>
                <th>Promedio de Pasos Diarios</th>
            </tr>
            <?php
            $sql = "SELECT c.unidad_trabajo, AVG(a.promedio_pasos_diarios) as avg_pasos 
                    FROM colaboradores c 
                    LEFT JOIN actividad_fisica a ON c.id_colaborador = a.id_colaborador 
                    GROUP BY c.unidad_trabajo";
            $resultado = $conexion->query($sql);
            if ($resultado->num_rows > 0) {
                while ($fila = $resultado->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $fila['unidad_trabajo'] . "</td>";
                    echo "<td>" . round($fila['avg_pasos'], 0) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No hay datos disponibles</td></tr>";
            }
            $conexion->close();
            ?>
        </table>
    </div>
</body>
</html>