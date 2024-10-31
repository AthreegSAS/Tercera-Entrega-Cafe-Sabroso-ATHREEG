<?php
// Cargar productos desde archivo PHP
include 'productos.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="cafe">
    <meta name="author" content="Cafe Sabrosos">
    <title>Café Sabrosos - Mercadería</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
        integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
        crossorigin="" />
    <link rel="stylesheet" href="../style/index5.css">
    <link rel="icon" href="../img/icon.png">
    <!-- Script de Idiomas -->
   <div class="gtranslate_wrapper"></div>
   <script>window.gtranslateSettings = {"default_language":"es","native_language_names":true,"detect_browser_language":true,"languages":["es","fr","de","pt","en"],"wrapper_selector":".gtranslate_wrapper"}</script>
   <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script> 
    
</head>
<body>
    <header>
        <div class="logo">
            <img src="../img/logo_nav.png" alt="Café Sabrosos Logo" class="logo-image">
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
                    </ul></li>
                    <li><a href="../php/pedido.php">PEDIDO</a></li>
                    <li><a href="../php/back/votos.php">OPINAR</a></li>
                    <li><a href="../php/login.php">LOGIN</a></li>
                    <li><a href="../php/contactar.php">CONTACTANOS</a></li>
                </ul>
            </div> 
        </div>
         
    </header>
    <main>
        <div class="wave-image">
        <div class="slogan">
            <h1>Descubre Nuestra Mercadería Exclusiva</h1>
        </div>
        </div>
        
        <div class="content">
            <h1 class="titulo">Te ofrecemos</h1>
            <p class="descripcion">  
                Aquí puedes ver varios de nuestros productos, tales como remeras, tazas, entre otras con nuestro logo. Próximamente estarán disponibles para la venta.
            </p>
            
            <!-- Carrusel de Productos -->
            <div class="carousel">
                <div class="carousel-inner">
                    <?php foreach ($productos as $producto): ?>
                        <div class="carousel-item">
                            <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                            <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <p class="precio">Precio: €<?php echo number_format($producto['precio'], 2); ?></p>
                            <p class="proximamente">Próximamente disponible</p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-button prev" onclick="moveCarousel(-1)">&#10094;</button>
                <button class="carousel-button next" onclick="moveCarousel(1)">&#10095;</button>
            </div>

            <!-- Nuevo texto y botón para dirigir a locales.html -->
            <div class="locales-info">
                <p>¿Quieres saber dónde encontrarnos?</p>
                <p>Descubre nuestros locales.</p>
                <a href="../html/locales.html" class="locales-button">Ver Locales</a>
            </div>
        </div>
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

    <script>
        let currentIndex = 0;
        const totalItems = document.querySelectorAll('.carousel-item').length;

        function moveCarousel(direction) {
            currentIndex = (currentIndex + direction + totalItems) % totalItems;
            updateCarousel();
        }

        function updateCarousel() {
            const carouselInner = document.querySelector('.carousel-inner');
            carouselInner.style.transform = `translateX(-${currentIndex * 100}%)`;
        }

        // Función para mover el carrusel automáticamente
        function autoSlide() {
            moveCarousel(1);
        }

        // Iniciar el deslizamiento automático inmediatamente
        let autoSlideInterval = setInterval(autoSlide, 5000); // Cambia cada 5 segundos

        // Detener el deslizamiento automático cuando el usuario interactúa con el carrusel
        document.querySelector('.carousel').addEventListener('mouseenter', () => {
            clearInterval(autoSlideInterval);
        });

        // Reanudar el deslizamiento automático cuando el usuario deja de interactuar
        document.querySelector('.carousel').addEventListener('mouseleave', () => {
            autoSlideInterval = setInterval(autoSlide, 5000);
        });

        // Asegurarse de que el carrusel comience a deslizarse automáticamente cuando se carga la página
        document.addEventListener('DOMContentLoaded', () => {
            updateCarousel(); // Asegura que el carrusel esté en la posición correcta al inicio
        });
    </script>
</body>
</html>
