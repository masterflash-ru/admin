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

--
-- Table structure for table `admin_menu`
--

DROP TABLE IF EXISTS `admin_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Ключ',
  `name` char(255) NOT NULL COMMENT 'Текст элемента меню',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT 'Уровень в дереве',
  `subid` int(11) NOT NULL DEFAULT '0' COMMENT 'Ссылка на родителя (ключ)',
  `locale` char(20) NOT NULL COMMENT 'ID языка',
  `url` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `subid` (`subid`),
  KEY `locale` (`locale`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` (`id`, `name`, `level`, `subid`, `locale`, `url`) VALUES 
  (1, 'Система управления', 0, 0, 'ru_RU', ''),
  (2, 'Меню администраторов', 1, 1, 'ru_RU', '/adm/tree/admin_menu'),
  (3, 'Навигация/структура сайта', 0, 0, 'ru_RU', ''),
  (4, 'Резервир./восстановл. базы', 1, 1, 'ru_RU', '/adm/backuprestore'),
  (5, 'Меню сайта', 1, 3, 'ru_RU', '/adm/tree/menu'),
  (6, 'Интерфейсы', 1, 1, 'ru_RU', ''),
  (7, 'Линейные интерфейсы', 2, 6, 'ru_RU', '/adm/constructorline'),
  (8, 'Древовидные интерфесы', 2, 6, 'ru_RU', '/adm/constructortree'),
  (9, 'Генератор Entity', 1, 1, 'ru_RU', '/adm/entity'),
  (10, 'Пользователи и группы', 1, 1, 'ru_RU', ''),
  (11, 'Системные группы польз.', 2, 10, 'ru_RU', '/adm/line/users_group'),
  (12, 'Группы пользователей', 2, 10, 'ru_RU', '/adm/line/users_group_nonsystem'),
  (13, 'Пользователи', 2, 10, 'ru_RU', '/adm/line/users');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_tables`
--

DROP TABLE IF EXISTS `design_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `interface_name` varchar(127) NOT NULL DEFAULT '' COMMENT 'Имя интерфеса',
  `table_name` varchar(255) NOT NULL DEFAULT '',
  `table_type` int(11) NOT NULL DEFAULT '0' COMMENT 'тип:0-line,1-tree,2-form_dialog,3-',
  `col_name` varchar(255) NOT NULL DEFAULT '',
  `caption_style` varchar(255) NOT NULL DEFAULT '',
  `row_type` int(11) NOT NULL DEFAULT '0',
  `col_por` int(11) NOT NULL DEFAULT '0',
  `pole_spisok_sql` text NOT NULL,
  `pole_global_const` varchar(255) NOT NULL DEFAULT '',
  `pole_prop` varchar(255) NOT NULL DEFAULT '',
  `pole_type` varchar(255) NOT NULL DEFAULT '',
  `pole_style` varchar(255) NOT NULL DEFAULT '',
  `pole_name` varchar(255) NOT NULL DEFAULT '',
  `default_sql` text NOT NULL,
  `functions_befo` varchar(50) NOT NULL DEFAULT '',
  `functions_after` varchar(50) NOT NULL DEFAULT '',
  `functions_befo_out` varchar(50) NOT NULL DEFAULT '',
  `functions_befo_del` varchar(50) NOT NULL DEFAULT '',
  `properties` text NOT NULL,
  `value` varbinary(255) NOT NULL DEFAULT '',
  `validator` varchar(255) NOT NULL DEFAULT '',
  `sort_item_flag` int(11) NOT NULL DEFAULT '0' COMMENT 'флаг сортировки поля 0-нет,1 да',
  `col_function_array` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `table_name` (`table_name`),
  KEY `interface_name` (`interface_name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_tables`
--

LOCK TABLES `design_tables` WRITE;
/*!40000 ALTER TABLE `design_tables` DISABLE KEYS */;
INSERT INTO `design_tables` VALUES (1,'admin_menu','admin_menu',1,'1,1,1,1,1,0','',0,0,' locale=\'$pole_dop0\'  order by id','','id,subid,level','','','','','','','','','','','',0,''),(2,'admin_menu','admin_menu',1,'','',1,0,'create temporary table sp1 (id char(11), name char(50)) ENGINE=MEMORY; insert into sp1 (id,name) values (\"ru_RU\",\"ru_RU\"); select * from sp1','','onChange=\"this.form.submit()\"','4','','','select id from sp1','','','','','a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}','','',0,''),(5,'admin_menu','admin_menu',1,'locale','',2,0,'','','','0','','pole_dop0','','','','','','','','N;',0,''),(6,'admin_menu','admin_menu',1,'name','',2,1,'','','size=\"50\"','2','','name','','','','','','N;','','N;',0,''),(7,'admin_menu','admin_menu',1,'url','',2,2,'','','','9','','url','','','','\\Admin\\Lib\\Func\\AdminMenu','','a:1:{i:0;s:1:\"1\";}','','N;',0,'');
/*!40000 ALTER TABLE `design_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `design_tables_text_interfase`
--

DROP TABLE IF EXISTS `design_tables_text_interfase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `design_tables_text_interfase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` char(10) NOT NULL,
  `table_type` tinyint(1) NOT NULL DEFAULT '0',
  `interface_name` varchar(255) NOT NULL DEFAULT '',
  `item_name` varchar(255) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `language` (`language`),
  KEY `interface_name` (`interface_name`),
  KEY `item_name` (`item_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `design_tables_text_interfase`
--

LOCK TABLES `design_tables_text_interfase` WRITE;
/*!40000 ALTER TABLE `design_tables_text_interfase` DISABLE KEYS */;
INSERT INTO `design_tables_text_interfase` VALUES (1,'ru_RU',1,'admin_menu','caption_dop_',''),(2,'ru_RU',1,'admin_menu','caption_dop_1','Роль'),(3,'ru_RU',1,'admin_menu','caption0','Меню администраторов'),(4,'ru_RU',1,'admin_menu','caption_col_roles',''),(5,'ru_RU',1,'admin_menu','caption_col_language',''),(6,'ru_RU',1,'admin_menu','caption_col_modul','Модуль'),(7,'ru_RU',1,'admin_menu','coment0','Страница редактирования панели администрирования сайта. Вы можете менять дерево меню по вашему усмотрению. После создания пункта меню, необходимо выбрать модуль, который будет вызван, когда администратор щелкнет по данному пункту меню. Если Выбранный модуль предоставляет автоматические параметры (список), то необходимо выбрать из предложенного списка нужный вариант. Этот выбранный пункт передается в константе param_a. Список вариантов автомараметров определяется инструкцией \"автовыборка параметров из модуля SQL иструкция\" в модуле управления модулями системы.'),(8,'ru_RU',1,'admin_menu','caption_dop_0','Локаль:'),(9,'ru_RU',1,'admin_menu','caption_col_url','URL');
/*!40000 ALTER TABLE `design_tables_text_interfase` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-10-12  9:23:50
