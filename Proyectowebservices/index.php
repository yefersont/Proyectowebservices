<?php
session_start(); // Inicia la sesión

// Verificar si ya está logueado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    header('Location: dashboard.php'); // Si ya está logueado, redirigir al dashboard
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    // Definir el token de seguridad
    $token = "12345"; // El token que el servidor de servicios SOAP requiere

    // Solicitud SOAP
    $soapRequest = "
    <soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:urn='urn:miserviciowsdl'>
        <soapenv:Header/>
        <soapenv:Body>
            <urn:Servidor.Login>
                <usuario>$usuario</usuario>
                <contraseña>$contraseña</contraseña>
                <token>$token</token>
            </urn:Servidor.Login>
        </soapenv:Body>
    </soapenv:Envelope>
    ";

    $url = "http://127.0.0.1/webservices/Proyectowebservices/invocar.php"; // URL del servicio SOAP
    $headers = array(
        "Content-Type: text/xml"
    );
    

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $soapRequest);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $xml = simplexml_load_string($response);
        $result = (string)$xml->xpath("//return")[0];

        if ($result == "Login exitoso") {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['logged_in'] = true; 
            header('Location: dashboard.php'); 
            exit();
        } else {
            $error_message = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error_message = "Error en la conexión con el servidor.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
        }
        .container {
            display: flex;
            width: 800px;
            height: 400px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-container h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover {
            background: linear-gradient(to right, #feb47b, #ff7e5f);
        }
        .info-container {
            flex: 1;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 40px;
        }
        .info-container h2 {
            margin-bottom: 15px;
        }
        .info-container p {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Iniciar Sesión</h2>
            <form method="POST">
                <div class="input-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" name="usuario" placeholder="Correo electrónico" required>
                </div>
                <div class="input-group">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" name="contraseña" placeholder="***********" required>
                </div>
                <button type="submit" class="btn">Ingresar</button>
            </form>
            <?php if (isset($error_message)): ?>
                <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>
        <div class="info-container">
            <h2>Bienvenidos a nuestro servicio</h2>
            <p>Inicia sesión para acceder a tu panel de control. ¡Nos alegra tenerte aquí!</p>
        </div>
    </div>
</body>
</html>
