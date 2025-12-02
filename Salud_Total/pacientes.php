<?php
include("conexion.php"); 
include("navbar.php"); 

$message = ''; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = htmlspecialchars($_POST['nombre'] ?? '');
    $apellido = htmlspecialchars($_POST['apellido'] ?? '');
    $dni = htmlspecialchars($_POST['dni'] ?? '');
    $fecha_de_nacimiento = htmlspecialchars($_POST['fecha_de_nacimiento'] ?? '');
    $telefono = htmlspecialchars($_POST['telefono'] ?? '');
    $correo_electronico = htmlspecialchars($_POST['correo_electronico'] ?? '');
    $direccion = htmlspecialchars($_POST['direccion'] ?? '');
    $obra_social = htmlspecialchars($_POST['obra_social'] ?? '');


    
    
      // 1) CONTROLAR SI EL DNI YA EXISTE
    $check = $conn->prepare("SELECT dni FROM pacientes WHERE dni = ?");
    $check->bind_param("s", $dni);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>¡Error!</strong> El DNI ya está registrado.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        $check->close();
    } else {
        $check->close();

        // 2) INSERTAR EL PACIENTE
        $sql = "INSERT INTO pacientes (nombre, apellido, dni, fecha_de_nacimiento, telefono, correo_electronico, direccion, obra_social) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssssssss", 
                $nombre, 
                $apellido, 
                $dni, 
                $fecha_de_nacimiento, 
                $telefono, 
                $correo_electronico, 
                $direccion, 
                $obra_social
            );

            if ($stmt->execute()) {
                $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>¡Éxito!</strong> Paciente registrado correctamente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
            } else {
                $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>¡Error!</strong> No se pudo registrar el paciente. Error: ' . $stmt->error . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
            }
            $stmt->close();
        }
    }
}

$pacientes = [];

$result = $conn->query("SELECT id_paciente, nombre, apellido, dni, telefono FROM pacientes ORDER BY id_paciente DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pacientes[] = $row;
    }
    $result->close();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="pacientes.css" rel="stylesheet">
    <title>Gestión de Pacientes</title>
</head>

<body>

    <div class="container mt-4">

        <?php echo $message; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-0">Pacientes</h1>
                <p class="text-muted small mb-0">Gestión de pacientes registrados</p>
            </div>
            <a href="nuevo_paciente.php" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Paciente
            </a>
        </div>
        
        <div class="mb-4">
            <div class="input-group rounded-pill border border-primary">
                <span class="input-group-text bg-white border-0 rounded-start-pill">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" class="form-control border-0 py-2 rounded-end-pill"
                    placeholder="Buscar por nombre, DNI o teléfono..." aria-label="Buscar pacientes">
            </div>
        </div>

        <div class="card p-3 shadow-lg border-0 rounded-3">
            
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col"># ID</th>
                            <th scope="col">Nombre Completo</th>
                            <th scope="col">DNI</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Última Consulta</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pacientes)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay pacientes registrados aún.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pacientes as $paciente): ?>
                            <tr>
                                <th scope="row"><?php echo htmlspecialchars($paciente['id_paciente'] ?? 'N/A'); ?></th>
                                <td><?php echo htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['dni']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['telefono']); ?></td>
                                <td>N/A</td> 
                                <td class="text-center">
                                    <a href="editar_paciente.php?id=<?php echo ($paciente['id_paciente'] ?? ''); ?>" class="btn btn-sm btn-outline-success me-2" title="Editar">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <a href="eliminar_paciente.php?id=<?php echo ($paciente['id_paciente'] ?? ''); ?>" class="btn btn-sm btn-outline-danger me-2" title="Eliminar">
                                        <i class="bi bi-trash-fill"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>