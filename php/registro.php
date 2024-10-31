<?php
// Incluir el archivo de conexión
include 'conectar.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ci = $_POST['ci'];
    $nombre = $_POST['nombre'];
    $celular = $_POST['celular'];
    $direccion = $_POST['direccion'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Contraseña sin encriptamiento
    $rol = NULL; // Rol como NULL

    // Verificar el reCAPTCHA
    $responseKey = $_POST['g-recaptcha-response'];
    $userIP = $_SERVER['REMOTE_ADDR'];
    $secretKey = '6Lf6vPApAAAAAHTh_8BWhn1kW-gnfFYHvHSvIdAA'; // Tu clave secreta
    $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKey&remoteip=$userIP";
    $response = file_get_contents($url);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        $message = 'Por favor, completa el reCAPTCHA correctamente.';
    } else {
        // Verificar si el CI ya existe
        $checkSql = "SELECT COUNT(*) FROM usuario WHERE CI = :ci";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(':ci', $ci);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $message = "Error: El CI ya está registrado.";
        } else {
            $sql = "INSERT INTO usuario (CI, nombre, celular, direccion, email, password, rol) VALUES (:ci, :nombre, :celular, :direccion, :email, :password, :rol)";
            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':ci', $ci);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':celular', $celular);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindValue(':rol', $rol, PDO::PARAM_NULL); // Asignar NULL al rol

            if ($stmt->execute()) {
                // Si el rol es NULL, insertar en la tabla Cliente
                if (is_null($rol)) {
                    $clienteSql = "INSERT INTO cliente (ciCliente) VALUES (:ci)";
                    $clienteStmt = $pdo->prepare($clienteSql);
                    $clienteStmt->bindParam(':ci', $ci);
                    $clienteStmt->execute();
                }
                $message = "¡Bienvenido a Café Sabrosos!";
            } else {
                $message = "Error: No se pudo registrar el usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../style/index5.css">
    <link rel="icon" href="../img/icon.png" />
    <script src="https://www.google.com/recaptcha/api.js" async defer></script> <!-- Para que Funcione el recaptcha -->

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
                    <li><a href="#">SESION</a>
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


    <section class="contact-section">
        <h3>Registro de Usuario</h3>
        <?php if (!empty($message)): ?>
            <p><?php echo $message; ?></p>
        <?php else: ?>
            <form class="register-form" method="post" action="">
                <div class="form-group">
                    <label for="ci">CI:</label>
                    <input type="text" id="ci" name="ci" required />
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required />
                </div>
                <div class="form-group">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" required />
                </div>
                <div class="form-group">
                    <label for="celular">Celular:</label>
                    <input type="tel" id="celular" name="celular" required />
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" required />
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="recaptcha-container">
                <div class="g-recaptcha" data-sitekey="6Lf6vPApAAAAALqmM0j3qv_-KwKlI-ZrPQOGIbO0"></div>
                </div>
                <br>
                <button type="submit" class="btn-submit">Registrar</button>
            </form>
        <?php endif; ?>
    </section>
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
