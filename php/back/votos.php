<?php
session_start();
require_once '../conectar.php';

// Verificar si el usuario está logueado y no tiene rol de mozo, gerente o chef
if (!isset($_SESSION['usuario']) || in_array($_SESSION['usuario']['rol'], ['mozo', 'chef', 'gerente'])) {
    header('Location: ../login.php');
    exit;
}

$nombreCliente = $_SESSION['usuario']['nombre'] ?? 'Cliente';

// Función para obtener la lista de locales
function obtenerLocales()
{
    global $pdo;
    $sql = "SELECT idLocal, pais, ciudad FROM local";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener la lista de productos
function obtenerProductos()
{
    global $pdo;
    $sql = "SELECT Idproducto, nombre FROM producto";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener la lista de mozos
function obtenerMozos()
{
    global $pdo;
    $sql = "SELECT CI, nombre FROM usuario WHERE rol = 'mozo'";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

// Función para registrar un voto
function registrarVoto($tiempoEspera, $idProducto, $idLocal, $nombreMozo)
{
    global $pdo;

    // Construir la consulta SQL
    $sql = "INSERT INTO voto (tiempoEspera, Idproducto, Idlocal, nombreMozo) 
            VALUES (?, ?, ?, ?)";
    $params = [$tiempoEspera, $idProducto, $idLocal, $nombreMozo];

    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Error al registrar voto: " . $e->getMessage());
        return false;
    }
}

$locales = obtenerLocales();
$productos = obtenerProductos();
$mozos = obtenerMozos();

// Procesar el formulario de voto
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tiempoEspera = $_POST['tiempoEspera'];
    $idProducto = $_POST['idProducto'];
    $idLocal = $_POST['idLocal'];
    $ciMozo = $_POST['ciMozo'] ?? null;

    // Obtener el nombre del mozo seleccionado si se proporciona
    $nombreMozo = null;
    if ($ciMozo !== null && $ciMozo !== '') {
        foreach ($mozos as $mozo) {
            if ($mozo['CI'] == $ciMozo) {
                $nombreMozo = $mozo['nombre'];
                break;
            }
        }
    }

    if (registrarVoto($tiempoEspera, $idProducto, $idLocal, $nombreMozo)) {
        $mensajeExito = "¡Gracias por su voto! Apreciamos su opinión.";
    } else {
        $mensajeError = "Error al registrar el voto. Por favor, intente nuevamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votar - Café Sabroso</title>
    <link rel="stylesheet" href="../../style/index5.css">
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
            <div class="menu">
                <ul>
                    <li><a href="../../index.html">INICIO</a></li>
                    <!-- <li><a href="../nosotros.php">NOSOTROS</a>
                        <ul class="submenu">
                            <li><a href="../../html/histotria.html">HISTORIA</a></li>
                            <li><a href="../../html/locales.html">LOCALES</a></li>
                            <li><a href="../mercaderia.php">MERCADERIA</a></li>
                        </ul>
                    </li> -->
                    </li>
                    <li><a href="../pedido.php">PEDIDO</a></li>

                    </li>
                    <li><a href="../logout.php">LOGOUT</a></li>
                    <!-- <li class="cart">
                        <a href="#"><img src="../../img/carrito.png" alt="Carrito de Compras" width="10%" heigth="10%"> Carrito (<span id="cart-count"><?php echo count($_SESSION['carrito']); ?></span>)</a>
                    </li> -->

                </ul>
            </div>
        </div>
    </header>

    <div class="wave-image-2">
      <div class="slogan-2">
      </div>
    </div>


    <main class="main-content">
    <section class="login-section">
        <div class="form-container">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombreCliente); ?></h2>

            <?php if (isset($mensajeExito)): ?>
                <p class="exito"><?php echo $mensajeExito; ?></p>
            <?php elseif (isset($mensajeError)): ?>
                <p class="error"><?php echo $mensajeError; ?></p>
            <?php else: ?>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                        <label for="tiempoEspera">Tiempo de espera:</label>
                        <select name="tiempoEspera" required>
                            <option value="1">1 - Espera alta</option>
                            <option value="2">2 - Espera normal</option>
                            <option value="3">3 - Poca espera</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idProducto">Producto:</label>
                        <select name="idProducto" required>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?php echo $producto['Idproducto']; ?>">
                                    <?php echo htmlspecialchars($producto['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="idLocal">Local:</label>
                        <select name="idLocal" required>
                            <?php foreach ($locales as $local): ?>
                                <option value="<?php echo $local['idLocal']; ?>">
                                    <?php echo htmlspecialchars($local['ciudad'] . ', ' . $local['pais']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ciMozo">Mozo:</label>
                        <select name="ciMozo">
                            <option value="">Seleccione un mozo (opcional)</option>
                            <?php foreach ($mozos as $mozo): ?>
                                <option value="<?php echo htmlspecialchars($mozo['CI']); ?>">
                                    <?php echo htmlspecialchars($mozo['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-submit">
                        <input type="submit" value="Votar">
                    </div>
                </form>
            <?php endif; ?>
        </div>
        </section>
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