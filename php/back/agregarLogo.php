<?php
// Directorios de las imágenes y el logo
$directorioOrigen = '../../img/SinLogo'; // Directorio de imágenes originales
$directorioDestino = '../../img'; // Directorio donde se guardan las imágenes con el logo
$logo = '../../img/logo_navTransparente.png'; // Ruta del logo

// Crear el directorio de destino si no existe
if (!is_dir($directorioDestino)) {
    mkdir($directorioDestino, 0755, true);
}

// Obtener todas las imágenes JPG del directorio de origen
$imagenes = glob($directorioOrigen . '/*.jpg');

foreach ($imagenes as $imagenOriginal) {
    // Cargar la imagen original y el logo
    $imagen = imagecreatefromjpeg($imagenOriginal);
    $marca = imagecreatefrompng($logo);

    if (!$marca) {
        die("Error: No se pudo cargar el logo desde la ruta: $logo");
    }

    // Obtener dimensiones de la imagen original y del logo
    $anchoImagen = imagesx($imagen);
    $altoImagen = imagesy($imagen);
    $anchoLogoOriginal = imagesx($marca);
    $altoLogoOriginal = imagesy($marca);

    // Definir el nuevo tamaño del logo (ajusta estos valores según sea necesario)
    $factorRedimensionamiento = 0.5; // Cambia este valor para hacer el logo más pequeño o más grande
    $anchoLogo = $anchoLogoOriginal * $factorRedimensionamiento;
    $altoLogo = $altoLogoOriginal * $factorRedimensionamiento;

    // Crear una nueva imagen para el logo redimensionado
    $marcaRedimensionada = imagecreatetruecolor($anchoLogo, $altoLogo);
    imagealphablending($marcaRedimensionada, false);
    imagesavealpha($marcaRedimensionada, true);
    $transparente = imagecolorallocatealpha($marcaRedimensionada, 255, 255, 255, 127);
    imagefilledrectangle($marcaRedimensionada, 0, 0, $anchoLogo, $altoLogo, $transparente);

    // Redimensionar el logo
    imagecopyresampled($marcaRedimensionada, $marca, 0, 0, 0, 0, $anchoLogo, $altoLogo, $anchoLogoOriginal, $altoLogoOriginal);

    // Calcular la posición para colocar el logo (esquina inferior derecha)
    $x = $anchoImagen - $anchoLogo - 10; // Margen de 10px
    $y = $altoImagen - $altoLogo - 10; // Margen de 10px

    // Combinar el logo redimensionado con la imagen
    imagecopy($imagen, $marcaRedimensionada, $x, $y, 0, 0, $anchoLogo, $altoLogo);

    // Guardar la imagen con el logo en el directorio de destino
    $nombreArchivo = $directorioDestino . '/' . basename($imagenOriginal);
    imagejpeg($imagen, $nombreArchivo);

    // Liberar memoria
    imagedestroy($imagen);
    imagedestroy($marca);
    imagedestroy($marcaRedimensionada);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Logo - Café Sabroso</title>
    <link rel="stylesheet" href="../../style/index5.css">
    <link rel="icon" href="../../img/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!-- Incluir fuente Poppins -->
    <style>
        body {
            font-family: 'Poppins', sans-serif; /* Aplicar fuente Poppins */
        }
        .mensaje-confirmacion {
            color: white; /* Color blanco */
            text-align: center; /* Centrar el texto */
            margin: 20px 0; /* Margen superior e inferior */
        }
        .mensaje-confirmacion a {
            color: white; /* Color blanco para el enlace */
            text-decoration: underline; /* Subrayar el enlace */
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <a href="../../index.html">
                <img src="../../img/logo nav.png" alt="Café Sabrosos Logo" class="logo-image">
            </a>
        </div>
        <!-- Mensaje de confirmación -->
        <div class="mensaje-confirmacion">
            <p>Imágenes procesadas y guardadas en <?php echo htmlspecialchars($directorioDestino); ?>.</p>
            <a href="cocina.php">Volver a Cocina</a>
        </div>
    </header>
    
    <!-- Aquí va el contenido específico de agregarLogo.php -->

    <footer>
        <div class="social-media">
            <ul>
                <li><a href="https://www.instagram.com/" target="_blank"><img src="../../img/ig.png" alt="Instagram">.</a></li>
                <li><a href="https://x.com/" target="_blank"><img src="../../img/x.png" alt="X"> .</a></li>
                <li><a href="https://www.facebook.com/" target="_blank"><img src="../../img/facebook.png" alt="Facebook">.</a></li>
            </ul>
        </div>
        <p>&copy; Café Sabrosos. A tu servicio desde 1990</p>
    </footer>
</body>

</html>
