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
    $codigoIngresado = implode('', array_map('trim', $_POST['codigo']));
    
    $sql = $con->prepare("SELECT codigo, expira FROM restablecimientos_contraseña WHERE email = ? ORDER BY id_contraseña_rest DESC LIMIT 1");
    $sql->execute([$email]);
    $row = $sql->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $codigoCorrecto = $row['codigo'];
        $expira = $row['expira'];

        if ($codigoIngresado == $codigoCorrecto && strtotime($expira) > time()) {
            // Código correcto y no ha expirado
            header("Location: actualizar_contraseña.php?email=" . urlencode($email));
            exit;
        } else {
            $error[] = "El código es incorrecto o ha expirado.";
        }
    } else {
        $error[] = "No se encontró una solicitud de recuperación para este correo.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verificar Código</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .code-input {
            width: 50px;
            height: 50px;
            margin: 5px;
            text-align: center;
            font-size: 24px;
            border: 2px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .code-container {
            display: flex;
            justify-content: center;
        }

        @media (max-width: 576px) {
            .code-input {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
        }
    </style>
    <script>
        function moveNext(current, nextFieldID) {
            if (current.value.length === current.maxLength) {
                document.getElementById(nextFieldID).focus();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Verificar Código</h4>
                    </div>
                    <div class="card-body">
                        <?php mostrarMensajes($error); ?>
                        
                        <form action="" method="post">
                            <div class="code-container">
                                <input type="text" name="codigo[]" class="code-input" maxlength="1" oninput="moveNext(this, 'code2')" id="code1" required>
                                <input type="text" name="codigo[]" class="code-input" maxlength="1" oninput="moveNext(this, 'code3')" id="code2" required>
                                <input type="text" name="codigo[]" class="code-input" maxlength="1" oninput="moveNext(this, 'code4')" id="code3" required>
                                <input type="text" name="codigo[]" class="code-input" maxlength="1" oninput="moveNext(this, 'code5')" id="code4" required>
                                <input type="text" name="codigo[]" class="code-input" maxlength="1" oninput="moveNext(this, 'code6')" id="code5" required>
                                <input type="text" name="codigo[]" class="code-input" maxlength="1" id="code6" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-3">Verificar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
