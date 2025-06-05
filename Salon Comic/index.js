const AWS = require('aws-sdk');
const fs = require('fs');
const mjml2html = require('mjml');
const csvParser = require('csv-parser');
const nodemailer = require('nodemailer');

//Configura AWS SES
const ses = new AWS.SES({ region: 'us-east-1' });//Region de AWS

//Traducciones
const translations = {
  es: {
    subject: nombre => `Mensaje personalizado para ${nombre}`,
    saludo: 'Hola buenas',
    despedida: 'Saludos cordiales',
    mensaje: 'tenemos un regalo para usted.'
  },
  en: {
    subject: nombre => `Personalized message for ${nombre}`,
    saludo: 'Hi there',
    despedida: 'Best regards',
    mensaje: 'we have a gift for you.'
  }
};

//Lee CSV
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

//Lee MJML
function readMJMLTemplate(filePath) {
  return fs.readFileSync(filePath, 'utf8');
}

//Personalizar HTML
function personalizeHtml(html, recipient, texts) {
  return html
    .replace(/{{nombre}}/g, recipient.nombre)
    .replace(/{{apellido1}}/g, recipient.apellido1)
    .replace(/{{apellido2}}/g, recipient.apellido2)
    .replace(/{{saludo}}/g, texts.saludo)
    .replace(/{{mensaje}}/g, texts.mensaje)
    .replace(/{{despedida}}/g, texts.despedida);
}

//Envia correos
async function sendEmail(toAddress, subject, htmlBody) {
  const transporter = nodemailer.createTransport({
    service: 'gmail', //Servicio de envio
    auth: {
      user: 'feriavalenciacorreos@gmail.com', //Correo que envia
      pass: 'bhygrngatqkvlern' //Contraseña de aplicacion
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

//Función principal
async function main() {
  try {
    const recipients = await readCSV('correos.csv');
    const mjmlTemplate = readMJMLTemplate('index.mjml');
    const { html } = mjml2html(mjmlTemplate);

    for (const recipient of recipients) {
      //Obtener el idioma del destinatario, por defecto "es" si no existe o no está soportado
      const lang = (recipient.idioma && translations[recipient.idioma.toLowerCase()]) ? recipient.idioma.toLowerCase() : 'es';

      //Obtener textos traducidos para este idioma
      const texts = translations[lang];
      //Personalizar el HTML incluyendo traducciones
      const personalizedHtml = personalizeHtml(html, recipient, texts);
      //Traducción dinámica del asunto
      const subject = texts.subject(recipient.nombre);

      await sendEmail(recipient.correo_electronico, subject, personalizedHtml);
      console.log(`Correo enviado a ${recipient.correo_electronico} [idioma: ${lang}]`);
    }
  } catch (error) {
    console.error('Error:', error);
  }
}

//Ejecutar la función principal
main();
