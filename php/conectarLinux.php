<?php

$host = '192.168.30.101'; // Cambia esto si tu host es diferente
$dbname = 'cafesabrosos';
$username = 'usuarioCS'; // Cambia esto si tu usuario es diferente
$password = 'CafeSabr0sos_2024'; // Cambia esto si tu contraseña es diferente

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Conexión fallida: ' . $e->getMessage();
}
?>