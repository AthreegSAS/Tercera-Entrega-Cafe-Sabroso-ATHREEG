<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="cafe" />
  <meta name="author" content="Cafe Sabrosos" />
  <title>Café Sabrosos - Nosotros</title>

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin="" />
  <link rel="stylesheet" href="../style/index5.css" />
  <link rel="icon" href="../img/icon.png" />
<!-- Script de Idiomas -->
<div class="gtranslate_wrapper"></div>
   <script>window.gtranslateSettings = {"default_language":"es","native_language_names":true,"detect_browser_language":true,"languages":["es","fr","de","pt","en"],"wrapper_selector":".gtranslate_wrapper"}</script>
   <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>



</head>

<body>
  <header>
    <div class="logo">
      <img
        src="../img/logo nav.png"
        alt="Café Sabrosos Logo"
        class="logo-image" />
    </div>
    <div class="navbar">
      <input type="checkbox" id="menu-toggle" class="menu-toggle" />
      <label for="menu-toggle" class="menu-toggle-label">☰</label>
      <div class="menu">
        <ul>
          <li><a href="../index.html">INICIO</a></li>
          <li>
            <a href="nosotros.php">NOSOTROS</a>
            <ul class="submenu">
              <li><a href="../html/histotria.html">HISTORIA</a></li>
              <li><a href="../html/locales.html">LOCALES</a></li>
              <li><a href="mercaderia.php">MERCANCIA</a></li>
            </ul>
          </li>
          <li><a href="../php/pedido.php">PEDIDO</a></li>
          <li><a href="../php/back/votos.php">OPINAR</a></li>
          <li><a href="../php/login.php">LOGIN</a></li>
          <li><a href="../php/contactar.php">CONTACTANOS</a></li>
        </ul>
      </div>
    </div>
  </header>
  <div class="wave-image">
      <div class="slogan">
        <h1>Un poco de Nosotros...</h1>
      </div>
    </div>
  <main>
   <div class="content">
      <h1>Creando Vínculos con la Sociedad</h1>
      <pre class="par">
      <span class="highlight">  Sabrosos no era solo un café;</span> era un lugar donde las historias se tejían entre sorbos de espresso. Los vecinos compartían sus alegrías y penas en las mesas de madera desgastada. Los estudiantes estudiaban para sus exámenes finales mientras el aroma del café flotaba en el aire. Los turistas, al descubrirlo, lo consideraban su secreto mejor guardado.

        La familia Valdez tenía un fuerte compromiso con la comunidad. Patrocinaban eventos locales, donaban café a la biblioteca y ofrecían descuentos a los maestros. <span class="highlight">La gente no solo venía por el café, sino también por la calidez y el sentido de pertenencia que encontraban en Sabrosos.</span>
      </pre>
    </div>

    <h2>Queremos compartir contigo los productos de nuestra cafetería</h2>

    <?php
    include '../php/conectar.php'; // Incluir el archivo de conexión

    $imagenes = [];
    // Consulta para obtener las imágenes y los nombres de los productos
    $sql = "SELECT p.nombre, f.url FROM producto p JOIN foto f ON p.idproducto = f.idFoto"; // Asegúrate de que la relación sea correcta
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $imagenes[] = $row;
      }
    }
    ?>

<?php if (count($imagenes) > 0): ?>
      <div class="custom-carousel">
        <div class="carousel-inner">
          <?php foreach ($imagenes as $index => $imagen): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
              <img src="../<?= htmlspecialchars($imagen['url']) ?>" alt="<?= htmlspecialchars($imagen['nombre']) ?>" />
              <div class="product-name"><?= htmlspecialchars($imagen['nombre']) ?></div> <!-- Mostrar el nombre del producto -->
              <p class="carousel-text">Sabrosos no era solo un café; era un lugar donde los aromas se mezclaban con historias y cada sorbo era una experiencia única.</p>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="carousel-control prev" onclick="moveSlide(-1)">❮</button>
        <button class="carousel-control next" onclick="moveSlide(1)">❯</button>
        <div class="progress-bar"></div>
      </div>
    <?php else: ?>
      <p>No hay imágenes disponibles.</p>
    <?php endif; ?>



    <script>
      let currentIndex = 0;
      const items = document.querySelectorAll('.carousel-item');
      const totalItems = items.length;
      let intervalId;

      function moveSlide(direction) {
        currentIndex = (currentIndex + direction + totalItems) % totalItems;
        updateCarousel();
      }

      function updateCarousel() {
        const offset = -currentIndex * 100;
        document.querySelector('.carousel-inner').style.transform = `translateX(${offset}%)`;

        items.forEach((item, index) => {
          item.classList.toggle('active', index === currentIndex);
        });

        resetProgressBar();
      }

      function autoSlide() {
        moveSlide(1);
      }

      function startAutoSlide() {
        intervalId = setInterval(autoSlide, 5000);
      }

      function stopAutoSlide() {
        clearInterval(intervalId);
      }

      function resetProgressBar() {
        const progressBar = document.querySelector('.progress-bar');
        progressBar.style.width = '0%';
        setTimeout(() => {
          progressBar.style.width = '100%';
        }, 50);
      }

      document.querySelector('.custom-carousel').addEventListener('mouseenter', stopAutoSlide);
      document.querySelector('.custom-carousel').addEventListener('mouseleave', startAutoSlide);

      startAutoSlide();
      updateCarousel();
    </script>
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
  </footer>
</body>

</html>