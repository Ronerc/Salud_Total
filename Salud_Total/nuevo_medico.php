<?php
include("conexion.php");
include("navbar.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = htmlspecialchars($_POST['nombre']);
    $apellido = htmlspecialchars($_POST['apellido']);
    $dni = htmlspecialchars($_POST['dni']);
    $telefono = htmlspecialchars($_POST['telefono']);
    $correo = htmlspecialchars($_POST['correo_electronico']);

    $especialidad = htmlspecialchars($_POST['especialidad']);
    $horario_inicio = htmlspecialchars($_POST['horario_inicio'] ?? '');
    $horario_fin = htmlspecialchars($_POST['horario_fin'] ?? '');
    $horario_atencion = trim($horario_inicio . ' - ' . $horario_fin);

    // Comprobar que el correo no exista (restricción unique en BD)
    $chk = $conn->prepare("SELECT COUNT(*) AS cnt FROM medicos WHERE correo_electronico = ?");
    $chk->bind_param("s", $correo);
    $chk->execute();
    $res_chk = $chk->get_result()->fetch_assoc();
    $chk->close();

    if (!empty($res_chk['cnt'])) {
        $message = '<div class="alert alert-danger">Error: el correo electrónico ya está registrado.</div>';
    } else {
        // INSERT MÉDICO
        $sql1 = "INSERT INTO medicos (nombre, apellido, dni, telefono, correo_electronico)
                 VALUES (?, ?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sssss", $nombre, $apellido, $dni, $telefono, $correo);
        if ($stmt1->execute()) {
            $id_medico = $conn->insert_id;
            $stmt1->close();

            // INSERT ESPECIALIDAD
            $sql2 = "INSERT INTO especialidades (especialidad, horario_atencion)
                     VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ss", $especialidad, $horario_atencion);
            $stmt2->execute();
            $id_especialidad = $conn->insert_id;
            $stmt2->close();

            // TABLA INTERMEDIA
            $sql3 = "INSERT INTO medicos_especialidades (id_medico, id_especialidad)
                     VALUES (?, ?)";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->bind_param("ii", $id_medico, $id_especialidad);
            $stmt3->execute();
            $stmt3->close();

            $message = '<div class="alert alert-success">Médico registrado correctamente.</div>';
        } else {
            $message = '<div class="alert alert-danger">Error al insertar el médico: ' . htmlspecialchars($stmt1->error ?? 'unknown') . '</div>';
        }
    }


}
?>

<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Nuevo Médico</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>

<body style="background-color: #f2f7ff">

<div class="container mt-5">
    <div class="card shadow p-0">
        <div class="card-header bg-dark text-white p-4">
        <h1 class="mb-4">Registrar Nuevo Médico</h1>
        </div>
        <?= $message ?>

        <form method="POST">

            <div class="card-body p-4 p-md-5">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="apellido" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">DNI</label>
                    <input type="text" name="dni" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo_electronico" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Especialidad</label>
                    <input type="text" name="especialidad" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Horario inicio</label>
                    <input type="time" name="horario_inicio" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Horario fin</label>
                    <input type="time" name="horario_fin" class="form-control" required>
                </div>

                <div class="col-12 text-end">
                    <a href="administracion.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Médico</button>
                </div>

            </div>

        </form>
    </div>
</div>

</body>
</html>
