-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-11-2025 a las 21:17:17
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `digibook2`
--

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `crearNuevoLibro`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `crearNuevoLibro` (IN `p_titulo` VARCHAR(255), IN `p_precio` DECIMAL(10,2), IN `p_idEditorial` INT, IN `p_sinopsis` TEXT, IN `p_paginas` INT, IN `p_idAutor` INT, IN `p_idGenero` INT, IN `p_img` VARCHAR(100), IN `p_fecha` DATE, OUT `p_resultado` INT, OUT `p_msj_error` VARCHAR(255))   BEGIN
    -- Manejador de errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 @text = MESSAGE_TEXT;
        ROLLBACK;
        SET p_resultado = 0;
        SET p_msj_error = CONCAT('Error SQL: ', @text);
    END;

    START TRANSACTION;

    -- 1. Insertar en ARTICULO (Sin fecha)
    INSERT INTO articulo (titulo, precio, idEditorial, sinopsis, paginas, idGenero, img)
    VALUES (p_titulo, p_precio, p_idEditorial, p_sinopsis, p_paginas, p_idGenero, p_img);

    -- Recuperar ID
    SET @idNuevoLibro = LAST_INSERT_ID();

    -- 2. Insertar en ARTICULOAUTOR (Con fecha)
    -- Aquí usamos 'fechaPublicacion' tal cual me confirmaste
    INSERT INTO articuloautor (idLibro, idAutor, fechaPublicacion)
    VALUES (@idNuevoLibro, p_idAutor, p_fecha);

    COMMIT;
    
    SET p_resultado = 1;
    SET p_msj_error = 'Libro creado exitosamente.';

END$$

DROP PROCEDURE IF EXISTS `getAutores`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getAutores` ()   BEGIN
    SELECT idAutor, nombre, apellido FROM Autor;
END$$

DROP PROCEDURE IF EXISTS `getEditoriales`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getEditoriales` ()   BEGIN
    SELECT idEditorial, nombre, email FROM editorial;
END$$

DROP PROCEDURE IF EXISTS `getGeneros`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `getGeneros` ()   BEGIN
    SELECT idGenero, nombre, descripcion FROM genero;
END$$

DROP PROCEDURE IF EXISTS `obtenerLibroPorId`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerLibroPorId` (IN `p_idLibro` INT)   BEGIN
    SELECT 
        l.idLibro as 'id',
        l.titulo AS 'Título',
        l.precio AS 'Precio',
        g.nombre AS 'Género',
        GROUP_CONCAT(CONCAT(a.nombre, ' ', a.apellido) SEPARATOR ', ') AS 'Autores',
        e.nombre AS 'Editorial'
    FROM 
        articulo l
    JOIN 
        editorial e ON l.idEditorial = e.idEditorial
    JOIN 
        genero g ON l.idGenero = g.idGenero
    JOIN 
        articuloautor la ON l.idLibro = la.idLibro
    JOIN 
        autor a ON la.idAutor = a.idAutor
    WHERE l.idLibro=p_idLibro
    GROUP BY 
        l.idLibro, l.titulo, l.precio, g.nombre, e.nombre;

END$$

DROP PROCEDURE IF EXISTS `obtenerLibros`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerLibros` ()   BEGIN
    SELECT 
        l.idLibro as 'id',
        l.titulo AS 'Título',
        l.img,   -- <--- ¡AQUÍ ESTÁ LA CLAVE! Agregamos la columna de la imagen
        l.precio AS 'Precio',
        g.nombre AS 'Género',
        GROUP_CONCAT(CONCAT(a.nombre, ' ', a.apellido) SEPARATOR ', ') AS 'Autores',
        e.nombre AS 'Editorial'
    FROM 
        articulo l
    JOIN 
        editorial e ON l.idEditorial = e.idEditorial
    JOIN 
        genero g ON l.idGenero = g.idGenero
    JOIN 
        articuloautor la ON l.idLibro = la.idLibro
    JOIN 
        autor a ON la.idAutor = a.idAutor
    GROUP BY 
        l.idLibro; -- Importante: Agrupa por libro para que los autores se junten
END$$

DROP PROCEDURE IF EXISTS `registrar_compra_con_detalles`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `registrar_compra_con_detalles` (IN `p_total` DECIMAL(10,2), IN `p_fecha` DATE, IN `p_dni` VARCHAR(15), IN `p_detalles` JSON, OUT `p_resultado` VARCHAR(255), OUT `p_idCompra` INT)   BEGIN
    DECLARE v_idCompra INT;
    DECLARE i INT;
    DECLARE n INT;

    DECLARE v_unidades INT;
    DECLARE v_idLibro INT;
    DECLARE v_precio INT;

    -- Manejador de error general
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_resultado = CONCAT('Error: fallo interno en la base de datos (verifica datos, claves foráneas o integridad referencial).');
        SET p_idCompra = -1;
    END;

    -- Bloque etiquetado
    compra_loop: BEGIN
        SET i = 0;

        -- Validación del parámetro JSON
        IF p_detalles IS NULL THEN
            SET p_resultado = 'Error: los detalles de la compra no pueden ser NULL.';
            SET p_idCompra = -1;
            LEAVE compra_loop;
        END IF;

        SET n = JSON_LENGTH(p_detalles);
        IF n = 0 OR n IS NULL THEN
            SET p_resultado = 'Error: los detalles de la compra están vacíos o mal formateados.';
            SET p_idCompra = -1;
            LEAVE compra_loop;
        END IF;

        -- Iniciar transacción
        START TRANSACTION;

        -- Insertar compra
        INSERT INTO compra (total, fecha, dni)
        VALUES (p_total, p_fecha, p_dni);

        SET v_idCompra = LAST_INSERT_ID();

        -- Procesar los detalles
        WHILE i < n DO
            SET v_unidades = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_detalles, CONCAT('$[', i, '].qty'))) AS UNSIGNED);
            IF v_unidades IS NULL OR v_unidades <= 0 THEN
                ROLLBACK;
                SET p_resultado = CONCAT('Error: unidades inválidas en el detalle #', i + 1, '. Valor proporcionado: ', v_unidades);
                SET p_idCompra = -1;
                LEAVE compra_loop;
            END IF;

SET v_precio = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_detalles, CONCAT('$[', i, '].precio'))) AS UNSIGNED);
            IF v_precio IS NULL OR v_precio <= 0 THEN
                ROLLBACK;
                SET p_resultado = CONCAT('Error: precio inválido en el detalle #', i + 1, '. Valor proporcionado: ', v_precio);
                SET p_idCompra = -1;
                LEAVE compra_loop;
            END IF;

            SET v_idLibro = CAST(JSON_UNQUOTE(JSON_EXTRACT(p_detalles, CONCAT('$[', i, '].id'))) AS UNSIGNED);
            IF v_idLibro IS NULL OR v_idLibro <= 0 THEN
                ROLLBACK;
                SET p_resultado = CONCAT('Error: idLibro inválido en el detalle #', i + 1, '. Valor proporcionado: ', v_idLibro);
                SET p_idCompra = -1;
                LEAVE compra_loop;
            END IF;

            -- Validar existencia de articulo
            IF NOT EXISTS (SELECT 1 FROM articulo WHERE idLibro = v_idLibro) THEN
                ROLLBACK;
                SET p_resultado = CONCAT('Error: el articulo con id ', v_idLibro, ' no existe en la base de datos.');
                SET p_idCompra = -1;
                LEAVE compra_loop;
            END IF;

            -- Insertar detalle
            INSERT INTO detallescompra (unidades, precio, idLibro, idCompra)
            VALUES (v_unidades,v_precio, v_idLibro, v_idCompra);

            SET i = i + 1;
        END WHILE;

        COMMIT;

        SET p_resultado = 'La compra fue registrada correctamente.';
        SET p_idCompra = v_idCompra;

    END compra_loop;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulo`
--

DROP TABLE IF EXISTS `articulo`;
CREATE TABLE `articulo` (
  `idLibro` int(10) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `precio` double(10,2) UNSIGNED NOT NULL,
  `paginas` int(10) NOT NULL,
  `sinopsis` varchar(250) NOT NULL,
  `img` varchar(100) DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT 1,
  `idGenero` int(10) NOT NULL,
  `idEditorial` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `articulo`
--

INSERT INTO `articulo` (`idLibro`, `titulo`, `precio`, `paginas`, `sinopsis`, `img`, `activo`, `idGenero`, `idEditorial`) VALUES
(33, 'El Psicoanalista', 48900.00, 523, 'El psicoanalista es una novela escrita por John Katzenbach, publicada en 2002. Este thriller psicológico es una de las novelas más exitosas del autor. Cuenta con más de un millón de ejemplares vendidos y en 2004 ganó el Gran Premio de la Literatura P', 'port1.jpg', 1, 1, 2),
(34, 'Departamento 14', 89655.00, 899, 'Algunas puertas deberían permanecer cerradas... En Barrington House, un elegante bloque de pisos londinense, hay un apartamento vacío. Nadie entra, nadie sale. Y ha permanecido así durante cincuenta años. Hasta que una noche el vigilante oye unos rui', '1764515683_87e8819a4c2720c39144.webp', 1, 2, 1),
(35, 'Autoayuda 2025', 69888.00, 598, 'Algunas puertas deberían permanecer cerradas... En Barrington House, un elegante bloque de pisos londinense, hay un apartamento vacío. Nadie entra, nadie sale. Y ha permanecido así durante cincuenta años. Hasta que una noche el vigilante oye unos rui', NULL, 1, 1, 1),
(36, 'Autoayuda 2da edicion', 98777.00, 547, 'Ésta es la oportunidad de cambiar tu vida. Empfohlen 18 bis 99 Jahre. 1. Auflage. Sprache: Spanisch.', '1764516562_c0a85a660966a78078b1.jpg', 1, 3, 2),
(37, 'Los Crimenes de la Calle Morgue', 548700.00, 465, 'Los crímenes de la calle Morgue, también conocido como Los asesinatos de la calle Morgue o Los asesinatos de la rue Morgue, es un cuento del género policíaco y de terror del escritor estadounidense Edgar Allan Poe, publicado por primera vez en la rev', '1764518470_46e5d8183324b68f4c73.jpg', 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articuloautor`
--

DROP TABLE IF EXISTS `articuloautor`;
CREATE TABLE `articuloautor` (
  `idLibro` int(10) NOT NULL,
  `idAutor` int(10) NOT NULL,
  `fechaPublicacion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `articuloautor`
--

INSERT INTO `articuloautor` (`idLibro`, `idAutor`, `fechaPublicacion`) VALUES
(33, 2, '2025-04-18'),
(34, 2, '2019-01-02'),
(35, 1, '2023-04-05'),
(36, 2, '1999-05-01'),
(37, 1, '1975-01-10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autor`
--

DROP TABLE IF EXISTS `autor`;
CREATE TABLE `autor` (
  `idAutor` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `autor`
--

INSERT INTO `autor` (`idAutor`, `nombre`, `apellido`) VALUES
(1, 'George ', 'Orwell'),
(2, 'Stephen', 'King');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

DROP TABLE IF EXISTS `compra`;
CREATE TABLE `compra` (
  `idCompra` int(10) NOT NULL,
  `total` double(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `dni` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`idCompra`, `total`, `fecha`, `dni`) VALUES
(47, 19000.00, '2025-11-27', 32837262),
(48, 46556.00, '2025-11-27', 32837262),
(49, 1230.00, '2025-11-27', 32837262),
(50, 31000.00, '2025-11-27', 32837262),
(51, 48900.00, '2025-11-30', 32837262),
(52, 168665.00, '2025-11-30', 32837262);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallescompra`
--

DROP TABLE IF EXISTS `detallescompra`;
CREATE TABLE `detallescompra` (
  `idDetalle` int(10) NOT NULL,
  `unidades` int(3) NOT NULL,
  `precio` float NOT NULL,
  `idLibro` int(10) NOT NULL,
  `idCompra` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `detallescompra`
--

INSERT INTO `detallescompra` (`idDetalle`, `unidades`, `precio`, `idLibro`, `idCompra`) VALUES
(47, 1, 48900, 33, 51),
(48, 1, 69888, 35, 52),
(49, 1, 98777, 36, 52);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `editorial`
--

DROP TABLE IF EXISTS `editorial`;
CREATE TABLE `editorial` (
  `idEditorial` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `editorial`
--

INSERT INTO `editorial` (`idEditorial`, `nombre`, `email`) VALUES
(1, 'Pegasus', 'pegasus_contact@pegasus.com'),
(2, 'Estrada', 'estrada_contact@estrada.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

DROP TABLE IF EXISTS `genero`;
CREATE TABLE `genero` (
  `idGenero` int(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`idGenero`, `nombre`, `descripcion`) VALUES
(1, 'Terror', 'Géneros de libros basados en terror, desde psicológico hasta sobrenatural.'),
(2, 'Ciencia Ficción', 'Género basado en ciencia ficción clásica y moderna.'),
(3, 'Distopia', 'Representación ficticia de una sociedad futura de características negativas causantes de la alienación humana.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipousuario`
--

DROP TABLE IF EXISTS `tipousuario`;
CREATE TABLE `tipousuario` (
  `idTipoUsuario` int(10) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `tipousuario`
--

INSERT INTO `tipousuario` (`idTipoUsuario`, `tipo`, `descripcion`) VALUES
(1, 'Administrador', 'Un administrador del sistema'),
(2, 'Cliente', 'El usuario que realizará las compras');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `dni` int(8) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `contrasenia` varchar(50) NOT NULL,
  `email` varchar(40) NOT NULL,
  `idTipoUsuario` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`dni`, `nombre`, `apellido`, `contrasenia`, `email`, `idTipoUsuario`) VALUES
(32837262, 'Kike', 'Espinoza', '12345', 'kike_87@gmail.com', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD PRIMARY KEY (`idLibro`),
  ADD KEY `idGenero` (`idGenero`),
  ADD KEY `idEditorial` (`idEditorial`);

--
-- Indices de la tabla `articuloautor`
--
ALTER TABLE `articuloautor`
  ADD PRIMARY KEY (`idLibro`,`idAutor`),
  ADD KEY `idAutor` (`idAutor`);

--
-- Indices de la tabla `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`idAutor`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`idCompra`),
  ADD KEY `dni` (`dni`);

--
-- Indices de la tabla `detallescompra`
--
ALTER TABLE `detallescompra`
  ADD PRIMARY KEY (`idDetalle`),
  ADD KEY `idCompra` (`idCompra`),
  ADD KEY `idLibro` (`idLibro`);

--
-- Indices de la tabla `editorial`
--
ALTER TABLE `editorial`
  ADD PRIMARY KEY (`idEditorial`);

--
-- Indices de la tabla `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`idGenero`);

--
-- Indices de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  ADD PRIMARY KEY (`idTipoUsuario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`dni`),
  ADD KEY `idTipoUsuario` (`idTipoUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `articulo`
--
ALTER TABLE `articulo`
  MODIFY `idLibro` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `idCompra` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `detallescompra`
--
ALTER TABLE `detallescompra`
  MODIFY `idDetalle` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT de la tabla `editorial`
--
ALTER TABLE `editorial`
  MODIFY `idEditorial` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `idGenero` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  MODIFY `idTipoUsuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `dni` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32837263;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD CONSTRAINT `libro_ibfk_1` FOREIGN KEY (`idGenero`) REFERENCES `genero` (`idGenero`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `libro_ibfk_2` FOREIGN KEY (`idEditorial`) REFERENCES `editorial` (`idEditorial`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `articuloautor`
--
ALTER TABLE `articuloautor`
  ADD CONSTRAINT `libroautor_ibfk_1` FOREIGN KEY (`idLibro`) REFERENCES `articulo` (`idLibro`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `libroautor_ibfk_2` FOREIGN KEY (`idAutor`) REFERENCES `autor` (`idAutor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`dni`) REFERENCES `usuario` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detallescompra`
--
ALTER TABLE `detallescompra`
  ADD CONSTRAINT `detallescompra_ibfk_1` FOREIGN KEY (`idCompra`) REFERENCES `compra` (`idCompra`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detallescompra_ibfk_2` FOREIGN KEY (`idLibro`) REFERENCES `articulo` (`idLibro`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`idTipoUsuario`) REFERENCES `tipousuario` (`idTipoUsuario`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
