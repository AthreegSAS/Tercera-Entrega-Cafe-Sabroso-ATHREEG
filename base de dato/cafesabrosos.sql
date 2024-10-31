-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-09-2024 a las 04:21:25
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `chef`
DROP TABLE IF EXISTS `chef`;
CREATE TABLE `chef` (
  `ciUsuario` varchar(20) NOT NULL,
  `num_chef` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Disparadores `chef`
DELIMITER $$
CREATE TRIGGER `asignar_rol_chef` AFTER INSERT ON `chef` FOR EACH ROW BEGIN
    UPDATE Usuario SET rol = 'chef' WHERE CI = NEW.ciUsuario;
END
$$
DELIMITER ;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `cliente`
DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `ciCliente` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `foto`
DROP TABLE IF EXISTS `foto`;
CREATE TABLE `foto` (
  `idFoto` int(11) NOT NULL,
  `URL` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `gerente`
DROP TABLE IF EXISTS `gerente`;
CREATE TABLE `gerente` (
  `ciUsuario` varchar(20) NOT NULL,
  `idLocal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `local`
DROP TABLE IF EXISTS `local`;
CREATE TABLE `local` (
  `idLocal` int(11) NOT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `mozo`
DROP TABLE IF EXISTS `mozo`;
CREATE TABLE `mozo` (
  `ciUsuario` varchar(20) NOT NULL,
  `num_mozo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `pedido`
DROP TABLE IF EXISTS `pedido`;
CREATE TABLE `pedido` (
  `num_pedido` int(11) NOT NULL,
  `hora` time DEFAULT NULL,
  `hora_retiro` time NOT NULL,
  `ciChef` varchar(20) DEFAULT NULL,
  `ciMozo` varchar(20) DEFAULT NULL,
  `ciCliente` varchar(20) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT 0.00,
  `estado` varchar(20) NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `pedido`
INSERT INTO `pedido` (`num_pedido`, `hora`, `hora_retiro`, `ciChef`, `ciMozo`, `ciCliente`, `total`, `estado`) VALUES
(1, '19:29:22', '00:00:00', NULL, NULL, '01234567', 0.00, 'terminado'),
(2, '19:29:47', '00:00:00', NULL, NULL, '01234567', 0.00, 'terminado'),
(3, '19:30:51', '00:00:00', NULL, NULL, '01234567', 0.00, 'terminado'),
(4, '20:38:30', '00:00:00', NULL, NULL, '01234567', 0.00, 'terminado'),
(5, '20:39:33', '00:00:00', NULL, NULL, '01234567', 0.00, 'terminado');

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `pedido_producto`
DROP TABLE IF EXISTS `pedido_producto`;
CREATE TABLE `pedido_producto` (
  `Idproducto` int(11) NOT NULL,
  `num_pedido` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `pedido_producto`
INSERT INTO `pedido_producto` (`Idproducto`, `num_pedido`, `cantidad`) VALUES
(1, 1, 3),
(1, 2, 1),
(1, 3, 1),
(1, 4, 1),
(1, 5, 1);

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `producto`
DROP TABLE IF EXISTS `producto`;
CREATE TABLE `producto` (
  `Idproducto` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `Idfoto` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para la tabla `producto`
INSERT INTO `producto` (`Idproducto`, `nombre`, `precio`, `descripcion`, `Idfoto`) VALUES
(1, 'cafe exportado', 150.00, 'detalle', 1),
(2, 'Café Tradicional', 180.00, 'Un café preparado con una receta familiar', 2),
(3, 'Café con Leche', 200.00, 'Café suave mezclado con leche cremosa', 3),
(4, 'Té Verde', 170.00, 'Té verde natural, perfecto para relajarse', 4),
(5, 'Café Expreso', 220.00, 'Un café fuerte con un sabor intenso', 5);

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `producto_voto`
DROP TABLE IF EXISTS `producto_voto`;
CREATE TABLE `producto_voto` (
  `Idproducto` int(11) NOT NULL,
  `Idvoto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `usuario`
DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `CI` varchar(20) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `rol` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

-- Estructura de tabla para la tabla `voto`
DROP TABLE IF EXISTS `voto`;
CREATE TABLE `voto` (
  `Idvoto` int(11) NOT NULL,
  `tiempoEspera` int(11) DEFAULT NULL,
  `Idproducto` int(11) DEFAULT NULL,
  `Idlocal` int(11) DEFAULT NULL,
  `ciMozo` varchar(20) DEFAULT NULL,
  `nombreMozo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Volcado de datos para las tablas
-- (Asegúrate de que los datos sean consistentes con las tablas creadas)

-- Volcado de datos para la tabla `cliente`
INSERT INTO `cliente` (`ciCliente`) VALUES
('01234567'),
('10111213'),
('11223344'),
('12345678'),
('22334455'),
('23456789'),
('33445566'),
('34567890'),
('37759372'),
('37759374'),
('37759390'),
('44444431'),
('44444441'),
('44556677'),
('45678901'),
('55667788'),
('56789012'),
('57759372'),
('66778899'),
('67890123'),
('77889900'),
('78901234'),
('88990011'),
('89012345'),
('90123456'),
('99001122');

-- Volcado de datos para la tabla `foto`
INSERT INTO `foto` (`idFoto`, `URL`) VALUES
(1, 'img/cafe1.jpg'),
(2, 'img/cafe2.jpg'),
(3, 'img/cafe3.jpg'),
(4, 'img/te1.jpg'),
(5, 'img/cafe4.jpg'),
(6, 'img/te2.jpg'),
(7, 'img/cafe5.jpg'),
(8, 'img/te3.jpg'),
(9, 'img/cafe6.jpg'),
(10, 'img/te4.jpg'),
(11, 'img/cafe7.jpg'),
(12, 'img/te5.jpg'),
(13, 'img/cafe8.jpg'),
(14, 'img/te6.jpg'),
(15, 'img/cafe9.jpg'),
(16, 'img/te7.jpg'),
(17, 'img/cafe10.jpg'),
(18, 'img/te8.jpg'),
(19, 'img/cafe11.jpg'),
(20, 'img/te9.jpg'),
(21, 'img/tePaguay.jpg');

-- Volcado de datos para la tabla `local`
INSERT INTO `local` (`idLocal`, `pais`, `ciudad`) VALUES
(1, 'España', 'Madrid'),
(2, 'Francia', 'París'),
(3, 'Alemania', 'Berlín'),
(4, 'Portugal', 'Lisboa');

-- Volcado de datos para la tabla `usuario`
INSERT INTO `usuario` (`CI`, `nombre`, `celular`, `direccion`, `email`, `password`, `rol`) VALUES
('01234567', 'Lucía Fernández', '098012345', 'Calle Colón 876', 'lucia@example.com', 'lucia,1234', 'mozo'),
('10111213', 'Agustina Ortiz', '098101112', 'Boulevard Independencia 567', 'agustina@example.com', 'agustina,1234', 'chef'),
('11223344', 'Gabriel Pérez', '098112233', 'Avenida Lavalle 444', 'gabriel@example.com', 'gabriel,1234', 'gerente'),
('12345678', 'Juan Pérez', '098123456', 'Calle Falsa 123', 'juan@example.com', 'juan,1234', 'chef'),
('22334455', 'Martina Castro', '098223344', 'Calle Tucumán 999', 'martina@example.com', 'martina,1234', 'mozo'),
('23456789', 'Ana García', '098234567', 'Avenida Siempre Viva 456', 'ana@example.com', 'ana,1234', NULL),
('33445566', 'Federico Suárez', '098334455', 'Boulevard Oroño 654', 'federico@example.com', 'federico,1234', NULL),
('34567890', 'Carlos López', '098345678', 'Boulevard Central 789', 'carlos@example.com', 'carlos,1234', NULL),
('37759371', 'Pedrito Sinombre', '09898177', 'España 1264', 'pedritos@example.com', 'pedrito,1234', NULL),
('37759372', 'Ale Aranda', '099 918 777', 'Francia 2273', 'jalejandroreyes@gmail.com', 'ale,1234', NULL),
('37759374', 'Pedro Reyes', '099918778', 'Francia 1273', 'pedroreyes@example.com', 'pedro,1234', NULL),
('37759379', 'José Aranda', '098981774', 'España 1260', 'jose@example.com', 'jose,1234', NULL),
('37759390', 'Ale', '4857121145', 'Francia 1273', 'ale@yo.com', 'ale,1234', NULL),
('37759399', 'Ale', '09898177', 'Francia 1273', 'castrillo@yo.com', 'ale,1234', NULL),
('44444431', 'Alejandro', '25558748', 'Francias 1277', 'ale@examples.com', 'ale,1234', NULL),
('44444441', 'Alejandro Rey', '25558747', 'Francias 1277', 'alerey@example.com', 'ale,1234', NULL),
('44556677', 'Valentina Benítez', '098445566', 'Avenida Rivadavia 321', 'valentina@example.com', 'ccbe657a123', NULL),
('45678901', 'María Martínez', '098456789', 'Calle Principal 135', 'maria@example.com', 'c51832a3123', NULL),
('48783391', 'manuelita tortuga', '09898771', 'Rua 1234', 'matortuga@example.com', 'manuelita,1234', NULL),
('55667788', 'Javier Ruiz', '098556677', 'Calle Alsina 123', 'javier@example.com', '634df7eb123', NULL),
('56205646', 'Axel Gonzalez', '088787952', 'Camino de los Mares 2123', 'axelgonzalez@example.com', 'axel,1234', NULL),
('56789012', 'Luis Fernández', '098567890', 'Avenida del Sol 246', 'luis@example.com', 'c0e9f1e3123', NULL),
('57759372', 'Andrea', '09898100', 'Rua 1237', 'andrea@yo.com', 'andrea,1234', NULL),
('66778899', 'Camila Díaz', '098667788', 'Avenida Corrientes 890', 'camila@example.com', '051c2005123', NULL),
('67890123', 'Sofía González', '098678901', 'Calle 9 de Julio 357', 'sofia@example.com', '202e7379123', NULL),
('77889900', 'Emiliano Acosta', '098778899', 'Calle Reconquista 234', 'emiliano@example.com', '2cb97ffc123', NULL),
('78901234', 'Ricardo Méndez', '098789012', 'Avenida Libertador 123', 'ricardo@example.com', '8919df53123', NULL),
('88990011', 'Florencia Soto', '098889900', 'Avenida de Mayo 345', 'florencia@example.com', 'a392c9bd123', NULL),
('89012345', 'Laura Gómez', '098890123', 'Calle San Martín 567', 'laura@example.com', 'e3f9202b123', NULL),
('90123456', 'Pedro Rodríguez', '098901234', 'Avenida Belgrano 321', 'pedro@example.com', 'aab49755123', NULL),
('99001122', 'Matías Ramírez', '098990011', 'Calle Bolívar 456', 'matias@example.com', 'ed9cb5b3123', NULL);

-- --------------------------------------------------------

-- Índices para tablas volcadas
ALTER TABLE `chef`
  ADD PRIMARY KEY (`ciUsuario`);

ALTER TABLE `cliente`
  ADD PRIMARY KEY (`ciCliente`);

ALTER TABLE `foto`
  ADD PRIMARY KEY (`idFoto`);

ALTER TABLE `gerente`
  ADD PRIMARY KEY (`ciUsuario`),
  ADD KEY `idLocal` (`idLocal`);

ALTER TABLE `local`
  ADD PRIMARY KEY (`idLocal`);

ALTER TABLE `mozo`
  ADD PRIMARY KEY (`ciUsuario`);

ALTER TABLE `pedido`
  ADD PRIMARY KEY (`num_pedido`),
  ADD KEY `ciChef` (`ciChef`),
  ADD KEY `ciMozo` (`ciMozo`),
  ADD KEY `ciCliente` (`ciCliente`);

ALTER TABLE `pedido_producto`
  ADD PRIMARY KEY (`Idproducto`,`num_pedido`),
  ADD KEY `num_pedido` (`num_pedido`);

ALTER TABLE `producto`
  ADD PRIMARY KEY (`Idproducto`),
  ADD KEY `Idfoto` (`Idfoto`);

ALTER TABLE `producto_voto`
  ADD PRIMARY KEY (`Idproducto`,`Idvoto`),
  ADD KEY `Idvoto` (`Idvoto`);

ALTER TABLE `usuario`
  ADD PRIMARY KEY (`CI`);

ALTER TABLE `voto`
  ADD PRIMARY KEY (`Idvoto`),
  ADD KEY `Idproducto` (`Idproducto`),
  ADD KEY `Idlocal` (`Idlocal`),
  ADD KEY `ciMozo` (`ciMozo`);

-- AUTO_INCREMENT de las tablas volcadas
ALTER TABLE `pedido`
  MODIFY `num_pedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

ALTER TABLE `voto`
  MODIFY `Idvoto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

-- Restricciones para tablas volcadas
ALTER TABLE `chef`
  ADD CONSTRAINT `chef_ibfk_1` FOREIGN KEY (`ciUsuario`) REFERENCES `usuario` (`CI`);

ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`ciCliente`) REFERENCES `usuario` (`CI`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `gerente`
  ADD CONSTRAINT `gerente_ibfk_1` FOREIGN KEY (`ciUsuario`) REFERENCES `usuario` (`CI`),
  ADD CONSTRAINT `gerente_ibfk_2` FOREIGN KEY (`idLocal`) REFERENCES `local` (`idLocal`);

ALTER TABLE `mozo`
  ADD CONSTRAINT `mozo_ibfk_1` FOREIGN KEY (`ciUsuario`) REFERENCES `usuario` (`CI`);

ALTER TABLE `pedido`
  ADD CONSTRAINT `pedido_ibfk_1` FOREIGN KEY (`ciChef`) REFERENCES `chef` (`ciUsuario`),
  ADD CONSTRAINT `pedido_ibfk_2` FOREIGN KEY (`ciMozo`) REFERENCES `mozo` (`ciUsuario`);

ALTER TABLE `pedido_producto`
  ADD CONSTRAINT `pedido_producto_ibfk_1` FOREIGN KEY (`Idproducto`) REFERENCES `producto` (`Idproducto`),
  ADD CONSTRAINT `pedido_producto_ibfk_2` FOREIGN KEY (`num_pedido`) REFERENCES `pedido` (`num_pedido`);

ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`Idfoto`) REFERENCES `foto` (`idFoto`);

ALTER TABLE `producto_voto`
  ADD CONSTRAINT `producto_voto_ibfk_1` FOREIGN KEY (`Idproducto`) REFERENCES `producto` (`Idproducto`),
  ADD CONSTRAINT `producto_voto_ibfk_2` FOREIGN KEY (`Idvoto`) REFERENCES `voto` (`Idvoto`);

ALTER TABLE `voto`
  ADD CONSTRAINT `voto_ibfk_1` FOREIGN KEY (`Idproducto`) REFERENCES `producto` (`Idproducto`),
  ADD CONSTRAINT `voto_ibfk_2` FOREIGN KEY (`Idlocal`) REFERENCES `local` (`idLocal`),
  ADD CONSTRAINT `voto_ibfk_3` FOREIGN KEY (`ciMozo`) REFERENCES `mozo` (`ciUsuario`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;