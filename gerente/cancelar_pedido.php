<?php
require '../config/config.php';
require '../config/conexion.php';

if (isset($_GET['fecha_pedido'])) {
    $fecha_pedido = $_GET['fecha_pedido'];

    // Actualizar el estado de los pedidos a "cancelado"
    $sql_update = "UPDATE pedidos_proveedor SET estado_pedido = 'cancelado' WHERE fecha_pedido = ?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param('s', $fecha_pedido);
    $stmt->execute();

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Cancelado</title>
    <!-- Cargar los scripts de SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<script>
    // Mostrar alerta después de que todo el procesamiento haya sido completado
    Swal.fire({
        title: 'Cancelado',
        text: 'El pedido ha sido cancelado correctamente.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'pedidos.php';  // Redirigir a pedidos.php
    });
</script>

</body>
</html>
