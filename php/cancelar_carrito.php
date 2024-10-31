<?php
session_start();
$_SESSION['carrito'] = []; // Limpia el carrito
header('Location: carrito.php'); // Redirige de vuelta al carrito
exit;
?>