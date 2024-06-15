<?php
session_start();
if ($_SESSION['rol'] !== 'docente') {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "control_asistencia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$materia_id = $_GET['id'];
$docente_id = $_SESSION['userid'];

$sql = "SELECT * FROM materias WHERE id='$materia_id' AND docente_id='$docente_id'";
$result = $conn->query($sql);
$materia = $result->fetch_assoc();

$sql = "SELECT e.id, e.nombre, e.uid, a.fecha_hora 
        FROM estudiantes e
        JOIN asistencias a ON e.uid = a.uid
        WHERE a.materia_id='$materia_id'";
$result = $conn->query($sql);
$estudiantes = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $estudiantes[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Materia</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Detalles de la Materia</h1>
    <h2><?php echo $materia['nombre']; ?></h2>
    
    <h3>Estudiantes Registrados</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>UID</th>
                <th>Fecha y Hora de Registro</th> <!-- Nueva columna -->
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($estudiantes)): ?>
                <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><?php echo $estudiante['id']; ?></td>
                        <td><?php echo $estudiante['nombre']; ?></td>
                        <td><?php echo $estudiante['uid']; ?></td>
                        <td><?php echo $estudiante['fecha_hora']; ?></td> <!-- Mostrar la fecha y hora del registro -->
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay estudiantes registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Agrega el botón para generar el reporte en PDF -->
    <form action="generar_reporte.php" method="POST" target="_blank">
        <input type="hidden" name="materia_id" value="<?php echo $materia_id; ?>">
        <input type="submit" name="generar_reporte" value="Generar Reporte PDF">
    </form>

    <br>
    <a href="docente.php">Volver al Panel de Docente</a>
</body>
</html>
