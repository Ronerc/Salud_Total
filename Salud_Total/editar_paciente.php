<?php
session_start();
include("conexion.php");

if (isset($_POST['guardar'])) {
 
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: ID de paciente no válido o faltante.");
}
$id_paciente = $_GET['id'];

// 3. Consulta segura y manejo de resultados
$stmt_select = $conn->prepare("SELECT * FROM pacientes WHERE id_paciente = ?"); 
$stmt_select->bind_param("i", $id_paciente);
$stmt_select->execute();
$resultado = $stmt_select->get_result();

if ($resultado->num_rows > 0) {
    // El paciente fue encontrado.
    $paciente = $resultado->fetch_assoc();
} else {
   
    $paciente = [
        'id_paciente' => $id_paciente, 
        'nombre' => '', 
        'apellido' => '', 
        'dni' => '',
        'direccion' => '',
        'telefono' => '', 
        'correo_electronico' => '', 
        'fecha_de_nacimiento' => '', 
        'obra_social' => '',
    ];
}
$stmt_select->close();

if (isset($_POST['guardar'])) {

    $id = $_POST['id_paciente'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $dni = $_POST['dni'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo_electronico'];
    $fecha = $_POST['fecha_de_nacimiento'];
    $obra = $_POST['obra_social'];

    // 1) Verificar que el DNI no lo tenga otro paciente
    $check = $conn->prepare("SELECT id_paciente FROM pacientes WHERE dni = ? AND id_paciente != ?");
    $check->bind_param("si", $dni, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<div class='alert alert-danger'>Error: Ese DNI ya pertenece a otro paciente.</div>";
        $check->close();
    } else {
        $check->close();

        // 2) Actualizar datos
        $stmt = $conn->prepare("UPDATE pacientes 
            SET nombre=?, apellido=?, dni=?, direccion=?, telefono=?, correo_electronico=?, fecha_de_nacimiento=?, obra_social=?
            WHERE id_paciente=?");

        $stmt->bind_param("ssssssssi", 
            $nombre, $apellido, $dni, $direccion, $telefono,
            $correo, $fecha, $obra, $id
        );

        if ($stmt->execute()) {
            header("Location: pacientes.php?edit=ok");
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error al actualizar: " . $stmt->error . "</div>";
        }

        $stmt->close();
    }
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Paciente</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light"> 
    <nav id="navbar-container"><?php include("navbar.php")?></nav>
    <div class="container mt-5 mb-5">
        <div class="card shadow-lg border-0">
            
            <div class="card-header bg-dark text-white p-4 text-center">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-person-lines-fill me-2"></i> Editar Datos del Paciente
                </h3>
                <p class="mb-0 opacity-75">Modifique la información necesaria y guarde los cambios.</p>
            </div>
            
            <div class="card-body p-4 p-md-5">
                <form action="editar_paciente.php?id=<?= $id_paciente ?>" method="POST">

                    
                    <input type="hidden" name="id_paciente" value="<?= $paciente['id_paciente'] ?>">
                    
                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label for="nombre" class="form-label fw-bold">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $paciente['nombre'] ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="apellido" class="form-label fw-bold">Apellido *</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" value="<?= $paciente['apellido'] ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label for="dni" class="form-label fw-bold">DNI *</label>
                            <input type="text" class="form-control" id="dni" name="dni" value="<?= $paciente['dni'] ?>" required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="telefono" class="form-label fw-bold">Teléfono *</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= $paciente['telefono'] ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label for="fecha_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_de_nacimiento" name="fecha_de_nacimiento" value="<?= $paciente['fecha_de_nacimiento'] ?>" required>
                        </div>
                        
                        <div class="col-md-8">
                            <label for="direccion" class="form-label fw-bold">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="<?= $paciente['direccion'] ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label for="obra_social" class="form-label fw-bold">Obra Social</label>
                            <input type="text" class="form-control" id="obra_social" name="obra_social" value="<?= $paciente['obra_social'] ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="correo_electronico" class="form-label fw-bold">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" value="<?= $paciente['correo_electronico'] ?>" required>
                        </div>
                        
                        <div class="col-12"><hr class="mt-4 mb-2"></div>

                        <div class="col-12 text-end">
                            <a href="pacientes.php" class="btn btn-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                            <button type="submit" name="guardar" class="btn btn-success">
                                <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                            </button>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>