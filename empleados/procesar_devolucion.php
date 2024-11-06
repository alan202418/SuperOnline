<?php 
require '../config/config.php';
require '../config/conexion.php';

// Incluir PHPMailer (Asegúrate de haberlo instalado correctamente)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';  // Asegúrate de que la ruta a PHPMailer sea correcta

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <!-- Cargar solo SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
if (isset($_GET['id_compra']) && isset($_GET['codigo_del_producto'])) {
    $id_compra = $_GET['id_compra'];
    $codigo_producto = $_GET['codigo_del_producto'];

    // Comprobar si existe el id_compra en la tabla devoluciones antes de actualizar
    $sql_check = "SELECT * FROM devoluciones WHERE id_compra = '$id_compra'";
    $result_check = mysqli_query($conexion, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Realizar el UPDATE solo si existe la fila
        $sql_update = "UPDATE devoluciones SET estado_devolucion = 'devolucion enviada' WHERE id_compra = '$id_compra'";
        $result_update = mysqli_query($conexion, $sql_update);

        if ($result_update) {
            // Obtener el codigo_del_producto y el id_usuario del id_compra actual
            $sql_get_info = "SELECT codigo_del_producto, id_usuario FROM compras WHERE id_compra = '$id_compra'";
            $result_get_info = mysqli_query($conexion, $sql_get_info);
            $row_info = mysqli_fetch_assoc($result_get_info);
            $codigo_producto_actual = $row_info['codigo_del_producto'];
            $id_usuario = $row_info['id_usuario'];

            // Buscar el email del usuario en la tabla usuarios
            $sql_get_email = "SELECT email FROM usuarios WHERE id_usuario = '$id_usuario'";
            $result_get_email = mysqli_query($conexion, $sql_get_email);

            if (mysqli_num_rows($result_get_email) > 0) {
                $row_email = mysqli_fetch_assoc($result_get_email);
                $email_usuario = $row_email['email'];

                // Enviar el correo utilizando PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // Configuración del servidor de correo
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';  // Cambia al servidor SMTP que estés usando
                    $mail->SMTPAuth = true;
                    $mail->Username = 'alangod2020@gmail.com';  // Cambia a tu cuenta de correo
                    $mail->Password = 'yybg nrwp lrvh yuts';  // Cambia a la contraseña de tu cuenta de correo
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // Configuración del correo
                    $mail->setFrom('alangod2020@gmail.com', 'SuperOnline');
                    $mail->addAddress($email_usuario);  // Correo del destinatario

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = 'Su reembolso ha sido procesado';
                    $mail->Body    = 'Estimado cliente, su reembolso ha sido procesado y llegará en unos minutos.';

                    // Enviar el correo
                    $mail->send();
                    
                } catch (Exception $e) {
                    echo "El correo no pudo ser enviado. Error de Mailer: {$mail->ErrorInfo}";
                }
            }

            // Actualizar todas las compras con el mismo codigo_del_producto
            $sql_update_compras = "UPDATE compras SET estado_compra = 'compra ya reembolsada' WHERE codigo_del_producto = '$codigo_producto_actual'";
            $result_update_compras = mysqli_query($conexion, $sql_update_compras);

            if ($result_update_compras) {
                // Nuevo UPDATE en la tabla 'pedido_realizado' para actualizar 'estado_compra'
                $sql_update_pedido_realizado = "UPDATE pedido_realizado SET estado_compra = 'Compra Reembolsada' WHERE id_compra = '$id_compra'";
                $result_update_pedido_realizado = mysqli_query($conexion, $sql_update_pedido_realizado);

                if ($result_update_pedido_realizado) {
                    echo "
                        <script>
                            Swal.fire({
                                icon: 'success',
                                title: 'Éxito',
                                text: 'La devolución ha sido enviada, el estado de las compras ha sido actualizado, y el correo de reembolso ha sido enviado.'
                            }).then(function() {
                                window.location.href = 'pedidos.php';
                            });
                        </script>";
                } else {
                    echo "
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un error al actualizar el estado del pedido en \"pedido_realizado\".'
                            }).then(function() {
                                window.location.href = 'devolucion_detalles.php?id_compra=$id_compra&codigo_del_producto=$codigo_producto';
                            });
                        </script>";
                }
            } else {
                echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un error al actualizar el estado de las compras.'
                        }).then(function() {
                            window.location.href = 'devolucion_detalles.php?id_compra=$id_compra&codigo_del_producto=$codigo_producto';
                        });
                    </script>";
            }
        }
    } else {
        echo "
              <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'El id_compra no existe en la tabla devoluciones.'
                }).then(function() {
                    window.location.href = 'devolucion_detalles.php?id_compra=$id_compra&codigo_del_producto=$codigo_producto';
                });
              </script>";
    }
} else {
    echo "
          <script>
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Parámetros inválidos para la devolución.'
            }).then(function() {
                window.location.href = 'pedidos.php';
            });
          </script>";
}
?>
</body>
</html>
