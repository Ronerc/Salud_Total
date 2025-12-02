<?php
include("conexion.php");

// CARGAR DATOS DEL USUARIO
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {

    $id = intval($_GET["id"]); // Seguridad

    // Consulta segura
    $sql = "SELECT * FROM usuarios WHERE id_usuario = $id";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc(); 
    } else {
        echo "<h3>Usuario no encontrado</h3>";
        exit;
    }

} else {
    echo "<h3>ID de usuario no especificado o inválido</h3>";
    exit;
}

// ELIMINAR USUARIO
if (isset($_POST["eliminar"])) {
    $sql = "DELETE FROM usuarios WHERE id_usuario = $id";
    $conn->query($sql);
    volver();
}

// CANCELAR
if (isset($_POST["cancelar"])) {     
    volver();
} 

function volver(){
    header("Location: nuevo_usuario.php");
    exit();       
}
?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>    
    <div class="container text-center">
      <div class="row">       
        <div class="col align-self-center">
          <h1>Eliminar un Usuario</h1><hr>
          <form class="row g-3" method="POST">
            <div class="col-md-4">
              <label for="nombre" class="form-label">Nombre de Usuario</label>
              <input type="text" name="nombre" class="form-control" id="nombre" value="<?php echo $usuario['nombre']; ?>">
            </div>           
              <div class="col-md-6">
                <button type="submit" name="eliminar" class="btn btn-danger">Eliminar el Usuario</button>
              </div>
              <div class="col-md-6">
                <button type="submit" name="cancelar" class="btn btn-primary">Cancelar</button>
              </div>
            
          </form>
        </div>        
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>