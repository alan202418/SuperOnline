<?php
require 'config/config.php';
require 'config/database.php';
require 'clases/clienteFunciones.php';

$db = new Database();
$con = $db->conectar();

$error = [];
$mensaje = "";
$email = $_GET['email'] ?? '';

if (!empty($_POST)) {
    $nuevaContraseña = trim($_POST['nueva_contraseña']);

    if (esNulo([$nuevaContraseña])) {
        $error[] = "Debe ingresar una nueva contraseña";
    } else {
        $nuevaContraseñaHash = password_hash($nuevaContraseña, PASSWORD_BCRYPT);

        $sql = $con->prepare("UPDATE usuarios SET contraseña = ? WHERE email = ?");
        $sql->execute([$nuevaContraseñaHash, $email]);

        // Limpiar códigos anteriores
        $sql = $con->prepare("DELETE FROM restablecimientos_contraseña WHERE email = ?");
        $sql->execute([$email]);

        $mensaje = "Contraseña actualizada correctamente. Ahora puedes <a href='login.php'>iniciar sesión</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Actualizar Contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Actualizar Contraseña</h4>
                    </div>
                    <div class="card-body">
                        <?php mostrarMensajes($error); ?>
                        <?php if ($mensaje) { echo "<div class='alert alert-success'>$mensaje</div>"; } ?>
                        
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="nueva_contraseña" class="form-label">Nueva Contraseña:</label>
                                <input type="password" name="nueva_contraseña" id="nueva_contraseña" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Actualizar Contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

