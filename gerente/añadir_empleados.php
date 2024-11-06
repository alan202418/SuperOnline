<?php
require '../config/config.php'; // Incluye session_start()
require '../config/conexion.php'; // Establece la conexión $conexion

$alert = ""; // Variable para controlar el alert

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];

    // Consultar el id_sucursal del usuario autenticado
    $sqlUsuario = "SELECT id_sucursal FROM usuarios WHERE usuario = '$user_name'";
    $resultadoUsuario = mysqli_query($conexion, $sqlUsuario);

    if ($resultadoUsuario && mysqli_num_rows($resultadoUsuario) > 0) {
        $usuarioData = mysqli_fetch_assoc($resultadoUsuario);
        $id_sucursal_usuario = $usuarioData['id_sucursal'];

        // Consulta de productos filtrada por el id_sucursal del usuario
        $sqlProductos = "SELECT * FROM productos WHERE id_sucursal = '$id_sucursal_usuario'";
        $resultadoProductos = mysqli_query($conexion, $sqlProductos);
    } else {
        // Si el usuario no tiene id_sucursal asociado, no mostrar productos
        $resultadoProductos = null;
    }
} else {
    // Redireccionar a la página de login si el usuario no ha iniciado sesión
    header("Location: ../login.php");
    exit;
}

// Procesar el envío del formulario
if (isset($_POST['submit'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $apellido = mysqli_real_escape_string($conexion, $_POST['apellido']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $usuario_defecto = mysqli_real_escape_string($conexion, $_POST['usuario_defecto']);
    $contraseña_defecto = mysqli_real_escape_string($conexion, $_POST['contraseña_defecto']);
    $rol = 'empleado'; // Valor predeterminado para el rol

    // Encriptar la contraseña por defecto
    $contraseña_encriptada = password_hash($contraseña_defecto, PASSWORD_DEFAULT);

    // Realizar el INSERT directamente
    $sql_insert = "INSERT INTO usuarios (nombre, apellido, email, usuario, contraseña, usuario_defecto, contraseña_defecto, rol, id_sucursal) 
                   VALUES ('$nombre', '$apellido', '$email', '', '', '$usuario_defecto', '$contraseña_encriptada', '$rol', '$id_sucursal_usuario')";

    if (mysqli_query($conexion, $sql_insert)) {
        $alert = "success";
    } else {
        echo "Error al añadir empleado: " . mysqli_error($conexion);
    }
}

$conexion->close();
?>





<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Añadir Empleados</title>
    <!-- Incluir Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir SweetAlert desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Añadir Empleados</h1>
        <form action="añadir_empleados.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>

            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido:</label>
                <input type="text" class="form-control" name="apellido" required></input>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label for="usuario_defecto" class="form-label">Usuario por defecto:</label>
                <input type="text" class="form-control" name="usuario_defecto" required>
            </div>

            <div class="mb-3">
                <label for="contraseña_defecto" class="form-label">Contraseña por defecto:</label>
                <input type="text" class="form-control" name="contraseña_defecto" required>
            </div>

            <div class="d-grid">
                <button type="submit" name="submit" class="btn btn-primary">Guardar</button>
            </div><br>
        </form>
        <div class="d-grid">
            <a href="empleados.php" class="btn btn-secondary">Volver</a>
        </div><br>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para mostrar SweetAlert -->
    <script>
        <?php if ($alert == "success"): ?>
            Swal.fire({
                title: 'Empleado agregado con éxito',
                text: 'El Empleado fue agregado correctamente.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'empleados.php';
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>