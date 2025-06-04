const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();
const port = 3000;

// Middleware
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Configuración de la conexión a la base de datos
const db = mysql.createConnection({
    host: 'localhost',
    user: 'tu_usuario',
    password: 'tu_contraseña',
    database: 'sorteos'
});

// Conectar a la base de datos
db.connect((err) => {
    if (err) {
        console.error('Error de conexión: ' + err.stack);
        return;
    }
    console.log('Conectado a la base de datos MySQL');
});

// Ruta para manejar la participación
app.post('/participar', (req, res) => {
    const { nombre, apellidos, edad, email, telefono, codigoPostal } = req.body;

    const query = 'INSERT INTO participaciones (nombre, apellidos, edad, email, telefono, codigo_postal) VALUES (?, ?, ?, ?, ?, ?)';
    db.query(query, [nombre, apellidos, edad, email, telefono, codigoPostal], (err, result) => {
        if (err) {
            return res.status(500).send('Error al guardar la participación: ' + err);
        }
        res.status(200).send('¡Participación enviada con éxito!');
    });
});

// Iniciar el servidor
app.listen(port, () => {
    console.log(`Servidor escuchando en http://localhost:${port}`);
});
