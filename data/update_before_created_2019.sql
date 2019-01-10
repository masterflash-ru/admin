-- MySQL dump 10.13  Distrib 5.6.37, for FreeBSD11.0 (i386)
--
-- Host: localhost    Database: simba
-- ------------------------------------------------------
-- Server version	5.6.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


INSERT INTO `permissions` (`id`, `name`, `object`, `mode`, `owner_user`, `owner_group`) VALUES 
  (1, 'Конструктор древов. структур', 'Admin\\Controller\\ConstructorTreeController/index', 456, 1, 1),
  (2, 'Конструктор линейных структур', 'Admin\\Controller\\ConstructorLineController/index', 456, 1, 1),
  (3, 'Успешная авторизация админки', 'Admin\\Controller\\IndexController/index', 456, 1, 1),
  (9, 'админка 403', 'Admin\\Controller\\LoginController/e403', 457, 1, 1),
  (4, 'Специальный для магазинов', 'Admin\\Controller\\TovarController/index', 456, 1, 1),
  (5, 'Генератор сущностей', 'Admin\\Controller\\EntityController/index', 456, 1, 1),
  (6, 'архивация базы', 'Admin\\Controller\\BackupRestoreController/index', 456, 1, 1),
  (7, 'ввод-вывод древовид. структур', 'Admin\\Controller\\TreeController/index', 456, 1, 1),
  (8, 'ввод-вывод линейных структур', 'Admin\\Controller\\LineController/index', 456, 1, 1),
  (10, 'админка access denied', 'Admin\\Controller\\LoginController/accessdenied', 457, 1, 1),
  (11, 'админка форма входа', 'Admin\\Controller\\LoginController/login', 457, 1, 1),
  (12, 'админка меню', 'Admin/Menu', 480, 1, 1);
  
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-12  9:23:50
