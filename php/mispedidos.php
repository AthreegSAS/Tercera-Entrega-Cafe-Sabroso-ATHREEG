<?php
session_start();
include 'conectar.php'; // Incluye el archivo de conexión

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php'); // Redirige al login si no está logueado
    exit;
}

// Verifica si el CI del cliente está en la sesión
if (!isset($_SESSION['usuario']['CI'])) {
    echo "Error: No se encontró el CI del usuario en la sesión.";
    exit;
}

// Obtiene el CI del cliente logueado
$ciCliente = $_SESSION['usuario']['CI'];

// Consulta los pedidos del cliente actual
$stmt = $pdo->prepare('SELECT pedido.num_pedido, pedido.estado, pedido.hora, 
                              producto.nombre, producto.precio, pedido_producto.cantidad
                       FROM pedido
                       JOIN pedido_producto ON pedido.num_pedido = pedido_producto.num_pedido
                       JOIN producto ON pedido_producto.Idproducto = producto.Idproducto
                       WHERE pedido.ciCliente = ?
                       ORDER BY pedido.num_pedido DESC');
$stmt->execute([$ciCliente]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar los pedidos por número de pedido
$pedidos_agrupados = [];
foreach ($pedidos as $pedido) {
    $num_pedido = $pedido['num_pedido'];
    if (!isset($pedidos_agrupados[$num_pedido])) {
        $pedidos_agrupados[$num_pedido] = [
            'num_pedido' => $num_pedido,
            'estado' => $pedido['estado'],
            'hora' => $pedido['hora'],
            'productos' => [],
            'total' => 0
        ];
    }
    $pedidos_agrupados[$num_pedido]['productos'][] = [
        'nombre' => $pedido['nombre'],
        'cantidad' => $pedido['cantidad'],
        'precio' => $pedido['precio']
    ];
    $pedidos_agrupados[$num_pedido]['total'] += $pedido['precio'] * $pedido['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - Café Sabroso</title>
    <link rel="stylesheet" href="../style/index5.css">
    <link rel="icon" href="img/icon.png">
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
                    <li><a href="#">NOSOTROS</a>
                        <ul class="submenu">
                            <li><a href="../html/histotria.html">HISTORIA</a></li>
                            <li><a href="../html/locales.html">LOCALES</a></li>
                            <li><a href="mercaderia.php">MERCANCIA</a></li>
                        </ul>
                    </li>
                    <li><a href="pedido.php">PEDIDO</a></li>
                    <li><a href="contactar.php">CONTACTANOS</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>
                    <li class="cart">
                        <a href="carrito.php">
                            <img src="../img/carrito.png" alt="Carrito de Compras" width="20" height="20" style="vertical-align: middle; margin-right: 5px;">
                            <span id="cart-count"><?php echo count($_SESSION['carrito']); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <main>
        <div class="panel-gerente-mozo-chef">
            <h2 class="mozo-nombre">Mis Pedidos</h2>
            <div class="panel-mozo-container">
                <section class="pedidos-actuales">
                    <h3>Pedidos Actuales</h3>
                    <div class="pedidos-grid">
                        <?php foreach ($pedidos_agrupados as $pedido): ?>
                            <div class="pedido <?php echo $pedido['estado']; ?>">
                                <h4>Pedido #<?php echo htmlspecialchars($pedido['num_pedido']); ?></h4>
                                <p>Estado: <?php echo htmlspecialchars(ucfirst($pedido['estado'])); ?></p>
                                <p>Hora: <?php echo htmlspecialchars($pedido['hora']); ?></p>
                                <ul>
                                    <?php foreach ($pedido['productos'] as $producto): ?>
                                        <li><?php echo htmlspecialchars($producto['nombre']); ?> - Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?> - Precio: €<?php echo htmlspecialchars($producto['precio']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <p>Total: €<?php echo htmlspecialchars($pedido['total']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
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
</body>
</html>