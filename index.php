<?php
include 'header.php';
?>

<h1>Bienvenido a Bienestar BUAP</h1>
<p>Este sistema permite gestionar los datos de bienestar de los colaboradores de la BUAP.</p>
<ul>
    <li><a href="cargar_datos.php">Cargar datos de colaboradores</a></li>
    <li><a href="ver_colaboradores.php">Ver lista de colaboradores</a></li>
    <li><a href="generar_reporte.php">Generar reportes</a></li>
    <li><a href="registrar_colaborador.php">Registrar un nuevo colaborador</a></li>
</ul>

<?php
echo '</div>'; // Cierra el container de Bootstrap
echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>';
echo '</body>';
echo '</html>';
?>