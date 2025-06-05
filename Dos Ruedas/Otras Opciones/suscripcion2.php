<?php
// Configuración de la base de datos
$servername = "localhost"; // Cambia esto si tu servidor es diferente
$username = "root"; // Cambia esto por tu usuario de MySQL
$password = ""; // Cambia esto por tu contraseña de MySQL
$dbname = "suscriptores"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$nombre = $_POST['nombre'];
$apellido1 = $_POST['apellido1'];
$apellido2 = $_POST['apellido2'];
$correo = $_POST['correo'];

// Preparar y vincular
$stmt = $conn->prepare("INSERT INTO suscriptores (nombre, apellido1, apellido2, correo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $apellido1, $apellido2, $correo);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "Su información ha sido guardada correctamente.";
} else {
    echo "Error: " . $stmt->error;
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>