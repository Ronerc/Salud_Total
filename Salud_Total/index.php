<?php
    include("validar_login.php");
    if (isset($_POST["login"])) {
    session_start();

    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    $datos = validar_login($usuario, $password);

    if ($datos) {
        $_SESSION["usuario"] = $usuario;
        $_SESSION["nombre_usuario"] = $datos["nombre"];  
        $_SESSION["id_usuario"] = $datos["id"];          

        header("Location: ./inicio.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
    
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://codepen.io/ig_design/full/KKVQpVP">
    <link rel="stylesheet" href="index.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Document</title>
</head>
<body class="bg-dark custom-background">
    
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 custom-card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-body text-center">
                <h1 class="card-title mb-4">Salud Total</h1>
                
                <form id="loginForm" action="index.php" method="POST">
                    <div class="mb-3">
                        <input type="text" id="usuario" name="usuario" class="form-control custom-input py-3" placeholder="Usuario" required>
                    </div>

                    <div class="mb-5">
                        <input type="password" id="contrasena" name="password" class="form-control custom-input py-3" placeholder="Contraseña" required>
                    </div>
                    <button type="submit" name="login" class="btn  w-50 py-2 mb-3 custom-btn-outline">Entrar</button>     
                    
                </form>

                <a href="#" class="text-decoration-none custom-link d-block mt-3">
                    Te olvidaste la contraseña?
                </a>
            </div>
        </div>
    </div>




    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
	
    
    
    
    