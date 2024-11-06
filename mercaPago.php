<?php

require 'vendor/autoload.php';

MercadoPago\SDK::setAccessToken('APP_USR-3731307413845799-090518-0eb507d5c3e98c3df503cadd8d1fb797-1183108534');

$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = '0001';
$item->title = 'Producto de prueba';
$item->quantity = 1;
$item->unit_price = 1.00;
$item->currency_id = "ARS"; // Cambiado a pesos argentinos

$preference->items = array($item);
$preference->save(); // Esto genera el ID de la preferencia


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago con Mercado Pago</title>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
</head>
<body>

    <h3>Mercado Pago</h3>

    <div class="checkout-btn"></div>

    <script>
        const mp = new MercadoPago('APP_USR-c6418df4-b6b5-4586-b04b-f3ef36d99c70', {
            locale: 'es-AR'
        });

        mp.checkout({
            preference: {
                id: '<?php echo $preference->id; ?>' // Esto inserta el ID de la preferencia generada
            },
            render: {
                container: '.checkout-btn', // Aquí se mostrará el botón de pago
                label: 'Pagar con Mercado Pago'
            }
        });
    </script>
    
</body>
</html>
