<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="cafe">
    <meta name="author" content="Cafe Sabrosos">

    <title>Registrado correctamente</title>
    <link rel="icon" href="#">
    <style>
        body {
            background: #f5e7dc;
            background-size: cover;
            background-attachment: fixed;
            position: relative;
        }

        header {
            background-color: #322800;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        a {
            color: #FFF;
            text-decoration: none;
            text-size-adjust: 18px;

            margin: 15px;
            padding: 15px;
        }
    </style>
    <!-- Script de Idiomas -->
   <div class="gtranslate_wrapper"></div>
   <script>window.gtranslateSettings = {"default_language":"es","native_language_names":true,"detect_browser_language":true,"languages":["es","fr","de","pt","en"],"wrapper_selector":".gtranslate_wrapper"}</script>
   <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script> 
    
</head>

<body>
    <header>
        <div>
            <a href="../index.html"><img class="logo" src="../img/logo_nav.png" height="20%" width="25%" alt=""></a>
            <div>
                <nav><a href="../index.html">Inicio</a></nav>
            </div>
        </div>

    </header>
    <?php
    ini_set('default_charset', 'UTF-8');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $secretKey = '6Lf6vPApAAAAAHTh_8BWhn1kW-gnfFYHvHSvIdAA';
        $responseKey = $_POST['g-recaptcha-response'];
        $userIP = $_SERVER['REMOTE_ADDR'];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";
        $response = file_get_contents($url);
        $responseKeys = json_decode($response, true);

        if (intval($responseKeys["success"]) !== 1) {
            echo 'Por favor, completa el reCAPTCHA correctamente. Volver a <a href="../html/contacto.html">contacto</a>';
        } else {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $asunto = "Nuevo contacto desde Cafe Sabroso";
            $mensaje = "Detalles del contacto:\n\nNombre: $nombre\nCorreo electrónico: $email";

            $mail = new PHPMailer(true);
            try {
                // Configuración del servidor
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'cafesabroso1990@gmail.com';
                $mail->Password = 'greftmdofsshnhsq'; // Reemplaza con la contraseña de la aplicación generada
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Configuración para ignorar certificados SSL (solo para pruebas)
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    )
                );

                // Configuración de charset y codificación
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';

                // Remitente y destinatario
                $mail->setFrom('cafesabroso1990@gmail.com', 'Cafe Sabroso');
                $mail->addAddress('cafesabroso1990@gmail.com', 'Desde Cafe Sabroso');

                // Contenido del correo
                $mail->isHTML(false);
                $mail->Subject = $asunto;
                $mail->Body = $mensaje;

                // Enviar correo
                $mail->send();
                echo 'Correo electrónico enviado exitosamente.';
            } catch (Exception $e) {
                echo "Error al enviar el correo electrónico: {$mail->ErrorInfo}";
            }
        }
    } else {
        echo 'Método de solicitud no permitido.';
    }
    ?>
</body>

</html>