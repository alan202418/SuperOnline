<?php
require '../config/conexion.php';
require '../config/config.php';

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];

    // Consultar el id_sucursal del usuario autenticado
    $sqlUsuario = "SELECT id_sucursal FROM usuarios WHERE usuario = ?";
    $stmtUsuario = $conexion->prepare($sqlUsuario);
    $stmtUsuario->bind_param("s", $user_name);
    $stmtUsuario->execute();
    $resultadoUsuario = $stmtUsuario->get_result();

    if ($resultadoUsuario->num_rows > 0) {
        $usuarioData = $resultadoUsuario->fetch_assoc();
        $id_sucursal_usuario = $usuarioData['id_sucursal'];

        // Consulta para obtener el nombre_sucursal de la tabla sucursales
        $sqlSucursal = "SELECT nombre_sucursal FROM sucursales WHERE id_sucursal = ?";
        $stmtSucursal = $conexion->prepare($sqlSucursal);
        $stmtSucursal->bind_param("i", $id_sucursal_usuario);
        $stmtSucursal->execute();
        $resultadoSucursal = $stmtSucursal->get_result();

        if ($resultadoSucursal->num_rows > 0) {
            $sucursalData = $resultadoSucursal->fetch_assoc();
            $nombre_sucursal = $sucursalData['nombre_sucursal'];

            // Consulta para obtener compras donde lugar_retiro coincide con el nombre de la sucursal
            $sqlCompras = "SELECT * FROM compras WHERE lugar_retiro = ?";
            $stmtCompras = $conexion->prepare($sqlCompras);
            $stmtCompras->bind_param("s", $nombre_sucursal);
            $stmtCompras->execute();
            $resultadoCompras = $stmtCompras->get_result();
        } else {
            // Si no se encuentra la sucursal, no mostrar compras
            $resultadoCompras = null;
        }

        // Cerrar los statements
        $stmtSucursal->close();
        $stmtUsuario->close();
    } else {
        // Si el usuario no tiene id_sucursal asociado, no mostrar compras
        $resultadoCompras = null;
    }
} else {
    // Redireccionar a la página de login si el usuario no ha iniciado sesión
    header("Location: ../login.php");
    exit;
}


// Recibir los datos enviados desde el fetch
$data = json_decode(file_get_contents('php://input'), true);
$id_usuario = $data['id_usuario'];
$codigo_del_producto = $data['codigo_del_producto'];  // Recibimos el código del producto

// Actualizar solo los pedidos que están en estado 'pedido tomado' y coincidan con el código del producto
$update_sql = "UPDATE pedido_realizado 
               SET estado_pedido = 'Retirado' 
               WHERE id_usuario = $id_usuario 
               AND codigo_del_producto = '$codigo_del_producto' 
               AND estado_pedido = 'pedido tomado'";

if (mysqli_query($conexion, $update_sql)) {
    // Insertar los pedidos que han sido actualizados a 'Retirado' y que no estaban ya en ese estado en la tabla pedido_retirado
    $insert_sql = "INSERT INTO pedido_retirado (id_pedido, id_usuario, nombre_producto, cantidad, subtotal, codigo_del_producto, metodo_pago, total, total_pagado, estado_compra, estado_pedido, fecha_creacion, id_sucursal)
                   SELECT id_pedido, id_usuario, nombre_producto, cantidad, subtotal, codigo_del_producto, 'tarjeta', subtotal, subtotal, estado_compra, estado_pedido, fecha_creacion, $id_sucursal_usuario
                   FROM pedido_realizado
                   WHERE id_usuario = $id_usuario 
                   AND codigo_del_producto = '$codigo_del_producto'
                   AND estado_pedido = 'Retirado' 
                   AND id_pedido NOT IN (
                       SELECT id_pedido FROM pedido_retirado
                   )";

    mysqli_query($conexion, $insert_sql);

    // Obtener los id_compra desde la tabla pedido_realizado para los pedidos retirados
    $select_id_compra_sql = "SELECT id_compra 
                             FROM pedido_realizado 
                             WHERE id_usuario = $id_usuario 
                             AND codigo_del_producto = '$codigo_del_producto' 
                             AND estado_pedido = 'Retirado'";

    $result = mysqli_query($conexion, $select_id_compra_sql);

    // Si obtenemos resultados, procedemos a actualizar los registros en la tabla compras
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id_compra = $row['id_compra'];

            // Actualizar el estado_pedido en la tabla compras
            $update_compras_sql = "UPDATE compras 
                                   SET estado_pedido = 'Retirado' 
                                   WHERE id_compra = $id_compra";

            mysqli_query($conexion, $update_compras_sql);
        }
    }

    // Responder con éxito
    echo json_encode(['success' => true]);
} else {
    // En caso de error, responder con fallo
    echo json_encode(['success' => false, 'error' => mysqli_error($conexion)]);
}
