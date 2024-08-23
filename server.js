const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql2');

const app = express();
app.use(bodyParser.urlencoded({ extended: true }));

// Configuración de la base de datos
const db = mysql.createConnection({
    host: 'localhost',
    user: 'root', // Reemplaza con tu usuario de MySQL
    password: '', // Reemplaza con tu contraseña de MySQL
    database: 'usuarios' // La base de datos que ya tienes configurada para el login
});

db.connect((err) => {
    if (err) {
        console.error('Error conectando a la base de datos:', err);
        return;
    }
    console.log('Conectado a la base de datos MySQL');
});

// Ruta para manejar el formulario
app.post('/submit-form', (req, res) => {
    const { 
        'tipo-mascota': tipoMascota, 
        nombre, 
        'fecha-rescate': fechaRescate, 
        edad, 
        discapacidad, 
        'detalles-discapacidad': detallesDiscapacidad 
    } = req.body;

    const query = `
        INSERT INTO mascotas 
        (tipo_mascota, nombre, fecha_rescate, edad, discapacidad, detalles_discapacidad)
        VALUES (?, ?, ?, ?, ?, ?)
    `;
    db.query(query, [tipoMascota, nombre, fechaRescate, edad, discapacidad, detallesDiscapacidad], (err, result) => {
        if (err) {
            console.error('Error ejecutando la consulta:', err);
            res.status(500).send('Error en el servidor');
            return;
        }
        res.send('Formulario enviado con éxito');
    });
});

app.listen(3000, () => {
    console.log('Servidor corriendo en el puerto 3000');
});

