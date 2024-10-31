<?php
session_start();
include 'conectar.php'; // Incluye el archivo de conexión

// Verifica si se ha realizado un pedido
if (!isset($_SESSION['num_pedido']) || !isset($_SESSION['opcion'])) {
    // Muestra el mensaje de error en el cuerpo del HTML
    $error_message = 'Error: No se ha realizado ningún pedido.';
} else {
    $num_pedido = $_SESSION['num_pedido'];
    $opcion = $_SESSION['opcion'];

    // Inicializa las variables para evitar errores de variable indefinida
    $total = 0;
    $hora_retiro = '';
    $productos = [];

    // Recupera la información del pedido
    if ($opcion === 'retirar') {
        $stmt = $pdo->prepare("SELECT hora_retiro, total FROM Pedido WHERE num_pedido = ?");
        $stmt->execute([$num_pedido]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            $error_message = 'Error: No se encontró el pedido.';
        } else {
            $hora_retiro = $pedido['hora_retiro'];
            $total = $pedido['total'];
        }
    } elseif ($opcion === 'comer') {
        $stmt = $pdo->prepare("SELECT producto.nombre, producto.precio 
                               FROM pedido_producto 
                               JOIN producto ON pedido_producto.Idproducto = producto.Idproducto 
                               WHERE pedido_producto.num_pedido = ?");
        $stmt->execute([$num_pedido]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($productos) {
            $stmt = $pdo->prepare("SELECT total FROM pedido WHERE num_pedido = ?");
            $stmt->execute([$num_pedido]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pedido) {
                $total = $pedido['total'];
            }
        }
    } else {
        $error_message = 'Error: Opción de pedido inválida.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Pedido - Café Sabroso</title>
    <link rel="stylesheet" href="../style/index5.css">
    <link rel="icon" href="img/icon.png">
    <style>
        .hidden {
            display: none;
        }
    </style>
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
                    <li><a href="#">ADMINISTRAR</a>
                        <ul class="submenu">
                            <li><a href="logout.php">LOGOUT</a></li>
                            <li><a href="back/votos.php">OPINAR</a></li>
                            <li><a href="login.php">LOGIN</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main class="confirmation">
        <div class="confirmation-header">
            <h3>Confirmación de Pedido</h3>
        </div>
        <div class="confirmation-body">
            <?php if (isset($error_message)): ?>
                <p><?php echo htmlspecialchars($error_message); ?></p>
            <?php elseif ($opcion === 'comer'): ?>
                <p>Tu pedido ha sido registrado con el número: <strong id="pedido-numero"><?php echo htmlspecialchars($num_pedido); ?></strong>.</p>
                <p>Productos en tu pedido:</p>
                <ul>
                    <?php foreach ($productos as $producto): ?>
                        <li><?php echo htmlspecialchars($producto['nombre']); ?> - €<?php echo htmlspecialchars($producto['precio']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Total: €<?php echo htmlspecialchars($total); ?></strong></p>
            <?php elseif ($opcion === 'retirar'): ?>
                <p id="pedido-numero" class="hidden">Tu pedido ha sido registrado con el número: <strong><?php echo htmlspecialchars($num_pedido); ?></strong>.</p>
                <p>Hora estimada de retiro: <strong><?php echo htmlspecialchars($hora_retiro); ?></strong></p>
                <p><strong>Total: €<?php echo htmlspecialchars($total); ?></strong></p>
            <?php else: ?>
                <p>No se pudo completar tu pedido. Por favor, vuelve a intentarlo.</p>
            <?php endif; ?>
        </div>
        <input type="button" value="Hacer otro Pedido" class="btn-hacer-pedido" onclick="location.href='pedido.php'">
    </main>

    <footer>
        <div class="social-media">
            <ul>
                <li><a href="https://www.instagram.com/" target="_blank"><img src="../img/ig.png" alt="Instagram">.</a></li>
                <li><a href="https://x.com/" target="_blank"><img src="../img/x.png" alt="X"> .</a></li>
                <li><a href="https://www.facebook.com/" target="_blank"><img src="../img/facebook.png" alt="Facebook">.</a></li>
            </ul>
        </div>

        <p>&copy; Café Sabrosos. A tu servicio desde 1990</p>
    </footer>
</body>

</html>
