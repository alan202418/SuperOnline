<?php
require './config/conexion.php';
require './vendor/autoload.php'; // Librería Barcode

use Picqer\Barcode\BarcodeGeneratorPNG;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['codigo_del_producto'])) {
    $codigo_del_producto = $_POST['codigo_del_producto'];

    // Generar el código de barras a partir del código del producto
    $generator = new BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($codigo_del_producto, $generator::TYPE_CODE_128);
} else {
    // Si no se envía ningún código de producto, redirigir al historial de compras
    header("Location: historial_compras.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Barras</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos para ocultar los botones durante la impresión */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Para retirar su paquete debe presentar este código de barras</h2>
        <p class="text-center">Código de barras: <strong><?php echo $codigo_del_producto; ?></strong></p>
        <div class="text-center">
            <img src="data:image/png;base64,<?php echo base64_encode($barcode); ?>" alt="Código de Barras">
        </div>
        <div class="text-center mt-4 no-print">
            <a href="historial_compras.php" class="btn btn-primary">Volver al Historial</a>
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
