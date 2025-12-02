<?php
include("conexion.php");
include("navbar.php");

// Función para volver
function volver(){
    header("Location: pacientes.php");
    exit;
}

// Validar ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) volver();
$id = intval($_GET["id"]);

// Obtener datos del paciente
$stmt = $conn->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$paciente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$paciente) {
    echo "<h3>Paciente no encontrado</h3>";
    exit;
}

// Contar turnos
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM turnos WHERE id_paciente = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$turnos = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
$stmt->close();

// Procesar eliminación
if (isset($_POST["eliminar"])) {

    // Si tiene turnos, no se puede borrar
    if ($turnos > 0) {
        $alert = "
        <div class='alert alert-warning alert-dismissible fade show'>
            <strong>No permitido:</strong> El paciente posee $turnos turno(s) asociado(s).
            <a href='turnos.php' class='alert-link'>Ver turnos</a>
            <button class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    } else {
        // Eliminar
        $stmt = $conn->prepare("DELETE FROM pacientes WHERE id_paciente = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        volver();
    }
}

if (isset($_POST["cancelar"])) volver();
?>


<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Eliminar Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container text-center mt-5">
        <h1>Eliminar Paciente</h1>
        <hr>

        <?= $alert ?? '' ?>

        <form method="POST" class="mt-4">
            <div class="col-md-4">
              <label for="nombre" class="form-label">Paciente</label>
              <input type="text" name="nombre" class="form-control" id="nombre" value="<?php echo $paciente['nombre']; ?>">
            </div> 

            <?php if ($turnos == 0): ?>
                <button type="submit" name="eliminar" class="btn btn-danger">Eliminar Paciente</button>
            <?php else: ?>
                <button class="btn btn-danger" disabled>
                    No se puede eliminar (tiene <?= $turnos ?> turno/s)
                </button>
            <?php endif; ?>

            <button type="submit" name="cancelar" class="btn btn-secondary ms-2">Cancelar</button>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
