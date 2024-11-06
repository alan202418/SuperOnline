<?php
require '../config/config.php';
require '../config/conexion.php';

// Configurar la zona horaria de Buenos Aires
date_default_timezone_set('America/Argentina/Buenos_Aires');
$fecha_pedido = date("Y-m-d H:i:s"); // Fecha y hora actual de Buenos Aires

$success = false; // Variable para controlar el estado de éxito

// Si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sucursal_remitente = $_POST['sucursal_remitente'];
    $sucursal_destinatario = $_POST['sucursal_destinatario'];
    $productos = $_POST['producto'];
    $cantidades = $_POST['cantidad'];
    $estado_pedido = 'pendiente';

    // Obtener los IDs de las sucursales remitente y destinatario
    $sql_remitente = "SELECT id_sucursal FROM sucursales WHERE nombre_sucursal = '$sucursal_remitente'";
    $result_remitente = mysqli_query($conexion, $sql_remitente);
    $id_remitente = mysqli_fetch_assoc($result_remitente)['id_sucursal'];

    $sql_destinatario = "SELECT id_sucursal FROM sucursales WHERE nombre_sucursal = '$sucursal_destinatario'";
    $result_destinatario = mysqli_query($conexion, $sql_destinatario);
    $id_destinatario = mysqli_fetch_assoc($result_destinatario)['id_sucursal'];

    if ($id_remitente && $id_destinatario) {
        // Insertar en la tabla pedidos_gerente_general y actualizar stock
        foreach ($productos as $index => $producto) {
            $cantidad = (int)$cantidades[$index];

            // Obtener el ID del producto y verificar el stock en la sucursal remitente
            $sql_producto = "SELECT id_producto, stock FROM productos WHERE nombre = '$producto' AND id_sucursal = '$id_remitente'";
            $result_producto = mysqli_query($conexion, $sql_producto);
            $producto_data = mysqli_fetch_assoc($result_producto);

            if ($producto_data && $producto_data['stock'] >= $cantidad) {
                $id_producto = $producto_data['id_producto'];
                $nuevo_stock = $producto_data['stock'] - $cantidad;

                // Insertar en la tabla de pedidos
                $sql_pedido = "INSERT INTO pedido_gerente_general (sucursal_remitente, sucursal_destinatario, producto, cantidad_remitente, estado_pedido, fecha_pedida) 
                               VALUES ('$sucursal_remitente', '$sucursal_destinatario', '$producto', '$cantidad', '$estado_pedido', '$fecha_pedido')";
                $insert_pedido = mysqli_query($conexion, $sql_pedido);

                // Actualizar el stock del producto en la sucursal remitente
                $sql_update_stock = "UPDATE productos SET stock = '$nuevo_stock' WHERE id_producto = '$id_producto'";
                $update_stock = mysqli_query($conexion, $sql_update_stock);

                if (!$insert_pedido || !$update_stock) {
                    $success = false;
                    break;
                }

                $success = true;
            } else {
                echo "<script>alert('No hay suficiente stock de $producto en la sucursal remitente.');</script>";
            }
        }
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
                <label for="sucursales" class="form-label">Sucursales</label>
                <select class="form-control" id="sucursal_remitente" name="sucursal_remitente" required>
                    <option value="">Seleccione una sucursal para pedir</option>
                    <option value="SuperOnline Central">SuperOnline Central</option>
                    <option value="SuperOnline I">SuperOnline I</option>
                    <option value="SuperOnline II">SuperOnline II</option>
                    <option value="SuperOnline III">SuperOnline III</option>

                </select>
                <br>
                <select class="form-control" id="sucursal_destinatario" name="sucursal_destinatario" required>
                    <option value="">Seleccione una sucursal para enviar</option>
                    <option value="SuperOnline Central">SuperOnline Central</option>
                    <option value="SuperOnline I">SuperOnline I</option>
                    <option value="SuperOnline II">SuperOnline II</option>
                    <option value="SuperOnline III">SuperOnline III</option>

                </select>
            </div><br>



            <!-- Producto y Cantidad -->
            <div class="mb-3 d-flex">
                <div class="me-3">
                    <label for="producto" class="form-label">Producto</label>
                    <input type="text" class="form-control" id="producto" name="producto[]" required>
                </div>
                <div>
                    <label for="cantidad" class="form-label">Cantidad a pedir</label>
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

            <br><br><br>

            <!-- Botón para guardar el pedido -->
            <div class="d-flex justify-content-start">
                <button type="submit" class="btn btn-success me-2">Guardar Pedido</button>
                <a href="pedidos_sucursales.php" class="btn btn-secondary">Volver</a>
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
                    window.location.href = 'pedidos_sucursales.php';
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>