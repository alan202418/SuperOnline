<?php
require '../config/config.php';
require '../config/conexion.php';

// Consulta de la tabla productos
$sql = ("SELECT `id_usuario`, `nombre`, `apellido`, `email`, `usuario`, `contraseña`, `rol` FROM `usuarios` WHERE rol = 'empleado' AND id_sucursal = 1");
$resultado = mysqli_query($conexion, $sql);

// Consulta para contar los pedidos pendientes
$sqlPendientes = "SELECT COUNT(*) as totalPendientes FROM `pedido_gerente_general` WHERE estado_pedido = 'pendiente'";
$resultadoPendientes = mysqli_query($conexion, $sqlPendientes);
$pendientes = mysqli_fetch_assoc($resultadoPendientes)['totalPendientes'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperOnline | Empleados</title>
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
            top: 15px;
            /* Ajusta para la posición sobre el ícono */
            left: 50px;
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
                    <a href="perfil.php" class="sidebar-link">
                        <i class="bi bi-person-circle"></i> <!-- Icono de Clientes -->
                        <span>Perfil</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                        data-bs-target="#auth" aria-expanded="false" aria-controls="auth">
                        <i class="bi bi-buildings-fill"></i>
                        <span>Sucursales</span>
                    </a>
                    <ul id="auth" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                        <!-- SuperOnline Central -->
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                data-bs-target="#superonline-central-submenu" aria-expanded="false" aria-controls="superonline-central-submenu">
                                SuperOnline Central
                            </a>
                            <ul id="superonline-central-submenu" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#auth">
                                <li class="sidebar-item">
                                    <a href="ventas_primer_sucursal.php" class="sidebar-link">Estadísticas de Ventas</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="productos_1.php" class="sidebar-link">Productos</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="gerente_1.php" class="sidebar-link">Gerente</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="empleados_1.php" class="sidebar-link">Empleados</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="proveedores.php" class="sidebar-link">Proveedores</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="empresas.php" class="sidebar-link">Empresas</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pedidos_1.php" class="sidebar-link">Pedidos</a>
                                </li>
                            </ul>
                        </li>
                        <!-- SuperOnline I -->
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                data-bs-target="#superonline-i-submenu" aria-expanded="false" aria-controls="superonline-i-submenu">
                                SuperOnline I
                            </a>
                            <ul id="superonline-i-submenu" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#auth">
                                <li class="sidebar-item">
                                    <a href="ventas_segunda_sucursal.php" class="sidebar-link">Estadísticas de Ventas</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="productos_2.php" class="sidebar-link">Productos</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="gerente_2.php" class="sidebar-link">Gerente</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="empleados_2.php" class="sidebar-link">Empleados</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="proveedores.php" class="sidebar-link">Proveedores</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="empresas.php" class="sidebar-link">Empresas</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pedidos_2.php" class="sidebar-link">Pedidos</a>
                                </li>
                            </ul>
                        </li>
                        <!-- SuperOnline II -->
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                data-bs-target="#superonline-ii-submenu" aria-expanded="false" aria-controls="superonline-ii-submenu">
                                SuperOnline II
                            </a>
                            <ul id="superonline-ii-submenu" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#auth">
                                <li class="sidebar-item">
                                    <a href="ventas_tercer_sucursal.php" class="sidebar-link">Estadísticas de Ventas</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="productos_3.php" class="sidebar-link">Productos</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="gerente_3.php" class="sidebar-link">Gerente</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="empleados_3.php" class="sidebar-link">Empleados</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="proveedores.php" class="sidebar-link">Proveedores</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="empresas.php" class="sidebar-link">Empresas</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pedidos_3.php" class="sidebar-link">Pedidos</a>
                                </li>
                            </ul>
                        </li>
                        <!-- SuperOnline III -->
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse"
                                data-bs-target="#superonline-iii-submenu" aria-expanded="false" aria-controls="superonline-iii-submenu">
                                SuperOnline III
                            </a>
                            <ul id="superonline-iii-submenu" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#auth">
                                <li class="sidebar-item">
                                    <a href="ventas_segunda_sucursal.php" class="sidebar-link">Estadísticas de Ventas</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="productos_4.php" class="sidebar-link">Productos</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="gerente_4.php" class="sidebar-link">Gerente</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="empleados_4.php" class="sidebar-link">Empleados</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="proveedores.php" class="sidebar-link">Proveedores</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="empresas.php" class="sidebar-link">Empresas</a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="pedidos_4.php" class="sidebar-link">Pedidos</a>
                                </li>
                            </ul>
                        </li>

                    </ul>

                </li>

                <li class="sidebar-item">
                    <a href="pedidos_sucursales.php" class="sidebar-link position-relative">
                        <i class="bi bi-cart" style="font-size: 1.5rem; position: relative;"></i>
                        <!-- Mostrar contador de pedidos pendientes -->
                        <?php if ($pendientes > 0): ?>
                            <span class="badge bg-danger position-absolute rounded-circle">
                                <?= $pendientes ?>
                            </span>
                        <?php endif; ?>
                        <span>Pedidos</span>
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
                    <h1>Empleados</h1>
                </div>
                <br>

                <!-- Buscador -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar empleados..." onkeyup="searchTable()">
                </div>

                <!-- Tabla de Productos -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="productosTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Correo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Recorrer los resultados de la consulta
                            while ($producto = mysqli_fetch_assoc($resultado)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($producto['id_usuario']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['apellido']) . "</td>";
                                echo "<td>" . htmlspecialchars($producto['email']) . "</td>";
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