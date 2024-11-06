<?php
require '../config/config.php'; // Incluye session_start()
require '../config/conexion.php'; // Establece la conexión $conexion

$alert = ""; // Variable para controlar el alert


// Obtener las categorías de la tabla 'categorias'
$sql = "SELECT id_categoria, categoria FROM categorias";
$result_categorias = $conexion->query($sql);

// Procesar el envío del formulario
if (isset($_POST['submit'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $id_categoria = $_POST['id_categoria'];
    $descuento = $_POST['descuento'];
    $stock = $_POST['stock'];
    $imagen = $_FILES['imagen']['name'];
    $imagenTmp = $_FILES['imagen']['tmp_name'];

    // Mueve la imagen subida al directorio 'imagenes/'
    $uploadDir = '../imagenes/';
    $uploadFile = $uploadDir . basename($imagen);

    if (move_uploaded_file($imagenTmp, $uploadFile)) {
        // Inserta el producto en cada una de las cuatro sucursales (IDs: 1, 2, 3, y 4)
        $sql_insert = "INSERT INTO productos (nombre, descripcion, precio, id_categoria, descuento, imagen, stock, id_sucursal)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conexion->prepare($sql_insert);
    
        // Definir los IDs de sucursales en las que queremos hacer el insert
        $ids_sucursales = [1, 2, 3, 4];
        
        // Ejecutar el insert para cada id de sucursal
        foreach ($ids_sucursales as $id_sucursal) {
            $stmt_insert->bind_param("ssdiisii", $nombre, $descripcion, $precio, $id_categoria, $descuento, $imagen, $stock, $id_sucursal);
    
            if (!$stmt_insert->execute()) {
                echo "Error al añadir producto en la sucursal $id_sucursal: " . $stmt_insert->error;
            }
        }
    
        $alert = "success"; 
        $stmt_insert->close();
    } else {
        echo "Error al subir la imagen.";
    }
    
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Productos</title>
    <!-- Incluir Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Incluir SweetAlert desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Añadir Productos</h1>
        <form action="añadir_productos.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea class="form-control" name="descripcion" required></textarea>
            </div>

            <div class="mb-3">
                <label for="precio" class="form-label">Precio:</label>
                <input type="number" class="form-control" name="precio" step="0.01" required>
            </div>

            <div class="mb-3">
                <label for="id_categoria" class="form-label">Categoría:</label>
                <select class="form-select" name="id_categoria" required>
                    <?php
                    if ($result_categorias->num_rows > 0) {
                        while ($row = $result_categorias->fetch_assoc()) {
                            echo "<option value='" . $row['id_categoria'] . "'>" . $row['categoria'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="descuento" class="form-label">Descuento:</label>
                <input type="number" class="form-control" name="descuento" step="0.01">
            </div>

            <div class="mb-3">
                <label for="stock" class="form-label">Stock:</label>
                <input type="number" class="form-control" name="stock" required>
            </div>

            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen:</label>
                <input type="file" class="form-control" name="imagen" accept="image/*" required>
            </div><br>

            <div class="d-grid">
                <button type="submit" name="submit" class="btn btn-primary">Guardar</button>
            </div><br>
        </form>
        <div class="d-grid">
            <a href="productos.php" class="btn btn-secondary">Volver</a>
        </div><br>
    </div>

    <!-- Incluir Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para mostrar SweetAlert -->
    <script>
        <?php if ($alert == "success"): ?>
            Swal.fire({
                title: 'Producto agregado con éxito',
                text: 'El producto fue agregado correctamente.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'productos.php';
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>
