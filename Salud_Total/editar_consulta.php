<?php
session_start();
include("conexion.php");
include("navbar.php");

if (!isset($_GET['id'])) {
    header("Location: historial_clinico.php");
    exit;
}

$id = intval($_GET['id']);

// obtener datos de la consulta
$stmt = $conn->prepare("SELECT * FROM historial_clinico_del_paciente WHERE id_historial_clinico = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$consulta = $res->fetch_assoc();
$stmt->close();

if (!$consulta) {
    header("Location: historial_clinico.php");
    exit;
}

// obtener paciente asociado (tabla intermedia)
$stmt2 = $conn->prepare("SELECT id_paciente FROM pacientes_historial_clinico_del_paciente WHERE id_historial_clinico = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res2 = $stmt2->get_result();
$row2 = $res2->fetch_assoc();
$current_id_paciente = $row2['id_paciente'] ?? null;
$stmt2->close();

// listas para selects
$pacientes = $conn->query("SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY apellido, nombre");
$medicos = $conn->query("SELECT id_medicos AS id_medico, nombre, apellido FROM medicos ORDER BY apellido, nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Consulta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="inicio.css" />
</head>
<body class="bg-light">


<div class="container mt-5 mb-5">
  <div class="card shadow-lg border-0">
    <div class="card-header bg-dark text-white p-4">
      <h3 class="fw-bold mb-0">Editar Consulta</h3>
    </div>

    <div class="card-body p-4">
      <form action="update_consulta.php" method="POST">
        <input type="hidden" name="id_historial_clinico" value="<?php echo $consulta['id_historial_clinico']; ?>">

        <div class="row g-4">
          <div class="col-md-6">
            <label class="form-label fw-bold">Paciente</label>
            <select name="id_paciente" class="form-select" required>
              <option value="" disabled>Seleccione...</option>
              <?php while($p = $pacientes->fetch_assoc()): ?>
                <option value="<?= $p['id_paciente'] ?>"
                  <?= ($current_id_paciente == $p['id_paciente']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($p['apellido'].' '.$p['nombre']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Médico</label>
            <select name="id_medico" class="form-select" required>
              <option value="" disabled>Seleccione...</option>
              <?php while($m = $medicos->fetch_assoc()): ?>
                <option value="<?= $m['id_medico'] ?>"
                  <?= ($consulta['id_medico'] == $m['id_medico']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($m['apellido'].' '.$m['nombre']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label fw-bold">Motivo / Consulta</label>
            <textarea name="consulta_medica" class="form-control" rows="3"><?php echo htmlspecialchars($consulta['consulta_medica']); ?></textarea>
          </div>

          <div class="col-12">
            <label class="form-label fw-bold">Diagnóstico</label>
            <textarea name="diagnostico" class="form-control" rows="3"><?php echo htmlspecialchars($consulta['diagnostico']); ?></textarea>
          </div>

          <div class="col-12">
            <label class="form-label fw-bold">Tratamiento indicado</label>
            <textarea name="tratamiento_indicado" class="form-control" rows="3"><?php echo htmlspecialchars($consulta['tratamiento_indicado']); ?></textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Resultado de Estudio</label>
            <input type="text" name="resultado_de_estudio" class="form-control" value="<?php echo htmlspecialchars($consulta['resultado_de_estudio']); ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">Análisis de Laboratorio</label>
            <input type="text" name="analisis_de_laboratorio" class="form-control" value="<?php echo htmlspecialchars($consulta['analisis_de_laboratorio']); ?>">
          </div>

          <div class="col-12 text-end">
            <a href="historial_clinico.php" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
          </div>

        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
