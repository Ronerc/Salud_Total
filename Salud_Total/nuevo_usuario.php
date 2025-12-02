<?php
include("conexion.php");
$message = ''; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = htmlspecialchars($_POST['nombre'] ?? '');
    $clave = htmlspecialchars($_POST['clave'] ?? '');
    $hash = password_hash($clave, PASSWORD_DEFAULT);


    
    $sql = "INSERT INTO usuarios (nombre, clave) 
            VALUES (?, ?)";
    
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        
        $stmt->bind_param("ss", 
            $nombre, 
            $hash,
        );

        
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>¡Éxito!</strong> El usuario ' . htmlspecialchars($nombre) . ' ' . ' ha sido registrado correctamente.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>¡Error!</strong> No se pudo registrar el usuario. Error: ' . $stmt->error . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>¡Error!</strong> Falló la preparación de la consulta: ' . $conn->error . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    }
}


$usuarios = [];

$result = $conn->query("SELECT id_usuario, nombre, clave FROM usuarios ORDER BY id_usuario DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
    $result->close();
}



?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light"> 
    <nav id="navbar-container"><?php include("navbar.php")?></nav>
    <div class="container mt-5 mb-5">
        <div class="card shadow-lg border-0">
            
            <div class="card-header bg-dark text-white p-4">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-person-plus-fill me-2"></i> Registro de Nuevo Usuario
                </h3>
                <p class="mb-0 opacity-75">Complete todos los campos obligatorios (*)</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="nuevo_usuario.php" method="POST"> <div class="row g-4">
                
                    <div class="col-md-6">
                            <label for="nombre" class="form-label fw-bold">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Nombre del usuario">
                    </div>
                        
                    <div class="col-md-6">
                            <label for="clave" class="form-label fw-bold">Contraseña *</label>
                            <input type="password" class="form-control" id="clave" name="clave" required placeholder="Contraseña del usuario">
                    </div>
                    <div class="col-12 text-end">
                            <a href="administracion.php" class="btn btn-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i> Guardar Usuario
                            </button>
                    </div>

                
                </form>
            </div>
        </div>
    </div>

    <br><hr><br>

     <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Contraseña</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
                        <?php if (empty($usuarios)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados aún.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <th scope="row"><?php echo htmlspecialchars($usuario['id_usuario'] ?? 'N/A'); ?></th>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['clave']); ?></td>
                                <td class="text-center">
                                    <a href="editar_usuario.php?id=<?php echo $usuario['id_usuario'] ?? ''; ?>" class="btn btn-sm btn-outline-success me-2" title="Editar">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <a href="eliminar.php?id=<?php echo $usuario['id_usuario'] ?? ''; ?>" class="btn btn-sm btn-outline-danger me-2" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
            <tbody>
                <tr> <td></td>
                    <td></td>
                    <td></td>
                    
                    <!-- <td class="text-center">
                        Botón Editar
                        <button class="btn btn-sm btn-outline-success btn-action me-2">
                            <i class="bi bi-pencil-square"></i>
                                <span>Editar</span>
                        </button>

                        Botón Eliminar
                         <a href="eliminar.php">Eliminar</a>
                        <button class="btn btn-sm btn-outline-danger btn-action">
                            <i class="bi bi-trash"></i>
                            <span>Eliminar</span>
                        </button>
                    </td> -->
                </tr>
            </tbody>
          </table>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>