<?php


require './config/config.php';
require './config/conexion.php';

// Variable para manejar errores
$message = '';

// Verificamos si el usuario está autenticado
if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];

    // Consulta para obtener el id_usuario
    $sql_user = "SELECT id_usuario FROM usuarios WHERE usuario = '$user_name'";
    $resultado_user = mysqli_query($conexion, $sql_user);

    if ($resultado_user && mysqli_num_rows($resultado_user) > 0) {
        $row_user = mysqli_fetch_assoc($resultado_user);
        $id_usuario = $row_user['id_usuario'];

        // Consulta para obtener las compras del usuario
        $sql = "SELECT id_compra, nombre_producto, cantidad, subtotal, fecha_compra, hora_compra, metodo_pago, lugar_retiro, codigo_del_producto, estado_compra, estado_pedido FROM compras WHERE id_usuario = $id_usuario";
        $resultado = mysqli_query($conexion, $sql);
    } else {
        $message = "Error: No se encontró el usuario.";
    }
} else {
    // Alerta si no ha iniciado sesión
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Debe iniciar sesión',
                text: 'Para ver sus compras, por favor inicie sesión.',
                showCancelButton: true,
                confirmButtonText: 'Iniciar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php'; // Redirigir al login
                }
            });
        });
    </script>";
    exit(); // Detenemos la ejecución del script si no ha iniciado sesión
}


if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];

    // Consulta para obtener el id_usuario
    $sql_user = "SELECT id_usuario FROM usuarios WHERE usuario = '$user_name'";
    $resultado_user = mysqli_query($conexion, $sql_user);

    if ($resultado_user && mysqli_num_rows($resultado_user) > 0) {
        $row_user = mysqli_fetch_assoc($resultado_user);
        $id_usuario = $row_user['id_usuario'];

        // Consulta para obtener las compras del usuario
        $sql1 = "SELECT id_retirado, id_pedido, id_usuario, nombre_producto, cantidad, subtotal, codigo_del_producto, metodo_pago,total, total_pagado, estado_compra, estado_pedido, fecha_creacion, fecha_retirado FROM pedido_retirado WHERE id_usuario = $id_usuario";
        $resultado1 = mysqli_query($conexion, $sql1);
    } else {
        $message = "Error: No se encontró el usuario.";
    }
} else {
    // Alerta si no ha iniciado sesión
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'warning',
                title: 'Debe iniciar sesión',
                text: 'Para ver sus compras, por favor inicie sesión.',
                showCancelButton: true,
                confirmButtonText: 'Iniciar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php'; // Redirigir al login
                }
            });
        });
    </script>";
    exit(); // Detenemos la ejecución del script si no ha iniciado sesión
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_compra'])) {
    $id_compra = $_POST['id_compra'];

    // Consulta para obtener el codigo_del_producto y el metodo_pago de la compra que se va a cancelar
    $sql_get_purchase = "SELECT codigo_del_producto, metodo_pago FROM compras WHERE id_compra = ?";
    $stmt_get_purchase = $conexion->prepare($sql_get_purchase);
    if ($stmt_get_purchase === false) {
        die("Error al preparar la consulta: " . $conexion->error);
    }
    $stmt_get_purchase->bind_param("i", $id_compra);
    $stmt_get_purchase->execute();
    $result_purchase = $stmt_get_purchase->get_result();

    if ($result_purchase->num_rows > 0) {
        $row_purchase = $result_purchase->fetch_assoc();
        $codigo_del_producto = $row_purchase['codigo_del_producto'];
        $metodo_pago = $row_purchase['metodo_pago'];

        // Si el método de pago es "Pago con tarjeta", cambiar el estado en ambas tablas
        if ($metodo_pago === 'Pago con tarjeta') {

            // 1. Obtener el ID de la compra basado en el codigo_del_producto y id_usuario
            $sql_obtener_id_compra = "SELECT id_compra FROM compras WHERE codigo_del_producto = ? AND id_usuario = ?";
            $stmt_obtener_id_compra = $conexion->prepare($sql_obtener_id_compra);
            if ($stmt_obtener_id_compra === false) {
                die("Error al preparar la consulta para obtener el id_compra: " . $conexion->error);
            }
            $stmt_obtener_id_compra->bind_param("si", $codigo_del_producto, $id_usuario);
            $stmt_obtener_id_compra->execute();
            $stmt_obtener_id_compra->bind_result($id_compra);
            $stmt_obtener_id_compra->fetch();
            $stmt_obtener_id_compra->close();

            // Verifica que se haya encontrado un id_compra
            if (!$id_compra) {
                die("Error: No se encontró el id_compra para el producto especificado.");
            }

            // 2. Actualizar el estado en la tabla 'compras'
            $sql_update_estado_compras = "UPDATE compras SET estado_compra = 'compra en devolucion' WHERE codigo_del_producto = ? AND id_usuario = ?";
            $stmt_update_estado_compras = $conexion->prepare($sql_update_estado_compras);
            if ($stmt_update_estado_compras === false) {
                die("Error al preparar la consulta para actualizar estado en 'compras': " . $conexion->error);
            }
            $stmt_update_estado_compras->bind_param("si", $codigo_del_producto, $id_usuario);
            $stmt_update_estado_compras->execute();

            // 3. Verificar si el id_compra existe en la tabla 'pedido_realizado'
            $sql_verificar_pedido = "SELECT COUNT(*) FROM pedido_realizado WHERE id_compra = ?";
            $stmt_verificar_pedido = $conexion->prepare($sql_verificar_pedido);
            if ($stmt_verificar_pedido === false) {
                die("Error al preparar la consulta para verificar 'pedido_realizado': " . $conexion->error);
            }
            $stmt_verificar_pedido->bind_param("i", $id_compra);
            $stmt_verificar_pedido->execute();
            $stmt_verificar_pedido->bind_result($existe_pedido);
            $stmt_verificar_pedido->fetch();
            $stmt_verificar_pedido->close();

            // 4. Si existe el pedido, hacer el update en la tabla 'pedido_realizado'
            if ($existe_pedido > 0) {
                $sql_update_estado_pedido = "UPDATE pedido_realizado SET estado_compra = 'compra en devolucion' WHERE id_compra = ?";
                $stmt_update_estado_pedido = $conexion->prepare($sql_update_estado_pedido);
                if ($stmt_update_estado_pedido === false) {
                    die("Error al preparar la consulta para actualizar estado en 'pedido_realizado': " . $conexion->error);
                }
                $stmt_update_estado_pedido->bind_param("i", $id_compra);
                $stmt_update_estado_pedido->execute();

                // Verifica si ambas actualizaciones fueron exitosas
                if ($stmt_update_estado_compras->affected_rows > 0 && $stmt_update_estado_pedido->affected_rows > 0) {
                    // Redirigir a la página devolucion_pago.php si las actualizaciones fueron exitosas
                    header("Location: devolucion_pago.php?id_compra=" . $id_compra);
                    exit();
                } else {
                    die("Error: No se pudo actualizar el estado en una o ambas tablas.");
                }
            } else {
                // Si el pedido no existe, solo redirigir después de actualizar 'compras'
                if ($stmt_update_estado_compras->affected_rows > 0) {
                    header("Location: devolucion_pago.php?id_compra=" . $id_compra);
                    exit();
                } else {
                    die("Error: No se pudo actualizar el estado en la tabla 'compras'.");
                }
            }
        } else {

            // Si el método de pago no es "Pago con tarjeta", proceder con la cancelación
            $sql_get_compras = "SELECT id_compra, nombre_producto, cantidad FROM compras WHERE codigo_del_producto = ? AND id_usuario = ? AND estado_compra != 'cancelado'";
            $stmt_get_compras = $conexion->prepare($sql_get_compras);
            if ($stmt_get_compras === false) {
                die("Error al preparar la consulta: " . $conexion->error);
            }
            $stmt_get_compras->bind_param("si", $codigo_del_producto, $id_usuario);
            $stmt_get_compras->execute();
            $result_compras = $stmt_get_compras->get_result();

            // Iteramos sobre las compras y actualizamos el stock por cada producto
            while ($row_compras = $result_compras->fetch_assoc()) {
                $id_compra_to_cancel = $row_compras['id_compra'];
                $nombre_producto = $row_compras['nombre_producto'];
                $cantidad = $row_compras['cantidad'];

                // Cancelar la compra específica
                $sql_cancel_compras = "UPDATE compras SET estado_compra = 'cancelado' WHERE id_compra = ?";
                $stmt_cancel_compras = $conexion->prepare($sql_cancel_compras);
                if ($stmt_cancel_compras === false) {
                    die("Error al preparar la consulta para 'compras': " . $conexion->error);
                }
                $stmt_cancel_compras->bind_param("i", $id_compra_to_cancel);
                $stmt_cancel_compras->execute();

                // Obtener el lugar_retiro de la compra específica
                $sql_get_lugar_retiro = "SELECT lugar_retiro FROM compras WHERE id_compra = ?";
                $stmt_get_lugar_retiro = $conexion->prepare($sql_get_lugar_retiro);
                if ($stmt_get_lugar_retiro === false) {
                    die("Error al preparar la consulta para obtener lugar_retiro: " . $conexion->error);
                }
                $stmt_get_lugar_retiro->bind_param("i", $id_compra_to_cancel);
                $stmt_get_lugar_retiro->execute();
                $result_lugar_retiro = $stmt_get_lugar_retiro->get_result();

                if ($result_lugar_retiro->num_rows > 0) {
                    $row_retiro = $result_lugar_retiro->fetch_assoc();
                    $lugar_retiro = $row_retiro['lugar_retiro'];

                    // Obtener el id_sucursal correspondiente al lugar_retiro
                    $sql_get_sucursal = "SELECT id_sucursal FROM sucursales WHERE nombre_sucursal = ?";
                    $stmt_get_sucursal = $conexion->prepare($sql_get_sucursal);
                    if ($stmt_get_sucursal === false) {
                        die("Error al preparar la consulta para obtener id_sucursal: " . $conexion->error);
                    }
                    $stmt_get_sucursal->bind_param("s", $lugar_retiro);
                    $stmt_get_sucursal->execute();
                    $result_sucursal = $stmt_get_sucursal->get_result();

                    if ($result_sucursal->num_rows > 0) {
                        $row_sucursal = $result_sucursal->fetch_assoc();
                        $id_sucursal = $row_sucursal['id_sucursal'];

                        // Actualizar el stock del producto basado en el nombre del producto y la sucursal
                        $sql_update_product = "UPDATE productos SET stock = stock + ? WHERE nombre = ? AND id_sucursal = ?";
                        $stmt_update_product = $conexion->prepare($sql_update_product);
                        if ($stmt_update_product === false) {
                            die("Error al preparar la consulta para 'productos': " . $conexion->error);
                        }
                        $stmt_update_product->bind_param("isi", $cantidad, $nombre_producto, $id_sucursal);
                        $stmt_update_product->execute();
                    } else {
                        die("Error: No se encontró la sucursal para el lugar de retiro especificado.");
                    }

                    $stmt_get_sucursal->close();
                }

                $stmt_get_lugar_retiro->close();
                $stmt_cancel_compras->close();
            }


            // Cancelar también en la tabla 'pedido_realizado' todas las compras con ese codigo_del_producto
            $sql_cancel_pedidos = "UPDATE pedido_realizado SET estado_compra = 'cancelado' WHERE codigo_del_producto = ? AND id_usuario = ?";
            $stmt_cancel_pedidos = $conexion->prepare($sql_cancel_pedidos);
            if ($stmt_cancel_pedidos === false) {
                die("Error al preparar la consulta para 'pedido_realizado': " . $conexion->error);
            }
            $stmt_cancel_pedidos->bind_param("si", $codigo_del_producto, $id_usuario);
            $stmt_cancel_pedidos->execute();

            // Redirigir a la misma página después de cancelar
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    $stmt_get_purchase->close();
}









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
    <title>SuperOnline | Mis Compras</title>
    <!-- SweetAlert2 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/carousel/">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>




    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@docsearch/css@3">

    <link href="/docs/5.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="/docs/5.3/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="manifest" href="/docs/5.3/assets/img/favicons/manifest.json">
    <link rel="mask-icon" href="/docs/5.3/assets/img/favicons/safari-pinned-tab.svg" color="#712cf9">
    <link rel="icon" href="/docs/5.3/assets/img/favicons/favicon.ico">
    <meta name="theme-color" content="#712cf9">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">


    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }

        .bd-mode-toggle {
            z-index: 1500;
        }

        .bd-mode-toggle .dropdown-menu .active .bi {
            display: block !important;
        }
    </style>


    <!-- Custom styles for this template -->
    <link href="carousel.css" rel="stylesheet">
    <link rel="stylesheet" href="slider.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Acerca de
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="como_retirar.php">Como retirar mi producto</a></li>
                            </ul>
                        </li>
                    </ul>

                    <?php
                    if (isset($_SESSION['user_name'])) {
                        echo '<span style="color: green; font-weight: bold;">' . htmlspecialchars($_SESSION['user_name']) . '</span>
        <a href="login.php">
            <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 15px;" width="24" height="24" fill="currentColor" class="text-black bi bi-person-fill" viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
            </svg>
        </a>';
                    } else {
                        echo '<a href="login.php" style="color: green; font-weight: bold;">Iniciar sesión</a>';
                    }
                    ?>




                    <a href="checkout.php"> <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 20px;" class="text-black" width="24" height="24" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
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
    <br>



    <div class="container mt-5">
        <h2 class="text-center">Historial de Compras</h2>
        <br>

        <!-- Filtro por Estado -->
<div class="text-center mb-4">
    <div class="d-flex flex-wrap flex-md-nowrap justify-content-center gap-2">
        <button class="btn btn-primary filter-btn" data-filter="all">Mostrar Todas</button>
        <button class="btn btn-warning filter-btn" data-filter="activa">Pendientes</button>
        <button class="btn btn-danger filter-btn" data-filter="cancelado">Canceladas</button>
        <button class="btn btn-success filter-btn" data-filter="retirado">Retiradas</button>
    </div>
</div>


        <!-- Mostrar todas las compras en tarjetas -->
        <div class="row" id="comprasContainer">
            <?php if (isset($resultado) && mysqli_num_rows($resultado) > 0): ?>
                <?php foreach ($resultado as $row): ?>
                    <?php
                    // Determinamos la clase CSS según el estado de la compra
                    $estadoClass = strtolower($row['estado_compra']);
                    if ($row['estado_pedido'] === 'Retirado') {
                        $estadoClass = 'retirado';
                    }
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-3 compra-card <?php echo $estadoClass; ?>">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Compra ID: <?php echo htmlspecialchars($row['id_compra']); ?></h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Producto:</strong> <?php echo htmlspecialchars($row['nombre_producto']); ?></p>
                                <p><strong>Cantidad:</strong> <?php echo htmlspecialchars($row['cantidad']); ?></p>
                                <p><strong>Subtotal:</strong> $<?php echo htmlspecialchars($row['subtotal']); ?></p>
                                <p><strong>Fecha:</strong> <?php echo htmlspecialchars($row['fecha_compra']); ?></p>
                                <p><strong>Hora:</strong> <?php echo htmlspecialchars($row['hora_compra']); ?></p>
                                <p><strong>Método de Pago:</strong> <?php echo htmlspecialchars($row['metodo_pago']); ?></p>
                                <p><strong>Lugar de Retiro:</strong> <?php echo htmlspecialchars($row['lugar_retiro']); ?></p>
                                <p><strong>Código del Producto:</strong> <?php echo htmlspecialchars($row['codigo_del_producto']); ?></p>
                                <p><strong>Estado de Compra:</strong> <?php echo htmlspecialchars($row['estado_compra']); ?></p>
                                <p><strong>Estado de Pedido:</strong> <?php echo htmlspecialchars($row['estado_pedido']); ?></p>
                            </div>
                            <div class="card-footer">
                                <?php if ($row['estado_pedido'] === 'Retirado'): ?>
                                    <span class="text-success">Compra retirada</span>
                                <?php elseif ($row['estado_compra'] === 'compra en devolucion'): ?>
                                    <span class="text-warning">Compra en proceso de devolución</span>
                                <?php elseif ($row['estado_compra'] === 'compra ya reembolsada'): ?>
                                    <span class="text-info">Compra reembolsada</span>
                                <?php elseif ($row['estado_compra'] !== 'cancelado'): ?>
                                    <div class="d-flex gap-2">
                                        <form method="POST" action="codigo_barra.php">
                                            <input type="hidden" name="codigo_del_producto" value="<?php echo $row['codigo_del_producto']; ?>">
                                            <button type="submit" class="btn btn-success btn-sm">Ver más</button>
                                        </form>
                                        <form method="POST" onsubmit="return confirmCancel(this);">
                                            <input type="hidden" name="id_compra" value="<?php echo $row['id_compra']; ?>">
                                            <input type="hidden" name="metodo_pago" value="<?php echo htmlspecialchars($row['metodo_pago']); ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-danger">Compra cancelada</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No se encontraron compras para este usuario.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .card-body p {
            margin-bottom: 8px;
        }

        .card-footer {
            background-color: #f1f1f1;
            text-align: center;
            font-weight: bold;
        }
    </style>

    <script>
        // Filtro por estado de compra
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.getAttribute('data-filter');
                const cards = document.querySelectorAll('.compra-card');

                cards.forEach(card => {
                    if (filter === 'all' || card.classList.contains(filter)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>



    <br>



    </footer>

    
    <div class="container">
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
            <div class="col-md-4 d-flex align-items-center">
                <a href="tecnocode.html" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
                    <img src="tecnocode.svg" width="50" height="50">
                </a>
                <span class="mb-3 mb-md-0 text-body-secondary"> Desarrollada por TecnoCode.</span>
            </div>


            <ul class="nav col-md-4 justify-content-end list-unstyled d-flex">
                <li class="ms-3"><a class="text-body-secondary" href="#"><svg class="bi" width="24" height="24">
                            <use xlink:href="#twitter"></use>
                        </svg></a></li>
                <li class="ms-3"><a class="text-body-secondary" href="#"><svg class="bi" width="24" height="24">
                            <use xlink:href="#instagram"></use>
                        </svg></a></li>
                <li class="ms-3"><a class="text-body-secondary" href="#"><svg class="bi" width="24" height="24">
                            <use xlink:href="#facebook"></use>
                        </svg></a></li>
            </ul>
        </footer>


        <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <!-- Filtros de búsqueda y fecha -->
        <script>
            // Filtro de búsqueda
            document.getElementById('searchInput').addEventListener('keyup', function() {
                let input = this.value.toLowerCase();
                let rows = document.querySelectorAll('#comprasTable tbody tr');

                rows.forEach(row => {
                    let cells = row.querySelectorAll('td');
                    let match = Array.from(cells).some(cell => cell.innerText.toLowerCase().includes(input));
                    row.style.display = match ? '' : 'none';
                });
            });

            // Filtro por fecha
            document.getElementById('filterDate').addEventListener('change', function() {
                let selectedDate = this.value;
                let rows = document.querySelectorAll('#comprasTable tbody tr');

                rows.forEach(row => {
                    let fechaCell = row.querySelector('td:nth-child(5)').innerText; // Columna de fecha
                    if (fechaCell.includes(selectedDate)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Filtro de búsqueda
            document.getElementById('buscar').addEventListener('keyup', function() {
                let input = this.value.toLowerCase();
                let rows = document.querySelectorAll('#comprasTable tbody tr');

                rows.forEach(row => {
                    let cells = row.querySelectorAll('td');
                    let match = Array.from(cells).some(cell => cell.innerText.toLowerCase().includes(input));
                    row.style.display = match ? '' : 'none';
                });
            });

            // Filtro por fecha
            document.getElementById('filtrarFecha').addEventListener('change', function() {
                let selectedDate = this.value;
                let rows = document.querySelectorAll('#comprasTable tbody tr');

                rows.forEach(row => {
                    let fechaCell = row.querySelector('td:nth-child(5)').innerText; // Columna de fecha
                    if (fechaCell.includes(selectedDate)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        </script>
        <script>
            function confirmCancel(form) {
                const metodoPago = form.querySelector('input[name="metodo_pago"]').value;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Deseas cancelar esta compra?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'No, mantener'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (metodoPago === 'Pago con tarjeta') {
                            // Si es pago con tarjeta, redirigir directamente
                            form.submit();
                        } else {
                            // Si es por sucursal, mostrar mensaje de cancelación exitosa
                            Swal.fire({
                                icon: 'success',
                                title: 'Compra cancelada',
                                text: 'Tu compra ha sido cancelada exitosamente.',
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                form.submit();
                            });
                        }
                    }
                });
                return false; // Evitar que el formulario se envíe inmediatamente
            }
        </script>





</body>

</html>