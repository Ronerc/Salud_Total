<?php
include("conexion.php");

// Obtener pacientes
$pacientes = $conn->query("SELECT id_paciente, nombre, apellido FROM pacientes ORDER BY apellido");

// Obtener médicos (sin especialidad)
$medicos = $conn->query("SELECT id_medicos, nombre FROM medicos ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Turno</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="inicio.css" /> 
</head>

<body class="bg-light"> 
    <nav id="navbar-container"><?php include("navbar.php")?></nav>

    <div class="container mt-5 mb-5">
        <div class="card shadow-lg border-0">

            <div class="card-header bg-dark text-white p-4">
                <h3 class="fw-bold mb-0">
                    <i class="bi bi-calendar-plus-fill me-2"></i> Registrar Nuevo Turno
                </h3>
                <p class="mb-0 opacity-75">Seleccione los datos requeridos para el turno</p>
            </div>

            <div class="card-body p-4 p-md-5">

                <!-- CORREGIDO: turnos.php -->
                <form action="turnos.php" method="POST">
                    <div class="row g-4">

                        <!-- PACIENTE -->
                        <div class="col-md-6">
                            <label for="id_paciente" class="form-label fw-bold">Paciente *</label>
                            <select id="id_paciente" name="id_paciente" class="form-select" required>
                                <option value="" disabled selected>Seleccione un paciente...</option>
                                <?php while($p = $pacientes->fetch_assoc()): ?>
                                    <option value="<?= $p['id_paciente'] ?>">
                                        <?= $p['id_paciente'] ?> - <?= $p['apellido'] ?> <?= $p['nombre'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- MÉDICO (corregido) -->
                        <div class="col-md-6">
                            <label for="id_medico" class="form-label fw-bold">Médico *</label>
                            <select id="id_medico" name="id_medico" class="form-select" required>
                                <option value="" disabled selected>Seleccione un médico...</option>
                                <?php while($m = $medicos->fetch_assoc()): ?>
                                    <option value="<?= $m['id_medico'] ?>">
                                        <?= $m['id_medico'] ?> - <?= $m['nombre'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- FECHA -->
                        <div class="col-md-6">
                            <label for="horario_de_turno" class="form-label fw-bold">Fecha del Turno *</label>
                            <input type="datetime-local" class="form-control" id="horario_de_turno" name="horario_de_turno" required>
                        </div>

                        <!-- RECORDATORIO -->
                        <div class="col-12">
                            <label for="recordatorio" class="form-label fw-bold">Recordatorio / Notas</label>
                            <textarea class="form-control" id="recordatorio" name="recordatorio" rows="3" required></textarea>
                        </div>

                        <div class="col-12"><hr class="mt-4 mb-2"></div>

                        <!-- BOTONES -->
                        <div class="col-12 text-end">
                            <a href="turnos.php" class="btn btn-secondary me-2">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-check-lg"></i> Guardar Turno
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
