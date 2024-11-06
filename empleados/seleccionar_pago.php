<?php

// Establecer la zona horaria de Buenos Aires, Argentina
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Detalles de la conexión a la base de datos
$host = 'localhost'; // Cambia según tu configuración
$dbname = 'superonline'; // Nombre de la base de datos
$username = 'root'; // Usuario de la base de datos
$password = ''; // Contraseña de la base de datos

try {
    // Crear conexión a la base de datos usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Establecer el modo de error para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recibir el ID del usuario
    $id_usuario = $_GET['id_usuario'] ?? null;

    if (!$id_usuario) {
        echo "ID de usuario no proporcionado.";
        exit;
    }

    // Obtener la fecha y hora actual del sistema una sola vez
    $fechaHoraSistema = date('Y-m-d H:i:s');

    // Consulta para obtener los id_retirado que coinciden con la fecha_retirado exacta
    $sql = "SELECT id_retirado, subtotal FROM pedido_retirado WHERE fecha_retirado = :fechaActual";
    $stmt = $pdo->prepare($sql); // Preparar la consulta
    $stmt->bindParam(':fechaActual', $fechaHoraSistema); // Utilizar la variable inmutable
    $stmt->execute(); // Ejecutar la consulta
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener los resultados

    // Inicializar el monto total a pagar
    $montoTotal = 0;

    // Sumar todos los subtotales
    foreach ($resultados as $fila) {
        $montoTotal += $fila['subtotal'];
    }

} catch (PDOException $e) {
    // Mostrar un mensaje si ocurre un error
    die("Error en la conexión: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Método de Pago</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <h1 class="text-center mt-5">Seleccionar Método de Pago</h1>
        <br>
        <!-- Mostrar el monto total a pagar -->
        <h3>Monto a pagar: <?php echo number_format($montoTotal, 2); ?> </h3>

        <form id="paymentForm" class="mt-4">
            <div class="mb-3">
                <label for="paymentMethod" class="form-label">Método de Pago</label>
                <select id="paymentMethod" name="paymentMethod" class="form-select" required>
                    <option value="">Selecciona el método de pago</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
            </div>

            <div class="mb-3" id="montoContainer" style="display: none;">
                <label for="monto" class="form-label">Ingrese el monto pagado</label>
                <input type="number" id="monto" name="monto" class="form-control" placeholder="Ingrese el monto pagado">
            </div>

            <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>">

            <button type="submit" class="btn btn-primary">Continuar</button>
        </form>
    </div>

    <script>
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const montoContainer = document.getElementById('montoContainer');
        const montoInput = document.getElementById('monto');

        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'efectivo') {
                montoContainer.style.display = 'block';
                montoInput.required = true;
            } else {
                montoContainer.style.display = 'none';
                montoInput.required = false;
            }
        });

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener datos del formulario
            const paymentMethod = paymentMethodSelect.value;
            const monto = montoInput.value;
            const idUsuario = <?php echo $id_usuario; ?>;
            
            // Redirigir a comprobante.php con los datos seleccionados
            let url = `comprobante.php?id_usuario=${idUsuario}&metodo_pago=${paymentMethod}`;
            if (paymentMethod === 'efectivo' && monto) {
                url += `&monto_pagado=${monto}`;
            }

            // Redireccionar a la página del comprobante
            window.location.href = url;
        });
    </script>
</body>

</html>
