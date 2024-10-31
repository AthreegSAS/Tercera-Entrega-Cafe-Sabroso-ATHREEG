<?php
session_start();
include '../conectar.php';

// Verifica si el usuario está logueado y es un mozo
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'mozo') {
    header('Location: login.php');
    exit;
}

// Obtiene el nombre del mozo
$nombreMozo = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : 'Mozo';

// Inicializa el carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Manejo del formulario del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $idproducto = $_POST['idproducto'];
        $cantidad = $_POST['cantidad'];
        $_SESSION['carrito'][$idproducto] = isset($_SESSION['carrito'][$idproducto]) ? $_SESSION['carrito'][$idproducto] + $cantidad : $cantidad;
    } elseif (isset($_POST['checkout'])) {
        $opcion = $_POST['opcion'];
        $ciCliente = $_POST['ciCliente'];
        $hora = date('H:i:s');
        $hora_retiro = null;
        $num_pedido = null;

        // Inserta el pedido en la base de datos
        $stmt = $pdo->prepare("INSERT INTO pedido (hora, ciCliente) VALUES (NOW(), ?)");
        $stmt->execute([$ciCliente]);
        $num_pedido = $pdo->lastInsertId();

        if ($opcion === 'retirar') {
            $hora_retiro = date('H:i:s', strtotime($hora) + 45 * 60);
            $stmt = $pdo->prepare("UPDATE pedido SET hora_retiro = ? WHERE num_pedido = ?");
            $stmt->execute([$hora_retiro, $num_pedido]);
        }

        // Inserta los productos en el pedido
        foreach ($_SESSION['carrito'] as $idproducto => $cantidad) {
            $stmt = $pdo->prepare("INSERT INTO pedido_producto (Idproducto, num_pedido, cantidad) VALUES (?, ?, ?)");
            $stmt->execute([$idproducto, $num_pedido, $cantidad]);
        }

        // Limpia el carrito
        $_SESSION['carrito'] = [];

        // Redirige a la misma página para evitar reenvíos del formulario
        header('Location: mozo.php?pedido_realizado=1');
        exit;
    }
}

// Consulta los pedidos actuales incluyendo el estado
$stmt = $pdo->query('SELECT pedido.num_pedido, pedido.estado, pedido.hora, pedido.ciCliente, 
                            usuario.nombre AS nombre_cliente, usuario.rol,
                            producto.nombre, producto.precio, pedido_producto.cantidad
                     FROM pedido
                     JOIN pedido_producto ON pedido.num_pedido = pedido_producto.num_pedido
                     JOIN producto ON pedido_producto.Idproducto = producto.Idproducto
                     LEFT JOIN usuario ON pedido.ciCliente = usuario.ci
                     WHERE pedido.estado IN ("pendiente", "terminado", "cancelado")
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
            'cliente' => $pedido['rol'] === 'mozo' ? 'Mozo' : $pedido['nombre_cliente'],
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
    <title>Panel de Mozo - Café Sabroso</title>
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
                    <a href="../nosotros.php">NOSOTROS</a>
                        <ul class="submenu">
                            <li><a href="../../html/histotria.html">HISTORIA</a></li>
                            <li><a href="../../html/locales.html">LOCALES</a></li>
                            <li><a href="../mercaderia.php">MERCADERIA</a></li>
                        </ul>
                    
                    </li>
                    <li><a href="../pedido.php">PEDIDO</a></li>
                    </li>
                    <li><a href="../logout.php">LOGOUT</a></li>
                    

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
        <div class="panel-gerente-mozo-chef ">
            <h2 class="mozo-nombre">Vista del Mozo: <?php echo htmlspecialchars($nombreMozo); ?></h2>
            <?php if (isset($_GET['pedido_realizado'])): ?>
                <p class="mensaje-exito">El pedido ha sido realizado con éxito.</p>
            <?php endif; ?>
            <div class="panel-mozo-container">
                <section class="pedidos-actuales">
                    <h3>Pedidos Actuales</h3>
                    <div class="pedidos-grid">
                        <?php foreach ($pedidos_agrupados as $pedido): ?>
                            <div class="pedido <?php echo $pedido['estado']; ?>">
                                <h4>Pedido #<?php echo htmlspecialchars($pedido['num_pedido']); ?></h4>
                                <p>Estado: <?php echo htmlspecialchars(ucfirst($pedido['estado'])); ?></p>
                                <p>Hora: <?php echo htmlspecialchars($pedido['hora']); ?></p>
                                <p>Cliente: <?php echo htmlspecialchars($pedido['cliente']); ?></p>
                                <ul>
                                    <?php foreach ($pedido['productos'] as $producto): ?>
                                        <li><?php echo htmlspecialchars($producto['nombre']); ?> - Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?> - Precio: €<?php echo htmlspecialchars($producto['precio']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <p>Total: €<?php echo htmlspecialchars($pedido['total']); ?></p>
                                <!-- Botón para imprimir el pedido -->
                                <button class="btn-agregar" onclick="imprimirPedido(<?php echo htmlspecialchars($pedido['num_pedido']); ?>)">Imprimir Pedido</button>
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
                <li><a href="https://www.instagram.com/athreeg_s.a.s/" target="_blank"><img src="../../img/ig.png" alt="Instagram">.</a></li>
                <li><a href="https://x.com/" target="_blank"><img src="../../img/x.png" alt="X"> .</a></li>
                <li><a href="https://www.facebook.com/athreesas/" target="_blank"><img src="../../img/facebook.png" alt="Facebook">.</a></li>
            </ul>
        </div> 

        <p>&copy; Café Sabrosos. A tu servicio desde 1990</p>
    </footer>
</body>

</html>
<script>window.gtranslateSettings = {"default_language":"es","native_language_names":true,"detect_browser_language":true,"languages":["es","fr","de","pt","en"],"wrapper_selector":".gtranslate_wrapper"}</script>
    <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
    
<script>
const pedidos = Object.values(<?php echo json_encode($pedidos_agrupados); ?>); // Convertir el objeto a un array

// Verifica que pedidos sea un array
console.log(pedidos); // Agrega esta línea para verificar el contenido

function imprimirPedido(numPedido) {
    const pedido = pedidos.find(p => p.num_pedido === numPedido);
    
    if (!pedido) {
        alert('Pedido no encontrado.');
        return;
    }

    let contenido = `Pedido #${pedido.num_pedido}\nEstado: ${pedido.estado}\nHora: ${pedido.hora}\nCliente: ${pedido.cliente}\nProductos:\n`;
    pedido.productos.forEach(producto => {
        contenido += `- ${producto.nombre} - Cantidad: ${producto.cantidad} - Precio: €${producto.precio}\n`;
    });
    contenido += `Total: €${pedido.total}`;

    // Mostrar el contenido en una ventana de impresión
    const ventana = window.open('', '', 'width=600,height=400');
    ventana.document.write('<pre>' + contenido + '</pre>');
    ventana.document.close();
    ventana.print();
}

</script>
