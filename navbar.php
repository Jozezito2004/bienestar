<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        /* Estilos para la barra de navegación */
        .navbar {
            background-color: #2c3e50;
            padding: 10px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        .navbar li {
            margin: 0 15px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .navbar a:hover {
            background-color: #3498db;
        }
        .navbar a.active {
            background-color: #3498db;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <ul>
            <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Inicio</a></li>
            <li><a href="ver_colaboradores.php" <?php echo basename($_SERVER['PHP_SELF']) == 'ver_colaboradores.php' ? 'class="active"' : ''; ?>>Colaboradores</a></li>
            <li><a href="registrar_sesion.php" <?php echo basename($_SERVER['PHP_SELF']) == 'registrar_sesion.php' ? 'class="active"' : ''; ?>>Registrar Sesión</a></li>
            <li><a href="ver_sesiones.php" <?php echo basename($_SERVER['PHP_SELF']) == 'ver_sesiones.php' ? 'class="active"' : ''; ?>>Sesiones</a></li>
            <li><a href="registrar_asistencia.php" <?php echo basename($_SERVER['PHP_SELF']) == 'registrar_asistencia.php' ? 'class="active"' : ''; ?>>Registrar Asistencia</a></li>
            <li><a href="ver_asistencia.php" <?php echo basename($_SERVER['PHP_SELF']) == 'ver_asistencia.php' ? 'class="active"' : ''; ?>>Asistencia</a></li>
            <li><a href="cargar_datos.php" <?php echo basename($_SERVER['PHP_SELF']) == 'cargar_datos.php' ? 'class="active"' : ''; ?>>Cargar Datos</a></li>
            <li><a href="estadisticas.php" <?php echo basename($_SERVER['PHP_SELF']) == 'estadisticas.php' ? 'class="active"' : ''; ?>>Estadísticas</a></li>
            <li><a href="generar_reporte.php" <?php echo basename($_SERVER['PHP_SELF']) == 'generar_reporte.php' ? 'class="active"' : ''; ?>>Generar Reporte</a></li>
        </ul>
    </nav>
</body>
</html>