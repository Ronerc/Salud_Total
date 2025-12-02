<?php
session_start();
include("conexion.php");

if (!isset($_GET['id'])) {
    header("Location: historial_clinico.php");
    exit;
}

$id = intval($_GET['id']);

// eliminar relación en tabla intermedia
$stmt = $conn->prepare("DELETE FROM pacientes_historial_clinico_del_paciente WHERE id_historial_clinico = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// eliminar registro en historial
$stmt2 = $conn->prepare("DELETE FROM historial_clinico_del_paciente WHERE id_historial_clinico = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$stmt2->close();

header("Location: historial_clinico.php?mensaje=deleted");
exit;
?>
