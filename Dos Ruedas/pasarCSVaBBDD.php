<?php
$servername = "localhost";
$username = "root"; //Usuario root
$password = ""; //Sin contraseña
$database = "midb";
$csvFile = "/home/ubuntu/enviarEmailAuto/correos.csv";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Abrir el archivo CSV
if (!file_exists($csvFile) || !is_readable($csvFile)) {
    die("Archivo CSV no encontrado o no legible.");
}

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Preparar la consulta de inserción con sentencias preparadas
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido1, apellido2, correo_electronico, idioma) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la sentencia: " . $conn->error);
    }

    // Saltar la fila de encabezado (si hay)
    $header = fgetcsv($handle);

    // Leer cada fila y ejecutar inserciones
    while (($data = fgetcsv($handle)) !== FALSE) {
        // Suposición: el CSV tiene 5 columnas ordenadas así:
        // nombre, apellido1, apellido2, correo_electronico, idioma
        if (count($data) < 5) {
            // Si fila incompleta, saltar
            continue;
        }

        $nombre = $data[0];
        $apellido1 = $data[1];
        $apellido2 = $data[2];
        $correo_electronico = $data[3];
        $idioma = $data[4];

        // Ejecutar inserción
        $stmt->bind_param("sssss", $nombre, $apellido1, $apellido2, $correo_electronico, $idioma);
        $stmt->execute();
    }

    $stmt->close();
    fclose($handle);
    echo "Datos importados correctamente.";
} else {
    die("No se pudo abrir el archivo CSV.");
}

$conn->close();
?>
