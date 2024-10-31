<?php
session_start();
include 'conectar.php'; // Incluye el archivo de conexión

// Verifica si el usuario está logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php'); // Redirige al login si no está logueado
    exit;
}

// Manejo del formulario para actualizar el carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        $errores = [];
        foreach ($_POST['cantidad'] as $idproducto => $cantidad) {
            $cantidad = (int) $cantidad; // Asegura que es un número entero
            if ($cantidad > 0 && $cantidad <= 15) { // Validación: cantidad entre 1 y 15
                $_SESSION['carrito'][$idproducto] = $cantidad;
            } else {
                // Agregar mensaje de error si la cantidad no está en el rango adecuado
                $errores[] = "La cantidad del producto con ID $idproducto debe estar entre 1 y 15.";
                unset($_SESSION['carrito'][$idproducto]); // Elimina el producto si la cantidad es inválida
            }
        }
    } elseif (isset($_POST['checkout'])) {
        $opcion = $_POST['opcion']; // 'comer' o 'retirar'

        // Asegúrate de que $_SESSION['usuario'] contenga el valor adecuado
        if (isset($_SESSION['usuario'])) {
            if (is_array($_SESSION['usuario']) && isset($_SESSION['usuario']['CI'])) {
                $ciCliente = trim(strval($_SESSION['usuario']['CI']));
            } else {
                $ciCliente = trim(strval($_SESSION['usuario']));
            }
        } else {
            $ciCliente = ''; // O maneja el caso cuando $_SESSION['usuario'] no esté definida
        }

        // Verifica si el cliente existe en la base de datos
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cliente WHERE ciCliente = ?");
        $stmt->execute([$ciCliente]);
        $clienteExiste = $stmt->fetchColumn();

        if ($clienteExiste == 0) {
            die('Error: ciCliente no existe en la tabla cliente.');
        }

        // Calcula el total del carrito
        $total = 0;
        foreach ($_SESSION['carrito'] as $idproducto => $cantidad) {
            $stmt = $pdo->prepare("SELECT precio FROM producto WHERE Idproducto = ?");
            $stmt->execute([$idproducto]);
            $producto = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($producto) {
                $total += $producto['precio'] * $cantidad;
            }
        }

        // Inserta el pedido en la base de datos
        $stmt = $pdo->prepare("INSERT INTO pedido (hora, ciCliente, total) VALUES (NOW(), ?, ?)");
        $stmt->execute([$ciCliente, $total]);
        $num_pedido = $pdo->lastInsertId();

        if ($opcion === 'retirar') {
            // Calcula la hora de retiro sumando 45 minutos a la hora del pedido
            $stmt = $pdo->prepare("
                UPDATE Pedido
                SET hora_retiro = DATE_ADD(hora, INTERVAL 45 MINUTE)
                WHERE num_pedido = ?
            ");
            if (!$stmt->execute([$num_pedido])) {
                die('Error al actualizar la hora de retiro.');
            }
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
        $_SESSION['num_pedido'] = $num_pedido;

        // Redirige a la página de confirmación
        header('Location: confirmacion.php');
        exit;
    } elseif (isset($_POST['cancelar_pedido'])) {
        // Cancelar el pedido y limpiar el carrito
        $_SESSION['carrito'] = [];
        header('Location: carrito.php'); // Redirige al carrito después de cancelar
        exit;
    }
}


// Consulta los productos del carrito y calcula el total
$productos_carrito = [];
$total_carrito = 0;
foreach ($_SESSION['carrito'] as $idproducto => $cantidad) {
    $stmt = $pdo->prepare("SELECT Idproducto, nombre, precio FROM producto WHERE Idproducto = ?");
    $stmt->execute([$idproducto]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($producto) {
        $producto['cantidad'] = $cantidad;
        $productos_carrito[] = $producto;
        $total_carrito += $producto['precio'] * $cantidad;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Café Sabroso</title>
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
                    <!-- <li><a href="nosotros.php">NOSOTROS</a></li>
                    <li><a href="#">ADMINISTRAR</a></li>
                    <li><a href="contactar.php">CONTACTANOS</a></li> -->
                </ul>
            </div>
        </div>
    </header>

    <div class="wave-image-2">
      <div class="slogan-2">
      </div>
    </div>


    <main class="cart-container">
        <h1 class="cart-header">Tu Carrito</h1>
        <form action="carrito.php" method="post">
            <div class="cart-content"> <!-- Contenedor para ambos cuadrados -->
                <div class="cart-products">
                    <h2>Productos en tu carrito</h2> <!-- Título añadido -->
                    <?php if (count($productos_carrito) > 0): ?>
                        <table>
                            <tbody>
                                <?php foreach ($productos_carrito as $producto): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                        <td class="cart-item-price">€<?php echo htmlspecialchars($producto['precio']); ?></td>
                                        <td>
                                            <input type="number" name="cantidad[<?php echo htmlspecialchars($producto['Idproducto']); ?>]" min="1" max="15" value="<?php echo htmlspecialchars($producto['cantidad']); ?>" class="cantidad-input">
                                        </td>
                                        <td class="cart-item-price">€<?php echo htmlspecialchars($producto['precio'] * $producto['cantidad']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <label for="opcion" class="option-label">Elija una opción:</label>
                        <select name="opcion" id="opcion" class="option-select"> <!-- Clase añadida para estilo -->
                            <option value="comer">Comer en el local</option>
                            <option value="retirar">Retirar</option>
                        </select>
                        
                        <button type="submit" name="update_cart" class="btn small-btn">Actualizar Carrito</button> <!-- Botón más pequeño -->
                        <input type="button" value="Agregar Productos" onclick="location.href='pedido.php'" class="btn small-btn"> <!-- Botón más pequeño -->
                    <?php else: ?>
                        <p>Tu carrito está vacío.</p>
                        <input type="button" value="Volver a Pedido" onclick="location.href='pedido.php'" class="btn small-btn">
                    <?php endif; ?>
                </div>
                
                <div class="cart-summary">
                    <h2>Resumen de la compra</h2> <!-- Título añadido -->
                    <p><strong>Total de productos:</strong> <?php echo count($productos_carrito); ?></p> <!-- Total de productos -->
                    <p><strong>Precio total:</strong> <span class="cart-item-price">€<?php echo htmlspecialchars($total_carrito); ?></span></p> <!-- Precio total -->
                    <button type="submit" name="cancelar_pedido" class="btn small-btn">Cancelar Pedido</button> <!-- Botón más pequeño -->
                    <button type="submit" name="checkout" class="btn small-btn">Proceder al Pago</button> <!-- Botón más pequeño -->
                </div>
            </div> <!-- Fin de cart-content -->
        </form>
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
    document.getElementById('cancelarPedido').addEventListener('click', function() {
        // Redirige a una página que limpia el carrito
        window.location.href = 'cancelar_carrito.php'; // Cambia a la ruta correcta
    });
    </script>
</body>

</html>
