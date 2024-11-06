<?php
require '../config/config.php';
require '../config/conexion.php';

if (isset($_GET['fecha_pedida'])) {
    $fecha_pedido = $_GET['fecha_pedida'];

    // Obtener todos los productos con la misma fecha_pedida
    $sql_pedidos = "SELECT sucursal_remitente, producto, cantidad_remitente FROM pedido_gerente_general WHERE fecha_pedida = ?";
    $stmt_pedidos = $conexion->prepare($sql_pedidos);
    $stmt_pedidos->bind_param('s', $fecha_pedido);
    $stmt_pedidos->execute();
    $result_pedidos = $stmt_pedidos->get_result();

    // Procesar cada pedido con la misma fecha_pedida
    while ($pedido = $result_pedidos->fetch_assoc()) {
        $sucursal_remitente = $pedido['sucursal_remitente'];
        $producto = $pedido['producto'];
        $cantidad_remitente = $pedido['cantidad_remitente'];

        // Obtener el ID de la sucursal remitente
        $sql_sucursal = "SELECT id_sucursal FROM sucursales WHERE nombre_sucursal = ?";
        $stmt_sucursal = $conexion->prepare($sql_sucursal);
        $stmt_sucursal->bind_param('s', $sucursal_remitente);
        $stmt_sucursal->execute();
        $result_sucursal = $stmt_sucursal->get_result();
        $sucursal_data = $result_sucursal->fetch_assoc();
        $id_sucursal = $sucursal_data['id_sucursal'];

        // Actualizar el stock en la tabla productos para cada producto específico
        $sql_update_stock = "UPDATE productos SET stock = stock + ? WHERE nombre = ? AND id_sucursal = ?";
        $stmt_update_stock = $conexion->prepare($sql_update_stock);
        $stmt_update_stock->bind_param('isi', $cantidad_remitente, $producto, $id_sucursal);
        $stmt_update_stock->execute();

        // Cerrar statement para el stock
        $stmt_update_stock->close();
        $stmt_sucursal->close();
    }

    // Actualizar el estado de todos los pedidos con la misma fecha_pedida a "cancelado"
    $sql_update_pedido = "UPDATE pedido_gerente_general SET estado_pedido = 'cancelado' WHERE fecha_pedida = ?";
    $stmt_update_pedido = $conexion->prepare($sql_update_pedido);
    $stmt_update_pedido->bind_param('s', $fecha_pedido);
    $stmt_update_pedido->execute();

    // Cerrar consultas
    $stmt_pedidos->close();
    $stmt_update_pedido->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Cancelado</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        title: 'Cancelado',
        text: 'Todos los productos del pedido han sido cancelados correctamente y el stock ha sido actualizado.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'pedidos_sucursales.php';  // Redirigir a pedidos_sucursales.php
    });
</script>
</body>
</html>
