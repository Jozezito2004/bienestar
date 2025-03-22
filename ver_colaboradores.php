<?php include 'navbar.php'; ?>
<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Colaboradores Registrados</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Colaboradores Registrados</h1>
    <table>
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
        $sql = "SELECT id_colaborador, numero_identificacion, apellido, nombre, fecha_nacimiento, genero, unidad_trabajo, puesto, TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad FROM colaboradores";
        $resultado = $conexion->query($sql);
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $fila['id_colaborador'] . "</td>";
                echo "<td>" . $fila['numero_identificacion'] . "</td>";
                echo "<td>" . $fila['apellido'] . "</td>";
                echo "<td>" . $fila['nombre'] . "</td>";
                echo "<td>" . $fila['edad'] . "</td>";
                echo "<td>" . $fila['genero'] . "</td>";
                echo "<td>" . $fila['unidad_trabajo'] . "</td>";
                echo "<td>" . $fila['puesto'] . "</td>";
                echo "<td><a href='detalle_colaborador.php?id=" . $fila['id_colaborador'] . "'>Ver Detalles</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No hay colaboradores registrados</td></tr>";
        }
        $conexion->close();
        ?>
    </table>
</body>
</html>