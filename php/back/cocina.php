<?php
session_start();
include '../conectar.php';

// Verifica si el usuario está logueado y es un chef
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'chef') {
    header('Location: ../login.php');
    exit;
}

// Obtiene el nombre del chef
$nombreChef = isset($_SESSION['usuario']['nombre']) ? $_SESSION['usuario']['nombre'] : 'Chef';

// Procesar la adición de un nuevo producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $urlFoto = $_POST['urlFoto'];
    $categoria = $_POST['categoria']; // Obtener la categoría seleccionada

    // Obtener el último idFoto e incrementarlo
    $stmt = $pdo->query("SELECT MAX(idFoto) AS max_id FROM Foto");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $idfoto = $row['max_id'] + 1;

    // Insertar la nueva foto en la tabla Foto
    $stmt = $pdo->prepare("INSERT INTO Foto (idFoto, URL) VALUES (:idfoto, :urlFoto)");
    $stmt->execute(['idfoto' => $idfoto, 'urlFoto' => $urlFoto]);

    // Obtener el último Idproducto e incrementarlo
    $stmt = $pdo->query("SELECT MAX(Idproducto) AS max_id FROM producto");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $idproducto = $row['max_id'] + 1;

    // Insertar el nuevo producto en la tabla producto, incluyendo la categoría
    $stmt = $pdo->prepare("INSERT INTO producto (Idproducto, nombre, precio, descripcion, Idfoto, categoria) VALUES (:idproducto, :nombre, :precio, :descripcion, :idfoto, :categoria)");
    $stmt->execute(['idproducto' => $idproducto, 'nombre' => $nombre, 'precio' => $precio, 'descripcion' => $descripcion, 'idfoto' => $idfoto, 'categoria' => $categoria]);
    header('Location: cocina.php');
    exit;
}

// Consulta los pedidos actuales
$stmt = $pdo->query('SELECT pedido.num_pedido, producto.nombre, pedido_producto.cantidad, producto.precio
                     FROM pedido
                     JOIN pedido_producto ON pedido.num_pedido = pedido_producto.num_pedido
                     JOIN producto ON pedido_producto.Idproducto = producto.Idproducto
                     WHERE pedido.estado = "pendiente"
                     ORDER BY pedido.num_pedido DESC');
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar los pedidos por número de pedido
$pedidos_agrupados = [];
foreach ($pedidos as $pedido) {
    $num_pedido = $pedido['num_pedido'];
    if (!isset($pedidos_agrupados[$num_pedido])) {
        $pedidos_agrupados[$num_pedido] = [
            'num_pedido' => $num_pedido,
            'productos' => []
        ];
    }
    $pedidos_agrupados[$num_pedido]['productos'][] = [
        'nombre' => $pedido['nombre'],
        'cantidad' => $pedido['cantidad'],
        'precio' => $pedido['precio']
    ];
}

// Procesar la finalización o cancelación de un pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['agregar_producto'])) {
    $num_pedido = $_POST['num_pedido'];
    if (isset($_POST['finalizar_pedido'])) {
        $nuevo_estado = 'terminado';
    } elseif (isset($_POST['cancelar_pedido'])) {
        $nuevo_estado = 'cancelado';
    }

    if (isset($nuevo_estado)) {
        $stmt = $pdo->prepare("UPDATE pedido SET estado = :nuevo_estado WHERE num_pedido = :num_pedido");
        $stmt->execute(['nuevo_estado' => $nuevo_estado, 'num_pedido' => $num_pedido]);
        header('Location: cocina.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Cocina - Café Sabroso</title>
    <link rel="stylesheet" href="../../style/index5.css">
    <link rel="icon" href="../../img/icon.png">
    <script>
        function toggleView(view) {
            document.getElementById('pedidos-section').style.display = view === 'pedidos' ? 'block' : 'none';
            document.getElementById('nuevo-producto-section').style.display = view === 'nuevo-producto' ? 'block' : 'none';
        }
    </script>
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
                    <!-- <li><a href="../pedido.php">PEDIDO</a></li> -->
                    <!-- <li><a href="../contactar.php">CONTACTANOS</a></li> -->
                    <?php if ($_SESSION['usuario']['rol'] === 'chef'): ?>
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropbtn">ADMINISTRAR</a>
                            <ul class="submenu">
                                <li><a href="../back/agregarLogo.php">Agregar Logo</a></li>
                                <li><a href="javascript:void(0);" onclick="toggleView('nuevo-producto')">Agregar producto</a></li>
                                <li><a href="../logout.php">Cerrar sesión</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
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
            <h2 class="chef-nombre">Vista del Chef: <?php echo htmlspecialchars($nombreChef); ?></h2>
            <div class="panel-cocina-container">
                <section id="pedidos-section" class="pedidos-actuales" style="display: block;">
                    <h3>pedidos Pendientes</h3>
                    <div class="pedidos-grid">
                        <?php foreach ($pedidos_agrupados as $pedido): ?>
                            <div class="pedido">
                                <h4>pedido #<?php echo htmlspecialchars($pedido['num_pedido']); ?></h4>
                                <p class="estado-retirar">Tu pedido ha sido registrado con el número: <strong><?php echo htmlspecialchars($pedido['num_pedido']); ?></strong>.</p>
                                <ul>
                                    <?php foreach ($pedido['productos'] as $producto): ?>
                                        <li><?php echo htmlspecialchars($producto['nombre']); ?> - Cantidad: <?php echo htmlspecialchars($producto['cantidad']); ?> - Precio: €<?php echo htmlspecialchars($producto['precio']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <form method="POST">
                                    <input type="hidden" name="num_pedido" value="<?php echo $pedido['num_pedido']; ?>">
                                    <button class="btn-agregar" type="submit" name="finalizar_pedido" class="finalizar-pedido">Finalizar pedido</button>
                                    <button class="btn-agregar" type="submit" name="cancelar_pedido" class="cancelar-pedido">Cancelar pedido</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <section id="nuevo-producto-section" style="display: none;">
                    <h3>Agregar Nuevo producto</h3>
                    <form method="POST" class="form-container">
                        <input type="hidden" name="agregar_producto" value="1">
                        <div class="form-group">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="precio">Precio:</label>
                            <input type="number" id="precio" name="precio" step="0.5" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <textarea id="descripcion" name="descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="urlFoto">URL Foto:</label>
                            <select id="urlFoto" name="urlFoto" required>
                                <?php
                                $dir = '../../img/';
                                $files = array_diff(scandir($dir), array('.', '..'));
                                foreach ($files as $file) {
                                    echo "<option value='img/$file'>$file</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoría:</label>
                            <select id="categoria" name="categoria" required>
                                <option value="C">Café</option>
                                <option value="T">Té</option>
                                <option value="D">Dulce</option>
                                <option value="S">Salado</option>
                                <option value="J">Jugos</option>
                            </select>
                        </div>
                        <button type="submit">Agregar producto</button>
                    </form>
                    <button onclick="toggleView('pedidos')">Volver a pedidos</button>
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
