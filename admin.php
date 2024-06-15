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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $sql = "INSERT INTO usuarios (username, password, rol) VALUES ('$username', '$password', '$rol')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Usuario registrado correctamente";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);
$usuarios = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Panel de Administrador</h1>
    <p>Bienvenido, <?php echo $_SESSION['username']; ?></p>
    
    <h2>Registrar Nuevo Usuario</h2>
    <form method="POST" action="admin.php">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="admin">Administrador</option>
            <option value="docente">Docente</option>
            <option value="estudiante">Estudiante</option>
        </select>
        <br>
        <input type="submit" name="register_user" value="Registrar Usuario">
    </form>
    
    <h2>Usuarios Registrados</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo $usuario['username']; ?></td>
                        <td><?php echo $usuario['rol']; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $usuario['id']; ?>">Editar</a>
                            <a href="delete_user.php?id=<?php echo $usuario['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay usuarios registrados</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
