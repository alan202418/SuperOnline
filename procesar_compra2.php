<?php
require './config/config.php'; // Asegúrate de que session_start() se llame aquí
require './config/conexion.php';
require './vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

// Establecer la zona horaria a Argentina
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Verificar si los datos de la compra están en la sesión
if (isset($_SESSION['datos_compra'])) {
    $datos_compra = $_SESSION['datos_compra'];

    $user_name    = $datos_compra['user_name'];
    $lista_carrito = $datos_compra['productos'];
    $total        = $datos_compra['total'];
    $lugar_retiro = $datos_compra['lugar_retiro'];
    $metodo_pago  = $datos_compra['metodo_pago'];

    // Obtener la fecha y la hora del sistema
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");

    // Consultar el id del usuario a partir del nombre
    $sql_user = "SELECT id_usuario FROM usuarios WHERE usuario = ?";
    $stmt_user = $conexion->prepare($sql_user);
    $stmt_user->bind_param("s", $user_name);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();

    if ($row_user) {
        $id_usuario = $row_user['id_usuario'];
    } else {
        echo "Error: Usuario no encontrado.";
        exit;
    }

    $compra_exitosa = false;

    // Generar un único código aleatorio de 8 dígitos para toda la compra
    $codigo_unico_compra = rand(10000000, 99999999);

    // Obtener el id_sucursal basado en el nombre de la sucursal (lugar_retiro)
    $sql_sucursal = "SELECT id_sucursal FROM sucursales WHERE nombre_sucursal = ?";
    $stmt_sucursal = $conexion->prepare($sql_sucursal);
    $stmt_sucursal->bind_param("s", $lugar_retiro);
    $stmt_sucursal->execute();
    $result_sucursal = $stmt_sucursal->get_result();
    $row_sucursal = $result_sucursal->fetch_assoc();

    if ($row_sucursal) {
        $id_sucursal = $row_sucursal['id_sucursal'];
    } else {
        echo "Error: Sucursal no encontrada.";
        exit;
    }

    // Procesar los productos del carrito
    foreach ($lista_carrito as $producto) {
        $nombre      = $producto['nombre'];
        $precio      = $producto['precio'];
        $cantidad    = $producto['cantidad'];
        $descuento   = $producto['descuento'];
        $precio_desc = $precio - (($precio * $descuento) / 100);
        $subtotal    = $cantidad * $precio_desc;

        // Insertar la compra en la base de datos
        $sql = "INSERT INTO compras (id_usuario, nombre_producto, cantidad, subtotal, fecha_compra, hora_compra, metodo_pago, lugar_retiro, codigo_del_producto, estado_compra, estado_pedido)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activa', 'pendiente')";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("issdssssi", $id_usuario, $nombre, $cantidad, $subtotal, $fecha, $hora, $metodo_pago, $lugar_retiro, $codigo_unico_compra);

        if ($stmt->execute()) {
            $compra_exitosa = true;

            // Obtener el id_producto y stock del producto en la sucursal
            $sql_producto = "SELECT id_producto, stock FROM productos WHERE nombre = ? AND id_sucursal = ?";
            $stmt_producto = $conexion->prepare($sql_producto);
            $stmt_producto->bind_param("si", $nombre, $id_sucursal);
            $stmt_producto->execute();
            $result_producto = $stmt_producto->get_result();
            $row_producto = $result_producto->fetch_assoc();

            if ($row_producto) {
                $id_producto_sucursal = $row_producto['id_producto'];
                $stock_actual = $row_producto['stock'];
                $nuevo_stock = max(0, $stock_actual - $cantidad);

                // Actualizar el stock en la base de datos
                $sql_update_stock = "UPDATE productos SET stock = ? WHERE id_producto = ? AND id_sucursal = ?";
                $stmt_update_stock = $conexion->prepare($sql_update_stock);
                if ($stmt_update_stock) {
                    $stmt_update_stock->bind_param("iii", $nuevo_stock, $id_producto_sucursal, $id_sucursal);
                    $stmt_update_stock->execute();
                } else {
                    echo "Error: No se pudo preparar la consulta para actualizar el stock.";
                    exit;
                }
            } else {
                echo "Error: Producto '$nombre' no encontrado en la sucursal seleccionada.";
                exit;
            }
        } else {
            echo "Error en la inserción: " . $stmt->error;
            $compra_exitosa = false;
            break;
        }
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();

    // Si la compra fue exitosa, mostrar el código de barras
    if ($compra_exitosa) {
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($codigo_unico_compra, $generator::TYPE_CODE_128);
?>

        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Compra exitosa</title>
            <link rel="icon" href="logo.svg">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
            <style>
                /* Estilos para ocultar los botones durante la impresión */
                @media print {
                    .no-print {
                        display: none;
                    }
                }

                /* Centramos el contenido */
                .content-center {
                    text-align: center;
                    margin-top: 50px;
                }

                .content-center img {
                    margin: 20px 0;
                }
            </style>
        </head>

        <body>

            <div class="container content-center">
                <h2>Gracias por su Compra</h2>
                <p>Código de la compra: <strong><?php echo $codigo_unico_compra; ?></strong></p>
                <img src="data:image/png;base64,<?php echo base64_encode($barcode); ?>" alt="Código de Barras">

                <div class="mt-4 no-print">
                    <button class="btn btn-primary" onclick="window.location.href='index.php'">Volver al inicio</button>
                    <button class="btn btn-success" onclick="printBarcode()">Imprimir</button>
                </div>
            </div>

            <script>
                function printBarcode() {
                    window.print();
                }
            </script>

        </body>

        </html>

<?php
    } else {
        echo "Ha ocurrido un error al procesar su compra.";
    }

    // Limpiar las variables de sesión relacionadas con la compra
    unset($_SESSION['datos_compra']);
    unset($_SESSION['carrito']);
    unset($_SESSION['metodo_pago']);
    unset($_SESSION['lugar_retiro']);
} else {
    echo "No se encontraron datos de la compra.";
    exit;
}
?>