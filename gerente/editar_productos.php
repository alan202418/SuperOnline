<?php
require '../config/config.php';
require '../config/conexion.php';

// Verificar si se recibió el id del producto
if (isset($_GET['id_producto'])) {
    $id_producto = $_GET['id_producto'];

    // Obtener los detalles del producto
    $sql = "SELECT * FROM productos WHERE id_producto = '$id_producto'";
    $resultado = mysqli_query($conexion, $sql);
    $producto = mysqli_fetch_assoc($resultado);

    if (!$producto) {
        echo "Producto no encontrado.";
        exit;
    }

    // Procesar el formulario de actualización
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los nuevos valores de los campos
        $nuevo_nombre = $_POST['nombre'];
        $nueva_descripcion = $_POST['descripcion'];
        $nuevo_precio = $_POST['precio'];
        $nuevo_descuento = $_POST['descuento'];
        $nuevo_stock = $_POST['stock'];
        $nueva_imagen = $_FILES['imagen']['name'];
        $imagenTmp = $_FILES['imagen']['tmp_name'];

        // Verificar si se cargó una nueva imagen
        if (!empty($nueva_imagen)) {
            $uploadDir = '../imagenes/';
            $uploadFile = $uploadDir . basename($nueva_imagen);

            if (move_uploaded_file($imagenTmp, $uploadFile)) {
                $imagenPath = $nueva_imagen; // Se usa la nueva imagen
            } else {
                echo "Error al subir la imagen.";
                exit;
            }
        } else {
            $imagenPath = $producto['imagen']; // Mantener la imagen actual si no se subió una nueva
        }

        // Actualizar todos los productos con el mismo nombre en cualquier sucursal
        $nombre_producto = $producto['nombre']; // Usamos el nombre actual del producto
        $sql_update = "UPDATE productos SET 
                        nombre = '$nuevo_nombre',
                        descripcion = '$nueva_descripcion',
                        precio = '$nuevo_precio',
                        descuento = '$nuevo_descuento',
                        stock = '$nuevo_stock',
                        imagen = '$imagenPath'
                       WHERE nombre = '$nombre_producto'";

        if (mysqli_query($conexion, $sql_update)) {
            // Guardar un mensaje para mostrar SweetAlert después de la redirección
            echo "<script>
                window.onload = function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'El producto fue actualizado en todas las sucursales',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location = 'productos.php';
                    });
                }
            </script>";
        } else {
            echo "Error al actualizar el producto: " . mysqli_error($conexion);
        }
    }
} else {
    echo "ID de producto no recibido.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <!-- Incluir Bootstrap y SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Producto</h1>
        <form method="POST" enctype="multipart/form-data" id="productoForm">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" class="form-control" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descuento" class="form-label">Descuento</label>
                <input type="number" class="form-control" id="descuento" name="descuento" value="<?php echo htmlspecialchars($producto['descuento']); ?>">
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen (opcional)</label>
                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="productos.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
