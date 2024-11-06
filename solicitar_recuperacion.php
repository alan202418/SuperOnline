<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';
require 'vendor/autoload.php'; // Asegúrate de incluir PHPMailer correctamente

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = new Database();
$con = $db->conectar();

$error = [];
$mensaje = "";

if (!empty($_POST)) {
    $email = trim($_POST['email']);

    if (esNulo([$email])) {
        $error[] = "Debe proporcionar un correo electrónico";
    } else if (!esEmail($email)) {
        $error[] = "Debe proporcionar un correo electrónico válido";
    } else if (!emailExiste($email, $con)) {
        $error[] = "No se encontró una cuenta con ese correo electrónico";
    } else {
        // Generar código de 6 dígitos
        $codigo = rand(100000, 999999);
        $expira = date("Y-m-d H:i:s", strtotime('+10 minutes'));

        $sql = $con->prepare("INSERT INTO restablecimientos_contraseña (email, codigo, expira) VALUES (?, ?, ?)");
        $sql->execute([$email, $codigo, $expira]);

        // Enviar el correo con PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Cambia a tu servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'alangod2020@gmail.com'; // Tu correo SMTP
            $mail->Password = 'yybg nrwp lrvh yuts'; // Tu contraseña SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Configurar remitente y destinatario
            $mail->setFrom('alangod2020@gmail.com', 'SuperOnline');
            $mail->addAddress($email);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Código de verificación para restablecer la contraseña';
            $mail->Body    = "<p>Tu código de verificación para restablecer la contraseña es: <b>$codigo</b></p>";

            $mail->send();
            $mensaje = "Se ha enviado un código de verificación a tu correo.";
            header("Location: verificar_codigo.php?email=" . urlencode($email)); // Redirige para ingresar el código
            exit;
        } catch (Exception $e) {
            $error[] = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Recuperar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Fondo claro */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px; /* Espaciado para pantallas pequeñas */
        }

        .card {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px; /* Ajuste para que sea más ancho */
            width: 100%;
        }

        @media (max-width: 576px) {
            .card {
                max-width: 95%; /* Más ancho en pantallas pequeñas */
            }
        }

        @media (min-width: 768px) {
            .card {
                max-width: 700px; /* Más ancho en pantallas medianas */
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="card-header text-center">
            <h4>Recuperar Contraseña</h4>
        </div>
        <div class="card-body">
            <?php mostrarMensajes($error); ?>
            <?php if ($mensaje) { echo "<div class='alert alert-success'>$mensaje</div>"; } ?>
            
            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Enviar Código</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


