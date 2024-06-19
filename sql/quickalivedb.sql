-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-06-2024 a las 22:51:09
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `quickalivedb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad`
--

CREATE TABLE `actividad` (
  `idActividad` int(11) NOT NULL,
  `nombreActividad` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `duracion` int(11) NOT NULL,
  `tipoActividad` enum('simple','geolocalizable') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividad`
--

INSERT INTO `actividad` (`idActividad`, `nombreActividad`, `descripcion`, `duracion`, `tipoActividad`) VALUES
(168, 'Crea una lista de reproducción de música relajante', 'Dedica un tiempo para seleccionar tus canciones favoritas que te ayuden a relajarte y desconectar del estrés diario. Puedes escoger música suave, instrumental o de naturaleza para crear una lista de reproducción personalizada que te ayude a encontrar calma y tranquilidad.', 30, 'simple'),
(169, 'Practica meditación en casa', 'Encuentra un lugar tranquilo en tu hogar y dedica unos minutos al día para practicar la meditación. Sigue una guía en línea o simplemente concéntrate en tu respiración y en estar presente en el momento. La meditación es una excelente manera de reducir el estrés, mejorar la concentración y promover el bienestar emocional.', 15, 'simple'),
(170, 'Experimenta con recetas de cocina saludable', 'Explora tu creatividad en la cocina preparando nuevas recetas saludables. Busca inspiración en libros de cocina o en línea y prueba platos nutritivos y deliciosos. Cocinar en casa te permite controlar los ingredientes y te brinda la satisfacción de disfrutar de comidas caseras y saludables.', 60, 'simple'),
(171, 'Disfruta de una caminata en la naturaleza', 'Aprovecha para salir al aire libre y disfrutar de una caminata en un parque cercano o sendero natural. Conecta con la naturaleza, respira aire fresco y admira los paisajes mientras caminas a tu propio ritmo. La caminata es una actividad rejuvenecedora que beneficia tanto al cuerpo como a la mente.', 60, 'simple'),
(172, 'Haz una sesión de ejercicios en casa', 'Dedica un tiempo a hacer ejercicio en la comodidad de tu hogar. Sigue una rutina de ejercicios en línea o crea la tuya propia utilizando ejercicios de cardio, fuerza y flexibilidad. No necesitas equipos sofisticados; con ejercicios básicos como flexiones, abdominales, sentadillas y estiramientos puedes mantenerte activo y en forma.', 30, 'simple'),
(173, 'Organiza un espacio de tu hogar', 'Dedica un día para ordenar y organizar un área específica de tu hogar, como un armario, cajón o escritorio. Deshazte de lo que ya no necesitas, organiza tus pertenencias y crea un ambiente más limpio y armonioso. La organización del hogar puede ayudarte a reducir el estrés y aumentar la sensación de bienestar.', 60, 'simple'),
(214, 'Ricky Martin', 'undefined', 0, 'geolocalizable'),
(215, 'Ricky Martin', 'undefined', 0, 'geolocalizable'),
(216, 'El Barrio', 'undefined', 0, 'geolocalizable'),
(217, 'El Barrio', 'undefined', 0, 'geolocalizable'),
(218, 'Ricky Martin', 'undefined', 0, 'geolocalizable'),
(345, 'MANÁ', 'undefined', 0, 'geolocalizable'),
(346, 'MANÁ', 'undefined', 0, 'geolocalizable'),
(347, 'Ricky Martin', 'undefined', 0, 'geolocalizable'),
(348, 'El Barrio', 'undefined', 0, 'geolocalizable');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividadgeolocalizable`
--

CREATE TABLE `actividadgeolocalizable` (
  `idActividad` int(11) NOT NULL,
  `urlRemota` varchar(255) DEFAULT NULL,
  `idApi` varchar(100) DEFAULT NULL,
  `fechaLimite` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividadgeolocalizable`
--

INSERT INTO `actividadgeolocalizable` (`idActividad`, `urlRemota`, `idApi`, `fechaLimite`) VALUES
(214, 'https://s1.ticketm.net/dam/a/3c0/d2a69825-c9e9-44b6-8f78-6f8c274853c0_RECOMENDATION_16_9.jpg', 'Z698xZ2qZaaX-', '2024-07-11 00:00:00'),
(215, 'https://s1.ticketm.net/dam/a/3c0/d2a69825-c9e9-44b6-8f78-6f8c274853c0_RECOMENDATION_16_9.jpg', 'Z698xZ2qZaaX-', '2024-07-11 00:00:00'),
(216, 'https://s1.ticketm.net/dam/a/0bd/c42bb6c2-b579-4290-81fd-aa85f43560bd_EVENT_DETAIL_PAGE_16_9.jpg', 'Z698xZ2qZaaxT', '2024-10-26 00:00:00'),
(217, 'https://s1.ticketm.net/dam/a/0bd/c42bb6c2-b579-4290-81fd-aa85f43560bd_EVENT_DETAIL_PAGE_16_9.jpg', 'Z698xZ2qZaaxT', '2024-10-26 00:00:00'),
(218, 'https://s1.ticketm.net/dam/a/3c0/d2a69825-c9e9-44b6-8f78-6f8c274853c0_RECOMENDATION_16_9.jpg', 'Z698xZ2qZaaX-', '2024-07-11 00:00:00'),
(345, 'https://s1.ticketm.net/dam/a/292/0fcd984b-4beb-4c31-bf86-a5d64f18e292_EVENT_DETAIL_PAGE_16_9.jpg', 'Z698xZ2qZaa8a', '2024-06-19 21:30:00'),
(346, 'https://s1.ticketm.net/dam/a/292/0fcd984b-4beb-4c31-bf86-a5d64f18e292_EVENT_DETAIL_PAGE_16_9.jpg', 'Z698xZ2qZaa8a', '2024-06-19 21:30:00'),
(347, 'https://s1.ticketm.net/dam/a/3c0/d2a69825-c9e9-44b6-8f78-6f8c274853c0_RECOMENDATION_16_9.jpg', 'Z698xZ2qZaaX-', '2024-07-11 22:00:00'),
(348, 'https://s1.ticketm.net/dam/a/0bd/c42bb6c2-b579-4290-81fd-aa85f43560bd_EVENT_DETAIL_PAGE_16_9.jpg', 'Z698xZ2qZaaxT', '2024-10-26 21:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividadsimple`
--

CREATE TABLE `actividadsimple` (
  `idActividad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividadsimple`
--

INSERT INTO `actividadsimple` (`idActividad`) VALUES
(168),
(169),
(170),
(171),
(172),
(173);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividad_tipopreferencia`
--

CREATE TABLE `actividad_tipopreferencia` (
  `idActividad` int(11) NOT NULL,
  `idTipoPreferencia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividad_tipopreferencia`
--

INSERT INTO `actividad_tipopreferencia` (`idActividad`, `idTipoPreferencia`) VALUES
(168, 338),
(168, 343),
(168, 345),
(169, 338),
(169, 342),
(169, 345),
(170, 330),
(170, 331),
(170, 338),
(170, 343),
(171, 338),
(171, 345),
(171, 346),
(171, 347),
(171, 348),
(172, 338),
(172, 345),
(172, 347),
(172, 349),
(172, 350),
(173, 338),
(173, 343),
(173, 347);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `galeriafotos`
--

CREATE TABLE `galeriafotos` (
  `numImagen` int(11) NOT NULL,
  `idActividad` int(11) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `galeriafotos`
--

INSERT INTO `galeriafotos` (`numImagen`, `idActividad`, `url`) VALUES
(317, 168, '/quickalive/imgs/actividad168.jpeg'),
(318, 169, '/quickalive/imgs/actividad169.jpg'),
(319, 170, '/quickalive/imgs/actividad170.jpg'),
(320, 171, '/quickalive/imgs/actividad171.avif'),
(321, 172, '/quickalive/imgs/actividad171.jpg'),
(322, 173, '/quickalive/imgs/actividad173.webp');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `realiza`
--

CREATE TABLE `realiza` (
  `idUsuario` int(11) NOT NULL,
  `idActividad` int(11) NOT NULL,
  `fechaRealizacion` datetime DEFAULT NULL,
  `completada` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `realiza`
--

INSERT INTO `realiza` (`idUsuario`, `idActividad`, `fechaRealizacion`, `completada`) VALUES
(198, 171, '2024-06-19 19:49:45', 1),
(198, 346, '2024-06-19 19:49:45', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rechazadas`
--

CREATE TABLE `rechazadas` (
  `idUsuario` int(11) NOT NULL,
  `idActividad` int(11) NOT NULL,
  `fechaRechazo` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipopreferencias`
--

CREATE TABLE `tipopreferencias` (
  `idTipoPreferencia` int(11) NOT NULL,
  `tipoPreferencia` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipopreferencias`
--

INSERT INTO `tipopreferencias` (`idTipoPreferencia`, `tipoPreferencia`) VALUES
(329, 'Comedia'),
(330, 'Comida'),
(331, 'Educación'),
(334, 'R&B'),
(335, 'Hip Hop'),
(337, 'Películas'),
(338, 'Desarrollo Personal'),
(339, 'Blues & Jazz'),
(340, 'Viajar'),
(341, 'Rock'),
(342, 'Yoga'),
(343, 'Emprendimiento'),
(345, 'Salud Mental'),
(346, 'Naturaleza '),
(347, 'Bienestar'),
(348, 'Senderismo'),
(349, 'Fitness'),
(350, 'Entrenamiento Cardiovascular');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idUsuario` int(11) NOT NULL,
  `nickName` varchar(50) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `tokenRecuperacion` varchar(255) DEFAULT NULL,
  `fechaExpiracionToken` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`idUsuario`, `nickName`, `telefono`, `correo`, `password`, `nombre`, `apellidos`, `edad`, `rol`, `tokenRecuperacion`, `fechaExpiracionToken`) VALUES
(7, 'root', '123456789', 'franexca@gmail.com', '$2y$10$n0W7C5BolDMo3Gm4oZoAwuvzzhocgftK3m5j.S9dhXhkRhYAL/yOK', 'Root', 'Root', 40, 'root', '2dc0f65831e6093893ec847a5939dba6a3a7ddb42cb196b1e235784a8df124ca', '2024-06-19 18:12:40'),
(198, 'prueba', '987654321', 'franexca@gmail.com', '$2y$10$n0W7C5BolDMo3Gm4oZoAwuvzzhocgftK3m5j.S9dhXhkRhYAL/yOK', 'asd', 'asd', 34, 'registrado', NULL, '2024-06-19 18:12:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuariopreferencias`
--

CREATE TABLE `usuariopreferencias` (
  `idUsuario` int(11) NOT NULL,
  `idTipoPreferencia` int(11) NOT NULL,
  `nombreTipoPreferencia` varchar(255) NOT NULL,
  `pInteres` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuariopreferencias`
--

INSERT INTO `usuariopreferencias` (`idUsuario`, `idTipoPreferencia`, `nombreTipoPreferencia`, `pInteres`) VALUES
(198, 338, 'Desarrollo Personal', 2),
(198, 339, 'Blues & Jazz', 1),
(198, 343, 'Emprendimiento', 1),
(198, 345, 'Salud Mental', 1),
(198, 346, 'Naturaleza ', 2),
(198, 347, 'Bienestar', 3),
(198, 348, 'Senderismo', 1);

--
-- Disparadores `usuariopreferencias`
--
DELIMITER $$
CREATE TRIGGER `eliminar_fila_si_cero` AFTER UPDATE ON `usuariopreferencias` FOR EACH ROW BEGIN
    IF NEW.pInteres = 0 THEN
        DELETE FROM usuariopreferencias WHERE idUsuario = NEW.idUsuario AND idTipoPreferencia = NEW.idTipoPreferencia;
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividad`
--
ALTER TABLE `actividad`
  ADD PRIMARY KEY (`idActividad`);

--
-- Indices de la tabla `actividadgeolocalizable`
--
ALTER TABLE `actividadgeolocalizable`
  ADD PRIMARY KEY (`idActividad`);

--
-- Indices de la tabla `actividadsimple`
--
ALTER TABLE `actividadsimple`
  ADD KEY `fk_actividad_simple_actividad` (`idActividad`);

--
-- Indices de la tabla `actividad_tipopreferencia`
--
ALTER TABLE `actividad_tipopreferencia`
  ADD PRIMARY KEY (`idActividad`,`idTipoPreferencia`),
  ADD KEY `idTipoPreferencia` (`idTipoPreferencia`);

--
-- Indices de la tabla `galeriafotos`
--
ALTER TABLE `galeriafotos`
  ADD PRIMARY KEY (`numImagen`),
  ADD KEY `galeriafotos_ibfk_1` (`idActividad`);

--
-- Indices de la tabla `realiza`
--
ALTER TABLE `realiza`
  ADD PRIMARY KEY (`idUsuario`,`idActividad`),
  ADD KEY `realiza_ibfk_2` (`idActividad`);

--
-- Indices de la tabla `rechazadas`
--
ALTER TABLE `rechazadas`
  ADD PRIMARY KEY (`idUsuario`,`idActividad`),
  ADD KEY `rechazadas_ibfk_2` (`idActividad`);

--
-- Indices de la tabla `tipopreferencias`
--
ALTER TABLE `tipopreferencias`
  ADD PRIMARY KEY (`idTipoPreferencia`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idUsuario`,`nickName`),
  ADD UNIQUE KEY `uk_correo_telefono` (`correo`,`telefono`);

--
-- Indices de la tabla `usuariopreferencias`
--
ALTER TABLE `usuariopreferencias`
  ADD PRIMARY KEY (`idUsuario`,`idTipoPreferencia`,`nombreTipoPreferencia`),
  ADD KEY `idTipoPreferencia` (`idTipoPreferencia`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividad`
--
ALTER TABLE `actividad`
  MODIFY `idActividad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=349;

--
-- AUTO_INCREMENT de la tabla `galeriafotos`
--
ALTER TABLE `galeriafotos`
  MODIFY `numImagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=323;

--
-- AUTO_INCREMENT de la tabla `tipopreferencias`
--
ALTER TABLE `tipopreferencias`
  MODIFY `idTipoPreferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=419;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividadgeolocalizable`
--
ALTER TABLE `actividadgeolocalizable`
  ADD CONSTRAINT `fk_actividad_geolocalizable` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_idActividad` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`);

--
-- Filtros para la tabla `actividadsimple`
--
ALTER TABLE `actividadsimple`
  ADD CONSTRAINT `actividadsimple_ibfk_1` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_actividad_simple_actividad` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`) ON DELETE CASCADE;

--
-- Filtros para la tabla `actividad_tipopreferencia`
--
ALTER TABLE `actividad_tipopreferencia`
  ADD CONSTRAINT `actividad_tipopreferencia_ibfk_1` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`) ON DELETE CASCADE,
  ADD CONSTRAINT `actividad_tipopreferencia_ibfk_2` FOREIGN KEY (`idTipoPreferencia`) REFERENCES `tipopreferencias` (`idTipoPreferencia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `galeriafotos`
--
ALTER TABLE `galeriafotos`
  ADD CONSTRAINT `galeriafotos_ibfk_1` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `realiza`
--
ALTER TABLE `realiza`
  ADD CONSTRAINT `realiza_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `realiza_ibfk_2` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `rechazadas`
--
ALTER TABLE `rechazadas`
  ADD CONSTRAINT `rechazadas_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rechazadas_ibfk_2` FOREIGN KEY (`idActividad`) REFERENCES `actividad` (`idActividad`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuariopreferencias`
--
ALTER TABLE `usuariopreferencias`
  ADD CONSTRAINT `usuariopreferencias_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`idUsuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuariopreferencias_ibfk_2` FOREIGN KEY (`idTipoPreferencia`) REFERENCES `tipopreferencias` (`idTipoPreferencia`) ON DELETE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `eliminar_actividades_rechazadas` ON SCHEDULE EVERY 1 DAY STARTS '2024-05-14 00:15:26' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM rechazadas WHERE fechaRechazo < NOW() - INTERVAL 60 SECOND$$

CREATE DEFINER=`root`@`localhost` EVENT `marcar_actividades_completadas` ON SCHEDULE EVERY 1 MINUTE STARTS '2024-05-20 17:39:37' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    -- Actualizar la tabla realiza para marcar actividades como completadas si la fechaRealizacion ha pasado
    UPDATE realiza
    SET completada = 1
    WHERE fechaRealizacion < NOW() AND completada = 0;
END$$

CREATE DEFINER=`root`@`localhost` EVENT `eliminar_actividades_expiradas` ON SCHEDULE EVERY 1 DAY STARTS '2024-06-14 14:41:18' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DELETE FROM actividadgeolocalizable
    WHERE fechaLimite < NOW();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
