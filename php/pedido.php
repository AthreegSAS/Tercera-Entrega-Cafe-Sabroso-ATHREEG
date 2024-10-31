<?php
session_start();
include 'conectar.php'; // Incluye el archivo de conexión

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php'); // Redirige al login si no está logueado
    exit;
}

// Asume que el rol del usuario está almacenado en $_SESSION['usuario']['rol']
$userRole = isset($_SESSION['usuario']['rol']) ? $_SESSION['usuario']['rol'] : '';

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
        $ciCliente = $_SESSION['usuario'];
        $hora = date('H:i:s');
        $hora_retiro = null;
        $num_pedido = null;

        // Inserta el pedido en la base de datos
        $stmt = $pdo->prepare("INSERT INTO Pedido (hora, ciCliente) VALUES (NOW(), ?)");
        $stmt->execute([$ciCliente]);
        $num_pedido = $pdo->lastInsertId();

        if ($opcion === 'retirar') {
            $hora_retiro = date('H:i:s', strtotime($hora) + 45 * 60);
            $stmt = $pdo->prepare("UPDATE Pedido SET hora_retiro = ? WHERE num_pedido = ?");
            $stmt->execute([$hora_retiro, $num_pedido]);
        }

        // Inserta los productos en el pedido
        foreach ($_SESSION['carrito'] as $idproducto => $cantidad) {
            $stmt = $pdo->prepare("INSERT INTO pedido_producto (Idproducto, num_pedido, cantidad) VALUES (?, ?, ?)");
            $stmt->execute([$idproducto, $num_pedido, $cantidad]);
        }

        // Limpia el carrito
        $_SESSION['carrito'] = [];

        // Guarda la opción seleccionada en la sesión
        $_SESSION['opcion'] = $opcion;
        $_SESSION['hora_retiro'] = $hora_retiro;
        $_SESSION['num_pedido'] = $num_pedido;

        // Redirige a la página de confirmación
        header('Location: confirmacion.php');
        exit;
    }
}
// Manejo del formulario de búsqueda
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Consulta los productos de la base de datos
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 6;
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : 'C';
$query = 'SELECT producto.Idproducto, producto.nombre, producto.precio, producto.descripcion, foto.URL AS IdFoto 
          FROM producto 
          JOIN foto ON producto.Idfoto = foto.idFoto';

// Si se ingresó un término de búsqueda, añade el filtro
if (!empty($searchTerm)) {
    $query .= ' WHERE LOWER(producto.nombre) LIKE :searchTerm';
} elseif (!empty($categoria)) {
    $query .= ' WHERE producto.categoria = :categoria';
}

$query .= ' ORDER BY producto.Idproducto ASC 
            LIMIT :current_page, :records_per_page';

$stmt = $pdo->prepare($query);

// Si hay término de búsqueda, añade el valor con comodines
if (!empty($searchTerm)) {
    $stmt->bindValue(':searchTerm', '%' . strtolower($searchTerm) . '%', PDO::PARAM_STR);
} elseif (!empty($categoria)) {
    $stmt->bindValue(':categoria', $categoria, PDO::PARAM_STR);
}

$stmt->bindValue(':current_page', ($page - 1) * $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();

$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$num_products = $pdo->query('SELECT count(*) FROM producto')->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido - Café Sabroso</title>
    <link rel="stylesheet" href="../style/index5.css">
    <link rel="icon" href="../img/icon.png">
    
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
                   <li><a href="../html/nosotros.html">NOSOTROS</a>
                        <ul class="submenu">
                            <li><a href="../html/histotria.html">HISTORIA</a></li>
                            <li><a href="../html/locales.html">LOCALES</a></li>
                            <li><a href="#">MERCANCIA</a></li>
                        </ul>
                    </li>
                        <?php if ($userRole === 'mozo'): ?>
                            <li class="mozo-option"><a href="back/mozo.php">MOZO</a></li>
                        <?php endif; ?>
                    
                    <li><a href="logout.php">LOGOUT</a></li>
                    <li><a href="back/votos.php">OPINAR</a></li>
                    <?php if (!in_array($userRole, ['chef', 'gerente', 'mozo'])): ?>
                        <li><a href="mispedidos.php">MIS PEDIDOS</a></li>
                    <?php endif; ?>
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

    <div class="wave-image-2">
      <div class="slogan-2">
      </div>
    </div>


    <!-- Menú horizontal de categorías -->
    <nav class="menu-categorias">
        <!-- Formulario de búsqueda -->
        <form action="pedido.php" method="get" class="search-form">
            <input type="text" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Buscar productos...">
            <button type="submit">Buscar</button>
        </form>

        <ul>
            <?php if (isset($_SESSION['usuario'])): ?>
                <li><a href="pedido.php?categoria=C">Café</a></li>
                <li><a href="pedido.php?categoria=T">Té</a></li>
                <li><a href="pedido.php?categoria=D">Dulce</a></li>
                <li><a href="pedido.php?categoria=S">Salado</a></li>
                <li><a href="pedido.php?categoria=J">Jugos</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <!-- Fin del menú horizontal de categorías -->
    <main class="shop">
      
        <div class="productos-container">
            <?php foreach ($productos as $producto): ?>
                <div class="producto-card">
                    <div class="producto-imagen">
                        <img src="../<?php echo htmlspecialchars($producto['IdFoto']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" width="30%" height="30%">
                    </div>
                    <div class="producto-detalles">
                        <label class="producto-precio">€<?php echo htmlspecialchars($producto['precio']); ?></label>
                        <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <form action="pedido.php" method="post">
                            <input type="hidden" name="idproducto" value="<?php echo htmlspecialchars($producto['Idproducto']); ?>">
                            <input type="number" name="cantidad" min="1" max="15" value="1" class="cantidad-input">
                            <button type="submit" name="add_to_cart" class="btn-agregar">Agregar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="pedido.php?page=<?php echo $page - 1; ?>" class="btn-paginacion">Página anterior</a>
            <?php endif; ?>
            
            <?php if ($page * $records_per_page < $num_products): ?>
                <a href="pedido.php?page=<?php echo $page + 1; ?>" class="btn-paginacion">Siguiente página</a>
            <?php endif; ?>
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
