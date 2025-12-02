<?php 
session_start();
include("conexion.php");
include("navbar.php");

// Obtener lista de pacientes
$sqlPacientes = "SELECT COUNT(id_paciente) AS contador_paciente FROM pacientes";
$pacientes = $conn->query($sqlPacientes);
$data_paciente = $pacientes->fetch_assoc();
$cont_paciente = $data_paciente ['contador_paciente'];

$sqlTurnosHoy = "SELECT COUNT(*) AS total FROM turnos WHERE DATE(horario_de_turno) = CURDATE()";

$result = $conn->query($sqlTurnosHoy);
$data = $result->fetch_assoc();

$turnosHoy = $data['total'];


$sqlTurnos = "SELECT t.id_turnos, p.nombre AS nombre_paciente, p.apellido AS apellido_paciente, m.nombre AS nombre_medico, t.horario_de_turno FROM turnos t
INNER JOIN pacientes p ON t.id_paciente = p.id_paciente
INNER JOIN medicos m ON t.id_medico = m.id_medico
ORDER BY t.id_turnos DESC";

$result = $conn->query($sqlTurnos);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $turnos[] = $row;
    }
    $result->close();
}






?>



<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Por ahora nada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="inicio.css" />
  </head>
  <body style="background-color: #f2f7ff">
    
    
    <?php include("head.php")?>
    <div id="header-content-container" class="bg-light p-3"></div>
    
    <div class="container mt-4">
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="card summary-card p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted mb-1">Total Pacientes</p>
                <h2 class="fw-bold"><?php echo ($cont_paciente) ?></h2>
              </div>
              <div class="d-flex flex-column align-items-end">
                <div class="icon-box bg-blue mb-2">
                  <i class="bi bi-people-fill"></i>
                </div>
                <div class="growth-text">
                  <i class="bi bi-arrow-up-right"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card summary-card p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted mb-1">Turnos Hoy</p>
                <h2 class="fw-bold"><?php echo ($turnosHoy) ?></h2>
              </div>
              <div class="d-flex flex-column align-items-end">
                <div class="icon-box bg-green mb-2">
                  <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="growth-text">
                  <i class="bi bi-arrow-up-right"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- <div class="col-md-6 col-lg-3">
          <div class="card summary-card p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted mb-1">Turnos Pendientes</p>
                <h2 class="fw-bold">0</h2>
              </div>
              <div class="d-flex flex-column align-items-end">
                <div class="icon-box bg-orange mb-2">
                  <i class="bi bi-clock-fill"></i>
                </div>
                <div class="growth-text">
                  <i class="bi bi-arrow-up-right"></i>
                </div>
              </div>
            </div>
          </div>
        </div> -->

        <!-- <div class="col-md-6 col-lg-3">
          <div class="card summary-card p-3">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted mb-1">Consultas Registradas</p>
                <h2 class="fw-bold">0</h2>
              </div>
              <div class="d-flex flex-column align-items-end">
                <div class="icon-box bg-purple mb-2">
                  <i class="bi bi-journal-check"></i>
                </div>
                <div class="growth-text">
                  <i class="bi bi-arrow-up-right"></i>
                </div>
              </div>
            </div>
          </div>
        </div> -->
      </div>

      <hr class="my-4 d-none d-lg-block" />

      <div class="row mt-4">
        <div class="col-12">
          <div class="appointments-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="fw-bold mb-0">Próximos Turnos</h5>
              <a href="turnos.php" class="text-decoration-none small"
                >Ver todos &rarr;</a
              >
            </div>
                <!-- TABLA DE TURNOS -->
        <div class="card shadow-sm">
            <table class="table mb-0">
                <thead class="table-dark">
                <tr>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha</th>
                </tr>
                </thead>

                <tbody>
                  <?php if (empty($turnos)): ?>
                      <tr>
                          <td colspan="3" class="text-center py-4">

                              <div class="no-appointments-box d-flex flex-column align-items-center">
                                  <div class="no-appointments-icon">
                                      <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                                  </div>
                                  <p class="mt-2 mb-0">No hay turnos próximos</p>
                              </div>

                          </td>
                      </tr>
                  <?php else: ?>
                      <?php foreach ($turnos as $turno): ?>
                          <tr>
                              <td><?= htmlspecialchars($turno['nombre_paciente'] . " " . $turno['apellido_paciente']); ?></td>
                              <td><?= htmlspecialchars($turno['nombre_medico']); ?></td>
                              <td><?= htmlspecialchars($turno['horario_de_turno']); ?></td>
                          </tr>
                      <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>

            </table>
          
          </div>
        </div>
      </div>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
