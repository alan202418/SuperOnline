<?php
require './config/config.php';
require './config/conexion.php';

// Verificar si se recibió el id de la compra
if (!isset($_GET['id_compra'])) {
    echo "No se especificó ninguna compra para devolución.";
    exit();
}

$id_compra = $_GET['id_compra'];

// Obtener el codigo_del_producto de la compra específica
$sql_get_codigo = "SELECT codigo_del_producto FROM compras WHERE id_compra = ?";
$stmt_get_codigo = $conexion->prepare($sql_get_codigo);
if ($stmt_get_codigo === false) {
    die("Error al preparar la consulta: " . $conexion->error);
}
$stmt_get_codigo->bind_param("i", $id_compra);
$stmt_get_codigo->execute();
$result_codigo = $stmt_get_codigo->get_result();

if ($result_codigo->num_rows > 0) {
    $row_codigo = $result_codigo->fetch_assoc();
    $codigo_del_producto = $row_codigo['codigo_del_producto'];
    
    // Consulta para obtener el total de los subtotales de todas las compras con el mismo codigo_del_producto y estado "compra en devolucion"
    $sql_get_subtotales = "SELECT SUM(subtotal) AS total_devolucion FROM compras WHERE codigo_del_producto = ? AND estado_compra = 'compra en devolucion'";
    $stmt_get_subtotales = $conexion->prepare($sql_get_subtotales);
    if ($stmt_get_subtotales === false) {
        die("Error al preparar la consulta para obtener subtotales: " . $conexion->error);
    }
    $stmt_get_subtotales->bind_param("s", $codigo_del_producto);
    $stmt_get_subtotales->execute();
    $result_subtotales = $stmt_get_subtotales->get_result();

    if ($result_subtotales->num_rows > 0) {
        $row_subtotales = $result_subtotales->fetch_assoc();
        $total_devolucion = $row_subtotales['total_devolucion'];
    } else {
        echo "No se encontraron compras para devolución.";
        exit();
    }
} else {
    echo "Código del producto no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Devolución de compra pagada</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <br>
    <br>
    <br>
    <br>

    <!-- Main content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Devolución de Compra Pagada</h4>
                    </div>
                    <div class="card-body">
                        <form action="procesar_devolucion.php" method="POST">
                            <input type="hidden" name="id_compra" value="<?php echo htmlspecialchars($id_compra); ?>">
                            <!-- Campo oculto para enviar el total de la devolución -->
                            <input type="hidden" name="total_devolucion" value="<?php echo htmlspecialchars($total_devolucion); ?>">
                            <div class="mb-3">
                                <label for="alias_cbu" class="form-label">Ingrese el alias o CBU de su tarjeta:</label>
                                <input type="text" id="alias_cbu" name="alias_cbu" class="form-control" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success">Enviar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
