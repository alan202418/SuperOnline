<?php
require '../config/config.php';
require '../config/conexion.php';

// Verificar si se recibió el id del producto
if (isset($_GET['id_producto'])) {
    $id_producto = $_GET['id_producto'];

    // Obtener el nombre del producto basado en el id_producto
    $sql = "SELECT nombre FROM productos WHERE id_producto = '$id_producto'";
    $resultado = mysqli_query($conexion, $sql);
    $producto = mysqli_fetch_assoc($resultado);

    if (!$producto) {
        echo "Producto no encontrado.";
        exit;
    }

    $nombre_producto = $producto['nombre'];

    // Eliminar todos los productos que tengan el mismo nombre
    $sql_delete = "DELETE FROM productos WHERE nombre = '$nombre_producto'";
    
    if (mysqli_query($conexion, $sql_delete)) {
        // Reiniciar el contador autoincremental de la tabla productos
        $sql_reset_auto_increment = "ALTER TABLE productos AUTO_INCREMENT = 1";
        mysqli_query($conexion, $sql_reset_auto_increment);

        // Mostrar alerta de éxito
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Producto eliminado con éxito',
                    confirmButtonText: 'OK'
                }).then(function() {
                    window.location = 'productos.php';
                });
            }
        </script>";
    } else {
        echo "Error al eliminar el producto: " . mysqli_error($conexion);
    }
} else {
    echo "ID de producto no recibido.";
    exit;
}
?>

<!-- Incluir SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
