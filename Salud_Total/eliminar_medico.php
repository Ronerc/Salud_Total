<?php
session_start();
include("conexion.php");
include("navbar.php");

// Validar ID por GET para mostrar confirmación
if (!isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: administracion.php");
    exit;
}

$alert = '';

// Si viene por POST, procesar la eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        header("Location: administracion.php");
        exit;
    }

    // comprobar si existen turnos asociados
    $chk = $conn->prepare("SELECT COUNT(*) AS cnt FROM turnos WHERE id_medicos = ?");
    $chk->bind_param("i", $id);
    $chk->execute();
    $res = $chk->get_result()->fetch_assoc();
    $chk->close();

    if (!empty($res['cnt'])) {
        $alert = '<div class="alert alert-warning">No se puede eliminar el médico porque tiene <strong>' . intval($res['cnt']) . '</strong> turnos asociados. Elimine los turnos primero.</div>';
    } else {
        // realizar eliminación en transacción
        $conn->begin_transaction();
        try {
            // eliminar relaciones en medicos_especialidades
            $d1 = $conn->prepare("DELETE FROM medicos_especialidades WHERE id_medico = ?");
            $d1->bind_param("i", $id);
            $d1->execute();
            $d1->close();

            // eliminar médico
            $d2 = $conn->prepare("DELETE FROM medicos WHERE id_medicos = ?");
            $d2->bind_param("i", $id);
            $d2->execute();
            $d2->close();

            $conn->commit();
            header("Location: administracion.php?mensaje=elim_ok");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $alert = '<div class="alert alert-danger">Error al eliminar el médico: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}

// Si llegó por GET mostramos la confirmación
$id = intval($_GET['id'] ?? ($_POST['id'] ?? 0));
if ($id <= 0) {
    header("Location: administracion.php");
    exit;
}

$stmt = $conn->prepare(
    "SELECT m.id_medicos, m.nombre, m.apellido, e.especialidad
     FROM medicos m
     LEFT JOIN medicos_especialidades me ON m.id_medicos = me.id_medico
     LEFT JOIN especialidades e ON me.id_especialidad = e.id_especialidad
     WHERE m.id_medicos = ? LIMIT 1"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$med = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$med) {
    header("Location: administracion.php?mensaje=no_encontrado");
    exit;
}

?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Eliminar Médico</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color:#f2f7ff">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-body">
            <h3 class="card-title">Eliminar Médico</h3>
            <?php echo $alert; ?>
            <p class="text-muted">Confirma que deseas eliminar al siguiente médico:</p>

            <ul class="list-group mb-3">
              <li class="list-group-item"><strong>Nombre:</strong> <?php echo htmlspecialchars($med['nombre'] . ' ' . $med['apellido']); ?></li>
              <li class="list-group-item"><strong>Especialidad:</strong> <?php echo htmlspecialchars($med['especialidad'] ?? 'N/A'); ?></li>
            </ul>

            <form method="POST">
              <input type="hidden" name="id" value="<?php echo intval($id); ?>">
              <?php if (empty($res['cnt'])): ?>
                <button type="submit" name="eliminar" class="btn btn-danger">Eliminar Médico</button>
              <?php else: ?>
                <a href="turnos.php?medico=<?php echo intval($id); ?>" class="btn btn-outline-primary">Ver Turnos</a>
              <?php endif; ?>
              <a href="administracion.php" class="btn btn-secondary">Cancelar</a>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
