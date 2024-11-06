<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
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
    } else {
    }

    $stmtUsuario->close();
} else {
    // Redireccionar a la página de login si el usuario no ha iniciado sesión
    header("Location: ../login.php");
    exit;
}


if (isset($_POST['tomar_pedido'])) {
    $id_usuario = $_POST['id_usuario'];
    $codigo_producto = $_POST['codigo_del_producto'];

    // Seleccionar todos los pedidos pendientes del usuario y con el código del producto especificado
    $sql_select = "SELECT * FROM compras WHERE id_usuario = '$id_usuario' AND codigo_del_producto = '$codigo_producto' AND estado_pedido = 'pendiente' AND estado_compra = 'activa'";
    $result_select = mysqli_query($conexion, $sql_select);

    while ($compra = mysqli_fetch_assoc($result_select)) {
        // Insertar cada compra en la tabla pedido_realizado
        $sql_insert = "INSERT INTO pedido_realizado (id_compra , id_usuario, nombre_producto, cantidad, subtotal, codigo_del_producto, metodo_pago, estado_compra, estado_pedido, id_sucursal)
               VALUES ('" . $compra['id_compra'] . "','" . $compra['id_usuario'] . "', '" . $compra['nombre_producto'] . "', '" . $compra['cantidad'] . "', '" . $compra['subtotal'] . "', '" . $compra['codigo_del_producto'] . "', '" . $compra['metodo_pago'] . "', '" . $compra['estado_compra'] . "', 'pedido tomado', $id_sucursal_usuario )";
        mysqli_query($conexion, $sql_insert);


        // Actualizar el estado del pedido en la tabla compras a 'aprobado'
        $sql_update = "UPDATE compras SET estado_pedido = 'aprobado' WHERE id_compra = '" . $compra['id_compra'] . "'";
        mysqli_query($conexion, $sql_update);
    }

    // Obtener el email del usuario para enviar notificación
    $sql_email = "SELECT email FROM usuarios WHERE id_usuario = '$id_usuario'";
    $result_email = mysqli_query($conexion, $sql_email);
    $usuario = mysqli_fetch_assoc($result_email);

    if ($usuario) {
        $email = $usuario['email'];  // Correo del usuario

        // Enviar el correo electrónico usando PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP de Gmail
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'alangod2020@gmail.com';  // Tu correo de Gmail
            $mail->Password   = 'yybg nrwp lrvh yuts';  // Contraseña de aplicación generada en Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Configuración del correo
            $mail->setFrom('alangod2020@gmail.com', 'SuperOnline');  // Tu correo y nombre
            $mail->addAddress($email);  // Enviar al correo del usuario

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Pedido tomado';
            $mail->Body    = 'Su pedido ha sido tomado y puede retirarlo.';

            // Enviar correo
            $mail->send();
            // Redirigir a comprobante_producto.php con el código del producto
            header("Location: comprobante_producto.php?codigo_producto=$codigo_producto");
            exit;
        } catch (Exception $e) {
            echo "Error al enviar el correo: {$mail->ErrorInfo}";
        }
    }
}


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
    <title>SuperOnline | Pedidos</title>
    <link rel="icon" href="../logo.svg">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



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
        <!-- Menú lateral -->
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

        <!-- Contenido principal -->
        <div class="main ms-5">
            <div class="container">
                <div class="text-center">
                    <br>
                    <h1>Pedidos</h1>
                </div>
                <br>

                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar..." onkeyup="searchTable()">
                </div>

                <!-- Tabla de Productos -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="productosTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre Cliente</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Codigo de barras</th>
                                <th>Metodo de Pago</th>
                                <th>Estado de la compra</th>
                                <th>Estado del Pedido</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Recorrer los resultados de la consulta
                            while ($producto = mysqli_fetch_assoc($resultadoCompras)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($producto['id_compra']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['id_usuario']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['nombre_producto']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['cantidad']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['subtotal']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['codigo_del_producto']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['metodo_pago']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['estado_compra']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['estado_pedido']) . "</td>";
                                echo "<td>";

                                if ($producto['estado_pedido'] == 'pendiente' && $producto['estado_compra'] == 'activa') {
                                    echo "<form method='POST' action=''>";
                                    echo "<input type='hidden' name='id_usuario' value='" . htmlspecialchars($producto['id_usuario']) . "'>";
                                    echo "<input type='hidden' name='codigo_del_producto' value='" . htmlspecialchars($producto['codigo_del_producto']) . "'>";
                                    echo "<button type='submit' name='tomar_pedido' class='btn btn-success'>Tomar Pedido</button>";
                                    echo "</form>";
                                } elseif ($producto['estado_pedido'] == 'pendiente' && $producto['estado_compra'] == 'cancelado') {
                                    echo "Pedido Cancelado";
                                } elseif ($producto['estado_pedido'] == 'pendiente' && $producto['estado_compra'] == 'compra en devolucion') {
                                    echo "<form method='GET' action='devolucion_detalles.php'>";
                                    echo "<input type='hidden' name='id_usuario' value='" . htmlspecialchars($producto['id_usuario']) . "'>";
                                    echo "<input type='hidden' name='codigo_del_producto' value='" . htmlspecialchars($producto['codigo_del_producto']) . "'>";
                                    echo "<button type='submit' class='btn btn-primary'>Realizar Devolucion</button>";
                                    echo "</form>";
                                } elseif ($producto['estado_compra'] == 'compra ya reembolsada') {
                                    // Nueva condición para estado "compra ya reembolsada"
                                    echo "Compra Reembolsada";
                                } else {
                                    echo "Pedido Tomado";
                                }

                                echo "</td>";

                                echo "</tr>";
                            }

                            ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
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
    <!-- JavaScript para buscar en la tabla -->
    <script>
        function searchTable() {
            // Obtener el valor del input de búsqueda
            const searchInput = document.getElementById("searchInput").value.toLowerCase();

            // Obtener las filas de la tabla
            const table = document.getElementById("productosTable");
            const rows = table.getElementsByTagName("tr");

            // Recorrer todas las filas (excepto la cabecera) y ocultar las que no coincidan
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName("td");
                let match = false;

                // Recorrer todas las celdas de la fila
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j]) {
                        const cellText = cells[j].textContent || cells[j].innerText;
                        if (cellText.toLowerCase().indexOf(searchInput) > -1) {
                            match = true;
                            break;
                        }
                    }
                }

                // Mostrar u ocultar la fila según si coincide con la búsqueda
                if (match) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    </script>

</body>

</html>