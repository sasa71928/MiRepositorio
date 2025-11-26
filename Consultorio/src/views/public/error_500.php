<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicio no disponible - CliniGest</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
            color: #2c4964;
        }
        .error-card {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
        }
        h1 { color: #dc3545; font-size: 2rem; margin-bottom: 10px; }
        p { color: #6c757d; margin-bottom: 20px; line-height: 1.5; }
        .icon { font-size: 4rem; margin-bottom: 20px; display: block; }
        .btn {
            background-color: #1977cc;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn:hover { background-color: #166ab5; }
    </style>
</head>
<body>
    <div class="error-card">
        <span class="icon">🚑</span>
        <h1>Ups, algo salió mal</h1>
        <p>Nuestro sistema está experimentando problemas técnicos momentáneos. El equipo de soporte ya ha sido notificado automáticamente.</p>
        <p>Por favor, intenta recargar la página en unos minutos.</p>
        <a href="/ProyectoConsultorio/Consultorio/public/" class="btn">Volver al Inicio</a>
    </div>
</body>
</html>