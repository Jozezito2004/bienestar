<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienestar BUAP</title>
    <!-- Incluir Bootstrap para un diseño más amigable -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Bienestar BUAP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cargar_datos.php">Cargar Datos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ver_colaboradores.php">Ver Colaboradores</a>
                    </li>
                    <!-- Menú contextual para generar reportes -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reportesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Generar Reportes
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="reportesDropdown">
                            <li><a class="dropdown-item" href="generar_reporte.php">Reporte General</a></li>
                            <li><a class="dropdown-item" href="generar_reporte.php?tipo=colaborador">Por Colaborador</a></li>
                            <li><a class="dropdown-item" href="generar_reporte.php?tipo=area">Por Área</a></li>
                            <li><a class="dropdown-item" href="generar_reporte.php?tipo=clase">Por Clase</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">