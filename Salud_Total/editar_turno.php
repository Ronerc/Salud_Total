<?php

session_start();
include("conexion.php");

// Validación: debe venir un ID por GET
if (!isset($_GET['id'])) {
    header("Location: turnos.php?mensaje=error");
    exit();
}

$id_turno = intval($_GET['id']);

// Consulta para obtener los datos del turno
$sql = "SELECT t.*, 
               p.nombre AS nombre_paciente, 
               p.apellido AS apellido_paciente,
               m.nombre AS nombre_medico,
               m.apellido AS apellido_medico
        FROM turnos t
        INNER JOIN pacientes p ON t.id_paciente = p.id_paciente
        INNER JOIN medicos m ON t.id_medicos = m.id_medicos
        WHERE t.id_turnos = $id_turno";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: turnos.php?mensaje=no_encontrado");
    exit();
}

$turno = $result->fetch_assoc();

$pacientes = $conn->query("SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY apellido");

$medicos = $conn->query("SELECT id_medicos, nombre, apellido FROM medicos ORDER BY apellido");

$fecha = date('Y-m-d', strtotime($turno['horario_de_turno']));

$hora = date('H:i', strtotime($turno['horario_de_turno']));

?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Turno</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav id="navbar-container"><?php include("navbar.php") ?></nav>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg border-0">

        <div class="card-header bg-dark text-white p-4 text-center">
            <h3 class="fw-bold mb-0">
                <i class="bi bi-pencil-square me-2"></i> Editar Datos del Turno
            </h3>
            <p class="mb-0 opacity-75">Modifique la información y guarde los cambios.</p>
        </div>

        <div class="card-body p-4 p-md-5">
            <form action="turnos.php" method="POST">
                <input type="hidden" name="id_turno" value="<?= $id_turno ?>">

                <div class="row g-4">

                     <!-- PACIENTE -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Paciente *</label>
                        <select name="id_paciente" class="form-select" required>
                            <?php while($p = $pacientes->fetch_assoc()): ?>
                                <option value="<?= $p['id_paciente'] ?>"
                                    <?= ($p['id_paciente'] == $turno['id_paciente']) ? 'selected' : '' ?>>
                                    <?= $p['apellido'] . " " . $p['nombre'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- MEDICO -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Médico *</label>
                        <select name="id_medico" class="form-select" required>
                            <?php while($m = $medicos->fetch_assoc()): ?>
                                <option value="<?= $m['id_medicos'] ?>"
                                    <?= ($m['id_medicos'] == $turno['id_medicos']) ? 'selected' : '' ?>>
                                    <?= $m['apellido'] . " " . $m['nombre'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>



                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha *</label>
                        <input type="datetime-local" class="form-control" name="horario_de_turno" 
                            value="<?= $turno['horario_de_turno'] ? date('Y-m-d\TH:i', strtotime($turno['horario_de_turno'])) : '' ?>" required>

                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Recordatorio / Notas</label>
                        <textarea class="form-control" name="recordatorio" rows="3"><?= htmlspecialchars($turno['recordatorio'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-12 text-end">
                        <a href="turnos.php" class="btn btn-secondary me-2">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                        </button>
                    </div>

                </div>

            </form>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
