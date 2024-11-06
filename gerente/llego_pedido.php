<?php
require '../config/config.php';
require '../config/conexion.php';


// Verificar si el usuario ha iniciado sesión y obtener su nombre de usuario
if (!isset($_SESSION['user_name'])) {
    // Redirigir a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];

// Consultar el id_sucursal del usuario actual
$sql_sucursal = "SELECT id_sucursal FROM usuarios WHERE usuario = ?";
$stmt_sucursal = $conexion->prepare($sql_sucursal);
$stmt_sucursal->bind_param('s', $user_name);
$stmt_sucursal->execute();
$result_sucursal = $stmt_sucursal->get_result();
$sucursal_data = $result_sucursal->fetch_assoc();
$id_sucursal = $sucursal_data['id_sucursal'] ?? null; // Usar null si no se encuentra el id_sucursal

// Verificar que el id_sucursal se haya obtenido correctamente
if (!$id_sucursal) {
    echo "<script>alert('No se encontró la sucursal para el usuario.');</script>";
    exit();
}

if (isset($_GET['fecha_pedido'])) {
    $fecha_pedido = $_GET['fecha_pedido'];

    // Actualizar el estado de los pedidos a "ingreso a sucursal"
    $sql_update = "UPDATE pedidos_proveedor SET estado_pedido = 'ingreso a sucursal' WHERE fecha_pedido = ?";
    $stmt = $conexion->prepare($sql_update);
    $stmt->bind_param('s', $fecha_pedido);
    $stmt->execute();

    // Actualizar el stock de los productos
    $sql_pedidos = "SELECT producto, cantidad FROM pedidos_proveedor WHERE fecha_pedido = ?";
    $stmt_pedidos = $conexion->prepare($sql_pedidos);
    $stmt_pedidos->bind_param('s', $fecha_pedido);
    $stmt_pedidos->execute();
    $result_pedidos = $stmt_pedidos->get_result();

    while ($pedido = $result_pedidos->fetch_assoc()) {
        $producto = $pedido['producto'];
        $cantidad = $pedido['cantidad'];

        // Verificar si el producto existe en la tabla productos
        $sql_producto = "SELECT stock FROM productos WHERE nombre = ?";
        $stmt_producto = $conexion->prepare($sql_producto);
        $stmt_producto->bind_param('s', $producto);
        $stmt_producto->execute();
        $result_producto = $stmt_producto->get_result();

        if ($result_producto->num_rows > 0) {
            $producto_data = $result_producto->fetch_assoc();
            $nuevo_stock = $producto_data['stock'] + $cantidad;

            // Actualizar el stock
            $sql_update_stock = "UPDATE productos SET stock = ? WHERE nombre = ? AND id_sucursal = $id_sucursal";
            $stmt_update_stock = $conexion->prepare($sql_update_stock);
            $stmt_update_stock->bind_param('is', $nuevo_stock, $producto);
            $stmt_update_stock->execute();
        }
    }

    $stmt->close();
    $stmt_pedidos->close();
    $stmt_producto->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Ingresado</title>
    <!-- Cargar los scripts de SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <script>
        // Mostrar alerta después de que todo el procesamiento haya sido completado
        Swal.fire({
            title: 'Pedido ingresado',
            text: 'El stock ha sido actualizado correctamente.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'pedidos.php'; // Redirigir a pedidos.php
        });
    </script>

</body>

</html>