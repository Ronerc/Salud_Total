<?php
session_start();
include("conexion.php");
include("navbar.php");

// Obtener lista de pacientes
$sqlPacientes = "SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY apellido ASC";
$pacientes = $conn->query($sqlPacientes);

// Obtener lista de médicos
$sqlMedicos = "SELECT id_medicos, nombre, apellido FROM medicos ORDER BY apellido ASC";
$medicos = $conn->query($sqlMedicos);

$message = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (empty($_POST["id_paciente"])) {
        $message = '<div class="alert alert-danger"> Error: Paciente no especificado</div>';
    } elseif (empty($_POST["id_medico"])) {
      $message = '<div class="alert alert-danger"> Error: Médico no especificado</div>';
    } elseif (empty($_POST["consulta_medica"])) {
        $message = '<div class="alert alert-danger"> Error: Debe ingresar el motivo/consulta médica</div>';
    } else {

        $id_paciente = $_POST["id_paciente"];
        $id_medico = $_POST["id_medico"];
        $consulta = $_POST["consulta_medica"];
        $diagnostico = $_POST["diagnostico"];
        $tratamiento = $_POST["tratamiento_indicado"];
        $observaciones = $_POST["observaciones"];
        $resultado = $_POST["resultado_de_estudio"];
        $analisis = $_POST["analisis_de_laboratorio"];

        // INSERTAR EN HISTORIAL CLÍNICO
        $sqlInsert = $conn->prepare("
            INSERT INTO historial_clinico_del_paciente 
            (consulta_medica, diagnostico, tratamiento_indicado, observaciones, resultado_de_estudio, analisis_de_laboratorio, id_medico)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $sqlInsert->bind_param(
            "ssssssi",
            $consulta,
            $diagnostico,
            $tratamiento,
            $observaciones,
            $resultado,
            $analisis,
            $id_medico
        );

        if ($sqlInsert->execute()) {

            // OBTENER ID RECIÉN CREADO
            $id_historial = $conn->insert_id;

            // INSERTAR RELACIÓN PACIENTE ↔ HISTORIAL
            $sqlRel = $conn->prepare("
                INSERT INTO pacientes_historial_clinico_del_paciente (id_paciente, id_historial_clinico)
                VALUES (?, ?)
            ");

            $sqlRel->bind_param("ii", $id_paciente, $id_historial);
            $sqlRel->execute();

            header("Location: historial_clinico.php?mensaje=ok");
            exit;
        } else {
            $message = '<div class="alert alert-danger">❌ Error al registrar la consulta.</div>';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Nueva Consulta</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-color:#f2f7ff">

<div class="container py-4">
  
  <h1 class="fw-bold mb-3">Nueva Consulta</h1>
  <?php echo $message; ?>

  <div class="card p-4 shadow-sm">

    <form method="POST">

      <!-- PACIENTE -->
      <div class="mb-3">
        <label class="form-label">Paciente</label>
        <select name="id_paciente" class="form-select" required>
          <option value="">Seleccione un paciente...</option>
          <?php while ($p = $pacientes->fetch_assoc()): ?>
            <option value="<?php echo $p['id_paciente']; ?>">
              <?php echo $p['apellido'] . ", " . $p['nombre']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- MÉDICO -->
      <div class="mb-3">
        <label class="form-label">Médico</label>
        <select name="id_medico" class="form-select" required>
          <option value="">Seleccione un médico...</option>
          <?php while ($m = $medicos->fetch_assoc()): ?>
            <option value="<?php echo $m['id_medicos']; ?>">
              <?php echo $m['apellido'] . ", " . $m['nombre']; ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- CONSULTA -->
      <div class="mb-3">
        <label class="form-label">Motivo / Consulta Médica</label>
        <input type="text" class="form-control" name="consulta_medica" required>
      </div>

      <!-- DIAGNOSTICO -->
      <div class="mb-3">
        <label class="form-label">Diagnóstico</label>
        <textarea class="form-control" name="diagnostico"></textarea>
      </div>

      <!-- TRATAMIENTO -->
      <div class="mb-3">
        <label class="form-label">Tratamiento Indicado</label>
        <textarea class="form-control" name="tratamiento_indicado"></textarea>
      </div>

      <!-- OBSERVACIONES -->
      <div class="mb-3">
        <label class="form-label">Observaciones</label>
        <textarea class="form-control" name="observaciones"></textarea>
      </div>

      <!-- RESULTADO -->
      <div class="mb-3">
        <label class="form-label">Resultado de Estudio</label>
        <textarea class="form-control" name="resultado_de_estudio"></textarea>
      </div>

      <!-- ANALISIS -->
      <div class="mb-3">
        <label class="form-label">Análisis de Laboratorio</label>
        <textarea class="form-control" name="analisis_de_laboratorio"></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-100">Guardar Consulta</button>

    </form>
  </div>

</div>

</body>
</html>
