<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienestar BUAP - Registrar Sesión</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <h1>Registrar Sesión de Bienestar BUAP</h1>
    <form action="agregar_sesion.php" method="POST">
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>

        <label for="turno">Turno:</label>
        <select id="turno" name="turno" required>
            <option value="Matutino">Matutino</option>
            <option value="Vespertino">Vespertino</option>
            <option value="Nocturno">Nocturno</option>
        </select>

        <label for="tipo_actividad">Tipo de Actividad:</label>
        <input type="text" id="tipo_actividad" name="tipo_actividad" required>

        <label for="duracion">Duración (minutos):</label>
        <input type="number" id="duracion" name="duracion" required>

        <label for="descripcion">Descripción (opcional):</label>
        <textarea id="descripcion" name="descripcion"></textarea>

        <button type="submit">Guardar Sesión</button>
    </form>
</body>
</html>