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
  `roles` int(11) NOT NULL DEFAULT '0' COMMENT 'ID роли админа',
  `url` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `subid` (`subid`),
  KEY `roles` (`roles`),
  KEY `locale` (`locale`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,'Система управления',0,0,'ru_RU',1,''),(2,'Меню администраторов',1,1,'ru_RU',1,'/adm/tree/admin_menu'),(3,'Навигация/структура сайта',0,0,'ru_RU',1,''),(4,'Резервир./восстановл. базы',1,1,'ru_RU',1,'/adm/backuprestore'),(5,'Меню сайта',1,3,'ru_RU',1,'/adm/tree/menu'),(6,'Интерфейсы',1,1,'ru_RU',1,''),(7,'Линейные интерфейсы',2,6,'ru_RU',1,'/adm/constructorline'),(8,'Древовидные интерфесы',2,6,'ru_RU',1,'/adm/constructortree'),(9,'Генератор Entity',1,1,'ru_RU',1,'/adm/entity');
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
INSERT INTO `design_tables` VALUES (1,'admin_menu','admin_menu',1,'1,1,1,1,1,0','',0,0,' locale=\'$pole_dop0\' and roles=\'$pole_dop1\' order by id','','id,subid,level','','','','','','','','','','','',0,''),(2,'admin_menu','admin_menu',1,'','',1,0,'create temporary table sp1 (id char(11), name char(50)) ENGINE=MEMORY; insert into sp1 (id,name) values (\"ru_RU\",\"ru_RU\"); select * from sp1','','onChange=\"this.form.submit()\"','4','','','select id from sp1','','','','','a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}','','',0,''),(3,'admin_menu','admin_menu',1,'','',1,0,'select id,name from role order by name','','','4','','','select id,name from role order by name limit 1','','','','','a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}','','',0,''),(4,'admin_menu','admin_menu',1,'roles','',2,0,'','','','0','','pole_dop1','','','','','','','','N;',0,''),(5,'admin_menu','admin_menu',1,'locale','',2,0,'','','','0','','pole_dop0','','','','','','','','N;',0,''),(6,'admin_menu','admin_menu',1,'name','',2,1,'','','size=\"50\"','2','','name','','','','','','N;','','N;',0,''),(7,'admin_menu','admin_menu',1,'url','',2,2,'','','','9','','url','','','','\\Admin\\Lib\\Func\\AdminMenu','','a:1:{i:0;s:1:\"1\";}','','N;',0,'');
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

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(127) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='доступы';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission`
--

LOCK TABLES `permission` WRITE;
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
INSERT INTO `permission` VALUES (1,'admin.login','Вход в админку','2017-09-22 13:15:49'),(3,'admin.manage','Управление администраторами','2017-09-23 07:02:03'),(4,'permission.manage','Управление привелегиями','2017-09-23 07:02:03'),(5,'role.manage','Управление ролями','2017-09-23 07:02:03'),(6,'profile.any.view','Управление любыми профилями','2017-09-23 07:02:03'),(7,'profile.own.view','Управление своим профилем','2017-09-23 07:02:03');
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(127) DEFAULT NULL,
  `description` char(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='роли';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'admin','Администратор','2017-09-22 13:14:39'),(2,'guest','Гостевой вход','2017-09-22 13:16:53');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role2permission`
--

DROP TABLE IF EXISTS `role2permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role2permission` (
  `role` int(11) NOT NULL,
  `permission` int(11) NOT NULL,
  PRIMARY KEY (`role`,`permission`),
  KEY `permission` (`permission`),
  KEY `role` (`role`),
  CONSTRAINT `role2permission_fk` FOREIGN KEY (`permission`) REFERENCES `permission` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role2permission_fk1` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='роль-доступ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role2permission`
--

LOCK TABLES `role2permission` WRITE;
/*!40000 ALTER TABLE `role2permission` DISABLE KEYS */;
INSERT INTO `role2permission` VALUES (1,1);
/*!40000 ALTER TABLE `role2permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_tree`
--

DROP TABLE IF EXISTS `role_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_tree` (
  `role` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  PRIMARY KEY (`role`,`parent`),
  KEY `role` (`role`),
  KEY `parent` (`parent`),
  CONSTRAINT `role_tree_fk` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_tree_fk1` FOREIGN KEY (`parent`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='зависимости ролей';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_tree`
--

LOCK TABLES `role_tree` WRITE;
/*!40000 ALTER TABLE `role_tree` DISABLE KEYS */;
INSERT INTO `role_tree` VALUES (2,1);
/*!40000 ALTER TABLE `role_tree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` char(127) NOT NULL COMMENT 'логин, можно мыло',
  `status` int(11) NOT NULL COMMENT 'состояние юзера 1-нормальный',
  `phone` char(127) DEFAULT NULL,
  `password` char(127) NOT NULL COMMENT 'текущий пароль',
  `name` char(127) DEFAULT NULL COMMENT 'псевдоним',
  `full_name` char(255) DEFAULT NULL COMMENT 'ФИО',
  `avatar` char(70) DEFAULT NULL COMMENT 'аватар',
  `my_info` text COMMENT 'обо мне',
  `temp_password` char(127) DEFAULT NULL COMMENT 'временный пароль для восстановления',
  `temp_date` datetime DEFAULT NULL COMMENT 'дата годности временного пароля для активации',
  `confirm_hash` char(50) DEFAULT NULL COMMENT 'строка для подтверждения регистрации',
  `date_registration` datetime DEFAULT NULL COMMENT 'дата регистрации',
  `date_last_login` datetime DEFAULT NULL COMMENT 'дата входа',
  `sex` int(11) DEFAULT NULL COMMENT '1-муж',
  `role` int(11) DEFAULT NULL COMMENT 'ID роли',
  PRIMARY KEY (`id`),
  KEY `temp_date` (`temp_date`),
  KEY `confirm_hash` (`confirm_hash`),
  KEY `status` (`status`),
  KEY `date_registration` (`date_registration`),
  KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='регистрированные юзеры';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'root',1,NULL,'$2y$10$TryLBUTSX7lZdSD8NBUFMOu8.vzvfoqaFlHgsv2C460EgxGkkYff6',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users2role`
--

DROP TABLE IF EXISTS `users2role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users2role` (
  `users` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  PRIMARY KEY (`users`,`role`),
  KEY `users` (`users`),
  KEY `role` (`role`),
  CONSTRAINT `users2role_fk` FOREIGN KEY (`users`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users2role_fk1` FOREIGN KEY (`role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='юзер-роль';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users2role`
--

LOCK TABLES `users2role` WRITE;
/*!40000 ALTER TABLE `users2role` DISABLE KEYS */;
INSERT INTO `users2role` VALUES (1,1);
/*!40000 ALTER TABLE `users2role` ENABLE KEYS */;
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
