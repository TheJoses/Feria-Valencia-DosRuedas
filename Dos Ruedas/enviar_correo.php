<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; //Cargara automaticamente las dependencias del composer

$mail = new PHPMailer(true);

try {
    //Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; //Servidor SMTP de Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'feriavalenciacorreos@gmail.com'; //Correo que enviara los emails
    $mail->Password = 'mtzgvkajeyjixerj'; //Contraseña de aplicacion para este correo
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
    $mail->Port = 587;

    // Leer el archivo CSV
    if (($handle = fopen('listado.csv', 'r')) !== FALSE) {
        // Saltar la primera línea (encabezados)
        fgetcsv($handle);

        // Leer cada línea del archivo CSV
        while (($data = fgetcsv($handle)) !== FALSE) {
            $nombre = $data[0]; // Nombre
            // $apellidos = $data[1]; // Apellidos (no usados)
            // $edad = $data[2]; // Edad (no usados)
            $email = $data[3]; // Correo electrónico
            // $telefono = $data[4]; // Teléfono (no usados)
            // $codigo_postal = $data[5]; // Código postal (no usados)

            //Comprueba que el email sea correcto antes de enviarlo
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Correo no válido, se omitirá: $email<br>";
                continue; // Saltar a la siguiente fila
            }

            //Remitente y destinatario
            $mail->setFrom('feriavalenciacorreos@gmail.com', 'Feria Valencia');
            $mail->addAddress($email, $nombre); //Destinatario

            //Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Asunto del correo';
            $mail->Body = '<h1>¡Hola ' . htmlspecialchars($nombre) . '!</h1><p>Este es un correo enviado mediante SMTP con PHPMailer.</p>';
            $mail->AltBody = 'Hola ' . $nombre . ', este es el contenido en texto plano para clientes que no soportan HTML.';

            //Enviar correo
            $mail->send();
            echo "Correo enviado a: $nombre <$email><br>";

            //Limpiar destinatarios para el siguiente envío
            $mail->clearAddresses();
        }
        fclose($handle);
    } else {
        echo "No se pudo abrir el archivo CSV.";
    }
} catch (Exception $e) {
    echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
}
?>
