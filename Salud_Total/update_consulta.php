<?php
session_start();
include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: historial_clinico.php");
    exit;
}

$id_historial = intval($_POST['id_historial_clinico']);
$id_paciente_new = intval($_POST['id_paciente'] ?? 0);
$id_medico = intval($_POST['id_medico'] ?? 0);
$consulta_medica = htmlspecialchars($_POST['consulta_medica'] ?? '');
$diagnostico = htmlspecialchars($_POST['diagnostico'] ?? '');
$tratamiento_indicado = htmlspecialchars($_POST['tratamiento_indicado'] ?? '');
$resultado_de_estudio = htmlspecialchars($_POST['resultado_de_estudio'] ?? '');
$analisis_de_laboratorio = htmlspecialchars($_POST['analisis_de_laboratorio'] ?? '');

// 1) actualizar historial
$sql = "UPDATE historial_clinico_del_paciente SET
    consulta_medica = ?, diagnostico = ?, tratamiento_indicado = ?, resultado_de_estudio = ?, analisis_de_laboratorio = ?, id_medico = ?
    WHERE id_historial_clinico = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssiii",
    $consulta_medica,
    $diagnostico,
    $tratamiento_indicado,
    $resultado_de_estudio,
    $analisis_de_laboratorio,
    $id_medico,
    $id_historial
);
$stmt->execute();
$stmt->close();

// 2) sincronizar tabla intermedia: obtener paciente actual
$stmt2 = $conn->prepare("SELECT id_paciente FROM pacientes_historial_clinico_del_paciente WHERE id_historial_clinico = ?");
$stmt2->bind_param("i", $id_historial);
$stmt2->execute();
$r = $stmt2->get_result();
$row = $r->fetch_assoc();
$current_id_paciente = $row['id_paciente'] ?? null;
$stmt2->close();

if ($current_id_paciente === null) {
    // no existía relación: insertar
    $ins = $conn->prepare("INSERT INTO pacientes_historial_clinico_del_paciente (id_paciente, id_historial_clinico) VALUES (?, ?)");
    $ins->bind_param("ii", $id_paciente_new, $id_historial);
    $ins->execute();
    $ins->close();
} elseif ($current_id_paciente != $id_paciente_new) {
    // actualizar relación (más simple: delete + insert)
    $del = $conn->prepare("DELETE FROM pacientes_historial_clinico_del_paciente WHERE id_historial_clinico = ?");
    $del->bind_param("i", $id_historial);
    $del->execute();
    $del->close();

    $ins2 = $conn->prepare("INSERT INTO pacientes_historial_clinico_del_paciente (id_paciente, id_historial_clinico) VALUES (?, ?)");
    $ins2->bind_param("ii", $id_paciente_new, $id_historial);
    $ins2->execute();
    $ins2->close();
}

// redirigir con mensaje
header("Location: historial_clinico.php?mensaje=edit_ok");
exit;
?>
