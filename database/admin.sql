-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: laravel-shop
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu18.04.1

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
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,0,1,'首页','fa-bar-chart','/',NULL,'2019-01-18 10:13:01'),(2,0,6,'系统管理','fa-tasks',NULL,NULL,'2019-02-01 06:41:58'),(3,2,7,'管理员','fa-users','auth/users',NULL,'2019-02-01 06:41:58'),(4,2,8,'角色','fa-user','auth/roles',NULL,'2019-02-01 06:41:58'),(5,2,9,'权限','fa-ban','auth/permissions',NULL,'2019-02-01 06:41:58'),(6,2,10,'菜单','fa-bars','auth/menu',NULL,'2019-02-01 06:41:58'),(7,2,11,'日志','fa-history','auth/logs',NULL,'2019-02-01 06:41:58'),(8,0,2,'用户管理','fa-users','/users','2019-01-18 10:28:43','2019-01-18 10:28:54'),(9,0,3,'商品管理','fa-cubes','/products','2019-01-21 01:37:59','2019-01-21 01:39:12'),(10,0,4,'订单管理','fa-rmb','/orders','2019-01-30 11:29:24','2019-01-30 11:29:44'),(11,0,5,'优惠券管理','fa-tags','/coupon_codes','2019-02-01 06:41:23','2019-02-01 06:41:58'),(12,9,0,'众筹商品','fa-cubes','/crowdfunding_products','2019-02-18 11:04:34','2019-02-18 11:04:34'),(13,9,0,'普通商品','fa-linux','/products','2019-02-18 11:05:39','2019-02-18 11:05:39'),(14,9,0,'秒杀产品','fa-bolt','/seckill_products','2019-03-27 10:41:39','2019-03-27 10:42:26');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` VALUES (1,'All permission','*','','*',NULL,NULL),(2,'Dashboard','dashboard','GET','/',NULL,NULL),(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),(5,'Auth management','auth.management','','/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs',NULL,NULL),(6,'用户管理','users','','/users*','2019-01-18 10:48:11','2019-01-18 10:48:11'),(7,'商品管理','products','','/products*','2019-02-02 06:03:14','2019-02-02 06:03:14'),(8,'订单管理','orders','','/orders*','2019-02-02 06:03:59','2019-02-02 06:03:59'),(9,'优惠券管理','coupon_code','','/coupon_codes*','2019-02-02 06:05:13','2019-02-02 06:05:13');
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_menu`
--

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;
INSERT INTO `admin_role_menu` VALUES (1,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_permissions`
--

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;
INSERT INTO `admin_role_permissions` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL),(2,3,NULL,NULL),(2,4,NULL,NULL),(2,6,NULL,NULL),(2,7,NULL,NULL),(2,8,NULL,NULL),(2,9,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_users`
--

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;
INSERT INTO `admin_role_users` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,'Administrator','administrator','2019-01-18 09:52:09','2019-01-18 09:52:09'),(2,'运营','operator','2019-01-18 10:50:18','2019-01-18 10:50:18');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_user_permissions`
--

LOCK TABLES `admin_user_permissions` WRITE;
/*!40000 ALTER TABLE `admin_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$au/MZvkOijXj21g0jB7e9OCJrqaGzsSPRJ9ldGIxjOnckvey6HmGO','Administrator',NULL,'YBhyVCcUMfpL6IpXhdPxzAWULMuLH8Hgx6gY5SmUxRWv654JEY9VMJLsqnH7','2019-01-18 09:52:09','2019-01-18 09:52:09'),(2,'operator','$2y$10$Sh1WJtqPNxixMQdKeCLxSua5zXiai6hML33cF7B1jGW09VSVw9MR2','运营','images/f141783776e3779b36fb5c6e20bedf7d.jpeg',NULL,'2019-01-18 10:52:14','2019-01-18 10:52:14');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-03-27 10:57:25
