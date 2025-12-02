<?php
include("conexion.php");
include("navbar.php");

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: turnos.php");
    exit();
}

$id = htmlspecialchars($_GET['id']);

// Obtener turno
$sql = "SELECT t.*, p.nombre AS nombre_paciente, p.apellido AS apellido_paciente, m.nombre AS nombre_medico, m.apellido AS apellido_medico
        FROM turnos t
        INNER JOIN pacientes p ON t.id_paciente = p.id_paciente
        INNER JOIN medicos m ON t.id_medicos = m.id_medicos
        WHERE t.id_turnos = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: turnos.php?mensaje=error");
    exit();
}
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: turnos.php?mensaje=no_encontrado");
    exit();
}
$turno = $res->fetch_assoc();
$stmt->close();

// Manejar POST
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
  if (isset($_POST['eliminar'])) {
    $stmt = $conn->prepare("DELETE FROM turnos WHERE id_turnos = ?");
    if ($stmt) {
      $stmt->bind_param("i", $id);
      if ($stmt->execute()) {
        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">Turno eliminado correctamente.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
      } else {
        $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error al eliminar el turno: ' . htmlspecialchars($stmt->error, ENT_QUOTES) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
      }
      $stmt->close();
    } else {
      $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Error en la consulta de eliminación.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }

    // Después de eliminar, redirigir al listado
    header("Location: turnos.php?mensaje=elim_ok");
    exit();
  }

  if (isset($_POST['cancelar'])) {
    header("Location: turnos.php");
    exit();
  }
}
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Eliminar Turno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title">Eliminar Turno</h3>
            <p class="text-muted">Confirma que deseas eliminar el siguiente turno:</p>

            <ul class="list-group mb-3">
              <li class="list-group-item"><strong>Paciente:</strong> <?php echo htmlspecialchars($turno['nombre_paciente'] . ' ' . $turno['apellido_paciente']); ?></li>
              <li class="list-group-item"><strong>Médico:</strong> <?php echo htmlspecialchars($turno['nombre_medico'] . ' ' . $turno['apellido_medico']); ?></li>
              <li class="list-group-item"><strong>Fecha:</strong> <?php echo htmlspecialchars($turno['horario_de_turno']); ?></li>
              <li class="list-group-item"><strong>Recordatorio:</strong> <?php echo htmlspecialchars($turno['recordatorio'] ?? ''); ?></li>
            </ul>

            <form method="POST">
              <input type="hidden" name="id_turno" value="<?php echo intval($id); ?>">
              <button type="submit" name="eliminar" class="btn btn-danger">Eliminar Turno</button>
              <button type="submit" name="cancelar" class="btn btn-secondary">Cancelar</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>