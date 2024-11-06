<?php
require '../config/config.php';
require '../config/conexion.php';

// Verificar si se recibió el id del usuario
if (isset($_GET['id_usuario'])) {
    $id_usuario = $_GET['id_usuario'];

    // Verificar que el usuario exista en la base de datos
    $sql_check = "SELECT nombre FROM usuarios WHERE id_usuario = '$id_usuario'";
    $resultado = mysqli_query($conexion, $sql_check);
    $empleado = mysqli_fetch_assoc($resultado);

    if (!$empleado) {
        echo "Empleado no encontrado.";
        exit;
    }

    // Eliminar al empleado basado en el id_usuario
    $sql_delete = "DELETE FROM usuarios WHERE id_usuario = '$id_usuario'";
    
    if (mysqli_query($conexion, $sql_delete)) {
        // Reiniciar el contador autoincremental de la tabla usuarios
        $sql_reset_auto_increment = "ALTER TABLE usuarios AUTO_INCREMENT = 1";
        mysqli_query($conexion, $sql_reset_auto_increment);

        // Mostrar alerta de éxito
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Empleado eliminado con éxito',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location = 'empleados.php';
                });
            }
        </script>";
    } else {
        echo "Error al eliminar el empleado: " . mysqli_error($conexion);
    }
} else {
    echo "ID de usuario no recibido.";
    exit;
}
?>

<!-- Incluir SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
