<?php
require '../vendor/autoload.php';
use Picqer\Barcode\BarcodeGeneratorPNG;

$codigo_producto = $_GET['codigo_producto'] ?? '';

// Generador de código de barras
$generator = new BarcodeGeneratorPNG();
$barcode = base64_encode($generator->getBarcode($codigo_producto, $generator::TYPE_CODE_128));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        /* Estilo para agrandar el código de barras */
        .barcode-img {
            width: 400px;
            height: auto;
            margin-top: 20px;
        }

        /* Ocultar botones al imprimir */
        @media print {
            .print-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container text-center mt-5">
        <h1>Código del Producto para Imprimir</h1>
        <div class="mt-4">
            <?php if ($codigo_producto): ?>
                <img src="data:image/png;base64,<?= $barcode ?>" alt="Código de barras" class="barcode-img">
            <?php else: ?>
                <p>Código de producto no disponible.</p>
            <?php endif; ?>
        </div>
        <div class="mt-4 print-buttons">
            <button onclick="window.print()" class="btn btn-primary">Imprimir</button>
            <a href="pedidos.php" class="btn btn-secondary">Volver</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>
</html>
