<?php
session_start();
include("conexion.php");
include("navbar.php");

$message = '';
if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'ok') {
    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>¡Éxito!</strong> La consulta fue registrada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
}

// Obtener consultas con paciente y médico
$sql = "
SELECT h.id_historial_clinico,
       h.consulta_medica,
       h.diagnostico,
       h.tratamiento_indicado,
       h.observaciones,
       h.resultado_de_estudio,
       h.analisis_de_laboratorio,
       h.id_medico,
       p.id_paciente,
       p.nombre AS paciente_nombre,
       p.apellido AS paciente_apellido,
       m.nombre AS medico_nombre,
       m.apellido AS medico_apellido
FROM historial_clinico_del_paciente h
LEFT JOIN pacientes_historial_clinico_del_paciente ph ON h.id_historial_clinico = ph.id_historial_clinico
LEFT JOIN pacientes p ON ph.id_paciente = p.id_paciente
LEFT JOIN medicos m ON h.id_medico = m.id_medicos
ORDER BY h.id_historial_clinico DESC
";

$result = $conn->query($sql);
$historial = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $historial[] = $row;
    }
    $result->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Historial Clínico</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="inicio.css" />
</head>
<body style="background-color: #f2f7ff;">


  <div class="container py-4">
    <?php echo $message; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="fw-bold mb-0">Historial Clínico</h1>
        <p class="text-muted small mb-0">Registro de consultas médicas</p>
      </div>
      <a href="nueva_consulta.php" class="btn btn-primary">
        <i class="bi bi-file-earmark-medical-fill me-1"></i> Nueva Consulta
    </a>


    </div>

    <div class="mb-4">
      <div class="input-group rounded-pill border border-primary">
        <span class="input-group-text bg-white border-0 rounded-start-pill">
          <i class="bi bi-search text-primary"></i>
        </span>
        <input type="text" class="form-control border-0 py-2 rounded-end-pill"
               placeholder="Buscar por paciente o medico" aria-label="Buscar pacientes">
      </div>
    </div>

    <div class="card p-3 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>Paciente</th>
              <th>Médico</th>
              <th>Diagnóstico / Motivo</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($historial)): ?>
              <tr>
                <td colspan="4" class="text-center py-4 text-muted">No hay registros de consultas médicas.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($historial as $h): ?>
                <tr>
                  <td><?php
                      $pn = $h['paciente_nombre'] ?? 'N/A';
                      $pa = $h['paciente_apellido'] ?? '';
                      echo htmlspecialchars(trim($pa . ' ' . $pn));
                  ?></td>

                  <td><?php
                      $mn = $h['medico_nombre'] ?? 'N/A';
                      $ma = $h['medico_apellido'] ?? '';
                      echo htmlspecialchars(trim($ma . ' ' . $mn));
                  ?></td>

                  <td>
                    <strong class="text-primary"><?php echo htmlspecialchars($h['consulta_medica']); ?></strong>
                    <br>
                    <span class="text-muted small"><?php echo htmlspecialchars($h['diagnostico']); ?></span>
                  </td>

                  <td class="text-center ">
            
                    <a href="editar_consulta.php?id=<?php echo $h['id_historial_clinico']; ?>" class="btn btn-sm btn-outline-success me-2 ">Editar</a>
                    <a href="eliminar_consulta.php?id=<?php echo $h['id_historial_clinico']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta consulta?')">Eliminar</a>
                    
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
