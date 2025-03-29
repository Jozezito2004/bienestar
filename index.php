<?php
$page_title = "Inicio - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';
include 'control_acceso.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Bienvenido a Bienestar BUAP</h1>
            <p class="card-text">Gestiona el bienestar de los colaboradores de la BUAP.</p>
            <div class="section">
                <h2>Opciones Disponibles</h2>
                <ul>
                    <li><a href="ver_colaboradores.php" class="btn">Ver Colaboradores</a></li>
                    <li><a href="cargar_pruebas_psicometricas.php" class="btn">Cargar Pruebas Psicométricas</a></li>
                    <li><a href="seguimiento_actividad_fisica.php" class="btn">Seguimiento de Actividad Física</a></li>
                    <li><a href="cargar_alimentacion.php" class="btn">Cargar Alimentación</a></li>
                    <li><a href="admin_ip.php" class="btn">Administrar IPs Autorizadas</a></li>
                </ul>
            </div>

            <!-- Resumen rápido de alimentación -->
            <div class="section mt-4">
                <h2>Resumen de Alimentación</h2>
                <?php
                $total_registros = $conexion->query("SELECT COUNT(*) as total FROM alimentacion_diaria")->fetch_assoc()['total'];
                $registros_hoy = $conexion->query("SELECT COUNT(*) as hoy FROM alimentacion_diaria WHERE DATE(fecha_registro) = CURDATE()")->fetch_assoc()['hoy'];
                ?>
                <p>Total de registros: <?php echo $total_registros; ?></p>
                <p>Registros de hoy: <?php echo $registros_hoy; ?></p>
            </div>
        </div>
    </div>
</div>

<?php $conexion->close(); ?>
</body>
</html>