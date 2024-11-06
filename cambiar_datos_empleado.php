<?php

require 'config/config.php';
require 'config/database.php';

$db = new Database();
$con = $db->conectar();

// Verificar si el usuario ha iniciado sesión y tiene el rol "empleado"
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'empleado') {
    header("Location: login.php");
    exit;
}

// Inicializar variable de errores
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevo_usuario = trim($_POST['nuevo_usuario']);
    $nueva_contraseña = trim($_POST['nueva_contraseña']);
    $confirmar_contraseña = trim($_POST['confirmar_contraseña']);
    
    // Validar los campos
    if (empty($nuevo_usuario) || empty($nueva_contraseña) || empty($confirmar_contraseña)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif ($nueva_contraseña !== $confirmar_contraseña) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Encriptar la nueva contraseña
        $hashed_password = password_hash($nueva_contraseña, PASSWORD_BCRYPT);
        
        // Actualizar los datos en la base de datos
        $sql = $con->prepare("UPDATE usuarios SET usuario = ?, contraseña = ?, usuario_defecto = NULL, contraseña_defecto = NULL WHERE id_usuario = ?");
        $result = $sql->execute([$nuevo_usuario, $hashed_password, $_SESSION['user_id']]);
        
        if ($result) {
            $success = 'Credenciales actualizadas con éxito. Ahora puede usar sus nuevas credenciales para iniciar sesión.';
            // Redirigir al área principal de empleados
            header("Location: login.php");
            exit;
        } else {
            $error = 'Error al actualizar las credenciales. Inténtelo de nuevo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Credenciales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>
    <div class="container mt-5">
        <h2>Cambiar Credenciales</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="cambiar_datos_empleado.php" method="POST">
            <div class="mb-3">
                <label for="nuevo_usuario" class="form-label">Nuevo Usuario</label>
                <input type="text" class="form-control" name="nuevo_usuario" id="nuevo_usuario" required>
            </div>
            <div class="mb-3">
                <label for="nueva_contraseña" class="form-label">Nueva Contraseña</label>
                <input type="password" class="form-control" name="nueva_contraseña" id="nueva_contraseña" required>
            </div>
            <div class="mb-3">
                <label for="confirmar_contraseña" class="form-label">Confirmar Nueva Contraseña</label>
                <input type="password" class="form-control" name="confirmar_contraseña" id="confirmar_contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Credenciales</button>
        </form>
    </div>
</body>
</html>
