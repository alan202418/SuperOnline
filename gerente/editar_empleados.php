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
        // Campos editables
        $nuevo_nombre = $_POST['nombre'];
        $nuevo_apellido = $_POST['apellido'];
        $nuevo_email = $_POST['email'];
        
        // Solo actualizar usuario_defecto y contraseña_defecto si tienen valor en la base de datos
        $nuevo_usuario_defecto = !empty($perfil['usuario_defecto']) ? $_POST['usuario_defecto'] : $perfil['usuario_defecto'];
        $nueva_contraseña_defecto = !empty($perfil['contraseña_defecto']) && !empty($_POST['contraseña_defecto']) 
                                    ? password_hash($_POST['contraseña_defecto'], PASSWORD_DEFAULT) 
                                    : $perfil['contraseña_defecto'];

        // Construir la consulta de actualización
        $sql_update = "UPDATE usuarios SET 
                        nombre = '$nuevo_nombre', 
                        apellido = '$nuevo_apellido', 
                        email = '$nuevo_email', 
                        usuario_defecto = '$nuevo_usuario_defecto', 
                        contraseña_defecto = '$nueva_contraseña_defecto' 
                       WHERE id_usuario = '$id_usuario'";

        if (mysqli_query($conexion, $sql_update)) {
            // Guardar un mensaje para mostrar SweetAlert después de la redirección
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'El empleado fue actualizado con éxito',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location = 'empleados.php';
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
    <title>Editar Empleados</title>
    <!-- Incluir Bootstrap y SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Empleados</h1><br>
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
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($perfil['email']); ?>" required>
            </div>

            <?php if (!empty($perfil['usuario_defecto'])): ?>
            <div class="mb-3">
                <label for="usuario_defecto" class="form-label">Usuario por Defecto</label>
                <input type="text" class="form-control" id="usuario_defecto" name="usuario_defecto" value="<?php echo htmlspecialchars($perfil['usuario_defecto']); ?>" required>
            </div>
            <?php endif; ?>

            <?php if (!empty($perfil['contraseña_defecto'])): ?>
            <div class="mb-3">
                <label for="contraseña_defecto" class="form-label">Contraseña por Defecto</label>
                <input type="text" class="form-control" id="contraseña_defecto" name="contraseña_defecto" placeholder="Ingrese nueva contraseña si desea cambiarla">
            </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="empleados.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
