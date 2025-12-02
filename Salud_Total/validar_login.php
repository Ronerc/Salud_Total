<?php
include ("conexion.php");

function validar_login($nombre, $password) 
{
    global $conn;

    $sql = "SELECT * FROM usuarios WHERE nombre = '$nombre' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        return false;
    }

    $row = $result->fetch_assoc();

    // Verificar contraseña
    if (password_verify($password, $row['clave'])) {
        return $row;   // <<< DEVUELVE TODOS LOS DATOS DEL USUARIO
    } else {
        return false;
    }
}

?>

