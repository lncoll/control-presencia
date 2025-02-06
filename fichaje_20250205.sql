
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
-- - CREATE DATABASE IF NOT EXISTS `fichaje` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
-- - USE `fichaje`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cambios`
--

CREATE TABLE IF NOT EXISTS `cambios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `estado` int(11) NOT NULL DEFAULT 0,
  `anterior` datetime NOT NULL,
  `posterior` datetime NOT NULL,
  `comentario` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE IF NOT EXISTS `empleados` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `NIF` varchar(16) NOT NULL,
  `email` varchar(64) NOT NULL,
  `dentro` tinyint(1) NOT NULL DEFAULT 0,
  `role` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `NIF` (`NIF`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE IF NOT EXISTS `mensajes` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` int(11) NOT NULL DEFAULT 0,
  `de` int(11) NOT NULL,
  `para` int(11) NOT NULL,
  `hora` datetime NOT NULL DEFAULT current_timestamp(),
  `texto` text NOT NULL,
  PRIMARY KEY (`msg_id`),
  KEY `para_estado` (`para`,`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros`
--

CREATE TABLE IF NOT EXISTS `registros` (
  `reg_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reg_time` datetime NOT NULL DEFAULT current_timestamp(),
  `entrada` tinyint(1) NOT NULL,
  `IP` varchar(16) NOT NULL,
  `location` varchar(64) NOT NULL,
  `creado` datetime NOT NULL DEFAULT current_timestamp(),
  `modificado` datetime DEFAULT NULL,
  PRIMARY KEY (`reg_id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
