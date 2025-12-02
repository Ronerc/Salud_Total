<?php
session_start();
include("conexion.php");

// Validar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: nueva_consulta.php");
    exit;
}

$id_paciente = intval($_POST['id_paciente'] ?? 0);
$id_medico = intval($_POST['id_medico'] ?? 0);
$consulta_medica = htmlspecialchars($_POST['consulta_medica'] ?? '');
$diagnostico = htmlspecialchars($_POST['diagnostico'] ?? '');
$tratamiento_indicado = htmlspecialchars($_POST['tratamiento_indicado'] ?? '');
$observaciones = htmlspecialchars($_POST['observaciones'] ?? '');
$resultado_de_estudio = htmlspecialchars($_POST['resultado_de_estudio'] ?? '');
$analisis_de_laboratorio = htmlspecialchars($_POST['analisis_de_laboratorio'] ?? '');

// Insertar en historial_clinico_del_paciente
$sql = "INSERT INTO historial_clinico_del_paciente 
    (consulta_medica, diagnostico, tratamiento_indicado, observaciones, resultado_de_estudio, analisis_de_laboratorio, id_medico)
    VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error en prepare: " . $conn->error);
}
$stmt->bind_param("ssssssi",
    $consulta_medica,
    $diagnostico,
    $tratamiento_indicado,
    $observaciones,
    $resultado_de_estudio,
    $analisis_de_laboratorio,
    $id_medico
);

if (!$stmt->execute()) {
    $stmt->close();
    die("Error al guardar consulta: " . $stmt->error);
}

$id_historial = $stmt->insert_id;
$stmt->close();

// Insertar relación paciente <-> historial
$sql2 = "INSERT INTO pacientes_historial_clinico_del_paciente (id_paciente, id_historial_clinico) VALUES (?, ?)";
$stmt2 = $conn->prepare($sql2);
if (!$stmt2) {
    // intentar limpiar historial creado? (opcional)
    die("Error en prepare relación: " . $conn->error);
}
$stmt2->bind_param("ii", $id_paciente, $id_historial);
$stmt2->execute();
$stmt2->close();

header("Location: historial_clinico.php?mensaje=ok");
exit;
?>
