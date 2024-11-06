<?php
require './config/conexion.php';

// Verificar si se recibieron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_compra'], $_POST['alias_cbu'], $_POST['total_devolucion'])) {
    $id_compra = $_POST['id_compra'];
    $alias_cbu = $_POST['alias_cbu'];
    $total_devolucion = $_POST['total_devolucion']; // Recibir el total de la devolución

    // Preparar la consulta SQL para insertar la devolución
    $estado_devolucion = "pendiente"; 
    $sql = "INSERT INTO devoluciones (id_compra, cuenta_destinatario, estado_devolucion, total_devolucion) VALUES (?, ?, ?, ?)";

    // Preparar y enlazar la declaración
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issd", $id_compra, $alias_cbu, $estado_devolucion, $total_devolucion);

    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Devolución en proceso',
                    text: 'Su devolución estará en proceso.',
                    icon: 'info',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'historial_compras.php'; // Redirige a historial de compras
                });
            });
        </script>";
    } else {
        echo "Error al insertar la devolución: " . $stmt->error;
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conexion->close();

} else {
    echo "Datos insuficientes para procesar la devolución.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>
    
</body>
</html>
