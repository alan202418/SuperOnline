<?php
require '../config/config.php';
require '../config/conexion.php';

// Configurar la zona horaria de Buenos Aires
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Obtener la fecha y hora actual del sistema
$fecha_registro = date("Y-m-d H:i:s"); // Fecha y hora actual de Buenos Aires

$success = false; // Variable para controlar el estado de éxito

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $nombre_empresa = $_POST['nombre_empresa'];
    $direccion_empresa = $_POST['direccion_empresa'];
    $telefono_empresa = $_POST['telefono_empresa'];
    $email_empresa = $_POST['email_empresa'];

    // Insertar en la tabla empresas
    $sql_empresa = "INSERT INTO empresas (nombre_empresa, direccion_empresa, telefono_empresa, email_empresa, fecha_registro) 
                    VALUES ('$nombre_empresa', '$direccion_empresa', '$telefono_empresa', '$email_empresa', '$fecha_registro')";
    $insert_empresa = mysqli_query($conexion, $sql_empresa);

    // Obtener el id de la empresa recién insertada
    $id_empresa = mysqli_insert_id($conexion);

    // Verificar si se obtuvo correctamente el id de la empresa
    if ($id_empresa) {
        // Insertar en la tabla proveedores usando el id de empresa recién obtenido
        $sql_proveedor = "INSERT INTO proveedores (id_empresa, nombre, direccion, telefono, email, fecha_registro) 
                          VALUES ('$id_empresa', '$nombre', '$direccion', '$telefono', '$email', '$fecha_registro')";
        $insert_proveedor = mysqli_query($conexion, $sql_proveedor);
    }

    if ($insert_empresa && $insert_proveedor) {
        $success = true; // Si ambas inserciones son exitosas, marca como éxito
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Proveedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Añadir Proveedor</h1>
        <br>
        <form action="añadir_proveedor.php" method="POST">
            <h4>Datos del Proveedor</h4>
            <br>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Proveedor</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <br>

            <h4>Datos de la Empresa</h4>
            <br>
            <div class="mb-3">
                <label for="nombre_empresa" class="form-label">Nombre de la Empresa</label>
                <input type="text" class="form-control" id="nombre_empresa" name="nombre_empresa" required>
            </div>
            <div class="mb-3">
                <label for="direccion_empresa" class="form-label">Dirección de la Empresa</label>
                <input type="text" class="form-control" id="direccion_empresa" name="direccion_empresa" required>
            </div>
            <div class="mb-3">
                <label for="telefono_empresa" class="form-label">Teléfono de la Empresa</label>
                <input type="text" class="form-control" id="telefono_empresa" name="telefono_empresa" required>
            </div>
            <div class="mb-3">
                <label for="email_empresa" class="form-label">Correo Electrónico de la Empresa</label>
                <input type="email" class="form-control" id="email_empresa" name="email_empresa" required>
            </div>
            <br>
            <div class="d-flex justify-content-start">
                <!-- Botón Guardar -->
                <button type="submit" class="btn btn-success me-2">Guardar</button>

                <!-- Botón Volver -->
                <a href="proveedores.php" class="btn btn-secondary">Volver</a>
            </div>

           
            
        </form>
        <br>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert para mostrar mensaje de éxito -->
    <?php if ($success): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Proveedor añadido correctamente',
            text: 'Los datos del proveedor y empresa han sido guardados.',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'proveedores.php'; // Redirigir a la página de proveedores después de cerrar el SweetAlert
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
