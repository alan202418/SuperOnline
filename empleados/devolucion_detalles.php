<?php
require '../config/config.php';
require '../config/conexion.php';

$cuenta_destinatario = '';

if (isset($_GET['id_usuario']) && isset($_GET['codigo_del_producto'])) {
    $id_usuario = $_GET['id_usuario'];
    $codigo_producto = $_GET['codigo_del_producto'];

    // Seleccionar todas las compras en devolución del usuario con el código del producto especificado
    $sql_select = "SELECT * FROM compras WHERE id_usuario = '$id_usuario' AND codigo_del_producto = '$codigo_producto' AND estado_compra = 'compra en devolucion'";
    $result_select = mysqli_query($conexion, $sql_select);

    $total_a_devolver = 0;
    $compras = [];
    $id_compras = [];

    // Obtener todos los id_compra y calcular el total a devolver
    while ($compra = mysqli_fetch_assoc($result_select)) {
        $compras[] = $compra;
        $id_compras[] = $compra['id_compra'];
        $total_a_devolver += $compra['subtotal'];
    }

    // Recorrer todos los id_compra para encontrar la cuenta_destinatario en devoluciones
    foreach ($id_compras as $id_compra) {
        // Verificar si el id_compra existe en la tabla devoluciones
        $sql_devolucion = "SELECT cuenta_destinatario FROM devoluciones WHERE id_compra = '$id_compra'";
        $result_devolucion = mysqli_query($conexion, $sql_devolucion);

        if ($result_devolucion && mysqli_num_rows($result_devolucion) > 0) {
            // Si se encuentra la cuenta_destinatario, tomarla y salir del bucle
            $devolucion = mysqli_fetch_assoc($result_devolucion);
            $cuenta_destinatario = $devolucion['cuenta_destinatario'];
            break;
        } else {
            // Si no se encuentra en devoluciones, buscar por código de producto en compras
            $sql_compras_codigo = "SELECT id_compra FROM compras WHERE codigo_del_producto = '$codigo_producto' AND id_usuario = '$id_usuario'";
            $result_compras_codigo = mysqli_query($conexion, $sql_compras_codigo);

            while ($compra_codigo = mysqli_fetch_assoc($result_compras_codigo)) {
                // Verificar si alguno de estos id_compra tiene cuenta_destinatario en devoluciones
                $sql_devolucion_codigo = "SELECT cuenta_destinatario FROM devoluciones WHERE id_compra = '" . $compra_codigo['id_compra'] . "'";
                $result_devolucion_codigo = mysqli_query($conexion, $sql_devolucion_codigo);

                if ($result_devolucion_codigo && mysqli_num_rows($result_devolucion_codigo) > 0) {
                    $devolucion_codigo = mysqli_fetch_assoc($result_devolucion_codigo);
                    $cuenta_destinatario = $devolucion_codigo['cuenta_destinatario'];
                    break 2; // Salir de ambos bucles si se encuentra
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Devolución</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1>Detalles de Devolución</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compras as $compra): ?>
                    <tr>
                        <td><?= htmlspecialchars($compra['nombre_producto']) ?></td>
                        <td><?= htmlspecialchars($compra['cantidad']) ?></td>
                        <td>$<?= htmlspecialchars($compra['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total a Devolver</th>
                    <th>$<?= htmlspecialchars($total_a_devolver) ?></th>
                </tr>
                <tr>
                    <th colspan="2">Cuenta Destinatario</th>
                    <th><?= htmlspecialchars($cuenta_destinatario) ?></th>
                </tr>
            </tfoot>
        </table>
        <div class="mt-4">
            <a href="pedidos.php" class="btn btn-secondary">Volver a Pedidos</a>
            <!-- Usamos el primer id_compra de la lista -->
            <a href="procesar_devolucion.php?id_compra=<?= htmlspecialchars($id_compras[0]) ?>&codigo_del_producto=<?= htmlspecialchars($codigo_producto) ?>" class="btn btn-primary">Enviar Devolución</a>
        </div>

    </div>
</body>

</html>