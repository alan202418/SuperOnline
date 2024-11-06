<?php
require '../config/config.php';
require '../config/conexion.php';

// Configurar la zona horaria de Buenos Aires
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fecha_pedido = date("Y-m-d H:i:s"); // Fecha y hora actual de Buenos Aires

$success = false; // Variable para controlar el estado de éxito

// Verificar si el usuario ha iniciado sesión y obtener su nombre de usuario
if (!isset($_SESSION['user_name'])) {
    // Redirigir a la página de inicio de sesión si no está autenticado
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];

// Consultar el id_sucursal del usuario actual
$sql_sucursal = "SELECT id_sucursal FROM usuarios WHERE usuario = ?";
$stmt_sucursal = $conexion->prepare($sql_sucursal);
$stmt_sucursal->bind_param('s', $user_name);
$stmt_sucursal->execute();
$result_sucursal = $stmt_sucursal->get_result();
$sucursal_data = $result_sucursal->fetch_assoc();
$id_sucursal = $sucursal_data['id_sucursal'] ?? null; // Usar null si no se encuentra el id_sucursal

// Verificar que el id_sucursal se haya obtenido correctamente
if (!$id_sucursal) {
    echo "<script>alert('No se encontró la sucursal para el usuario.');</script>";
    exit();
}

// Obtener proveedores para el select
$sql_proveedores = "SELECT id_proveedor, nombre FROM proveedores";
$result_proveedores = mysqli_query($conexion, $sql_proveedores);

// Si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_proveedor = $_POST['id_proveedor'];
    $productos = $_POST['producto'];
    $cantidades = $_POST['cantidad'];
    $metodo_pago = $_POST['metodo_pago'];
    $estado_pedido = 'pendiente';

    // Insertar en la tabla pedidos_proveedor
    foreach ($productos as $index => $producto) {
        $cantidad = $cantidades[$index];
        $sql_pedido = "INSERT INTO pedidos_proveedor (id_proveedor, producto, cantidad, metodo_pago, estado_pedido, fecha_pedido, id_sucursal) 
                       VALUES ('$id_proveedor', '$producto', '$cantidad', '$metodo_pago', '$estado_pedido', '$fecha_pedido', '$id_sucursal')";
        $insert_pedido = mysqli_query($conexion, $sql_pedido);
    }

    if ($insert_pedido) {
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <h1 class="mt-4">Crear Pedido</h1>
        <form action="" method="POST">
            <br>
            <h4>Datos del Pedido</h4>
            <br>
            <!-- Select para seleccionar el proveedor -->
            <div class="mb-3">
                <label for="id_proveedor" class="form-label">Proveedor</label>
                <select class="form-control" id="id_proveedor" name="id_proveedor" required>
                    <option value="">Seleccione un proveedor</option>
                    <?php while ($proveedor = mysqli_fetch_assoc($result_proveedores)) : ?>
                        <option value="<?= $proveedor['id_proveedor'] ?>"><?= $proveedor['nombre'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div><br>

            <!-- Producto y Cantidad -->
            <div class="mb-3 d-flex">
                <div class="me-3">
                    <label for="producto" class="form-label">Producto</label>
                    <input type="text" class="form-control" id="producto" name="producto[]" required>
                </div>
                <div>
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad[]" required>
                </div>
            </div>

            <!-- Botón para añadir más productos -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarProductos">
                Añadir más productos
            </button>

            <!-- Modal para añadir más productos -->
            <div class="modal fade" id="modalAgregarProductos" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">Añadir Productos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label for="cantidad_productos" class="form-label">¿Cuántos productos desea añadir?</label>
                            <input type="number" class="form-control" id="cantidad_productos" min="1" value="1">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="confirmarAgregar">Añadir</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campo de método de pago -->
            <div class="mb-3">
                <br>
                <label for="metodo_pago" class="form-label">Método de Pago</label>
                <select class="form-control" id="metodo_pago" name="metodo_pago" required>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
            </div><br><br><br>

            <!-- Botón para guardar el pedido -->
            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-success me-2">Guardar Pedido</button>
                <a href="pedidos.php" class="btn btn-secondary">Volver</a>
            </div>
        </form>
    </div>

    <!-- Script para manejar la adición de más productos -->
    <script>
        document.getElementById('confirmarAgregar').addEventListener('click', function() {
            const cantidadProductos = parseInt(document.getElementById('cantidad_productos').value);
            const container = document.querySelector('form');

            for (let i = 0; i < cantidadProductos; i++) {
                const productRow = document.createElement('div');
                productRow.classList.add('mb-3', 'd-flex');
                productRow.innerHTML = `
                    <div class="me-3">
                        <label for="producto" class="form-label">Producto</label>
                        <input type="text" class="form-control" name="producto[]" required>
                    </div>
                    <div>
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" name="cantidad[]" required>
                    </div>
                `;
                container.insertBefore(productRow, document.querySelector('.btn-primary'));
            }

            // Cerrar el modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregarProductos'));
            modal.hide();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($success): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Pedido añadido correctamente',
                text: 'El pedido ha sido guardado exitosamente.',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'pedidos.php';
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>