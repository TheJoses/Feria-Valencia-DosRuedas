<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos
$servername = "localhost"; // Cambia esto si es necesario
$username = "root"; // Cambia esto por tu usuario de MySQL
$password = ""; // Cambia esto por tu contraseña de MySQL
$dbname = "suscriptores"; // Cambia esto por el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Preparar y vincular
$stmt = $conn->prepare("INSERT INTO suscriptores (nombre, apellido1, apellido2, correo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $apellido1, $apellido2, $correo);

// Establecer parámetros y ejecutar
$nombre = $_POST['nombre'];
$apellido1 = $_POST['apellido1'];
$apellido2 = $_POST['apellido2'];
$correo = $_POST['correo'];

if ($stmt->execute()) {
    echo "Suscripción exitosa. Se ha enviado un correo de confirmación.";

    // Convertir MJML a HTML
    require 'vendor/autoload.php'; // Asegúrate de que la ruta sea correcta
    $mjml = file_get_contents('correo.mjml');
    $mjmlClient = new \Mailjet\Client('tu_api_key', 'tu_api_secret', true, ['version' => 'v3']);
    $response = $mjmlClient->post('mjml', ['body' => ['mjml' => $mjml]]);

    if ($response->success()) {
        $html = $response->getData()['html'];

        // Enviar correo de confirmación
        $to = $correo;
        $subject = "Confirmación de Suscripción";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: no-reply@comicvalencia.com" . "\r\n";

        mail($to, $subject, $html, $headers);
    } else {
        echo "Error al convertir MJML a HTML: " . $response->getStatus();
    }
} else {
    echo "Error: " . $stmt->error; // Mensaje de error
}

// Cerrar conexión
$stmt->close();
$conn->close();
?>
