<?php
require 'config/config.php';
require 'config/database.php';

$db = new Database();
$con = $db->conectar();

$data = json_decode(file_get_contents("php://input"), true);
$id_sucursal = $data['id_sucursal'];

$productos = isset($_SESSION['carrito']['productos']) ? $_SESSION['carrito']['productos'] : null;
$resultado = ['success' => true, 'productos' => []];

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        // Obtener el nombre del producto por su id
        $sql = $con->prepare("SELECT nombre FROM productos WHERE id_producto = ?");
        $sql->execute([$clave]);
        $producto = $sql->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $nombre_producto = $producto['nombre'];

            // Verificar el stock en la sucursal seleccionada
            $sql_stock = $con->prepare("SELECT stock FROM productos WHERE nombre = ? AND id_sucursal = ?");
            $sql_stock->execute([$nombre_producto, $id_sucursal]);
            $producto_stock = $sql_stock->fetch(PDO::FETCH_ASSOC);

            if (!$producto_stock || $producto_stock['stock'] < $cantidad) {
                $resultado['success'] = false;
                $resultado['productos'][] = $nombre_producto;
            }
        }
    }
}

echo json_encode($resultado);
?>
