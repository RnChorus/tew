<?php
session_start();
if ($_SESSION['rol'] !== 'admin') {
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

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM usuarios WHERE id=$id";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
} else {
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $rol = $_POST['rol'];
    $password = $_POST['password'];

    if (empty($password)) {
        $sql = "UPDATE usuarios SET username='$username', rol='$rol' WHERE id=$id";
    } else {
        $sql = "UPDATE usuarios SET username='$username', rol='$rol', password='$password' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        header('Location: admin.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Editar Usuario</h1>
    <form method="POST" action="">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
        <br>
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="admin" <?php if($user['rol'] == 'admin') echo 'selected'; ?>>Administrador</option>
            <option value="docente" <?php if($user['rol'] == 'docente') echo 'selected'; ?>>Docente</option>
            <option value="estudiante" <?php if($user['rol'] == 'estudiante') echo 'selected'; ?>>Estudiante</option>
        </select>
        <br>
        <label for="password">Contraseña (dejar en blanco para no cambiar):</label>
        <input type="password" id="password" name="password">
        <br>
        <input type="submit" value="Guardar cambios">
    </form>
</body>
</html>
