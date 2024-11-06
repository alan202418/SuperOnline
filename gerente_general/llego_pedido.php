<?php
require '../config/config.php';
require '../config/conexion.php';

if (isset($_GET['fecha_pedida'])) {
    $fecha_pedido = $_GET['fecha_pedida'];

    // Cambiar el estado de los pedidos a "ingreso a sucursal"
    $sql_update = "UPDATE pedido_gerente_general SET estado_pedido = 'ingreso a sucursal' WHERE fecha_pedida = ?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param('s', $fecha_pedido);
    $stmt->execute();

    // Obtener todos los productos con la misma fecha_pedida y sucursal_destinatario
    $sql_pedidos = "SELECT sucursal_destinatario, producto, cantidad_remitente FROM pedido_gerente_general WHERE fecha_pedida = ?";
    $stmt_pedidos = $conexion->prepare($sql_pedidos);
    $stmt_pedidos->bind_param('s', $fecha_pedido);
    $stmt_pedidos->execute();
    $result_pedidos = $stmt_pedidos->get_result();

    // Procesar cada producto en el pedido
    while ($pedido = $result_pedidos->fetch_assoc()) {
        $sucursal_destinatario = $pedido['sucursal_destinatario'];
        $producto = $pedido['producto'];
        $cantidad_remitente = $pedido['cantidad_remitente'];

        // Obtener el ID de la sucursal destinataria
        $sql_sucursal = "SELECT id_sucursal FROM sucursales WHERE nombre_sucursal = ?";
        $stmt_sucursal = $conexion->prepare($sql_sucursal);
        $stmt_sucursal->bind_param('s', $sucursal_destinatario);
        $stmt_sucursal->execute();
        $result_sucursal = $stmt_sucursal->get_result();
        $sucursal_data = $result_sucursal->fetch_assoc();
        $id_sucursal = $sucursal_data['id_sucursal'];

        // Actualizar el stock en la tabla productos para cada producto específico
        $sql_producto = "SELECT stock FROM productos WHERE nombre = ? AND id_sucursal = ?";
        $stmt_producto = $conexion->prepare($sql_producto);
        $stmt_producto->bind_param('si', $producto, $id_sucursal);
        $stmt_producto->execute();
        $result_producto = $stmt_producto->get_result();

        if ($result_producto->num_rows > 0) {
            $producto_data = $result_producto->fetch_assoc();
            $nuevo_stock = $producto_data['stock'] + $cantidad_remitente;

            // Actualizar el stock 
            $sql_update_stock = "UPDATE productos SET stock = ? WHERE nombre = ? AND id_sucursal = ?";
            $stmt_update_stock = $conexion->prepare($sql_update_stock);
            $stmt_update_stock->bind_param('isi', $nuevo_stock, $producto, $id_sucursal);
            $stmt_update_stock->execute();
        }

        // Cerrar los statements intermedios
        $stmt_producto->close();
        $stmt_sucursal->close();
    }

    // Cerrar los statements
    $stmt->close();
    $stmt_pedidos->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Ingresado</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        title: 'Pedido ingresado',
        text: 'El stock ha sido actualizado correctamente en la sucursal destinataria.',
        icon: 'success',
        confirmButtonText: 'OK'
    }).then(() => {
        window.location.href = 'pedidos_sucursales.php'; // Redirigir a pedidos_sucursales.php
    });
</script>
</body>
</html>
