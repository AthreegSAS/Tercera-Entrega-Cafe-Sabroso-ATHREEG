<?php
session_start();
include '../conectar.php';

// Verifica si el usuario está logueado y es un gerente
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'gerente') {
    header('Location: ../login.php');
    exit;
}

// Obtiene el nombre del gerente
$nombreGerente = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : 'Gerente';

// Determina qué mostrar: pedidos o votos
$vista = isset($_GET['vista']) ? $_GET['vista'] : 'pedidos';

if ($vista === 'pedidos') {
    // Consulta todos los pedidos
    $stmt = $pdo->query('SELECT pedido.num_pedido, producto.nombre, pedido_producto.cantidad, pedido.estado, pedido.hora, pedido.hora_retiro, pedido.total, 
                                usuario.nombre AS nombre_cliente
                         FROM pedido
                         JOIN pedido_producto ON pedido.num_pedido = pedido_producto.num_pedido
                         JOIN producto ON pedido_producto.Idproducto = producto.Idproducto
                         LEFT JOIN usuario ON pedido.ciCliente = usuario.ci
                         ORDER BY pedido.num_pedido DESC');
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
                'hora_retiro' => $pedido['hora_retiro'],
                'total' => $pedido['total'],
                'cliente' => $pedido['nombre_cliente'],
                'productos' => []
            ];
        }
        $pedidos_agrupados[$num_pedido]['productos'][] = [
            'nombre' => $pedido['nombre'],
            'cantidad' => $pedido['cantidad']
        ];
    }
} elseif ($vista === 'votos') {
    // Consulta todos los votos
    $stmt = $pdo->query('SELECT voto.Idvoto, voto.tiempoEspera, voto.nombreMozo, producto.nombre AS nombre_producto, local.pais, local.ciudad
                         FROM voto
                         JOIN producto ON voto.Idproducto = producto.Idproducto
                         JOIN local ON voto.Idlocal = local.idLocal
                         ORDER BY voto.Idvoto DESC');
    $votos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gerente - Café Sabroso</title>
    <link rel="stylesheet" href="../../style/index5.css">
    <link rel="icon" href="../../img/icon.png">
    <!-- Script de Idiomas -->
    <div class="gtranslate_wrapper"></div>
    <script>window.gtranslateSettings = {"default_language":"es","native_language_names":true,"detect_browser_language":true,"languages":["es","fr","de","pt","en"],"wrapper_selector":".gtranslate_wrapper"}</script>
    <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
</head>

<body>
    <header>
        <div class="logo">
            <a href="../../index.html">
                <img src="../../img/logo nav.png" alt="Café Sabrosos Logo" class="logo-image">
            </a>
        </div>
        <div class="navbar">
            <input type="checkbox" id="menu-toggle" class="menu-toggle" />
            <label for="menu-toggle" class="menu-toggle-label">☰</label>
            <div class="menu">
                <ul>
                    <li><a href="../../index.html">INICIO</a></li>
                    <li><a href="../nosotros.php">NOSOTROS</a>
                        <ul class="submenu">
                            <li><a href="../../html/histotria.html">HISTORIA</a></li>
                            <li><a href="../../html/locales.html">LOCALES</a></li>
                            <li><a href="../mercaderia.php">MERCADERIA</a></li>
                        </ul>
                    </li>
                    <li><a href="../pedido.php">PEDIDO</a></li>
                    <li><a href="../contactar.php">CONTACTANOS</a></li>
                    <li><a href="../logout.php">Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="wave-image-2">
        <div class="slogan-2">
        </div>
    </div>

    <main>
    <br><br>
        <div class="panel-gerente-mozo-chef">
            <h2 class="gerente-nombre">Vista del Gerente: <?php echo htmlspecialchars($nombreGerente ?? ''); ?></h2>
            <div>
                <form method="GET" action="gerente.php">
                    <label for="vista">Seleccionar vista:</label>
                    <select class="btn-agregar" name="vista" id="vista" onchange="this.form.submit()">
                        <option value="pedidos" <?php echo $vista === 'pedidos' ? 'selected' : ''; ?>>Pedidos</option>
                        <option  value="votos" <?php echo $vista === 'votos' ? 'selected' : ''; ?>>Votos</option>
                    </select>
                </form>
            </div>
            <?php if ($vista === 'pedidos'): ?>
                <section class="pedidos-todos">
                    <h3>Todos los Pedidos</h3>
                    <div class="pedidos-grid">
                        <?php foreach ($pedidos_agrupados as $pedido): ?>
                            <div class="pedido">
                                <h4>Pedido #<?php echo htmlspecialchars($pedido['num_pedido'] ?? ''); ?></h4>
                                <p>Estado: <?php echo htmlspecialchars($pedido['estado'] ?? ''); ?></p>
                                <p>Hora: <?php echo htmlspecialchars($pedido['hora'] ?? ''); ?></p>
                                <p>Hora de Retiro: <?php echo htmlspecialchars($pedido['hora_retiro'] ?? ''); ?></p>
                                <p>Total: €<?php echo htmlspecialchars($pedido['total'] ?? ''); ?></p>
                                <p>Cliente: <?php echo htmlspecialchars($pedido['cliente'] ?? ''); ?></p>
                                <ul>
                                    <?php foreach ($pedido['productos'] as $producto): ?>
                                        <li><?php echo htmlspecialchars($producto['nombre'] ?? ''); ?> - Cantidad: <?php echo htmlspecialchars($producto['cantidad'] ?? ''); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php elseif ($vista === 'votos'): ?>
                <section class="votos-todos">
                    <h3>Todos los Votos</h3>
                    <div class="votos-grid">
                        <?php foreach ($votos as $voto): ?>
                            <div class="voto">
                                <h4>Voto #<?php echo htmlspecialchars($voto['Idvoto'] ?? ''); ?></h4>
                                <p>Tiempo de Espera: <?php echo htmlspecialchars($voto['tiempoEspera'] ?? ''); ?></p>
                                <p>Mozo: <?php echo htmlspecialchars($voto['nombreMozo'] ?? ''); ?></p>
                                <p>Producto: <?php echo htmlspecialchars($voto['nombre_producto'] ?? ''); ?></p>
                                <p>Local: <?php echo htmlspecialchars(($voto['pais'] ?? '') . ', ' . ($voto['ciudad'] ?? '')); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <div class="social-media">
            <ul>
                <li><a href="https://www.instagram.com/athreeg_s.a.s/" target="_blank"><img src="../../img/ig.png" alt="Instagram">.</a></li>
                <li><a href="https://x.com/" target="_blank"><img src="../../img/x.png" alt="X"> .</a></li>
                <li><a href="https://www.facebook.com/athreesas/" target="_blank"><img src="../../img/facebook.png" alt="Facebook">.</a></li>
            </ul>
        </div> 

        <p>&copy; Café Sabrosos. A tu servicio desde 1990</p>
    </footer>
</body> 

</html>
