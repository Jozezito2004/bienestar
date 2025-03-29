<?php
$page_title = "Cargar Datos - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Cargar Datos de Colaboradores</h1>
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

                    <button type="submit" class="btn">Cargar Datos</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>