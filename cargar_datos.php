<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cargar Datos - Bienestar BUAP</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .import-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
        }
        textarea.import-box {
            width: 100%;
            height: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
            font-family: monospace;
        }
        .options {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>Cargar Datos de Colaboradores</h1>
    <div class="import-container">
        <h2>Cargar Colaboradores desde una Hoja de Cálculo</h2>
        <form action="procesar_datos.php" method="POST" enctype="multipart/form-data">
            <!-- Opción 1: Pegar datos en un cuadro de texto -->
            <h3>Pegar Datos</h3>
            <textarea class="import-box" name="datos_pegados" placeholder="Pega aquí los datos en el formato especificado (por ejemplo, ##COLABORADOR## <tab> NUMERO_IDENTIFICACION <tab> NOMBRE <tab> APELLIDO...)"></textarea>

            <!-- Opción 2: Subir un archivo CSV -->
            <h3>Subir Archivo CSV</h3>
            <input type="file" name="archivo_csv" accept=".csv">
            <p>Sin archivo seleccionado</p>

            <!-- Opciones de importación -->
            <div class="options">
                <h3>Opciones de Importación</h3>
                <label><input type="checkbox" name="borrar_no_listados"> Borrar las inscripciones existentes que no estén en la lista</label><br>
                <label><input type="checkbox" name="sobrescribir"> Sobrescribir datos anteriores</label><br>
                <label><input type="checkbox" name="no_actualizar_historial"> No actualizar historial de salud</label><br>
            </div>

            <button type="submit">Cargar Datos</button>
        </form>
    </div>
</body>
</html>