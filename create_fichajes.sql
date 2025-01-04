SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `fichaje` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `fichaje`;

CREATE TABLE IF NOT EXISTS `cambios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `estado` int(11) NOT NULL,
  `anterior` datetime NOT NULL,
  `posterior` datetime NOT NULL,
  `comentario` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `empleados` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `nombre` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `NIF` varchar(16) NOT NULL,
  `email` varchar(64) NOT NULL,
  `dentro` tinyint(1) NOT NULL,
  `role` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `NIF` (`NIF`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `empleados` 
  (`username`, `nombre`, `password`, `NIF`, `email`, `dentro`, `role`)
VALUES 
  ('admin', 'Administrador', PASSWORD('12345678'), '00000000T', 'admin@localhost.local', 0, 10);

CREATE TABLE IF NOT EXISTS `mensajes` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` int(11) NOT NULL,
  `de` int(11) NOT NULL,
  `para` int(11) NOT NULL,
  `texto` text NOT NULL,
  PRIMARY KEY (`msg_id`),
  KEY `para_estado` (`para`,`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `registros` (
  `reg_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reg_time` datetime DEFAULT NULL,
  `entrada` tinyint(1) NOT NULL,
  `creado` datetime NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `spare` int(11) DEFAULT NULL,
  PRIMARY KEY (`reg_id`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
