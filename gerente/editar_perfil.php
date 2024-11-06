<?php
require '../config/config.php';
require '../config/conexion.php';

// Verificar si se recibió el id del usuario
if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Obtener los detalles del usuario
    $sql = "SELECT * FROM usuarios WHERE id_usuario = '$id_usuario'";
    $resultado = mysqli_query($conexion, $sql);
    $perfil = mysqli_fetch_assoc($resultado);

    if (!$perfil) {
        echo "Usuario no encontrado.";
        exit;
    }

    // Procesar el formulario de actualización
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nuevo_nombre = $_POST['nombre'];
        $nuevo_apellido = $_POST['apellido'];
        $nuevo_correo = $_POST['email'];
        $nuevo_usuario = $_POST['usuario'];
        $nuevo_contraseña = $_POST['contraseña'];

        // Si se cambió la contraseña, encriptarla
        if (!empty($nuevo_contraseña)) {
            $nuevo_contraseña = password_hash($nuevo_contraseña, PASSWORD_DEFAULT);
        } else {
            // Si no se cambió la contraseña, mantener la existente
            $nuevo_contraseña = $perfil['contraseña'];
        }

        // Actualizar el perfil de usuario en la base de datos
        $sql_update = "UPDATE usuarios SET nombre = '$nuevo_nombre', apellido = '$nuevo_apellido', email = '$nuevo_correo', usuario = '$nuevo_usuario', contraseña = '$nuevo_contraseña' WHERE id_usuario = '$id_usuario'";
        
        if (mysqli_query($conexion, $sql_update)) {
            // Guardar un mensaje para mostrar SweetAlert después de la redirección
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'El perfil fue cambiado con éxito',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location = 'perfil.php';
                    });
                }
            </script>";
        } else {
            echo "Error al actualizar el perfil: " . mysqli_error($conexion);
        }
    }
} else {
    echo "ID de usuario no recibido.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <!-- Incluir Bootstrap y SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Perfil de Usuario</h1><br>
        <form method="POST" id="usuarioForm">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($perfil['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($perfil['apellido']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electronico</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($perfil['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo htmlspecialchars($perfil['usuario']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña</label>
                <input type="text" class="form-control" id="contraseña" name="contraseña" value="<?php echo htmlspecialchars($perfil['contraseña']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="perfil.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
