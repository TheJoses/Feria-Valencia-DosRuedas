const AWS = require('aws-sdk');
const fs = require('fs');
const mjml2html = require('mjml');
const csvParser = require('csv-parser');
const nodemailer = require('nodemailer');

// Configura AWS SES
const ses = new AWS.SES({ region: 'us-east-1' }); // Cambia la región si es necesario

// Función para leer el archivo CSV
function readCSV(filePath) {
  return new Promise((resolve, reject) => {
    const results = [];
    fs.createReadStream(filePath)
      .pipe(csvParser())
      .on('data', (data) => results.push(data))
      .on('end', () => resolve(results))
      .on('error', (error) => reject(error));
  });
}

// Función para leer la plantilla MJML
function readMJMLTemplate(filePath) {
  return fs.readFileSync(filePath, 'utf8');
}

// Función para personalizar el HTML
function personalizeHtml(html, recipient) {
  return html
    .replace(/{{nombre}}/g, recipient.nombre)
    .replace(/{{apellido1}}/g, recipient.apellido1)
    .replace(/{{apellido2}}/g, recipient.apellido2);
}

// Función para enviar correos electrónicos
async function sendEmail(toAddress, subject, htmlBody) {
  const transporter = nodemailer.createTransport({
    service: 'gmail', // Cambia esto si usas otro servicio
    auth: {
      user: 'feriavalenciacorreos@gmail.com', // Cambia por tu correo
      pass: 'clflhypynxqdplse' // Cambia por tu contraseña
    }
  });

  const mailOptions = {
    from: 'feriavalenciacorreos@gmail.com',
    to: toAddress,
    subject: subject,
    html: htmlBody
  };

  return transporter.sendMail(mailOptions);
}

// Función principal
async function main() {
  try {
    const recipients = await readCSV('correos.csv');
    const mjmlTemplate = readMJMLTemplate('index.mjml');
    const { html } = mjml2html(mjmlTemplate);

    for (const recipient of recipients) {
      const personalizedHtml = personalizeHtml(html, recipient);
      const subject = `Mensaje personalizado para ${recipient.nombre}`;
      await sendEmail(recipient['correo_electrónico'], subject, personalizedHtml);
      console.log(`Correo enviado a ${recipient['correo_electrónico']}`);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

// Ejecutar la función principal
main();
