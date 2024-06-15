<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$rol = $_SESSION['rol'];

switch ($rol) {
    case 'admin':
        header("Location: admin.php");
        break;
    case 'docente':
        header("Location: docente.php");
        break;
    case 'estudiante':
        header("Location: estudiante.php");
        break;
    default:
        echo "Rol no reconocido";
        break;
}
?>
