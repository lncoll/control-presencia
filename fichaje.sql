-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 22-01-2025 a las 17:52:31
-- Versión del servidor: 10.11.8-MariaDB-0ubuntu0.24.04.1
-- Versión de PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fichaje`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios`
--

CREATE TABLE `cambios` (
  `id` int(11) NOT NULL,
  `reg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0,
  `anterior` datetime NOT NULL,
  `posterior` datetime NOT NULL,
  `comentario` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `user_id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `NIF` varchar(16) NOT NULL,
  `email` varchar(64) NOT NULL,
  `dentro` tinyint(1) NOT NULL DEFAULT 0,
  `role` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`user_id`, `username`, `nombre`, `password`, `NIF`, `email`, `dentro`, `role`) VALUES
(1, 'admin', 'Administrador', PASSWORD('123456789'), '00000000T', 'admin@localhost', 0, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `msg_id` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0,
  `de` int(11) NOT NULL,
  `para` int(11) NOT NULL,
  `hora` datetime NOT NULL DEFAULT current_timestamp(),
  `texto` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros`
--

CREATE TABLE `registros` (
  `reg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reg_time` datetime NOT NULL DEFAULT current_timestamp(),
  `entrada` tinyint(1) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cambios`
--
ALTER TABLE `cambios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estado` (`estado`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `NIF` (`NIF`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `para_estado` (`para`,`estado`);

--
-- Indices de la tabla `registros`
--
ALTER TABLE `registros`
  ADD PRIMARY KEY (`reg_id`),
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cambios`
--
ALTER TABLE `cambios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registros`
--
ALTER TABLE `registros`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
