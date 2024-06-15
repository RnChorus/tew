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

$docente_id = $_SESSION['userid'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_materia'])) {
    $nombre = $_POST['nombre'];
    $sql = "INSERT INTO materias (nombre, docente_id) VALUES ('$nombre', '$docente_id')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Materia creada correctamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM materias WHERE docente_id='$docente_id'";
$result = $conn->query($sql);
$materias = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $materias[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Docente</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Panel de Docente</h1>
    <p>Bienvenido, <?php echo $_SESSION['username']; ?></p>
    
    <h2>Crear Nueva Materia</h2>
    <form method="POST" action="docente.php">
        <label for="nombre">Nombre de la Materia:</label>
        <input type="text" id="nombre" name="nombre" required>
        <br>
        <input type="submit" name="create_materia" value="Crear Materia">
    </form>
    
    <h2>Materias Creadas</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($materias)): ?>
                <?php foreach ($materias as $materia): ?>
                    <tr>
                        <td><?php echo $materia['id']; ?></td>
                        <td><?php echo $materia['nombre']; ?></td>
                        <td><a href="materia.php?id=<?php echo $materia['id']; ?>">Ver Detalles</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No hay materias creadas</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
