<?php
include("conexion.php");
include("navbar.php");

$message = "";


// INSERTAR O ACTUALIZAR TURNO

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_paciente = htmlspecialchars($_POST['id_paciente'] ?? '');
    $id_medico = htmlspecialchars($_POST['id_medico'] ?? ''); 
    $horario_de_turno = htmlspecialchars($_POST['horario_de_turno'] ?? '');
    $recordatorio = htmlspecialchars($_POST['recordatorio'] ?? '');
    $id_turno = htmlspecialchars($_POST['id_turno'] ?? '');

    // Validar que los campos requeridos no estén vacíos
    if (empty($id_paciente) || empty($id_medico) || empty($horario_de_turno)) {
        $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>¡Error!</strong> Los campos Paciente, Médico y Fecha del Turno son obligatorios.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
    } else {
        // Si viene id_turno, es UPDATE; si no, es INSERT
        if (!empty($id_turno)) {
            // ACTUALIZAR TURNO
            $sql = "UPDATE turnos SET id_medicos = $id_medico, id_paciente = $id_paciente, 
                    horario_de_turno = '$horario_de_turno', recordatorio = '$recordatorio'
                    WHERE id_turnos = $id_turno";
        } else {
            // INSERTAR TURNO
            $sql = "INSERT INTO turnos (id_medicos, id_paciente, horario_de_turno, recordatorio)
                    VALUES ($id_medico, $id_paciente, '$horario_de_turno', '$recordatorio')";
        }

        if ($conn->query($sql)) {
            header("Location: turnos.php?mensaje=ok");
            exit();
        } else {
            $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>¡Error!</strong> No se pudo registrar el turno. Error: ' . $conn->error . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
        }
    }
}


// CONSULTAR TURNOS

$turnos = [];

$query = "SELECT 
    t.id_turnos,
    p.nombre AS nombre_paciente,
    p.apellido AS apellido_paciente,
    m.nombre AS nombre_medico,
    t.horario_de_turno,
    t.recordatorio
FROM turnos t
INNER JOIN pacientes p ON t.id_paciente = p.id_paciente
INNER JOIN medicos m ON t.id_medicos = m.id_medicos
ORDER BY t.id_turnos DESC";

$result = $conn->query($query);

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
    <title>Gestión de Turnos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="turnos.css" />
</head>

<body>

<div class="container mt-4">

    <!-- ALERTA DE ÉXITO -->
    <?php if (isset($_GET['mensaje']) && $_GET['mensaje'] == "ok"): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>¡Éxito!</strong> El turno ha sido registrado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- MENSAJES DE ERROR -->
    <?php echo $message; ?>
    

    <div class="container py-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-0">Turnos</h1>
                <p class="text-muted small mb-0">Gestión de turnos médicos</p>
            </div>
            <a href="nuevo_turno.php" class="btn btn-primary">
                <i class="bi bi-person-plus-fill me-1"></i> Nuevo Turno
            </a>
        </div>

        <!-- BUSCADOR -->
        <div class="mb-4">
            <div class="input-group rounded-pill border border-primary">
                <span class="input-group-text bg-white border-0 rounded-start-pill">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text" class="form-control border-0 py-2 rounded-end-pill"
                       placeholder="Buscar por paciente o médico">
            </div>
        </div>

        <!-- TABLA DE TURNOS -->
        <div class="card shadow-sm">
            <table class="table mb-0">
                <thead class="table-dark">
                <tr>
                    <th>Paciente</th>
                    <th>Médico</th>
                    <th>Fecha</th>
                    <th>Recordatorio</th>
                    <th class="text-center">Acciones</th>
                </tr>
                </thead>

                <tbody>
                <?php if (empty($turnos)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No hay turnos registrados aún.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($turnos as $turno): ?>
                        <tr>
                            <td><?= htmlspecialchars($turno['nombre_paciente'] . " " . $turno['apellido_paciente']); ?></td>
                            <td><?= htmlspecialchars($turno['nombre_medico']); ?></td>
                            <td><?= htmlspecialchars($turno['horario_de_turno']); ?></td>
                            <td><?= htmlspecialchars($turno['recordatorio']); ?></td>

                            <td class="text-center">
                                <a href="editar_turno.php?id=<?= $turno['id_turnos']; ?>" 
                                   class="btn btn-sm btn-outline-success me-2">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </a>

                                <a href="eliminar_turno.php?id=<?= $turno['id_turnos']; ?>" 
                                   class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash-fill"></i> Eliminar
                                </a>
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
