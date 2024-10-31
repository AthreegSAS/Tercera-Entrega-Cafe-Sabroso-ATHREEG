<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="cafe">
    <meta name="author" content="Cafe Sabrosos">
    <title>Cuenta</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../style/contactar.css">
    <link rel="icon" href="../img/icon.png">
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
                        
                <li><a #">ADMINISTRAR</a>
                    <ul class="submenu">
                        <li><a href="mispedidos.php">MIS PEDIDOS</a></li>
                        <li><a href="logout.php">LOGOUT</a></li>
                        <li><a href="back/votos.php">OPINAR</a></li>
                        <li><a href="login.php">LOGIN</a></li>
                    </ul>
                </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="wave-image">
      <div class="slogan">
      </div>
    </div>
    <main>
        <section class="contact-section">
        <h1>Contactanos</h1>
        <div class="contact-container">
            <form class="contact-form" action="enviar.php" method="post">
                <div class="inline-fields">
                    <div class="field nombre">
                        <label for="nombre">Nombre y Apellido:</label>
                        <input type="text" id="nombre" name="nombre" required />
                    </div>
                    <div class="field email">
                        <label for="email">Correo electrónico:</label>
                        <input type="email" id="email" name="email" required />
                    </div>
                </div>
                <div class="field asunto">
                    <label for="asunto">Asunto:</label>
                    <textarea name="asunto" id="asunto" placeholder="Asunto:" cols="60" rows="10"></textarea>
                </div>
                <div class="g-recaptcha" data-sitekey="6Lf6vPApAAAAALqmM0j3qv_-KwKlI-ZrPQOGIbO0"></div>
                <br> <button type="submit">Enviar</button>
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
