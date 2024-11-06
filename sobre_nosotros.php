<?php
require 'config/config.php';
require 'config/conexion.php';



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
    <title>SuperOnline | Sobre Nosotros</title>

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
                        <?php
                        // Verificamos si el usuario ha iniciado sesión
                        $is_logged_in = isset($_SESSION['user_name']);
                        ?>

                        <li class="nav-item">
                            <a href="javascript:void(0);" class="nav-link" id="misComprasLink">Mis compras</a>
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

    <style>
        .carousel-inner {
            background-color: #ffff;
        }
    </style>

    <main>
        <div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <video class="bd-placeholder-img" width="100%" height="100%" autoplay loop muted>
                        <source src="sobre_nosotros.mp4" type="video/mp4">
                        Tu navegador no soporta la etiqueta de video.
                    </video>
                </div>
            </div>
        </div>







        <style>
            h1 {
                font-family: 'noto sans';


            }

            b {
                color: #00bf5f;
            }
        </style>
        <div class="p-5 mb-4 bg-body-tertiary rounded-3 animated-div">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">¿Qué Somos..?</h1><br>
                <p class="col-md-8 fs-4">
                    En <b>SuperOnline</b>, somos un equipo comprometido con la innovación y la satisfacción del cliente. Nos especializamos en ofrecer una experiencia de compra en línea eficiente y sin complicaciones. Nuestro objetivo es proporcionar productos de alta calidad a precios competitivos, asegurando que nuestros clientes puedan recoger sus compras de manera rápida y conveniente en nuestros puntos de recogida estratégicamente ubicados.
                </p>
            </div>
        </div>

        <style>
            .animated-div {
                opacity: 0;
                transform: translateY(50px);
                transition: opacity 0.8s ease-out, transform 0.8s ease-out;
            }

            .animated-div.visible {
                opacity: 1;
                transform: translateY(0);
            }
        </style>
        <script>
            window.addEventListener('scroll', function() {
                const animatedDiv = document.querySelector('.animated-div');
                const rect = animatedDiv.getBoundingClientRect();

                // Si el div está visible en la ventana (viewport)
                if (rect.top < window.innerHeight && rect.bottom >= 0) {
                    animatedDiv.classList.add('visible');
                } else {
                    animatedDiv.classList.remove('visible');
                }
            });
        </script>

        <hr class="featurette-divider">
        <div class="p-5 mb-4 bg-body-tertiary rounded-3 animated-div">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Nuestra Misión y Visión</h1><br>
                <p class="col-md-8 fs-4">
                    La <b>mision</b> de SuperOnline es clara: simplificar la vida de nuestros clientes proporcionando una experiencia de compra en línea rápida, segura y conveniente. Nos esforzamos por ofrecer productos de alta calidad a precios competitivos, con un servicio al cliente excepcional.<br>

                    Nuestra <b>visión</b> es convertirnos en el supermercado en línea líder en toda Formosa, conocido por nuestra innovación constante y nuestro compromiso con la comunidad. Estamos trabajando en iniciativas que promuevan el bienestar social, como colaboraciones con productores locales y programas de apoyo a comunidades vulnerables.
                </p>
            </div>
        </div>
        <style>
            .animated-div {
                opacity: 0;
                transform: translateY(50px);
                transition: opacity 0.8s ease-out, transform 0.8s ease-out;
            }

            .animated-div.visible {
                opacity: 1;
                transform: translateY(0);
            }
        </style>
        <script>
            window.addEventListener('scroll', function() {
                const animatedDivs = document.querySelectorAll('.animated-div');

                animatedDivs.forEach(div => {
                    const rect = div.getBoundingClientRect();

                    // Si el div está visible en la ventana (viewport)
                    if (rect.top < window.innerHeight && rect.bottom >= 0) {
                        div.classList.add('visible');
                    } else {
                        div.classList.remove('visible');
                    }
                });
            });
        </script>
        <hr class="featurette-divider">


        <div class="p-5 mb-4 bg-body-tertiary rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Nuestros Valores</h1>
                <br>
                <div class="grid-container">
                    <div class="card animated-card" style="width: 18rem;">
                        <img src="https://th.bing.com/th/id/R.8d4f21d665ccd4661c4afc4f34f7b4ab?rik=Gzxv72LHkwd24g&pid=ImgRaw&r=0" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Calidad</h5>
                            <p class="card-text">Nos comprometemos a ofrecer productos frescos y de alta calidad.</p>
                        </div>
                    </div>
                    <div class="card animated-card" style="width: 18rem;">
                        <img src="https://th.bing.com/th/id/OIP.oNfwBtH0rCfq0Kj8cvlozQHaEK?rs=1&pid=ImgDetMain" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Innovación</h5>
                            <p class="card-text">Utilizamos tecnología avanzada para mejorar la experiencia de compra en línea.</p>
                        </div>
                    </div>
                    <div class="card animated-card" style="width: 18rem;">
                        <img src="https://th.bing.com/th/id/R.f55afe14dabfd18abadc6a8ac5cd1dcd?rik=Dlu9KDGGYY77AQ&pid=ImgRaw&r=0" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Sostenibilidad</h5>
                            <p class="card-text">Promovemos prácticas ecológicas y sostenibles en todas nuestras operaciones.</p>
                        </div>
                    </div>
                    <div class="card animated-card" style="width: 18rem;">
                        <img src="https://th.bing.com/th/id/OIP.IxdNyJ3g5UshOjL7DW7cnwHaFL?rs=1&pid=ImgDetMain" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Servicio al Cliente</h5>
                            <p class="card-text">Nuestro equipo está dedicado a brindar un servicio excepcional y personalizado.</p>
                        </div>
                    </div>
                </div>
                <br>
                <h1 class="display-5 fw-bold">Nuestros Productos</h1>
                <br>
                <div class="grid-container">
                    <div class="card animated-card" style="width: 18rem;">
                        <img src="https://www.foodretail.es/2020/07/08/food/Productos-frescos_1453964608_529960_1200x675.jpg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Alimentos Frescos</h5>
                            <p class="card-text">Frutas, verduras, carnes y productos lácteos.</p>
                        </div>
                    </div>
                    <div class="card animated-card" style="width: 18rem;">
                        <img src="https://th.bing.com/th/id/R.0531736c42cc8635a6a35c97547dec65?rik=0P3UX%2bJu322%2fGA&pid=ImgRaw&r=0" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Productos de Despensa</h5>
                            <p class="card-text">Alimentos no perecederos, bebidas y snacks.</p>
                        </div>
                    </div>
                    <div class="card animated-card" style="width: 18rem;">
                        <img src="https://www.claro.com.co/portal/recursos/co/cpp/promociones/imagenes/1590067395832-3-Tecnologia-en-el-hogar.jpg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Artículos del Hogar</h5>
                            <p class="card-text">Productos de limpieza, utensilios de cocina y más.</p>
                        </div>
                    </div>
                    <div class="card animated-card" style="width: 18rem;">
                        <img src="https://th.bing.com/th/id/R.1a1759ab94f6322ba7893e7a0c1254dd?rik=7E40HPn1%2fRsgpg&pid=ImgRaw&r=0" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">Cuidado Personal</h5>
                            <p class="card-text">Cosméticos, productos de higiene y cuidado personal.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            .animated-card {
                opacity: 0;
                transform: scale(0.8);
                transition: opacity 0.8s ease, transform 0.8s ease;
            }

            .animated-card.visible {
                opacity: 1;
                transform: scale(1);
            }

            .card-img-top {
                width: 100%;
                height: 200px;
                object-fit: cover;
            }
        </style>
        <script>
            window.addEventListener('scroll', function() {
                const animatedCards = document.querySelectorAll('.animated-card');

                animatedCards.forEach(card => {
                    const rect = card.getBoundingClientRect();

                    if (rect.top < window.innerHeight && rect.bottom >= 0) {
                        card.classList.add('visible');
                    } else {
                        card.classList.remove('visible');
                    }
                });
            });
        </script>

        <style>
            .grid-container {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1rem;
            }

            @media (max-width: 768px) {
                .grid-container {
                    grid-template-columns: 1fr;
                }
            }
        </style>


        <br>





        
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
        </div>
        </div>
    </main>
    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.getElementById('btnAgradecimiento').addEventListener('click', function() {
            Swal.fire({
                title: '¡Gracias por confiar en nosotros!',
                text: 'La cadena Changomas te lo agradece.',
                icon: 'success',
                confirmButtonText: 'OK',
                onClose: () => {
                    // Redirigir al inicio o realizar otras acciones aquí
                    // Por ejemplo: window.location.href = 'https://www.changomas.com.ar/';
                }
            });
        });
        document.getElementById('btnAgradecimiento1').addEventListener('click', function() {
            Swal.fire({
                title: '¡Gracias por confiar en nosotros!',
                text: 'La cadena Changomas te lo agradece.',
                icon: 'success',
                confirmButtonText: 'OK',
                onClose: () => {
                    // Redirigir al inicio o realizar otras acciones aquí
                    // Por ejemplo: window.location.href = 'https://www.changomas.com.ar/';
                }
            });
        });
        document.getElementById('btnAgradecimiento2').addEventListener('click', function() {
            Swal.fire({
                title: '¡Gracias por confiar en nosotros!',
                text: 'La cadena Changomas te lo agradece.',
                icon: 'success',
                confirmButtonText: 'OK',
                onClose: () => {
                    // Redirigir al inicio o realizar otras acciones aquí
                    // Por ejemplo: window.location.href = 'https://www.changomas.com.ar/';
                }
            });
        });
    </script>
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
    <script>
        // Obtenemos el estado de sesión desde PHP
        var isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

        document.getElementById('misComprasLink').addEventListener('click', function() {
            if (isLoggedIn) {
                // Si el usuario ha iniciado sesión, lo redirigimos a la página de compras
                window.location.href = 'historial_compras.php';
            } else {
                // Si no ha iniciado sesión, mostramos el SweetAlert
                Swal.fire({
                    title: 'Debe iniciar sesión',
                    text: 'Para acceder a sus compras debe iniciar sesión',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iniciar sesión',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php'; // Redirige a la página de inicio de sesión
                    }
                });
            }
        });
    </script>


</body>

</html>