<?php
require '../config/config.php';
require '../config/conexion.php';

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

// Consulta para contar los pedidos pendientes
$sqlPendientes = "SELECT COUNT(*) as totalPendientes FROM `compras` WHERE estado_pedido = 'pendiente' AND estado_compra = 'activa' AND lugar_retiro = '$nombre_sucursal'";
$resultadoPendientes = mysqli_query($conexion, $sqlPendientes);
$pendientes = mysqli_fetch_assoc($resultadoPendientes)['totalPendientes'];

// Consulta para contar los pedidos pendientes
$sqlPendientes1 = "SELECT COUNT(*) as totalPendientes FROM `pedido_realizado` WHERE estado_pedido = 'pedido tomado' AND estado_compra = 'activa' AND id_sucursal = '$id_sucursal_usuario'";
$resultadoPendientes1 = mysqli_query($conexion, $sqlPendientes1);
$pendientes1 = mysqli_fetch_assoc($resultadoPendientes1)['totalPendientes'];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperOnline | Inicio</title>
    <link rel="icon" href="../logo.svg">


    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Estilos personalizados -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        ::after,
        ::before {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        a {
            text-decoration: none;
        }

        li {
            list-style: none;
        }

        h1 {
            font-weight: 600;
            font-size: 1.5rem;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .wrapper {
            display: flex;
        }

        .main {
            min-height: 100vh;
            width: 100%;
            overflow: hidden;
            transition: all 0.35s ease-in-out;
            background-color: #FFFFFF;
        }

        #sidebar {
            width: 70px;
            min-width: 70px;
            z-index: 1000;
            transition: all .25s ease-in-out;
            background-color: #F8F8F8;
            display: flex;
            flex-direction: column;
        }

        #sidebar.expand {
            width: 260px;
            min-width: 260px;
        }

        .toggle-btn {
            background-color: transparent;
            cursor: pointer;
            border: 0;
            padding: 1rem 1.5rem;
        }

        .toggle-btn i {
            font-size: 1.5rem;
            color: black;
        }

        .sidebar-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 70px;
        }

        .logo-img {
            max-width: 100%;
            height: 40px;
            /* Reducimos el tamaño del logo */
            transition: all .25s ease-in-out;
        }

        #sidebar.expand .logo-img {
            display: block;
            max-width: 150px;
            /* Tamaño del logo cuando el menú está expandido */
            margin-left: 1rem;
            height: auto;
        }

        #sidebar:not(.expand) .logo-img {
            display: none;
        }

        #sidebar:not(.expand) a.sidebar-link span {
            display: none;
        }

        #sidebar:not(.expand) a.sidebar-link {
            padding: .625rem 1rem;
            text-align: center;
        }

        #sidebar:not(.expand) a.sidebar-link i {
            margin-right: 0;
            font-size: 1.5rem;
        }

        .sidebar-nav {
            padding: 2rem 0;
            flex: 1 1 auto;
        }

        a.sidebar-link {
            padding: .625rem 1.625rem;
            color: black;
            display: block;
            font-size: 0.9rem;
            white-space: nowrap;
            border-left: 3px solid transparent;
            position: relative;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            margin-right: .75rem;
        }

        a.sidebar-link:hover {
            background-color: rgba(255, 255, 255, .075);
            border-left: 3px solid green;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: center;
            /* Centrar el contenido */
            align-items: center;
        }

        .sidebar-footer span {
            color: green;
            font-weight: bold;
            margin-right: 10px;
            /* Espaciado entre el nombre y el ícono */
        }

        #sidebar:not(.expand) .sidebar-footer span {
            display: none;
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
        }

        /* Ajustes del icono en modo colapsado */
        #sidebar:not(.expand) .sidebar-footer {
            justify-content: center;
        }

        /* Flechas para indicar cuando el menú de ventas está colapsado o expandido */
        .sidebar-link[data-bs-toggle="collapse"]::after {
            content: '';
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            border: solid black;
            border-width: 0 2px 2px 0;
            display: inline-block;
            padding: 4px;
            transition: transform 0.2s ease;
            transform: rotate(45deg);
        }

        .sidebar-link[data-bs-toggle="collapse"].collapsed::after {
            transform: rotate(-135deg);
        }

        /* Ocultar la flecha de las secciones colapsables cuando el sidebar está cerrado */
        #sidebar:not(.expand) .sidebar-link[data-bs-toggle="collapse"]::after {
            display: none;
        }

        /* Deshabilitar el submenú cuando el sidebar está colapsado */
        #sidebar:not(.expand) .sidebar-item .sidebar-dropdown {
            display: none !important;
        }

        /* Mantener el badge siempre visible */
        .sidebar-item .badge {
            position: absolute;
            top: 20px;
            /* Ajusta para la posición sobre el ícono */
            left: 100px;
            /* Ajusta para la posición horizontal */
            font-size: 0.75rem;
            padding: 0.35em 0.5em;
            border-radius: 50%;
            min-width: 1.5em;
            text-align: center;
            line-height: 1;
            transform: translate(-50%, -50%);
            z-index: 10;
            /* Asegura que el badge esté siempre encima del ícono */
            visibility: visible !important;
            /* Forzar visibilidad */
        }
        /* Mantener el badge siempre visible */
        .sidebar-item1 .badge {
            position: absolute;
            top: 20px;
            /* Ajusta para la posición sobre el ícono */
            left: 180px;
            /* Ajusta para la posición horizontal */
            font-size: 0.75rem;
            padding: 0.35em 0.5em;
            border-radius: 50%;
            min-width: 1.5em;
            text-align: center;
            line-height: 1;
            transform: translate(-50%, -50%);
            z-index: 10;
            
            /* Asegura que el badge esté siempre encima del ícono */
            visibility: visible !important;
            /* Forzar visibilidad */
        }

        
    </style>
</head>

<body>
    <div class="wrapper">
        <aside id="sidebar">
            <div class="d-flex">
                <button class="toggle-btn" type="button">
                    <i class="bi bi-list" id="toggle-icon"></i> <!-- Icono inicial de Bootstrap -->
                </button>
                <div class="sidebar-logo">
                    <a href="../index.php">
                        <img src="../logo_empresa.png" alt="Logo" class="logo-img">
                    </a>
                </div>
            </div>
            <ul class="sidebar-nav">
                <li class="sidebar-item">
                    <a href="productos.php" class="sidebar-link">
                        <i class="bi bi-box-seam"></i> <!-- Icono de Productos -->
                        <span>Productos</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="clientes.php" class="sidebar-link">
                        <i class="bi bi-people"></i> <!-- Icono de Clientes -->
                        <span>Clientes</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                        data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
                        <i class="bi bi-cash-stack"></i> <!-- Icono de Ventas -->
                        <span>Ventas</span>
                    </a>
                    <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <li class="sidebar-item">
                            <a href="pedidos.php" class="sidebar-link position-relative">
                                <?php if ($pendientes > 0): ?>
                                    <span class="badge bg-danger position-absolute rounded-circle">
                                        <?= $pendientes ?>
                                    </span>
                                <?php endif; ?>
                                <span>Pedidos</span>
                            </a>
                        </li>
                        <li class="sidebar-item1">
                            <a href="pedidos_realizados.php" class="sidebar-link position-relative">
                                <?php if ($pendientes1 > 0): ?>
                                    <span class="badge bg-danger position-absolute rounded-circle">
                                        <?= $pendientes1 ?>
                                    </span>
                                <?php endif; ?>
                                <span>Pedidos Realizados</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a href="pedidos_retirados.php" class="sidebar-link">Pedidos Retirados</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="devoluciones.php" class="sidebar-link">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Devoluciones</span>
                    </a>
                </li>
            </ul>

            <!-- Usuario / Iniciar sesión -->
            <div class="sidebar-footer">
                <?php
                if (isset($_SESSION['user_name'])) {
                    echo '<span>' . htmlspecialchars($_SESSION['user_name']) . '</span>
                    <a href="../login.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg>
                    </a>';
                } else {
                    echo '<a href="../login.php" style="color: green; font-weight: bold;">Iniciar sesión</a>';
                }
                ?>
            </div>
        </aside>

        <div class="main p-3">
            <div class="text-center">
                <br><br><br><br>

                <!-- Card de bienvenida -->
                <div class="card text-center mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Bienvenido a SuperOnline</h5>
                        <p class="card-text">Aquí encontrarás todas las herramientas para gestionar los productos, clientes, pedidos de forma eficiente.</p>
                    </div>
                </div>
                <br><br><br>

                <!-- Íconos de navegación rápida -->
                <div class="d-flex justify-content-center mt-4">
                    <!-- Productos -->
                    <a href="productos.php" class="btn btn-outline-dark mx-2 btn-square">
                        <i class="bi bi-box-seam-fill"></i><br>Productos
                    </a>
                    <!-- Clientes -->
                    <a href="clientes.php" class="btn btn-outline-primary mx-2 btn-square">
                        <i class="bi bi-people-fill"></i><br>Clientes
                    </a>
                    <!-- Pedidos -->
                    <a href="pedidos.php" class="btn btn-outline-success mx-2 btn-square">
                        <i class="bi bi-basket-fill"></i><br>Pedidos
                    </a>
                    <!-- Pedidos Realizados -->
                    <a href="pedidos_realizados.php" class="btn btn-outline-warning mx-2 btn-square">
                        <i class="bi bi-check-circle-fill"></i><br>Pedidos Realizados
                    </a>
                    <!-- Pedidos Retirados -->
                    <a href="pedidos_retirados.php" class="btn btn-outline-danger mx-2 btn-square">
                        <i class="bi bi-truck"></i><br>Pedidos Retirados
                    </a>
                    <!-- Devoluciones de compras -->
                    <a href="devoluciones.php" class="btn btn-outline-success mx-2 btn-square">
                        <i class="bi bi-arrow-counterclockwise"></i><br>Devoluciones
                    </a>
                </div>

                
            </div>
        </div>

        <style>
            .btn-square {
                width: 120px;
                /* Ancho del botón */
                height: 120px;
                /* Alto del botón */
                text-align: center;
                vertical-align: middle;
                line-height: 1.5rem;
                /* Ajustar la altura de la línea */
                padding: 10px;
                font-size: 14px;
                /* Tamaño del texto */
            }

            .btn-square i {
                font-size: 1.5rem;
                /* Tamaño del ícono */
                display: block;
                /* Asegurar que el ícono esté en su propia línea */
                margin-bottom: 5px;
                /* Espaciado entre el ícono y el texto */
            }
        </style>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>

    <!-- JavaScript para alternar el icono de abrir/cerrar -->
    <script>
        const hamBurger = document.querySelector(".toggle-btn");
        const toggleIcon = document.querySelector("#toggle-icon");

        hamBurger.addEventListener("click", function() {
            const sidebar = document.querySelector("#sidebar");
            sidebar.classList.toggle("expand");

            if (sidebar.classList.contains("expand")) {
                toggleIcon.classList.remove("bi-list");
                toggleIcon.classList.add("bi-x-lg");
            } else {
                toggleIcon.classList.remove("bi-x-lg");
                toggleIcon.classList.add("bi-list");
            }
        });
    </script>
</body>

</html>