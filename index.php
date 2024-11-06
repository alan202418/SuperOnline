<?php
require 'config/config.php';
require 'config/conexion.php';


$sql = ("SELECT id_producto, nombre, id_categoria, imagen, precio
FROM (
    SELECT id_producto, nombre, id_categoria, imagen, precio,
           ROW_NUMBER() OVER (PARTITION BY id_categoria ORDER BY id_producto) AS row_num
    FROM productos 
     
) AS subquery 
WHERE row_num <= 3");


$resultado = mysqli_query($conexion, $sql);



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
  <title>SuperOnline | Sabemos de ofertas</title>

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
    b {
      color: #00bf5f;
    }

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

          .navbar-toggler {
            position: absolute;
            right: 120px;
            /* Ajusta según sea necesario */
            top: 10px;
            /* Ajusta según sea necesario */
            z-index: 1030;
            /* Asegura que esté por encima de otros elementos */
          }
        </style>






        <button class="navbar-toggler " type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation" style="background-color: black;">
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

            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Acerca de
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="como_retirar.php">Como retirar mi producto</a></li>
                <li><a class="dropdown-item" href="como_pedir_reembolso.php">Como pedir un reembolsado</a></li>
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
      background-color: #c1ff72;
    }
  </style>

  <main>

    <div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">

          <img class="bd-placeholder-img" width="100%" height="100%" src="fondo1.svg"></img>


        </div>
        <div class="carousel-item">
          <img class="bd-placeholder-img" width="100%" height="100%" src="fondo2.svg"></img>


        </div>
        <div class="carousel-item">
          <img class="bd-placeholder-img" width="100%" height="100%" src="fondo3.svg"></img>


        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
    <center>
      <h1>Nuestros Productos</h1>
    </center>
    <br>
    <style>
      .card {
        height: 100%;
        display: flex;
        flex-direction: column;
      }

      .card-img-top {
        object-fit: cover;
        height: 200px;
        /* Ajusta según la altura que prefieras para las imágenes */
      }

      .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
      }

      ul {
        padding-left: 20px;
        /* Para mejorar la estética de la lista */
      }

      .d-flex {
        margin-top: auto;
      }
    </style>
    <main>
      <div class="container">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
          <?php foreach ($resultado as $row) { ?>
            <div class="col">
              <div class="card shadow-sm">
                <img src="imagenes/<?php echo $row['imagen']; ?>" class="card-img-top" alt="...">
                <div class="card-body">
                  <h5 class="card-title"><?php echo $row['nombre']; ?></h5>
                  <p class="card-text">$<?php echo $row['precio']; ?></p>
                  <?php
                  // Obtener el stock y sucursal para el producto actual
                  $id_producto = $row['id_producto'];

                  // Consulta para obtener todas las sucursales y stock para este producto
                  $sql_stock = "SELECT id_sucursal, stock FROM productos WHERE nombre = '" . mysqli_real_escape_string($conexion, $row['nombre']) . "' AND stock > 0 ORDER BY id_sucursal";

                  $resultado_stock = mysqli_query($conexion, $sql_stock);

                  if ($resultado_stock && mysqli_num_rows($resultado_stock) > 0) {
                    echo "<p style='color: green;'>Disponible en:</p>";
                    echo "<ul>";
                    while ($row_stock = mysqli_fetch_assoc($resultado_stock)) {
                      // Obtener el nombre de la sucursal
                      $id_sucursal = $row_stock['id_sucursal'];
                      $sql_sucursal = "SELECT nombre_sucursal FROM sucursales WHERE id_sucursal = $id_sucursal";
                      $resultado_sucursal = mysqli_query($conexion, $sql_sucursal);
                      $nombre_sucursal = '';
                      if ($resultado_sucursal && mysqli_num_rows($resultado_sucursal) > 0) {
                        $row_sucursal = mysqli_fetch_assoc($resultado_sucursal);
                        $nombre_sucursal = htmlspecialchars($row_sucursal['nombre_sucursal']);
                      } else {
                        $nombre_sucursal = "Sucursal $id_sucursal";
                      }
                      echo "<li style='color: green;'>" . $nombre_sucursal . ": " . htmlspecialchars($row_stock['stock']) . "</li>";
                    }
                    echo "</ul>";
                  } else {
                    echo "<p>No hay stock disponible.</p>";
                  }
                  ?>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                      <?php if ($row['id_categoria'] == 1) { ?>
                        <a href="detalles1.php?id_producto=<?php echo $row['id_producto']; ?>&token=<?php echo hash_hmac('sha1', $row['id_producto'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>
                      <?php } elseif ($row['id_categoria'] == 2) { ?>
                        <a href="detalles2.php?id_producto=<?php echo $row['id_producto']; ?>&token=<?php echo hash_hmac('sha1', $row['id_producto'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>
                      <?php } elseif ($row['id_categoria'] == 3) { ?>
                        <a href="detalles3.php?id_producto=<?php echo $row['id_producto']; ?>&token=<?php echo hash_hmac('sha1', $row['id_producto'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>
                      <?php } elseif ($row['id_categoria'] == 4) {  ?>
                        <a href="detalles4.php?id_producto=<?php echo $row['id_producto']; ?>&token=<?php echo hash_hmac('sha1', $row['id_producto'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>
                      <?php } else { ?>
                        <a href="detalles5.php?id_producto=<?php echo $row['id_producto']; ?>&token=<?php echo hash_hmac('sha1', $row['id_producto'], KEY_TOKEN); ?>" class="btn btn-primary">Detalles</a>
                      <?php } ?>

                    </div>
                    <?php
                    // Verificamos si el usuario ha iniciado sesión
                    if (isset($_SESSION['user_name'])) {
                    ?>
                      <button class="btn btn-outline-success" type="button" onclick="addProducto(<?php echo $row['id_producto']; ?>, '<?php echo hash_hmac('sha1', $row['id_producto'], KEY_TOKEN); ?>')">
                        Agregar al Carrito
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                          <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z" />
                          <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                        </svg>
                      </button>
                    <?php
                    } else {
                    ?>
                      <button class="btn btn-outline-success" type="button" onclick="loginAlert()">
                        Agregar al Carrito
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-cart-plus" viewBox="0 0 16 16">
                          <path d="M9 5.5a.5.5 0 0 0-1 0V7H6.5a.5.5 0 0 0 0 1H8v1.5a.5.5 0 0 0 1 0V8h1.5a.5.5 0 0 0 0-1H9z" />
                          <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                        </svg>
                      </button>
                    <?php
                    }
                    ?>

                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </main>
    <style>
      .card {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.5s ease, transform 0.5s ease;
      }

      .card.in-view {
        opacity: 1;
        transform: translateY(0);
      }

      .card:hover {
        transform: scale(1.05);
        transition: transform 0.3s ease-in-out;
      }
    </style>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Seleccionamos todas las tarjetas
        const cards = document.querySelectorAll('.card');

        // Configuramos el observer
        const observer = new IntersectionObserver(entries => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('in-view'); // Agrega la clase cuando entra en vista
            } else {
              entry.target.classList.remove('in-view'); // Remueve la clase cuando sale de vista
            }
          });
        }, {
          threshold: 0.1 // El umbral indica que el 10% de la tarjeta debe ser visible para activar la animación
        });

        // Observamos cada tarjeta
        cards.forEach(card => {
          observer.observe(card);
        });
      });
    </script>





    <style>
      .card-img-top {
        width: 100%;
        height: auto;
        /* Ajusta la altura automáticamente */
        object-fit: cover;
        /* Esto asegura que la imagen se ajuste al contenedor sin distorsionarse */
      }
    </style>








    <div class="container marketing">



      <!-- START THE FEATURETTES -->

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading fw-normal lh-1 featurette-text">Todos Nuestros Productos al mejor precio!!</h2>
        </div>
        <div class="col-md-5">
          <img class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" src="https://www.casamagalhaes.com.br/blog/wp-content/uploads/2018/02/como-atrair-clientes-para-supermercado.jpg" width="500" height="500">
        </div>
      </div>
      <style>
        /* Animación para el texto */
        .featurette-text {
          opacity: 0;
          transform: translateX(-100px);
          transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .featurette-text.in-view {
          opacity: 1;
          transform: translateX(0);
        }

        /* Animación para la imagen */
        .featurette-image {
          opacity: 0;
          transform: translateX(100px);
          transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .featurette-image.in-view {
          opacity: 1;
          transform: translateX(0);
        }
      </style>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Seleccionamos el texto y la imagen
          const featuretteText = document.querySelector('.featurette-heading');
          const featuretteImage = document.querySelector('.featurette-image');

          // Configuramos el observer
          const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
              if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
              } else {
                entry.target.classList.remove('in-view');
              }
            });
          }, {
            threshold: 0.2 // El umbral indica que el 20% del elemento debe ser visible para activar la animación
          });

          // Observamos el texto y la imagen
          observer.observe(featuretteText);
          observer.observe(featuretteImage);
        });
      </script>



      <hr class="featurette-divider">

      <center>
        <h4>Comentarios</h4>
      </center>
      <br>

      <!-- Tres columnas de texto debajo del carrusel -->
      <div class="row">
        <div class="col-lg-4 comment-card">
          <img src="https://th.bing.com/th/id/OIP.Z7XDEmqpM_wzlRrL8ybrAwHaHa?w=500&h=500&rs=1&pid=ImgDetMain" class="bd-placeholder-img rounded-circle" width="160" height="160">
          <h2 class="fw-normal">Jose</h2>
          <p><b>Comentario:</b> Compré varios productos y estoy muy satisfecho. La calidad de los productos superó mis expectativas.</p>
          <p><a class="btn btn-success" href="#" id="btnAgradecimiento">Me sirve &raquo;</a></p>
        </div><!-- /.col-lg-4 -->

        <div class="col-lg-4 comment-card">
          <img src="https://th.bing.com/th/id/R.fdac192308674300500ac27506d139d2?rik=MCaibyjrWBakoQ&pid=ImgRaw&r=0" class="bd-placeholder-img rounded-circle" width="160" height="160">
          <h2 class="fw-normal">Marcos</h2>
          <p><b>Comentario:</b> El servicio al cliente es excepcional. Resolvieron mi problema con una devolución de manera rápida y amigable. ¡Gracias!</p>
          <p><a class="btn btn-success" href="#" id="btnAgradecimiento1">Me sirve &raquo;</a></p>
        </div><!-- /.col-lg-4 -->

        <div class="col-lg-4 comment-card">
          <img src="https://th.bing.com/th/id/OIP.q1EUKFyLSTK_qmztyOR_hAHaHM?rs=1&pid=ImgDetMain" class="bd-placeholder-img rounded-circle" width="140" height="140">
          <h2 class="fw-normal">Ester</h2>
          <p><b>Comentario:</b> ¡Increíble experiencia de compra! Todos los productos a los mejores precios!!. Definitivamente compraré aquí de nuevo.</p>
          <p><a class="btn btn-success" href="#" id="btnAgradecimiento2">Me sirve &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
      </div><!-- /.row -->
      <style>
        .comment-card {
          opacity: 0;
          transform: scale(0.8);
          transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .comment-card.in-view {
          opacity: 1;
          transform: scale(1);
        }
      </style>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Seleccionamos todas las tarjetas de comentarios
          const commentCards = document.querySelectorAll('.comment-card');

          // Configuramos el observer
          const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
              if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
              } else {
                entry.target.classList.remove('in-view');
              }
            });
          }, {
            threshold: 0.1 // El umbral indica que el 10% del comentario debe ser visible para activar la animación
          });

          // Observamos cada tarjeta de comentario
          commentCards.forEach(card => {
            observer.observe(card);
          });
        });
      </script>


      <hr class="featurette-divider">

      <!-- /END THE FEATURETTES -->

    </div><!-- /.container -->


    <!-- FOOTER -->
    <footer class="container">
      <div class="slider">
        <div class="slide-track">
          <div class="slide">
            <img src="1.png" alt="">
          </div>
          <div class="slide">
            <img src="2.png" alt="">
          </div>
          <div class="slide">
            <img src="3.png" alt="">
          </div>
          <div class="slide">
            <img src="4.png" alt="">
          </div>
          <div class="slide">
            <img src="5.png" alt="">
          </div>
          <div class="slide">
            <img src="6.png" alt="">
          </div>
          <div class="slide">
            <img src="7.png" alt="">
          </div>

          <div class="slide">
            <img src="1.png" alt="">
          </div>
          <div class="slide">
            <img src="2.png" alt="">
          </div>
          <div class="slide">
            <img src="3.png" alt="">
          </div>
          <div class="slide">
            <img src="4.png" alt="">
          </div>
          <div class="slide">
            <img src="5.png" alt="">
          </div>
          <div class="slide">
            <img src="6.png" alt="">
          </div>
          <div class="slide">
            <img src="7.png" alt="">
          </div>
        </div>
      </div>

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
    </div>
    </div>
  </main>
  <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    document.getElementById('btnAgradecimiento').addEventListener('click', function() {
      Swal.fire({
        title: '¡Gracias por confiar en nosotros!',
        text: 'La cadena SuperOnline te lo agradece.',
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
        text: 'La cadena SuperOnline te lo agradece.',
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
        text: 'La cadena SuperOnline te lo agradece.',
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
    function loginAlert() {
      Swal.fire({
        title: 'Debe iniciar sesión',
        text: 'Para realizar esta acción debe iniciar sesión',
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

    function addProducto(id, token) {
      Swal.fire({
        title: 'Producto agregado al carrito',
        text: 'El producto ha sido agregado correctamente',
        icon: 'success',
        timer: 2000, // Se cierra automáticamente después de 2 segundos
        showConfirmButton: false
      });

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