<?php

require 'vendor/autoload.php';

MercadoPago\SDK::setAccessToken('APP_USR-809949882908972-081403-8b9804566703cf9dfd2e416b98f3483f-1406623712');

$preference = new MercadoPago\Preference();

$item = new MercadoPago\Item();
$item->id = '0001';
$item->title = 'Producto de prueba';
$item->quantity = 1;
$item->unit_price = 200.00;
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
        const mp = new MercadoPago('APP_USR-9cae6f86-61f9-48e6-8c03-daa7884b0d80', {
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
