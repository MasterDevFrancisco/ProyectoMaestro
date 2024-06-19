<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            color: #555;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .password {
            display: inline-block;
            background-color: #f1f1f1;
            border-radius: 4px;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Nueva Contraseña Generada!</h1>
        <p>Tu nueva contraseña de cuenta es:</p>
        <div class="password">{{ $password }}</div>
    </div>
</body>
</html>
