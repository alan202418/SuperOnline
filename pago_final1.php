<?php
require 'config/config.php';
require 'config/database.php';
require 'vendor/autoload.php'; // Cargar MercadoPago SDK

use MercadoPago\SDK;
use MercadoPago\Item;
use MercadoPago\Preference;

$db = new Database();
$con = $db->conectar();

SDK::setAccessToken('APP_USR-3731307413845799-090518-0eb507d5c3e98c3df503cadd8d1fb797-1183108534'); // Reemplaza con tu Access Token

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;

$lista_carrito = array();

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        $sql = $con->prepare("SELECT id_producto, nombre, precio, descuento, $cantidad AS cantidad FROM productos WHERE id_producto=?");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
    }
} else {
    header("location: index.php");
    exit;
}

// Crear la preferencia de Mercado Pago
$preference = new Preference();
$items = [];

if ($lista_carrito != null) {
    foreach ($lista_carrito as $producto) {
        $item = new Item();
        $item->id = $producto['id_producto'];
        $item->title = $producto['nombre'];
        $item->quantity = $producto['cantidad'];
        $precio_desc = $producto['precio'] - (($producto['precio'] * $producto['descuento']) / 100);
        $item->unit_price = $precio_desc;
        $item->currency_id = "ARS";

        $items[] = $item;
    }

    $preference->items = $items;

    // Configurar las URLs de retorno
    $preference->back_urls = array(
        "success" => "http://localhost/SuperOnline/procesar_compra2.php",  // Reemplaza con tu dominio real
        "failure" => "http://tu-dominio.com/error_pago.php",
        "pending" => "http://tu-dominio.com/pago_pendiente.php"
    );
    $preference->auto_return = "approved";  // Permite el retorno automático en caso de éxito

    $preference->save(); // Guardar la preferencia
}

// Obtener $metodo_pago y $lugar_retiro de la sesión o POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $metodo_pago = isset($_POST['titulo']) ? $_POST['titulo'] : '';
    $lugar_retiro = isset($_POST['primer_titulo']) ? $_POST['primer_titulo'] : '';

    // Guardar en sesión para uso posterior
    $_SESSION['metodo_pago'] = $metodo_pago;
    $_SESSION['lugar_retiro'] = $lugar_retiro;
} else {
    // Si no vienen por POST, intentar obtenerlos de la sesión
    $metodo_pago = isset($_SESSION['metodo_pago']) ? $_SESSION['metodo_pago'] : '';
    $lugar_retiro = isset($_SESSION['lugar_retiro']) ? $_SESSION['lugar_retiro'] : '';
}

// Calcular el total
$total = 0;
foreach ($lista_carrito as $producto) {
    $cantidad = $producto['cantidad'];
    $precio = $producto['precio'];
    $descuento = $producto['descuento'];
    $precio_desc = $precio - (($precio * $descuento) / 100);
    $subtotal = $cantidad * $precio_desc;
    $total += $subtotal;
}

// Almacenar los detalles de la compra en la sesión
$_SESSION['datos_compra'] = [
    'user_name'    => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '',
    'productos'    => $lista_carrito,
    'total'        => $total,
    'lugar_retiro' => $lugar_retiro,
    'metodo_pago'  => $metodo_pago,
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/docs/5.3/assets/js/color-modes.js"></script>
    <link rel="icon" href="logo.svg">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>SuperOnline | Pago con Sucursal</title>
    <script src="https://sdk.mercadopago.com/js/v2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.paypal.com/sdk/js?client-id=AX3ZhiJzltz0GBTJsKcEC57x81hEQVTCNPONhaB-_BNoJoLmuS2Ha5b-84S0cWrwmHyldwQF7vJ1IcMg&currency=USD"></script>

</head>

<body>
    <header data-bs-theme="dark">
        <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-white">
            <div class="container-fluid">
                <img class="imagenes" src="logo_empresa.png" alt="Changomas Logo">
                <style>
                    /* Estilos para pantallas grandes (computadoras) */
                    @media (min-width: 768px) {
                        .imagenes {
                            max-width: 10%;
                        }
                    }

                    /* Estilos para pantallas pequeñas (dispositivos móviles) */
                    @media (max-width: 767px) {
                        .imagenes {
                            max-width: 28%;
                            /* Ajusta según tus necesidades */
                        }
                    }
                </style>
                <style>
                    /* Estilos para los enlaces al pasar el cursor */
                    .nav-link:hover,
                    .dropdown-item:hover {
                        color: red;
                    }
                </style>
                <style>
                    /* Estilos para los enlaces de navegación */
                    .nav-link {
                        color: black;
                        /* Enlace negro por defecto */
                    }

                    /* Estilos para el botón "Categorías" al activarse */
                    .dropdown-toggle.active {
                        color: red;
                        /* Texto rojo al activarse */
                    }



                    /* Estilos para el menú desplegable */
                    .dropdown-menu {
                        background-color: white;
                        /* Fondo blanco para el menú desplegable */
                    }

                    /* Estilos para los elementos de lista en el menú desplegable */
                    .dropdown-item {
                        color: black;
                        /* Texto negro para los elementos de lista */
                    }

                    /* Estilos para los elementos de lista al pasar el cursor */
                    .dropdown-item:hover {
                        color: white;
                        /* Texto blanco al pasar el cursor */
                        background-color: black;
                        /* Fondo negro al pasar el cursor */
                    }

                    a:link,
                    a:visited,
                    a:active {
                        text-decoration: none;
                    }
                </style>






                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation" style="background-color: black;">
                    <span class="navbar-toggler-icon bi bi-list" style="color: white;"></span>
                </button>


                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php"><b>Inicio</b></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="sobre_nosotros.php">Sobre Nosotros</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categorías
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="vestimenta.php">Vestimenta</a></li>
                                <li><a class="dropdown-item" href="lacteos.php">Lacteos</a></li>
                                <li><a class="dropdown-item" href="carniceria.php">Carniceria</a></li>
                                <li><a class="dropdown-item" href="electrodomesticos.php">Electrodomésticos</a></li>
                                <li><a class="dropdown-item" href="almacen.php">Almacen</a></li>


                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="sucursales.php">Sucursales</a>
                        </li>
                    </ul>

                    <?php
                    if (isset($_SESSION['user_name'])) {
                        echo '<span style="color: green; font-weight: bold;">' . htmlspecialchars($_SESSION['user_name']) . '</span>';
                        echo '<input type="hidden" name="user_name" value="' . htmlspecialchars($_SESSION['user_name']) . '">';
                    } else {
                        echo '<a href="login.php" style="color: green; font-weight: bold;">Iniciar sesión</a>';
                    }
                    ?>


                    <a href="./checkout.php"> <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 20px;" class="text-black" width="24" height="24" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                            <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l.5 2H5V5zM6 5v2h2V5zm3 0v2h2V5zm3 0v2h1.36l.5-2zm1.11 3H12v2h.61zM11 8H9v2h2zM8 8H6v2h2zM5 8H3.89l.5 2H5zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0" />

                        </svg>


                    </a>
                    <span id="num_cart" class="badge bg-secondary"> <?php echo $num_cart; ?></span>



                </div>

            </div>

        </nav>
    </header>
    <br>
    <br>


    <main><br><br><br>
        <div class="container">
            <br>
            <div class="row">
                <div class="col-12">
                    <h3>Detalle de la compra</h3>
                    <br>
                </div>
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Subtotal</th>
                                    <th>Lugar de retiro</th>
                                    <th>Método de Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($lista_carrito == null) {
                                    echo '<tr><td colspan="4" class="text-center"><b>Lista Vacía</b></td></tr>';
                                } else {
                                    foreach ($lista_carrito as $producto) {
                                        $nombre = $producto['nombre'];
                                        $cantidad = $producto['cantidad'];
                                        $precio = $producto['precio'];
                                        $descuento = $producto['descuento'];
                                        $precio_desc = $precio - (($precio * $descuento) / 100);
                                        $subtotal = $cantidad * $precio_desc;
                                ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($nombre); ?></td>
                                            <td><?php echo number_format($subtotal, 2); ?></td>
                                            <td><?php echo htmlspecialchars($lugar_retiro); ?></td>
                                            <td><?php echo htmlspecialchars($metodo_pago); ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end">
                                        <p class="h3">Pagas $<?php echo number_format($total, 2); ?></p>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <br>
                        <b>Métodos de pago</b>
                        <br>
                        <br>
                        <!-- Botón de Mercado Pago -->
                        <div class="checkout-btn"></div>
                        <script>
                            const mp = new MercadoPago('APP_USR-c6418df4-b6b5-4586-b04b-f3ef36d99c70', {
                                locale: 'es-AR'
                            });
                            mp.checkout({
                                preference: {
                                    id: '<?php echo $preference->id; ?>'
                                },
                                render: {
                                    container: '.checkout-btn',
                                    label: 'Pagar con Mercado Pago'
                                }
                            });
                        </script>
                        <br>
                        <!-- Botón de PayPal -->
                        <div id="paypal-button-container"></div>
                        <script>
                            paypal.Buttons({
                                style: {
                                    color: 'blue',
                                    shape: 'pill',
                                    label: 'pay'
                                },
                                createOrder: function(data, actions) {
                                    return actions.order.create({
                                        purchase_units: [{
                                            amount: {
                                                value: <?php echo $total; ?>
                                            }
                                        }]
                                    });
                                },
                                onApprove: function(data, actions) {
                                    return actions.order.capture().then(function(detalles) {
                                        // Redirige al usuario a procesar_compra2.php
                                        window.location.href = "procesar_compra2.php";
                                    });
                                },
                                onCancel: function(data) {
                                    alert("Pago cancelado");
                                }
                            }).render('#paypal-button-container');
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>