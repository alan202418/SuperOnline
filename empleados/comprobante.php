<?php
require '../config/conexion.php';
require '../vendor/autoload.php'; // Cargar autoload de Composer
require '../config/config.php';

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_name'])) {
    // Almacenar el nombre del usuario en una variable
    $nombre_empleado = $_SESSION['user_name'];

} else {
    // Redireccionar a la página de login si el usuario no ha iniciado sesión
    header("Location: ../login.php");
    exit;
}

use Picqer\Barcode\BarcodeGeneratorPNG;

$id_usuario = $_GET['id_usuario'];

// Obtener el método de pago y el monto pagado (si es efectivo)
$metodo_pago = $_GET['metodo_pago'] ?? 'tarjeta';  // Valor predeterminado 'tarjeta'
$monto_pagado = $_GET['monto_pagado'] ?? null;      // Valor predeterminado null

// Obtener el nombre del usuario de la tabla 'usuarios'
$sql_usuario = "SELECT nombre FROM usuarios WHERE id_usuario = $id_usuario";
$resultado_usuario = mysqli_query($conexion, $sql_usuario);
$nombre_usuario = '';

if ($resultado_usuario && mysqli_num_rows($resultado_usuario) > 0) {
    $fila_usuario = mysqli_fetch_assoc($resultado_usuario);
    $nombre_usuario = $fila_usuario['nombre'];
}

// Configurar la zona horaria de Buenos Aires
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fecha_hora_actual = date('Y-m-d H:i:s');

// Obtener los detalles del pedido retirado
$sql = "SELECT * FROM pedido_retirado 
        WHERE id_usuario = $id_usuario 
        AND DATE(fecha_retirado) = DATE('$fecha_hora_actual')
        AND TIME(fecha_retirado) BETWEEN TIME(DATE_SUB('$fecha_hora_actual', INTERVAL 2 MINUTE)) AND TIME(DATE_ADD('$fecha_hora_actual', INTERVAL 2 MINUTE))";

$resultado = mysqli_query($conexion, $sql);

// Variables para almacenar los datos del pedido
$productos = [];
$cantidades = [];
$precios = [];
$subtotales = [];
$total = 0;
$codigo_de_barras = '';

while ($producto = mysqli_fetch_assoc($resultado)) {
    $productos[] = $producto['nombre_producto'];
    $cantidades[] = $producto['cantidad'];
    $codigo_de_barras = $producto['codigo_del_producto'];

    // Obtener el precio del producto
    $sql_precio = "SELECT precio FROM productos WHERE nombre = '{$producto['nombre_producto']}'";
    $resultado_precio = mysqli_query($conexion, $sql_precio);
    $precio = 0;

    if ($resultado_precio && mysqli_num_rows($resultado_precio) > 0) {
        $fila_precio = mysqli_fetch_assoc($resultado_precio);
        $precio = floatval($fila_precio['precio']);
    }

    $precios[] = $precio;
    $subtotales[] = floatval($producto['subtotal']);
    $total += floatval($producto['subtotal']);
}

// Ajustar el monto pagado si el método de pago es tarjeta
if ($metodo_pago === 'tarjeta') {
    $monto_pagado = $total;  // Si es tarjeta, el monto pagado debe ser igual al total
}

// Después de obtener el valor de $codigo_de_barras
if (!empty($codigo_de_barras)) {
    // Verificar si existe un registro con ese código de barras en la tabla 'pedido_retirado'
    $sql_check_codigo = "SELECT id_retirado FROM pedido_retirado WHERE codigo_del_producto = '$codigo_de_barras'";
    $resultado_check = mysqli_query($conexion, $sql_check_codigo);

    if ($resultado_check && mysqli_num_rows($resultado_check) > 0) {
        // Obtener los IDs de los pedidos que coinciden con el código de barras
        while ($fila = mysqli_fetch_assoc($resultado_check)) {
            $id_retirado = $fila['id_retirado'];

            // Actualizar los campos metodo_pago, total, y total_pagado
            $sql_update = "UPDATE pedido_retirado 
                           SET metodo_pago = '$metodo_pago', total = '$total', total_pagado = '$monto_pagado' 
                           WHERE id_retirado = $id_retirado";

            if (mysqli_query($conexion, $sql_update)) {
                
            } else {
                echo "Error al actualizar el pedido: " . mysqli_error($conexion) . "<br>";
            }
        }
    } else {
        echo "No se encontraron registros con el código de barras: $codigo_de_barras.";
    }
}

// Calcular el vuelto si se pagó en efectivo y el monto pagado es mayor o igual al total
$vuelto = null;
if ($metodo_pago === 'efectivo' && $monto_pagado !== null) {
    $vuelto = floatval($monto_pagado) - $total;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }

        .barcode-container img {
            width: 300px;
            height: auto;
        }

        .total-container {
            text-align: right;
            font-size: 1.6rem;
            margin-top: 10px;
        }

        .total-label {
            font-weight: bold;
        }
        .logo-empresa {
            display: block;
            margin: 0 auto;
            max-width: 300px; /* Ajusta el tamaño según tus necesidades */
        }
    </style>
</head>

<body>
    <br>
    <br>
    <div class="container">
    <img src="../logo_empresa.png" alt="Logo de la Empresa" class="logo-empresa">
        <h1 class="text-center">Comprobante de Compra</h1>
        <hr>
        <p><strong>Nombre del Empleado:</strong> <?php echo htmlspecialchars($nombre_empleado); ?></p>
        <p><strong>Nombre del Consumidor:</strong> <?php echo htmlspecialchars($nombre_usuario); ?></p>
        <p><strong>Fecha y Hora:</strong> <?php echo htmlspecialchars($fecha_hora_actual); ?></p>
        <hr>
        <table class="table table-striped table-custom">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $index => $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto); ?></td>
                        <td><?php echo htmlspecialchars($precios[$index]); ?></td>
                        <td><?php echo htmlspecialchars($cantidades[$index]); ?></td>
                        <td><?php echo htmlspecialchars($subtotales[$index]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Mostrar código de barras -->
        <span class="total-label">Codigo de barras: </span><?php echo htmlspecialchars($codigo_de_barras); ?><br>
        <div class="barcode-container">
            <?php
            $generator = new BarcodeGeneratorPNG();
            echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($codigo_de_barras, $generator::TYPE_CODE_128)) . '" alt="Código de Barras">';
            ?>
        </div>

        <!-- Mostrar total -->
        <div class="total-container">
            <span class="total-label">Total: </span><?php echo htmlspecialchars($total); ?>
        </div>
        <div class="total-container">

            <span class="total-label">Metodo de Pago: </span><?php echo htmlspecialchars($metodo_pago); ?>
        </div>


        <!-- Mostrar el monto pagado solo si el método de pago es efectivo -->
        <?php if ($metodo_pago === 'efectivo' && $monto_pagado): ?>


            <div class="total-container">

                <span class="total-label">Monto Pagado: </span><?php echo htmlspecialchars($monto_pagado); ?>
            </div>


            <!-- Mostrar el vuelto solo si el monto pagado es mayor o igual al total -->
            <?php if ($vuelto !== null && $vuelto >= 0): ?>
                <div class="total-container">
                    <span class="total-label">Vuelto: </span><?php echo htmlspecialchars($vuelto); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <hr>

        <!-- Botones para imprimir y volver -->
        <div class="btn-container no-print">
            <button class="btn btn-primary" onclick="window.print()">Imprimir Comprobante</button>
            <a href="pedidos_realizados.php" class="btn btn-secondary">Volver a Pedidos Realizados</a>
        </div>
    </div>
    <br>
    <br>
</body>

</html>