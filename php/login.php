<?php
session_start();
include 'conectar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Verificar reCAPTCHA
        $secretKey = '6Lf6vPApAAAAAHTh_8BWhn1kW-gnfFYHvHSvIdAA'; // Reemplaza con tu clave secreta
        $responseKey = $_POST['g-recaptcha-response'];
        $userIP = $_SERVER['REMOTE_ADDR'];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";
        $response = file_get_contents($url);
        $responseKeys = json_decode($response, true);

        if (intval($responseKeys["success"]) !== 1) {
            $error = 'Por favor, completa el reCAPTCHA correctamente.';
        } else {
            // Consulta para obtener el usuario
            $stmt = $pdo->prepare("SELECT * FROM usuario WHERE email = :email AND password = :password");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['usuario'] = $usuario;

                // Redirige según el rol del usuario
                switch ($usuario['rol']) {
                    case 'chef':
                        header('Location: back/cocina.php');
                        break;
                    case 'gerente':
                        header('Location: back/gerente.php');
                        break;
                    case 'mozo':
                        header('Location: back/mozo.php');
                        break;
                    default:
                        header('Location: pedido.php');
                        break;
                }
                exit;
            } else {
                $error = 'Email o contraseña incorrectos';
            }
        }
    } else {
        $error = 'Faltan campos del formulario.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Café Sabroso - Login</title>
    <link rel="stylesheet" href="../style/index5.css">
    <link rel="icon" href="../img/icon.png">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Script de Idiomas -->
   <div class="gtranslate_wrapper"></div>
   <script>window.gtranslateSettings = {"default_language":"es","native_language_names":true,"detect_browser_language":true,"languages":["es","fr","de","pt","en"],"wrapper_selector":".gtranslate_wrapper"}</script>
   <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script> 
    
</head>
<body>
    <header>
        <div class="logo">
        <a href="../index.html">
            <img src="../img/logo_nav.png" alt="Café Sabrosos Logo" class="logo-image">
        </a>
        </div>
        <div class="navbar">
            <input type="checkbox" id="menu-toggle" class="menu-toggle" />
            <label for="menu-toggle" class="menu-toggle-label">☰</label>
            <div class="menu">
                <ul>
                    <li><a href="../index.html">INICIO</a></li>
                    <li><a href="nosotros.php">NOSOTROS</a>
                        <ul class="submenu">
                            <li><a href="../html/histotria.html">HISTORIA</a></li>
                            <li><a href="../html/locales.html">LOCALES</a></li>
                            <li><a href="mercaderia.php">MERCADERIA</a></li>
                        </ul>
                    </li>
                    <li><a href="login.php">SESION</a>
                        <ul class="submenu">
                            <li><a href="registro.php">REGISTRO</a></li>
                            <li><a href="login.php">LOGIN</a></li>
                            <li><a href="logout.php">LOGOUT</a></li>
                        </ul>
                    </li>
                    <li><a href="contactar.php">CONTACTANOS</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="wave-image-2">
      <div class="slogan-2">
      </div>
    </div>

    <main class="main-content">
        <section class="login-section">
            <div class="form-container">
                <h2>Iniciar sesión</h2>
                <h4 align="center">Si aún no eres Usuario <a href="registro.php">Regístrate</a></h4>
                <?php if (isset($error)): ?>
                    <p class="error-message"><?php echo $error; ?></p>
                <?php endif; ?>
                <form class="form-content" action="login.php" method="post">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="recaptcha-container">
                    <div class="g-recaptcha" data-sitekey="6Lf6vPApAAAAALqmM0j3qv_-KwKlI-ZrPQOGIbO0"></div> <!-- reCAPTCHA dentro del formulario -->
                    </div>
                    <br>
                    <div class="form-submit">
                        <button type="submit">Iniciar sesión</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <div class="social-media">
            <ul>
                <li><a href="https://www.instagram.com/athreeg_s.a.s/" target="_blank"><img src="../img/ig.png" alt="Instagram">.</a></li>
                <li><a href="https://x.com/" target="_blank"><img src="../img/x.png" alt="X"> .</a></li>
                <li><a href="https://www.facebook.com/athreesas/"" target="_blank"><img src="../img/facebook.png" alt="Facebook">.</a></li>
            </ul>
        </div>
        <p>&copy; Café Sabrosos 2024. A tu servicio desde 1990</p>
    </footer>
</body>
</html>
