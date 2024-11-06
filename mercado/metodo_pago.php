<?php
require 'config/config.php';

//Forma de Pago
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
}


?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <script src="/docs/5.3/assets/js/color-modes.js"></script>


    <link rel="icon" href="logo.svg">


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.122.0">
    <title>SuperOnline | Metodo de Pagos</title>

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

                    <a href="./checkout.php"> <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 20px;" class="text-black" width="24" height="24" fill="currentColor" class="bi bi-cart4" viewBox="0 0 16 16">
                            <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5M3.14 5l.5 2H5V5zM6 5v2h2V5zm3 0v2h2V5zm3 0v2h1.36l.5-2zm1.11 3H12v2h.61zM11 8H9v2h2zM8 8H6v2h2zM5 8H3.89l.5 2H5zm0 5a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0m9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0" />

                        </svg>


                    </a>
                    <span id="num_cart" class="badge bg-secondary"> <?php echo $num_cart; ?></span>



                </div>

            </div>

        </nav>
    </header>


    <main>



        <center>
            <hr class="featurette-divider">
            <h1>Elegi que metodo de pago usaras</h1>

        </center>
        <br>
        <br>
        <style>
            @media (max-width: 767.98px) {
                .mt-sm-4 {
                    margin-top: 1.5rem;
                    /* Ajusta el valor según tus necesidades */
                }
            }
        </style>
        <center>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-6 mt-sm-4">
                        <div class="card" style="width: 20rem;">
                            <img src="sucursal.svg" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title" id="titulo1">Pagar en la Sucursal</h5>
                                <br>
                                <form method="POST" action="pago_final2.php">
                                    <input type="hidden" name="titulo" value="Pago en sucursal">
                                    <input type="hidden" name="primer_titulo" value="<?php echo $titulo; ?>">
                                    <button type="submit" class="btn btn-outline-success">Elegir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-sm-4">
                        <div class="card" style="width: 20rem;">
                            <img src="transferencia.svg" class="card-img-top" alt="...">
                            <div class="card-body">
                                <h5 class="card-title" id="titulo2">Pagar con Tarjeta</h5>
                                <br>
                                <form method="POST" action="pago_final1.php">
                                    <input type="hidden" name="titulo" value="Pago con tarjeta">
                                    <input type="hidden" name="primer_titulo" value="<?php echo $titulo; ?>">
                                    <button type="submit" class="btn btn-outline-success">Elegir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </center>




        <div class="container marketing">

            <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
                <symbol id="bootstrap" viewBox="0 0 118 94">
                    <title>Bootstrap</title>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M24.509 0c-6.733 0-11.715 5.893-11.492 12.284.214 6.14-.064 14.092-2.066 20.577C8.943 39.365 5.547 43.485 0 44.014v5.972c5.547.529 8.943 4.649 10.951 11.153 2.002 6.485 2.28 14.437 2.066 20.577C12.794 88.106 17.776 94 24.51 94H93.5c6.733 0 11.714-5.893 11.491-12.284-.214-6.14.064-14.092 2.066-20.577 2.009-6.504 5.396-10.624 10.943-11.153v-5.972c-5.547-.529-8.934-4.649-10.943-11.153-2.002-6.484-2.28-14.437-2.066-20.577C105.214 5.894 100.233 0 93.5 0H24.508zM80 57.863C80 66.663 73.436 72 62.543 72H44a2 2 0 01-2-2V24a2 2 0 012-2h18.437c9.083 0 15.044 4.92 15.044 12.474 0 5.302-4.01 10.049-9.119 10.88v.277C75.317 46.394 80 51.21 80 57.863zM60.521 28.34H49.948v14.934h8.905c6.884 0 10.68-2.772 10.68-7.727 0-4.643-3.264-7.207-9.012-7.207zM49.948 49.2v16.458H60.91c7.167 0 10.964-2.876 10.964-8.281 0-5.406-3.903-8.178-11.425-8.178H49.948z"></path>
                </symbol>
                <symbol id="facebook" viewBox="0 0 16 16">
                    <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"></path>
                </symbol>
                <symbol id="instagram" viewBox="0 0 16 16">
                    <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"></path>
                </symbol>
                <symbol id="twitter" viewBox="0 0 16 16">
                    <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"></path>
                </symbol>
            </svg>
            <div class="container">
                <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
                    <div class="col-md-4 d-flex align-items-center">
                        <a href="/" class="mb-3 me-2 mb-md-0 text-body-secondary text-decoration-none lh-1">
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
            </div>
        </div>
    </main>
    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        function addProducto(id, token) {
            swal("Agregado al carrito", "", "success");

            // Espera 5 segundos y luego cierra la alerta
            setTimeout(function() {
                swal.close();
            }, 4000);

            let url = 'clases/carrito.php'
            let formData = new FormData()
            formData.append('id_producto', id)
            formData.append('token', token)

            fetch(url, {
                    method: 'POST',
                    body: formData,
                    mode: 'cors'
                }).then(response => response.json())
                .then(data => {
                    if (data.ok) {
                        let elemento = document.getElementById("num_cart")
                        elemento.innerHTML = data.numero
                    }
                })
        }
    </script>

</body>

</html>