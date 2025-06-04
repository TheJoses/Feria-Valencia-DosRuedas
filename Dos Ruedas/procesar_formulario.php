<?php
//Configuración de la base de datos
$servername = "localhost"; //Servidor
$username = "root"; //Usuario(root)
$password = ""; //Contraseña SQL (no tengo)
$dbname = "participa"; //Nombre de la Base de Datos

//Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

//Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

//Obtener datos del formulario
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$edad = $_POST['edad'];
$email = $_POST['email'];
$telefono = $_POST['telefono'];
$codigo_postal = $_POST['codigo_postal'];

//Preparar y ejecutar la consulta SQL
$sql = "INSERT INTO participaciones (nombre, apellidos, edad, email, telefono, codigo_postal) VALUES (?, ?, ?, ?, ?, ?)"; //Nombre de la tabla SQL
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisss", $nombre, $apellidos, $edad, $email, $telefono, $codigo_postal);

if ($stmt->execute()) {
    //Enviar correo electrónico
    $to = $email;
    $subject = "Confirmación de Participación";
    $message = "
    <html>
    <head>
        <title>Tu participación ha sido guardada</title>
    </head>
    <body>
        <p>Tu participación ha sido guardada. Gracias por participar.</p>
    </body>
    </html>
    ";
    
    //Para enviar un correo HTML, se deben establecer las cabeceras adecuadas
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: feriavalenciacorreos@gmail.com" . "\r\n"; //Dirección de correo electronico

    mail($to, $subject, $message, $headers);
    
    echo "Participación guardada y correo enviado.";

    //Creara o abrira el archivo listado.csv
    $csvFile = 'listado.csv';
    $fileHandle = fopen($csvFile, 'a'); // 'a' para agregar al final del archivo

    //Si el archivo se abre correctamente, escribir los datos
    if ($fileHandle) {
        //Escribir la cabecera si el archivo está vacío
        if (filesize($csvFile) == 0) {
            fputcsv($fileHandle, ['nombre', 'apellidos', 'edad', 'email', 'telefono', 'codigo_postal']);
        }

        //Escribir los datos en el archivo CSV
        fputcsv($fileHandle, [$nombre, $apellidos, $edad, $email, $telefono, $codigo_postal]);
        fclose($fileHandle); //Cerrar el archivo
    } else {
        echo "Error al abrir el archivo CSV.";
    }
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
