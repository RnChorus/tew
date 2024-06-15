<?php
require_once('tcpdf/tcpdf.php');

// Verifica si se ha enviado el formulario y se ha recibido el ID de la materia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['materia_id'])) {
    // Obtiene el ID de la materia desde el formulario
    $materia_id = $_POST['materia_id'];

    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "control_asistencia";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar errores en la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta SQL para obtener el nombre de la materia y el nombre del docente
    $sql = "SELECT m.nombre AS nombre_materia, u.username AS nombre_docente
            FROM materias m
            JOIN usuarios u ON m.docente_id = u.id
            WHERE m.id='$materia_id'";
    $result = $conn->query($sql);

    // Verificar si la consulta fue exitosa
    if (!$result) {
        die("Error al ejecutar la consulta: " . $conn->error);
    }

    // Verificar si hay resultados
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre_materia = $row['nombre_materia'];
        $nombre_docente = $row['nombre_docente'];
    } else {
        die("No se encontraron datos para la materia con ID: $materia_id");
    }

    // Consulta SQL para obtener los datos de los estudiantes registrados en la materia
    $sql = "SELECT e.nombre, e.uid, a.fecha_hora 
            FROM estudiantes e
            JOIN asistencias a ON e.uid = a.uid
            WHERE a.materia_id='$materia_id'";
    $result = $conn->query($sql);
    
    // Crear PDF solo si hay resultados
    if ($result && $result->num_rows > 0) {
        // Inicializa el objeto TCPDF
        $pdf = new TCPDF();

        // Establece las propiedades del documento
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Docente');
        $pdf->SetTitle('Reporte de Asistencia de Estudiantes');
        $pdf->SetSubject('Asistencia de Estudiantes por Materia');
        $pdf->SetKeywords('Asistencia, Estudiantes, Materia, PDF');

        // Agrega una página
        $pdf->AddPage();

        // Agrega el contenido al PDF
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Write(0, 'Reporte de Asistencia de Estudiantes por Materia', '', 0, 'C', true, 0, false, false, 0);

        // Agrega el nombre de la materia y del docente al PDF
        $pdf->Ln(10); // Salto de línea
        $pdf->Write(0, 'Materia: ' . $nombre_materia, '', 0, 'L', true, 0, false, false, 0);
        $pdf->Ln(10); // Salto de línea
        $pdf->Write(0, 'Docente: ' . $nombre_docente, '', 0, 'L', true, 0, false, false, 0);
        $pdf->Ln(20); // Salto de línea

        // Iterar sobre los resultados y agregarlos al PDF
        while ($row = $result->fetch_assoc()) {
            $pdf->Write(0, 'Nombre: ' . $row['nombre'] . ', UID: ' . $row['uid'] . ', Fecha y Hora de Registro: ' . $row['fecha_hora'], '', 0, 'L', true, 0, false, false, 0);
            $pdf->Ln(10); // Salto de línea
        }

        // Salida del PDF (descarga o visualización)
        $pdf->Output('reporte.pdf', 'D');
    } else {
        echo "No hay datos para generar el reporte.";
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    // Si no se recibió el ID de la materia, redirigir a una página de error o a la página anterior
    header("Location: materia.php");
    exit();
}
?>
