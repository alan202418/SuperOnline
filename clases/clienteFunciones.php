
<?php  

function esNulo(array $parametro){
    foreach ($parametro as $parametro){
       if(strlen(trim($parametro)) < 1){
        return true;
       }
    }
    return false;

}

function esEmail($email){
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        return true;
    }
    return false;
}



function registrarCliente(array $datos, $con){
    $sql = $con->prepare("INSERT INTO usuarios (nombre, apellido, email, usuario, contraseña, rol ) VALUES (?,?,?,?,?,'cliente')");
    if($sql->execute($datos)){
        return true;
    }
    return false;
}




function  usuarioExiste($usuario, $con){
    $sql = $con->prepare("SELECT id_usuario FROM usuarios WHERE usuario LIKE ? LIMIT 1");
    $sql->execute([$usuario]);
    if($sql->fetchColumn() > 0 ){
        return true;
    }
    return false;
  
}

function  emailExiste($email, $con){
    $sql = $con->prepare("SELECT id_usuario FROM usuarios WHERE email LIKE ? LIMIT 1");
    $sql->execute([$email]);
    if($sql->fetchColumn() > 0 ){
        return true;
    }
    return false;
  
}

function mostrarMensajes(array $error){
    if(count($error) > 0 ){
        echo '<div class="alert alert-warning alert-dismissible fade show" role="alert"><ul>';
        foreach($error as $error){
            echo '<li>'.$error. '</li>';
        }
        echo '</ul>';
        echo ' <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

        
    }
}

function login($usuario, $contraseña, $con) {
    // Consulta para obtener todos los datos necesarios del usuario.
    $sql = $con->prepare("SELECT id_usuario, usuario, contraseña, usuario_defecto, contraseña_defecto, rol FROM usuarios WHERE usuario = ? OR usuario_defecto = ? LIMIT 1");
    $sql->execute([$usuario, $usuario]);

    if ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        // Verificar si tiene valores en usuario_defecto y contraseña_defecto (usuario recién registrado)
        if ($row['rol'] === 'empleado' && !empty($row['usuario_defecto']) && !empty($row['contraseña_defecto'])) {
            // Comparar con usuario_defecto y contraseña_defecto
            if ($usuario === $row['usuario_defecto'] && password_verify($contraseña, $row['contraseña_defecto'])) {
                session_start();
                $_SESSION['user_id'] = $row['id_usuario'];
                $_SESSION['user_name'] = $row['usuario'];
                $_SESSION['user_role'] = $row['rol'];

                // Redirigir a la página de actualización de datos
                header("Location: /cambiar_datos_empleado.php");
                exit;
            }
        } 
        // Si no tiene valores en usuario_defecto y contraseña_defecto, utilizar usuario y contraseña normales
        elseif (password_verify($contraseña, $row['contraseña'])) {
            session_start();
            $_SESSION['user_id'] = $row['id_usuario'];
            $_SESSION['user_name'] = $row['usuario'];
            $_SESSION['user_role'] = $row['rol'];

            // Redirecciones basadas en el rol del usuario
            if ($row['rol'] === 'cliente') {
                header("Location: index.php");
            } elseif ($row['rol'] === 'empleado') {
                header("Location: /empleados/index.php");
            } elseif ($row['rol'] === 'gerente_general') {
                header("Location: /gerente_general/index.php");
            } elseif ($row['rol'] === 'gerente') {
                header("Location: /gerente/index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }
    return 'El usuario y/o contraseña son incorrectos.';
}





?>
