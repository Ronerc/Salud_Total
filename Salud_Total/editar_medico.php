<?php
session_start();
include("conexion.php");
include("navbar.php");

$message = "";


// Si envió el formulario: actualizar datos

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = intval($_POST["id_medico"] ?? 0);
    if ($id <= 0) { header("Location: administracion.php"); exit; }

    // Datos del médico
    $nombre = $_POST["nombre"] ?? "";
    $apellido = $_POST["apellido"] ?? "";
    $dni = $_POST["dni"] ?? "";
    $telefono = $_POST["telefono"] ?? "";
    $correo = $_POST["correo_electronico"] ?? "";

    // Datos de especialidad
    $especialidad = $_POST["especialidad"] ?? "";
    $horario = $_POST["horario"] ?? "";


    // Actualizar médico
    $stmt = $conn->prepare("UPDATE medicos 
                            SET nombre=?, apellido=?, dni=?, telefono=?, correo_electronico=?
                            WHERE id_medicos=?");
    $stmt->bind_param("sssssi", $nombre, $apellido, $dni, $telefono, $correo, $id);
    $stmt->execute();
    $stmt->close();

    // Verificar si ya tiene especialidad
    $stmt = $conn->prepare("SELECT id_especialidad 
                            FROM medicos_especialidades 
                            WHERE id_medico=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($resultado) {
        // Actualizar especialidad existente
        $id_espec = $resultado["id_especialidad"];
        $stmt = $conn->prepare("UPDATE especialidades 
                                SET especialidad=?, horario=? 
                                WHERE id_especialidad=?");
        $stmt->bind_param("ssi", $especialidad, $horario, $id_espec);
        $stmt->execute();
        $stmt->close();
    } else {
        // Crear especialidad nueva
        $stmt = $conn->prepare("INSERT INTO especialidades (especialidad, horario) 
                                VALUES (?, ?)");
        $stmt->bind_param("ss", $especialidad, $horario);
        $stmt->execute();
        $id_nueva = $conn->insert_id;
        $stmt->close();

        // Relacionarla con el médico
        $stmt = $conn->prepare("INSERT INTO medicos_especialidades (id_medico, id_especialidad) 
                                VALUES (?, ?)");
        $stmt->bind_param("ii", $id, $id_nueva);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: administracion.php?mensaje=ok");
    exit;
}


// 2) Si llega por GET: cargar datos

$id = intval($_GET["id"] ?? 0);
if ($id <= 0) { header("Location: administracion.php"); exit; }

$stmt = $conn->prepare(
"SELECT m.id_medicos, m.nombre, m.apellido, m.dni, m.telefono, m.correo_electronico,
        e.especialidad, e.horario
 FROM medicos m
 LEFT JOIN medicos_especialidades me ON m.id_medicos = me.id_medico
 LEFT JOIN especialidades e ON me.id_especialidad = e.id_especialidad
 WHERE m.id_medicos=? LIMIT 1");

$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) { header("Location: administracion.php"); exit; }
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="inicio.css" />
</head>
<body style="background-color: #f2f7ff">

<div class="container py-4">
    <?= $message ?>
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white p-4">
            <h3 class="mb-0">Editar Médico</h3>
        </div>
        <div class="card-body p-4">
            <form method="POST">
                <input type="hidden" name="id_medico" value="<?= htmlspecialchars($data['id_medicos']) ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($data['nombre']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Apellido</label>
                        <input type="text" name="apellido" class="form-control" required value="<?= htmlspecialchars($data['apellido']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">DNI</label>
                        <input type="text" name="dni" class="form-control" required value="<?= htmlspecialchars($data['dni']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" required value="<?= htmlspecialchars($data['telefono']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo_electronico" class="form-control" required value="<?= htmlspecialchars($data['correo_electronico']) ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" required value="<?= htmlspecialchars($data['especialidad'] ?? '') ?>">
                    </div>

                     <div class="col-md-6">
                        <label class="form-label">Horario de atención</label>
                        <input type="text" name="horario" class="form-control" placeholder="Ej: 08:00 a 15:00" required value="<?= htmlspecialchars($data['horario'] ?? '') ?>">


                    </div>

                    <div class="col-12 text-end mt-3">
                        <a href="administracion.php" class="btn btn-secondary me-2">Cancelar</a>
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
