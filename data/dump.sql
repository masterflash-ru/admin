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
  (13, 'Пользователи', 2, 10, 'ru_RU', '/adm/line/users'),
  (14, 'Доступы', 1, 1, 'ru_RU', '/adm/line/permissions');
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
  `caption_style` varchar(255)  DEFAULT '',
  `row_type` int(11)  DEFAULT '0',
  `col_por` int(11)  DEFAULT '0',
  `pole_spisok_sql` text NOT NULL,
  `pole_global_const` varchar(255)  DEFAULT '',
  `pole_prop` varchar(255)  DEFAULT '',
  `pole_type` varchar(255)  DEFAULT '',
  `pole_style` varchar(255) DEFAULT '',
  `pole_name` varchar(255)  DEFAULT '',
  `default_sql` text,
  `functions_befo` varchar(50) DEFAULT '',
  `functions_after` varchar(50) DEFAULT '',
  `functions_befo_out` varchar(50) DEFAULT '',
  `functions_befo_del` varchar(50) DEFAULT '',
  `properties` text,
  `value` varbinary(255) DEFAULT '',
  `validator` varchar(255) DEFAULT '',
  `sort_item_flag` int(11) DEFAULT '0' COMMENT 'флаг сортировки поля 0-нет,1 да',
  `col_function_array` text,
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
INSERT INTO `design_tables` (`interface_name`, `table_name`, `table_type`, `col_name`, `caption_style`, `row_type`, `col_por`, `pole_spisok_sql`, `pole_global_const`, `pole_prop`, `pole_type`, `pole_style`, `pole_name`, `default_sql`, `functions_befo`, `functions_after`, `functions_befo_out`, `functions_befo_del`, `properties`, `value`, `validator`, `sort_item_flag`, `col_function_array`) VALUES 
  ('admin_menu', 'admin_menu', 1, '1,1,1,1,1,0', 'a:3:{s:10:\"owner_user\";s:1:\"1\";s:11:\"owner_group\";s:1:\"1\";s:10:\"permission\";i:416;}', 0, 0, ' locale=''$pole_dop0''  order by id', '', 'id,subid,level', '', '', '', '', '', '', '', '', '', '', '', 0, NULL),
  ('admin_menu', 'admin_menu', 1, '', '', 1, 0, 'create temporary table sp1 (id char(11), name char(50)) ENGINE=MEMORY; insert into sp1 (id,name) values (\"ru_RU\",\"ru_RU\"); select * from sp1', '', 'onChange=\"this.form.submit()\"', '4', '', '', 'select id from sp1', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', '', 0, ''),
  ('admin_menu', 'admin_menu', 1, 'locale', '', 2, 0, '', '', '', '0', '', 'pole_dop0', '', '', '', '', '', '', '', 'N;', 0, ''),
  ('admin_menu', 'admin_menu', 1, 'name', '', 2, 1, '', '', 'size=\"50\"', '2', '', 'name', '', '', '', '', '', 'N;', '', 'N;', 0, ''),
  ('admin_menu', 'admin_menu', 1, 'url', '', 2, 2, '', '', '', '9', '', 'url', '', '', '', '\\Admin\\Lib\\Func\\AdminMenu', '', 'a:1:{i:0;s:1:\"1\";}', '', 'N;', 0, ''),
  ('permissions', 'permissions', 0, 'name', '', 3, NULL, '', NULL, '', '2', NULL, 'name', NULL, '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', NULL, 'N;'),
  ('permissions', 'permissions', 0, 'object', '', 2, 4, '', NULL, 'size=60', '2', NULL, 'object', NULL, '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', NULL, 'N;'),
  ('permissions', 'permissions', 0, 'name', '', 2, 1, '', NULL, '', '2', NULL, 'name', NULL, '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', NULL, 'N;'),
  ('permissions', 'permissions', 0, 'permissions', 'a:3:{s:10:\"owner_user\";s:1:\"1\";s:11:\"owner_group\";s:1:\"1\";s:10:\"permission\";i:416;}', 0, 0, 'select * from permissions order by name', '', '1,1,0,0', 'permissions', '', 'id', 'delete from permissions where id=$id', '', '', '', '', '', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, 'permissions', 1, NULL),
  ('permissions', 'permissions', 0, 'object', '', 3, NULL, '', NULL, 'size=60', '2', NULL, 'object', NULL, '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', NULL, 'N;'),
  ('permissions', 'permissions', 0, 'permissions', '', 2, 5, '', NULL, '', '57', NULL, 'permissions', NULL, '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', NULL, 'N;'),
  ('permissions', 'permissions', 0, 'permissions', '', 3, NULL, '', NULL, '', '57', NULL, 'permissions', NULL, '', '', '', '', 'N;', '', 'N;', NULL, 'N;'),
  ('permissions', 'permissions', 0, '1', '', 2, 18, '', NULL, '', '19', NULL, 'save', NULL, '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:16:\"Добавить\";}', '', 'N;', NULL, 'N;'),
  ('permissions', 'permissions', 0, '1', '', 3, NULL, '', NULL, ',', '17', NULL, 'save,del', NULL, '', '', '', '', 'a:4:{i:0;s:1:\"1\";i:1;s:1:\"0\";i:2;s:33:\"Сохранить,Удалить\";i:3;s:1:\"0\";}', '', 'N;', NULL, 'N;'),
  ('users_edit_base', 'users', 0, 'date_registration', '', 3, 0, '', '', ',', '34', '', 'date_registration', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'date_last_login', '', 2, 8, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'date_registration', '', 2, 7, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'status', '', 3, 0, '', '', '', '4', '', 'status', '', '', '', '\\Mf\\Users\\Lib\\Func\\GetStatusList', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'status', '', 2, 6, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'full_name', '', 3, 0, '', '', 'size=60', '2', '', 'full_name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'full_name', '', 2, 4, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'login', '', 2, 1, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'login', '', 3, 0, '', '', 'size=60', '2', '', 'login', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'name', '', 2, 3, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'name', '', 3, 0, '', '', 'size=60', '2', '', 'name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'gr', 'a:3:{s:10:\"owner_user\";s:1:\"1\";s:11:\"owner_group\";s:1:\"1\";s:10:\"permission\";i:416;}', 0, 1, 'select users.*, (select group_concat(users_group) from users2group where users=users.id) as gr from users where id=$get_interface_input', '', '0,0,0,0', 'name', '', 'id', '', '', '', '', '', 'Mf\\Users\\Lib\\Func\\SaveUserDetal', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, NULL),
  ('users', 'users', 0, 'status', '', 3, 0, '', '', '', '4', '', '', '', '', '', '\\Mf\\Users\\Lib\\Func\\GetStatusList', '', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'status', '', 2, 9, '', NULL, '', '4', NULL, 'status', NULL, '', '', '\\Mf\\Users\\Lib\\Func\\GetStatusList', '', 'a:3:{i:0;s:1:\"0\";i:1;s:1:\"0\";i:2;s:1:\"0\";}', '', 'N;', NULL, 'N;'),
  ('users', 'users', 0, '1', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, '1', '', 2, 17, '', '', '', '19', '', 'save', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:16:\"Добавить\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'date_registration', '', 2, 6, '', '', ',', '34', '', 'date_registration', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'date_registration', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'date_last_login', '', 2, 7, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'date_last_login', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'full_name', '', 2, 5, '', '', '', '2', '', 'full_name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'full_name', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'name', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'name', '', 2, 4, '', '', '', '2', '', 'name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'login', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, 'login', '', 2, 3, '', NULL, '', '2', NULL, 'login', NULL, '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', NULL, 'N;'),
  ('users', 'users', 0, 'id', '', 3, 0, '', '', '', '56', '', '', '', '', '', '', '', 'a:5:{i:0;s:3:\"0,0\";i:1;s:30:\"users_edit_base,users_password\";i:2;s:6:\"button\";i:3;s:3:\"500\";i:4;s:3:\"400\";}', 0xD091D0B0D0B7D0BED0B2D18BD0B920D0BFD180D0BED184D0B8D0BBD18C20D0BFD0BED0BBD18CD0B7D0BED0B2D0B0D182D0B5D0BBD18F2CD098D0B7D0BCD0B5D0BDD0B8D182D18C20D0BFD0B0D180D0BED0BBD18C, 'N;', 0, 'N;'),
  ('users', 'users', 0, 'id', '', 2, 2, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users', 'users', 0, '', '', 1, 0, '', '', 'onChange=this.form.submit(),', '34', '', '', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', '', 0, NULL),
  ('users', 'users', 0, '', '', 1, 0, '', '', 'onChange=this.form.submit(),', '34', '', '', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', '', 0, NULL),
  ('users', 'users', 0, '', '', 1, 0, '', '', 'onChange=this.form.submit()', '4', '', '', '', '', '', '\\Mf\\Users\\Lib\\Func\\GetStatusList', '', 'a:3:{i:0;s:1:\"0\";i:1;s:1:\"0\";i:2;s:1:\"0\";}', '', '', 0, NULL),
  ('users', 'users', 0, '', '', 1, 0, 'select id,name from users_group order by name', '', 'onChange=this.form.submit()', '4', '', '', 'select id,name from users_group order by id', '', '', '', '', 'a:3:{i:0;s:1:\"0\";i:1;s:1:\"0\";i:2;s:1:\"0\";}', '', '', 0, NULL),
  ('users', 'users', 0, '', '', 1, 0, '', '', '', '47', '', '', '', '', '', '', '', 'a:7:{i:0;s:1:\"0\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:0:\"\";i:4;s:0:\"\";i:5;s:0:\"\";i:6;s:0:\"\";}', '', '', 0, NULL),
  ('users', 'users', 0, '', 'a:3:{s:10:\"owner_user\";s:1:\"1\";s:11:\"owner_group\";s:1:\"1\";s:10:\"permission\";i:416;}', 0, 0, 'select * from users where status=\"$pole_dop2\" and \r\n( (date_registration>=\"$pole_dop0 00:00:00\" or \"$pole_dop0\"=\"\") and  (date_registration<=\"$pole_dop1 23:59:59\" or \"$pole_dop1\"=\"\") or isnull(date_registration)) and\r\n(\"$pole_dop4\">0 and login like concat(char(\"$pole_dop4\"),\"%\") or \"$pole_dop4\"=0) and\r\n (id in(select users from users2group where users_group=''$pole_dop3'') or id not in(select users from users2group)  )', '50', '0,0,0,0', '', '', 'id', 'delete from users where id=$id and id>=10', '', '', '', '', 'Mf\\Users\\Lib\\Func\\SaveUser', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, NULL),
  ('users_group_nonsystem', 'users_group', 0, 'id', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'id', '', 2, 1, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'parent_group', '', 2, 5, 'select id,name from users_group order by id', '', '', '55', '', 'parent_group', '', '', '', '', '', 'a:3:{i:0;s:3:\"600\";i:1;s:0:\"\";i:2;s:1:\"2\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'parent_group', '', 3, 0, 'select id,name from users_group order by id', '', '', '55', '', 'parent_group', '', '', '', '', '', 'a:3:{i:0;s:3:\"600\";i:1;s:0:\"\";i:2;s:1:\"2\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, '1', '', 3, 0, '', '', ',', '17', '', 'save,del', '', '', '', '', '', 'a:4:{i:0;s:1:\"1\";i:1;s:1:\"0\";i:2;s:33:\"Сохранить,Удалить\";i:3;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, '1', '', 2, 8, '', '', '', '19', '', 'save', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:16:\"Добавить\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'description', '', 3, 0, '', '', 'size=55', '2', '', 'description', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'description', '', 2, 4, '', '', 'size=55', '2', '', 'description', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'name', '', 3, 0, '', '', 'size=55', '2', '', 'name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'name', '', 2, 3, '', '', 'size=55', '2', '', 'name', '', '', '', '', '', 'a:1:{i:0;s:4:\"Text\";}', '', 'N;', 0, 'N;'),
  ('users_group_nonsystem', 'users_group', 0, 'parent_group', 'a:3:{s:10:\"owner_user\";s:1:\"1\";s:11:\"owner_group\";s:1:\"1\";s:10:\"permission\";i:416;}', 0, 0, 'select users_group.*, (select group_concat(parent_id) from users_group_tree where users_group.id=users_group_tree.id) as parent_group from users_group where id>=10 order by name', '', '0,0,0,0', 'parent_group', '', 'id', 'delete from users_group where id=$id and id>=10', '', '', '', '', 'Mf\\Users\\Lib\\Func\\SaveGroupTree', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, NULL),
  ('users_group', 'users_group', 0, 'id', '', 3, 0, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'description', '', 2, 3, '', '', '', '1', '', 'description', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'description', '', 3, 0, '', '', '', '1', '', 'description', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'id', '', 2, 1, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'name', '', 3, 0, '', '', '', '1', '', 'name', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, 'name', '', 2, 3, '', '', 'size=60', '1', '', 'name', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_group', 'users_group', 0, '', 'a:3:{s:10:\"owner_user\";s:1:\"1\";s:11:\"owner_group\";s:1:\"1\";s:10:\"permission\";i:416;}', 0, 0, 'select * from users_group where id<10 order by id', '', '0,0,0,0', '', '', 'id', '', '', '', '', '', '', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, NULL),
  ('users_edit_base', 'users', 0, 'date_last_login', '', 3, 0, '', '', ',', '34', '', 'date_last_login', '', '', '', '', '', 'a:5:{i:0;s:0:\"\";i:1;s:1:\"0\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:4;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, '1', '', 2, 17, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, '1', '', 3, 0, '', '', '', '19', '', 'save', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:18:\"Сохранить\";}', '', 'N;', 0, 'N;'),
  ('users_password', 'users', 0, '', 'a:3:{s:10:\"owner_user\";s:1:\"1\";s:11:\"owner_group\";s:1:\"1\";s:10:\"permission\";i:384;}', 0, 0, 'select * from users where id=$get_interface_input', '', '0,0,0,0', '', '', 'id', '', '', '', '', '', '', 0x613A323A7B733A32343A22666F726D5F656C656D656E74735F6E65775F7265636F7264223B733A313A2230223B733A32343A22666F726D5F656C656D656E74735F6A6D705F7265636F7264223B733A313A2230223B7D, '', 0, NULL),
  ('users_password', 'users', 0, 'password', '', 2, 2, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_password', 'users', 0, 'password', '', 3, 0, '', '', ',', '13', '', 'password', '', '', '', '', '', 'N;', '', 'N;', 0, 'N;'),
  ('users_password', 'users', 0, '1', '', 2, 19, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_password', 'users', 0, '1', '', 3, 0, '', '', '', '19', '', 'save', '', '', '', '', '', 'a:2:{i:0;s:1:\"1\";i:1;s:18:\"Сохранить\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'gr', '', 2, 10, '', '', '', '1', '', '', '', '', '', '', '', 'a:2:{i:0;s:1:\"0\";i:1;s:1:\"0\";}', '', 'N;', 0, 'N;'),
  ('users_edit_base', 'users', 0, 'gr', '', 3, 0, 'select id,name from users_group order by name', '', '', '55', '', 'gr', '', '', '', '', '', 'a:3:{i:0;s:0:\"\";i:1;s:0:\"\";i:2;s:0:\"\";}', '', 'N;', 0, 'N;');

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

INSERT INTO `design_tables_text_interfase` (`language`, `table_type`, `interface_name`, `item_name`, `text`) VALUES 
  ('ru_RU', 1, 'admin_menu', 'caption_dop_', ''),
  ('ru_RU', 1, 'admin_menu', 'caption_dop_1', 'Роль'),
  ('ru_RU', 1, 'admin_menu', 'caption_col_roles', ''),
  ('ru_RU', 1, 'admin_menu', 'caption_col_language', ''),
  ('ru_RU', 1, 'admin_menu', 'caption_col_modul', 'Модуль'),
  ('ru_RU', 1, 'admin_menu', 'caption_dop_0', 'Локаль:'),
  ('ru_RU', 1, 'admin_menu', 'caption_col_url', 'URL'),
  ('ru_RU', 1, 'permissions', 'coment0', 'Доступ представлен в виде дерева, root - корневой уровень доступа, владелец и группа принадлежит супер пользователю root<br>\r\nУровень доступа описывается аналогично UNIX, и является восьмеричным числом, в виде строки можно представить как rwxr--r-- '),
  ('ru_RU', 1, 'permissions', 'caption0', '<h2>Описание доступов к объектам системы</h2>'),
  ('ru_RU', 1, 'permissions', 'caption_col_name', 'Описание: '),
  ('ru_RU', 1, 'permissions', 'caption_col_object', 'OBJ: '),
  ('ru_RU', 0, 'permissions', 'caption_col_name', 'Имя'),
  ('ru_RU', 0, 'permissions', 'caption_col_object', 'Объект'),
  ('ru_RU', 0, 'permissions', 'caption_col_permissions', 'доступ'),
  ('ru_RU', 0, 'permissions', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users_group', 'caption_col_name', 'Название'),
  ('ru_RU', 0, 'users_group', 'caption_col_id', 'ID'),
  ('ru_RU', 0, 'users_group', 'caption_col_description', 'Описание'),
  ('ru_RU', 0, 'users_group', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_name', 'Имя группы'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_description', 'Описание'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_id', 'ID группы'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption_col_parent_group', 'Является членом'),
  ('ru_RU', 0, 'users', 'caption_dop_0', 'Дата регистрации (фильтр, начало)'),
  ('ru_RU', 0, 'users', 'caption_dop_1', 'Дата регистрации (фильтр, конец)'),
  ('ru_RU', 0, 'users', 'caption_dop_2', 'Статус'),
  ('ru_RU', 0, 'users', 'caption_col_id', 'Подробно'),
  ('ru_RU', 0, 'users', 'caption_col_login', 'Логин'),
  ('ru_RU', 0, 'users', 'caption_col_name', 'Имя'),
  ('ru_RU', 0, 'users', 'caption_col_full_name', 'Полное имя'),
  ('ru_RU', 0, 'users', 'caption_col_date_registration', 'Дата регистрации'),
  ('ru_RU', 0, 'users', 'caption_col_date_last_login', 'Дата посл.входа'),
  ('ru_RU', 0, 'users', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users', 'caption_dop_4', 'Логин начинается на '),
  ('ru_RU', 0, 'users', 'caption_col_status', 'Статус'),
  ('ru_RU', 0, 'users', 'caption_dop_3', 'Группа'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_gr', 'Член групп'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Редактировать'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Редактировать'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_login', 'Логин'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_name', 'Имя'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_full_name', 'Полное имя'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_status', 'Статус'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_date_registration', 'Дата регистрации'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_date_last_login', 'дата последнего входа'),
  ('ru_RU', 0, 'users_edit_base', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users_password', 'caption_col_password', 'Новый пароль'),
  ('ru_RU', 0, 'users_password', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'users', 'values_message_id3', 'Базовый профиль пользователя,Изменить пароль'),
  ('ru_RU', 0, 'statpage', 'caption_col_name', 'Имя элемента'),
  ('ru_RU', 0, 'statpage', 'caption_col_sysname', 'Системное имя'),
  ('ru_RU', 0, 'statpage', 'caption_dop_0', 'Язык сайта:'),
  ('ru_RU', 0, 'statpage', 'button2', 'Сохранить,Удалить'),
  ('ru_RU', 0, 'statpage', 'button1', 'Добавить'),
  ('ru_RU', 0, 'statpage', 'caption_col_title', 'TITLE'),
  ('ru_RU', 0, 'statpage', 'caption_col_keywords', 'KEYWORDS'),
  ('ru_RU', 0, 'statpage', 'caption_col_description', 'DESCRIPTION'),
  ('ru_RU', 0, 'statpage', 'caption_col_tpl', 'Шаблон'),
  ('ru_RU', 0, 'statpage', 'caption_col_1', 'Операция'),
  ('ru_RU', 0, 'statpage', 'caption0', 'ПРОСТО СТРАНИЦЫ'),
  ('ru_RU', 0, 'statpage', 'caption_col_url', 'URL страницы, /page/'),
  ('ru_RU', 0, 'statpage', 'caption_col_page_type', 'Состояние'),
  ('ru_RU', 0, 'statpage', 'caption_col_content', 'Контент'),
  ('ru_RU', 0, 'statpage', 'caption_col_seo_options', 'SEO опции'),
  ('ru_RU', 0, 'statpage', 'caption_col_layout', 'Макет'),
  ('ru_RU', 1, 'admin_menu', 'coment0', 'Страница редактирования панели администрирования сайта. Вы можете менять дерево меню по вашему усмотрению. После создания пункта меню, необходимо выбрать модуль, который будет вызван, когда администратор щелкнет по данному пункту меню. Если Выбранный модуль предоставляет автоматические параметры (список), то необходимо выбрать из предложенного списка нужный вариант. Этот выбранный пункт передается в константе param_a. Список вариантов автомараметров определяется инструкцией \"автовыборка параметров из модуля SQL иструкция\" в модуле управления модулями системы.'),
  ('ru_RU', 1, 'admin_menu', 'caption0', 'Меню администраторов'),
  ('ru_RU', 0, 'permissions', 'coment0', 'Формат данных:<br>\r\nВладелец:Группа код_доступа в восьмеричной системе аналогично UNIX'),
  ('ru_RU', 0, 'permissions', 'caption0', 'Таблица доступов'),
  ('ru_RU', 0, 'users', 'caption0', 'Редактирование пользователей'),
  ('ru_RU', 0, 'users_edit_base', 'caption0', 'Базовая информация о пользователе'),
  ('ru_RU', 0, 'users_group', 'coment0', '<br><b>Редактировать можно только разработчику</b>'),
  ('ru_RU', 0, 'users_group', 'caption0', 'Системные группы пользователей'),
  ('ru_RU', 0, 'users_group_nonsystem', 'coment0', 'Не системные группы пользователей, которые можно редактировать'),
  ('ru_RU', 0, 'users_group_nonsystem', 'caption0', 'Группы сайта'),
  ('ru_RU', 0, 'users_password', 'caption0', 'Смена пароля пользователя');
/*!40000 ALTER TABLE `design_tables_text_interfase` ENABLE KEYS */;
UNLOCK TABLES;


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
