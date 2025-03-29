<?php
$page_title = "Administrar IPs Autorizadas - Bienestar BUAP";
include 'header.php';
include 'navbar.php';
include 'conexion.php';

// Procesar el formulario para agregar una IP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_ip'])) {
    $direccion_ip = trim($_POST['direccion_ip']);
    $descripcion = trim($_POST['descripcion']);

    // Validar que la IP no esté vacía y tenga un formato válido (IPv4 o IPv6)
    if (!empty($direccion_ip) && (filter_var($direccion_ip, FILTER_VALIDATE_IP))) {
        $sql = "INSERT INTO ip_autorizadas (direccion_ip, descripcion) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ss", $direccion_ip, $descripcion);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>IP agregada exitosamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al agregar la IP: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Por favor, ingresa una dirección IP válida.</div>";
    }
}

// Procesar la eliminación de una IP
if (isset($_GET['eliminar_ip'])) {
    $id_ip = (int)$_GET['eliminar_ip'];
    $sql = "DELETE FROM ip_autorizadas WHERE id_ip = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_ip);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>IP eliminada exitosamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error al eliminar la IP: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Obtener la lista de IPs autorizadas
$ips_autorizadas = $conexion->query("SELECT * FROM ip_autorizadas ORDER BY fecha_registro DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title">Administrar IPs Autorizadas</h1>
            <p class="card-text">Aquí puedes agregar o eliminar direcciones IP autorizadas para acceder al sistema.</p>

            <!-- Formulario para agregar una IP -->
            <form method="POST" action="admin_ip.php" class="import-container">
                <label for="direccion_ip">Dirección IP:</label>
                <input type="text" id="direccion_ip" name="direccion_ip" required placeholder="Ej. 192.168.1.100" pattern="^(\d{1,3}\.){3}\d{1,3}$|^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$">

                <label for="descripcion">Descripción (opcional):</label>
                <input type="text" id="descripcion" name="descripcion" placeholder="Ej. Computadora de Juan">

                <button type="submit" name="agregar_ip" class="btn btn-primary">Agregar IP</button>
            </form>

            <!-- Lista de IPs autorizadas -->
            <div class="section mt-4">
                <h2>IPs Autorizadas</h2>
                <?php if (empty($ips_autorizadas)): ?>
                    <p>No hay IPs autorizadas registradas.</p>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Dirección IP</th>
                                <th>Descripción</th>
                                <th>Fecha de Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ips_autorizadas as $ip): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ip['direccion_ip']); ?></td>
                                    <td><?php echo htmlspecialchars($ip['descripcion'] ?: 'N/A'); ?></td>
                                    <td><?php echo $ip['fecha_registro']; ?></td>
                                    <td>
                                        <a href="admin_ip.php?eliminar_ip=<?php echo $ip['id_ip']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta IP?');">Eliminar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
<?php $conexion->close(); ?>