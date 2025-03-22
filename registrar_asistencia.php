<?php include 'navbar.php'; ?>
<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Asistencia</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Registrar Asistencia</h1>
    <form action="agregar_asistencia.php" method="POST">
        <label for="id_colaborador">Colaborador:</label>
        <select id="id_colaborador" name="id_colaborador" required>
            <option value="">Seleccione un colaborador</option>
            <?php
            $sql = "SELECT id_colaborador, nombre, apellido FROM colaboradores";
            $resultado = $conexion->query($sql);
            while ($fila = $resultado->fetch_assoc()) {
                echo "<option value='" . $fila['id_colaborador'] . "'>" . $fila['nombre'] . " " . $fila['apellido'] . "</option>";
            }
            ?>
        </select>

        <label for="id_sesion">Sesión:</label>
        <select id="id_sesion" name="id_sesion" required>
            <option value="">Seleccione una sesión</option>
            <?php
            $sql = "SELECT id_sesion, fecha, tipo_actividad FROM sesiones";
            $resultado = $conexion->query($sql);
            while ($fila = $resultado->fetch_assoc()) {
                echo "<option value='" . $fila['id_sesion'] . "'>" . $fila['fecha'] . " - " . $fila['tipo_actividad'] . "</option>";
            }
            $conexion->close();
            ?>
        </select>

        <label for="evaluacion">Evaluación (1-10):</label>
        <input type="number" id="evaluacion" name="evaluacion" min="1" max="10">

        <label for="estado_salud">Estado de Salud:</label>
        <textarea id="estado_salud" name="estado_salud"></textarea>

        <button type="submit">Registrar Asistencia</button>
    </form>
</body>
</html>