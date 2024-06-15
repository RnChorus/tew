<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "control_asistencia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_POST['uid'];
    $materia_id = 1;  // Asume una materia ID fija por simplicidad, puedes modificarlo según tu lógica
    $fecha_hora = date('Y-m-d H:i:s');

    $sql = "INSERT INTO asistencias (uid, fecha_hora, materia_id) VALUES ('$uid', '$fecha_hora', '$materia_id')";

    if ($conn->query($sql) === TRUE) {
        echo "Asistencia registrada correctamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
