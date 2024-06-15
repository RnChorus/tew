<?php
session_start();
if ($_SESSION['rol'] !== 'estudiante') {
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

$usuario_id = $_SESSION['userid'];

// Obtener información del estudiante
$sql = "SELECT * FROM estudiantes WHERE usuario_id='$usuario_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $estudiante = $result->fetch_assoc();
} else {
    // Manejar el caso donde no se encuentre el estudiante
    echo "No se encontró información del estudiante.";
    $conn->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_asistencia'])) {
    $materia_id = $_POST['materia_id'];
    $uid = $estudiante['uid'];
    $fecha_hora = date('Y-m-d H:i:s');
    
    $sql = "INSERT INTO asistencias (uid, fecha_hora, materia_id) VALUES ('$uid', '$fecha_hora', '$materia_id')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Asistencia registrada correctamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Obtener la lista de materias
$sql = "SELECT * FROM materias";
$result = $conn->query($sql);
$materias = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $materias[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estudiante</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Panel de Estudiante</h1>
    <p>Bienvenido, <?php echo $_SESSION['username']; ?></p>
    
    <h2>Registrar Asistencia</h2>
    <form method="POST" action="estudiante.php">
        <label for="materia_id">Seleccionar Materia:</label>
        <select id="materia_id" name="materia_id" required>
            <?php if (!empty($materias)): ?>
                <?php foreach ($materias as $materia): ?>
                    <option value="<?php echo $materia['id']; ?>"><?php echo $materia['nombre']; ?></option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">No hay materias disponibles</option>
            <?php endif; ?>
        </select>
        <br>
        <input type="submit" name="register_asistencia" value="Registrar Asistencia">
    </form>
    
    <h2>Historial de Asistencia</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Materia</th>
                <th>Fecha y Hora</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT a.id, m.nombre as materia, a.fecha_hora 
                    FROM asistencias a 
                    JOIN materias m ON a.materia_id = m.id 
                    WHERE a.uid = '" . $estudiante['uid'] . "'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['materia'] . "</td>";
                    echo "<td>" . $row['fecha_hora'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No hay registros de asistencia</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <br>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
<?php
$conn->close();
?>
