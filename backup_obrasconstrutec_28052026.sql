-- MySQL dump 10.13  Distrib 5.7.44-48, for Linux (x86_64)
--
-- Host: localhost    Database: gdois634_diario_obras_construtec
-- ------------------------------------------------------
-- Server version	5.7.44-48

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
/*!50717 SELECT COUNT(*) INTO @rocksdb_has_p_s_session_variables FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'performance_schema' AND TABLE_NAME = 'session_variables' */;
/*!50717 SET @rocksdb_get_is_supported = IF (@rocksdb_has_p_s_session_variables, 'SELECT COUNT(*) INTO @rocksdb_is_supported FROM performance_schema.session_variables WHERE VARIABLE_NAME=\'rocksdb_bulk_load\'', 'SELECT 0') */;
/*!50717 PREPARE s FROM @rocksdb_get_is_supported */;
/*!50717 EXECUTE s */;
/*!50717 DEALLOCATE PREPARE s */;
/*!50717 SET @rocksdb_enable_bulk_load = IF (@rocksdb_is_supported, 'SET SESSION rocksdb_bulk_load = 1', 'SET @rocksdb_dummy_bulk_load = 0') */;
/*!50717 PREPARE s FROM @rocksdb_enable_bulk_load */;
/*!50717 EXECUTE s */;
/*!50717 DEALLOCATE PREPARE s */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('construtec-obras-cache-cassio@obrasconstrutec.com.br|191.189.2.6','i:1;',1780025264),('construtec-obras-cache-cassio@obrasconstrutec.com.br|191.189.2.6:timer','i:1780025264;',1780025264),('construtec-obras-cache-cassio@obrascontrutec.com|138.185.86.44','i:1;',1778719021),('construtec-obras-cache-cassio@obrascontrutec.com|138.185.86.44:timer','i:1778719021;',1778719021),('construtec-obras-cache-guilherme@gdoism.com.br|191.189.2.6','i:1;',1780025233),('construtec-obras-cache-guilherme@gdoism.com.br|191.189.2.6:timer','i:1780025233;',1780025233);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contratos`
--

DROP TABLE IF EXISTS `contratos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contratos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `conteudo` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rascunho',
  `assinado_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contratos_obra_id_unique` (`obra_id`),
  CONSTRAINT `contratos_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contratos`
--

LOCK TABLES `contratos` WRITE;
/*!40000 ALTER TABLE `contratos` DISABLE KEYS */;
/*!40000 ALTER TABLE `contratos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diario_posts`
--

DROP TABLE IF EXISTS `diario_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diario_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `etapa_obra_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `texto` text COLLATE utf8mb4_unicode_ci,
  `foto_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_postagem` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diario_posts_obra_id_foreign` (`obra_id`),
  KEY `diario_posts_user_id_foreign` (`user_id`),
  KEY `diario_posts_etapa_obra_id_foreign` (`etapa_obra_id`),
  CONSTRAINT `diario_posts_etapa_obra_id_foreign` FOREIGN KEY (`etapa_obra_id`) REFERENCES `etapa_obras` (`id`) ON DELETE SET NULL,
  CONSTRAINT `diario_posts_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `diario_posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diario_posts`
--

LOCK TABLES `diario_posts` WRITE;
/*!40000 ALTER TABLE `diario_posts` DISABLE KEYS */;
INSERT INTO `diario_posts` VALUES (6,1,NULL,1,'Fundação das sapatas do muro de arrimo','posts/uixqX50nmhJC7gGsscx3DKjwePFVPXsbccreRfdW.webp','2026-04-27 14:57:01','2026-04-27 17:57:01','2026-04-27 17:57:01'),(7,1,NULL,1,'Início da montagem do gabarito do prédio','posts/1zyPqUwaWp7EWuYvWLx4h66ad5mhGBAu54Cra3QI.webp','2026-04-27 20:42:17','2026-04-27 23:42:17','2026-04-27 23:42:17'),(8,1,NULL,1,'Solo encharcado','posts/uLvIMjaIYG2ei3bhoEqnNZbyVPUQCI6ve4g64H5V.webp','2026-04-28 12:51:06','2026-04-28 15:51:06','2026-04-28 15:51:06'),(9,1,NULL,1,'Início da ferragem do muro de arrimo','posts/gRXwkkIqiq2N9gZjlynLG8pVa4jrHLjxwgYFdDf9.webp','2026-04-28 12:53:38','2026-04-28 15:53:38','2026-04-28 15:53:38'),(10,1,NULL,1,'Montagem da gaiola dos pilares da cobertura','posts/YuMzAVnCwD1zlhbr2rBJrnFUpEsOMS1yKMAr1qFI.webp','2026-04-28 13:39:03','2026-04-28 16:39:03','2026-04-28 16:39:03'),(11,1,NULL,1,'Esquadrejando o prédio','posts/GD3Otf8kDhVB5nivdSv3BOgy8wtY14vLnWudSBET.webp','2026-04-28 18:44:43','2026-04-28 21:44:43','2026-04-28 21:44:43'),(12,1,NULL,1,'1/3 Radie gaiola 1,20x1,20x1,20','posts/55Lm2AaVexvLZ4ZkhFD3NI0OyXWP4r9aNMtOksBx.webp','2026-04-28 18:53:18','2026-04-28 21:53:18','2026-04-28 21:53:18'),(13,1,NULL,1,'Escavação de sapata do muro de arrimo concluído','posts/IZj5mZVM56g50EAhgXUGVojesxjwJT1HmfkOWYie.webp','2026-04-28 19:27:26','2026-04-28 22:27:26','2026-04-28 22:27:26'),(14,1,NULL,1,'Gabarito concluído','posts/tWQpUjY3sG0QhdZLjc5KQ38EFvB87LOJJE28V18K.webp','2026-04-28 19:29:03','2026-04-28 22:29:03','2026-04-28 22:29:03'),(15,1,NULL,1,'Sapata do muro de arrimo   \r\nLG 400 mm\r\nCp 600 mm\r\nPr 1200 mm','posts/SqGL40rY1RX0bBLV2YeuhbhMtb8zgowlFwmMFCmW.webp','2026-04-28 19:32:25','2026-04-28 22:32:25','2026-04-28 22:32:25'),(16,1,NULL,1,'Pilar moro de arrimo','posts/atp0EMbGGdOrAmhhibObaVDmYvgdhhpcpU740Rrb.webp','2026-04-28 19:39:05','2026-04-28 22:39:05','2026-04-28 22:39:05'),(17,1,NULL,1,NULL,'posts/yEqfeXqcbR0dbFxYIxYcFGBUo0u8BZZyjDtEmNbt.webp','2026-04-28 19:55:38','2026-04-28 22:55:38','2026-04-28 22:55:38'),(18,1,NULL,3,'Início da concretagem do muro de arrimo','posts/Zql6a63MUf1uAqTNO18GBRHO4Hsr1rWg5K0GvNOK.webp','2026-04-29 11:06:06','2026-04-29 14:06:06','2026-04-29 14:06:06'),(20,1,NULL,1,'Radie (gaiola ) do Pilar da cobertura concluído','posts/QNzvJsOERZ9A186hl1LuApaIfaYB1iyC8ETsg1Ta.webp','2026-04-29 16:22:01','2026-04-29 19:22:01','2026-04-29 19:22:01'),(21,1,NULL,3,'Foi cheio duas sapatas,devido a chuva as 14:00 hrs foi impossível encher maís \r\n5 sc de cimento','posts/v1kuwKIvNtzXqpTHFDcAqKbBccyyzc79fhFsxyN8.webp','2026-04-29 16:39:11','2026-04-29 19:39:11','2026-04-29 19:39:11'),(22,1,NULL,3,'Vigamento do muro de arrimo','posts/RXqK1BSFKqN1RJtZ39Tj0fnIW6XVqsirO2i9EZ2r.webp','2026-04-30 08:56:09','2026-04-30 11:56:09','2026-04-30 11:56:09'),(23,1,NULL,3,'Escavação da caixa de fundação da ilha','posts/OWpl3KEx0OOalqtjXX7nM07kHQF74pmBlHsZkoi8.webp','2026-04-30 09:54:47','2026-04-30 12:54:47','2026-04-30 12:54:47'),(24,1,NULL,3,'Execução de formas para concretagem','posts/a9kCdEKtHcHMCd2k9eCjn7bQ4EUcY40TRccgHKFQ.webp','2026-04-30 11:02:31','2026-04-30 14:02:31','2026-04-30 14:02:31'),(25,1,NULL,3,'Solo encharcado','posts/1pXP5gtl2NK3UpAv4wpMT50Hw7vDhV0o4RdKZ0go.webp','2026-05-04 10:37:12','2026-05-04 13:37:12','2026-05-04 13:37:12'),(26,1,NULL,3,'Início da concretagem da viga do murro de arrimo','posts/fByVUAn1NEGwozua65AFJJXvvmPEWfGuje87lpmq.webp','2026-05-04 11:12:57','2026-05-04 14:12:57','2026-05-04 14:12:57'),(27,1,NULL,3,'Murro de arrimo','posts/jOStbGmGXESpN7i6dEuFlGVV1RAsAgFFpJHg7rE8.webp','2026-05-04 17:48:14','2026-05-04 20:48:14','2026-05-04 20:48:14'),(28,1,NULL,3,'Solo encharcado','posts/4snL2b2PlqkvtQMunMD5HNjzUmpqY3c6IxRP5FLm.webp','2026-05-05 08:49:55','2026-05-05 11:49:55','2026-05-05 11:49:55'),(29,1,NULL,3,'Concretagem da viga do muro de arrimo','posts/5DFKVpoLvI5MeGkjsQEBCATKTitAI0gWItVzePgH.webp','2026-05-05 18:02:53','2026-05-05 21:02:53','2026-05-05 21:02:53'),(30,1,NULL,3,'Escavação da sapata do prédio','posts/WPrQkyfHYRG1nwhdKxBpMcLUbrMKhGF3jkBtD5LX.webp','2026-05-05 18:04:51','2026-05-05 21:04:51','2026-05-05 21:04:51'),(31,1,NULL,3,'Solo encharcado','posts/8q7PJa4ji3oAp0HWGT3Tzpbpyg2YmT4KQL6Wk4s2.webp','2026-05-06 09:36:18','2026-05-06 12:36:18','2026-05-06 12:36:18'),(32,1,NULL,3,'Execução de alvenaria de vedação em blocos de concreto sobre viga','posts/znS84g3ffFFqmFH517PuddbS7fqB3FVJhJbfJvH0.webp','2026-05-08 18:37:15','2026-05-08 21:37:16','2026-05-08 21:37:16'),(33,1,NULL,3,'Montagem das armaduras','posts/FK0dLceTUAZj5ZBoH6RdlhkFeivjo49GWwWnjNGd.webp','2026-05-08 18:43:12','2026-05-08 21:43:12','2026-05-08 21:43:12'),(34,1,NULL,1,'Foto adicionada retroativamente.','posts/jEN228hBHhAozWBK4egWXW3z8n1W8EoKmBw9tPK7.webp','2026-05-08 12:00:00','2026-05-10 21:56:42','2026-05-10 21:56:42'),(35,1,NULL,1,'Foto adicionada retroativamente.','posts/9VxGE7FFZtQkjkhDam5jEeOQUYqpvSPR3twN6ed1.webp','2026-05-07 12:00:00','2026-05-10 22:05:19','2026-05-10 22:05:19'),(36,1,NULL,1,'Foto adicionada retroativamente.','posts/g1MxIgmIPHjnCigjWe6iHzgMh5mpZcvpKA40Dvi5.webp','2026-05-07 12:00:00','2026-05-10 22:07:10','2026-05-10 22:07:10'),(37,1,NULL,1,'Foto adicionada retroativamente.','posts/FEXRx9UQW7FUgH1k3eXVDyMQWpVSx3VhLBnEdt2k.webp','2026-05-07 12:00:00','2026-05-10 22:08:51','2026-05-10 22:08:51'),(38,1,NULL,3,'Escavação da sapata do prédio','posts/jqFkZQDJxisDJhGe7IDpwM2OArBTCLY71hjcrLuh.webp','2026-05-11 14:29:36','2026-05-11 17:29:36','2026-05-11 17:29:36'),(39,1,NULL,3,'Escavação da sapata do murro de arrimo','posts/ohXkhy5N1jd3YEZ8pLUaMme488KcOeY6ymEVAsj8.webp','2026-05-11 14:30:55','2026-05-11 17:30:56','2026-05-11 17:30:56'),(40,1,NULL,3,'Radier e arranque da sapata do prédio','posts/551AfYVynezfibcAVBhaFb8zRBAkEXbPaC9YiUCw.webp','2026-05-11 14:32:31','2026-05-11 17:32:31','2026-05-11 17:32:31'),(47,1,NULL,3,'Ferragem da viga do muro de arrimo','posts/mAsRfM6WFntcR8SRsxyu5c72bODfET3ZuSFIUCgN.webp','2026-05-13 10:14:09','2026-05-13 13:14:09','2026-05-13 13:14:09'),(48,1,NULL,3,'Concretagem da sapata do prédio','posts/ngcFjrUXRR9bp78y7yX9hTwO1UVbXJ6f54YLhKsD.webp','2026-05-13 10:16:11','2026-05-13 13:16:12','2026-05-13 13:16:12'),(49,1,NULL,3,'Finalizando a soldagem estrutural da viga da ilha','posts/NRX8j2ovh8rCpqR6s5NUA1X7sNUKJdxxZOnkQjqN.webp','2026-05-13 10:26:31','2026-05-13 13:26:31','2026-05-13 13:26:31'),(50,1,NULL,3,'Em andamento armação do painel da viga','posts/9v17zXtK2Hxwy1cbeWFHu5QkTkczaqgZUFmqazk5.webp','2026-05-13 14:21:45','2026-05-13 17:21:45','2026-05-13 17:21:45'),(51,1,NULL,3,'Montagem de formas e armações em andamento','posts/6mjADNOt8qumy0cxB0jnhICoTPtc058xkLXdOSFX.webp','2026-05-14 09:13:07','2026-05-14 12:13:07','2026-05-14 12:13:07'),(52,1,NULL,3,'Montagem de formas e armação para viga','posts/DOTQMP8redzlW2IUuzsQYu9U78tnCtHGKsWiNUTS.webp','2026-05-14 09:20:00','2026-05-14 12:20:01','2026-05-14 12:20:01'),(53,1,NULL,3,'Ferragem da viga do muro de arrimo','posts/s3eejGYJ91eSDD9BNLAMhhnG8hNEJknihctFObje.webp','2026-05-14 09:22:28','2026-05-14 12:22:29','2026-05-14 12:22:29'),(54,1,NULL,3,'Fundação da sapata do muro de arrimo','posts/fSdQqHADiXIzagTdBAs271LblGYPDuu63srWUU4D.webp','2026-05-14 09:50:43','2026-05-14 12:50:43','2026-05-14 12:50:43'),(55,1,NULL,3,'Continuação da armação da viga do muro de arrimo','posts/ECRJOZjbtwPNovAYXavegWCVTObLTQ3nhQd76zju.webp','2026-05-14 17:07:17','2026-05-14 20:07:18','2026-05-14 20:07:18'),(56,1,NULL,3,'Concretagem da sapata do prédio','posts/mU0nJR1XPPH5m3KNGoWoWK9b8GbzAAUj9VBg9U87.webp','2026-05-14 17:09:54','2026-05-14 20:09:54','2026-05-14 20:09:54'),(57,1,NULL,3,'Muro de arrimo','posts/uxJ0xFqTZNuhZFltf88inUML3yIGfIT6iIqm7H3U.webp','2026-05-14 17:13:57','2026-05-14 20:13:58','2026-05-14 20:13:58'),(58,1,NULL,3,'Ferragem pro muro de arrimo','posts/muawudARqIewp8QJnucpg7OWYyF3fpvq9E2WR81b.webp','2026-05-15 09:47:41','2026-05-15 12:47:41','2026-05-15 12:47:41'),(59,1,NULL,3,'Concretagem do muro de arrimo','posts/zPF5Qk450bFiZYqqAj2NIl7FGdaGlQT5HJleik47.webp','2026-05-15 09:50:05','2026-05-15 12:50:05','2026-05-15 12:50:05'),(60,1,NULL,3,'Armação da viga do muro de arrimo','posts/kT1xIBgk28i8HqPwzYjhKEYTwcs0oYx5s9Tgs43z.webp','2026-05-15 09:51:55','2026-05-15 12:51:56','2026-05-15 12:51:56'),(61,1,NULL,3,NULL,'posts/DFw3vR2D4wvHzs44qzb6eoTtXSxnUm4ueEmPUem2.webp','2026-05-15 19:55:30','2026-05-15 22:55:30','2026-05-15 22:55:30'),(62,1,NULL,3,'Concretagem do caixote da sapata do prédio','posts/y1KKmmSgR0E1LgcQtNvnT6q9DK12X8JBN5DVD5is.webp','2026-05-15 19:56:39','2026-05-15 22:56:40','2026-05-15 22:56:40'),(63,1,NULL,3,'Caixaria do prédio','posts/G2RDbqZ33qAeYWAR6YYu3x06cSJ0NlPTiEm2HseG.webp','2026-05-18 09:40:58','2026-05-18 12:40:59','2026-05-18 12:40:59'),(64,1,NULL,3,'Viga do muro de arrimo finalizada','posts/FbAoy7Yc6y0BuEQU4TEAMIR2sc8j54ZWqnt6Y6G7.webp','2026-05-18 09:45:27','2026-05-18 12:45:27','2026-05-18 12:45:27'),(65,1,NULL,3,'Ferragem do prédio','posts/3w15wkP5qX6rP5HNsByJZdDvTWHlupXd1xFjl3sm.webp','2026-05-18 09:47:53','2026-05-18 12:47:53','2026-05-18 12:47:53'),(66,1,NULL,3,'Muro de arrimo','posts/xrQrnlLZr9ns72iyNv5vEhDGNAXqoV8iFzh0f3Kp.webp','2026-05-18 14:24:22','2026-05-18 17:24:23','2026-05-18 17:24:23'),(67,1,NULL,3,'Viga do prédio','posts/DuY2fU6JJirRiyXKbDxEco5X3RpFsSAdJXTm6lEX.webp','2026-05-19 15:13:59','2026-05-19 18:14:00','2026-05-19 18:14:00'),(68,1,NULL,3,'Bloco no murro de arrimo','posts/c81V0syKB4JcdRtqNEBB9wu9caP9ZByf07bRACFr.webp','2026-05-19 15:18:13','2026-05-19 18:18:14','2026-05-19 18:18:14'),(69,1,NULL,3,NULL,'posts/ON25AhTcelSy1KPgcuIpyrLtC0dxjoEkqywyJbtc.webp','2026-05-19 15:19:15','2026-05-19 18:19:15','2026-05-19 18:19:15'),(70,1,NULL,3,'Concretagem da viga do prédio','posts/vHuB18Q3Gh5XReeEOBy085YdrCmyTNWs8G9Fqkws.webp','2026-05-19 15:22:08','2026-05-19 18:22:08','2026-05-19 18:22:08'),(71,1,NULL,3,NULL,'posts/yfwtvExsqUcTXNKxcSAnmaLlJ56zb9vluHLQkzk4.webp','2026-05-19 15:25:02','2026-05-19 18:25:02','2026-05-19 18:25:02'),(72,1,NULL,3,'Material ecológico','posts/pBymewa7RMp2IwTDAWsB0D2dkp2SbN2sLUZBntfi.webp','2026-05-19 15:26:26','2026-05-19 18:26:26','2026-05-19 18:26:26'),(73,1,NULL,3,'Concretagem da viga do prédio','posts/aaSQQDoaGfoljfSDlZoqh6HsfLZ7zAGI1aEYpNbk.webp','2026-05-20 16:21:20','2026-05-20 19:21:20','2026-05-20 19:21:20'),(74,1,NULL,3,'Arranque do muro de arrimo','posts/ydGfAiWNFrcon5H1hSHgPYfsfuZmTUfTI1NVPSpm.webp','2026-05-20 16:22:34','2026-05-20 19:22:35','2026-05-20 19:22:35'),(75,1,NULL,3,'Caixaria do prédio','posts/hZ5OhCgZ4CPjTXVLlmtVOP94X7VDY9rynVoHvJeo.webp','2026-05-20 16:23:53','2026-05-20 19:23:53','2026-05-20 19:23:53'),(76,1,NULL,3,'Caixaria da sapata do prédio','posts/m3RyarJbCsmrl19MWkJ64efgfmPbgNfa9Nlml3yz.webp','2026-05-20 16:24:48','2026-05-20 19:24:48','2026-05-20 19:24:48'),(77,1,NULL,3,'Muro de arrimo','posts/fucJpzYC5q9G5ipLgJPPsdQXU306JJHKMfR9jAY0.webp','2026-05-20 16:26:07','2026-05-20 19:26:07','2026-05-20 19:26:07'),(78,1,NULL,3,'Serviço de execução do sump do tanque','posts/m1bHaqVNwDWGCfv6ITwqhZZKgnZ7oODyRXMqm43Y.webp','2026-05-20 16:33:39','2026-05-20 19:33:40','2026-05-20 19:33:40'),(79,1,NULL,3,'Viga do prédio','posts/hmVkZoxWo7MVDdkvSfC3GoaHbVamcMvu63nMhLLZ.webp','2026-05-21 18:59:44','2026-05-21 21:59:44','2026-05-21 21:59:44'),(80,1,NULL,3,'Sapata do muro de arrimo','posts/lSJdpvm20BROUl1kxQIq0yGHzT1gnQQ8DZofVp8F.webp','2026-05-21 19:00:43','2026-05-21 22:00:43','2026-05-21 22:00:43'),(81,1,NULL,3,NULL,'posts/DS2VDDAB8Z9MEfscG6kTrEZxQvaE4fUhsQ9mI53e.webp','2026-05-21 19:01:23','2026-05-21 22:01:24','2026-05-21 22:01:24'),(82,1,NULL,3,'Sump do tanque','posts/2JNiWvQqQiKOlpMM0V10di2WOAKq785yRX7YE9fB.webp','2026-05-21 19:07:07','2026-05-21 22:07:07','2026-05-21 22:07:07'),(83,1,NULL,3,NULL,'posts/PbO9goLyIOW6FyhrFO1BYVfvBPCDv46DCGaoY8NU.webp','2026-05-21 19:08:02','2026-05-21 22:08:02','2026-05-21 22:08:02'),(84,1,NULL,3,'Execução de impermeabilização em parede de contenção','posts/zsfPQnEwRDnLzD8KG9u3rnfL1w5Y04YP2N0VjtvR.webp','2026-05-27 18:28:56','2026-05-27 21:28:57','2026-05-27 21:28:57'),(85,1,NULL,3,'Execução da desforma das vigas baldrame','posts/hfuyYuypRzyn9wCzUUC4CZUytT9ZdtY1pKWR6l0O.webp','2026-05-27 18:31:39','2026-05-27 21:31:39','2026-05-27 21:31:39'),(86,1,NULL,3,'Execução da alvenaria de vedação em tijolo cerâmico','posts/Ejp8W5kzdlklGtPCKpnMg0QwXV95VxOpV3Kizk11.webp','2026-05-27 18:36:09','2026-05-27 21:36:10','2026-05-27 21:36:10'),(87,1,NULL,3,NULL,'posts/4BG0mjQDMhA20SonVYOAbYgtckRWsYSO1Zb6UjAM.webp','2026-05-27 18:37:08','2026-05-27 21:37:09','2026-05-27 21:37:09'),(88,1,NULL,3,'Execução de impermeabilização das vigas do prédio','posts/diTd0AWWEJ5fOXQ8UR95ZH4ol6kDqdVDPRWK6r66.webp','2026-05-27 18:39:21','2026-05-27 21:39:21','2026-05-27 21:39:21'),(89,1,NULL,3,'impermeabilização das vigas e alvenaria de vedação em tijolo','posts/xgdu1M9qjQfZYnwvMlhKgF6zTtUjBh9Hi08V6BQa.webp','2026-05-27 18:41:40','2026-05-27 21:41:40','2026-05-27 21:41:40'),(90,1,NULL,3,'Impermeabilização da viga do prédio','posts/awJz0CSHSzQfhkBveWjmiKPoQndbjp1FcX2PmHZY.webp','2026-05-28 19:25:35','2026-05-28 22:25:35','2026-05-28 22:25:35'),(91,1,NULL,3,NULL,'posts/9XTiFVdSO9Viid73abGMUy7fHNZfiyQyRMSSDjie.webp','2026-05-28 19:28:30','2026-05-28 22:28:31','2026-05-28 22:28:31'),(92,1,NULL,3,'Ferragem da viga do muro','posts/CPsm4ZMCOWDsSLHtzA7jH9fLH5ySOgpIP3TWHtpy.webp','2026-05-28 19:29:43','2026-05-28 22:29:44','2026-05-28 22:29:44'),(93,1,NULL,3,'Instalação da tubulação','posts/xDPmd8ljFQYoEZBSuQ4MFsKrp9h4sSNyO6HIgvzo.webp','2026-05-28 19:34:36','2026-05-28 22:34:36','2026-05-28 22:34:36'),(94,1,NULL,3,NULL,'posts/KDiLUCYbflQPwoTmmmbu96mz5RVb4nPO1F0xSL7h.webp','2026-05-28 19:35:59','2026-05-28 22:36:00','2026-05-28 22:36:00'),(95,1,NULL,3,'Execução da alvenaria','posts/WVZ1xqwMAS8YiH8QZW54zJqwEkUzEgqu9ZX8cCdF.webp','2026-05-28 19:37:50','2026-05-28 22:37:50','2026-05-28 22:37:50'),(96,1,NULL,3,NULL,'posts/eVJRfYWjFdbUlOpkGbfiJivfMHrAMO1cViInuWx0.webp','2026-05-28 19:38:40','2026-05-28 22:38:41','2026-05-28 22:38:41');
/*!40000 ALTER TABLE `diario_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diario_reports`
--

DROP TABLE IF EXISTS `diario_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diario_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `data_relatorio` date NOT NULL,
  `status_dia` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trabalhado',
  `servicos_iniciados` text COLLATE utf8mb4_unicode_ci,
  `servicos_execucao` text COLLATE utf8mb4_unicode_ci,
  `servicos_concluidos` text COLLATE utf8mb4_unicode_ci,
  `materiais_recebidos` text COLLATE utf8mb4_unicode_ci,
  `ocorrencias` text COLLATE utf8mb4_unicode_ci,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `motivo_paralisacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `editado_em` timestamp NULL DEFAULT NULL,
  `clima_horario` json DEFAULT NULL,
  `mao_de_obra` json DEFAULT NULL,
  `maquinario` json DEFAULT NULL,
  `dia_improdutivo` tinyint(1) NOT NULL DEFAULT '0',
  `editado_por` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `diario_reports_obra_id_foreign` (`obra_id`),
  KEY `diario_reports_user_id_foreign` (`user_id`),
  KEY `diario_reports_editado_por_foreign` (`editado_por`),
  CONSTRAINT `diario_reports_editado_por_foreign` FOREIGN KEY (`editado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `diario_reports_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `diario_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diario_reports`
--

LOCK TABLES `diario_reports` WRITE;
/*!40000 ALTER TABLE `diario_reports` DISABLE KEYS */;
INSERT INTO `diario_reports` VALUES (1,1,1,'2026-04-27','trabalhado','Fundação do muro de arrimo e gabarito do predio','- Fundação das sapatas do muro de arrimo\r\n- Início da montagem do gabarito do prédio',NULL,NULL,NULL,NULL,NULL,'2026-04-27 23:48:58','2026-04-28 00:22:41','2026-04-28 00:22:41','{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"bom\", \"14:00\": \"bom\", \"15:00\": \"bom\", \"16:00\": \"bom\", \"17:00\": \"nublado\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"Pedreiros\", \"quantidade\": \"1\"}, {\"funcao\": \"Ajudantes\", \"quantidade\": \"2\"}, {\"funcao\": \"Carpinteiro\", \"quantidade\": \"1\"}, {\"funcao\": \"Engenheiro Civil\", \"quantidade\": \"1\"}, {\"funcao\": \"Encarregado\", \"quantidade\": \"2\"}]','[{\"item\": null, \"quantidade\": null}]',0,1),(2,1,1,'2026-04-28','trabalhado',NULL,'- Solo encharcado\r\n- Início da ferragem do muro de arrimo\r\n- Montagem da gaiola dos pilares da cobertura\r\n- Esquadrejando o prédio\r\n- 1/3 Radie gaiola 1,20x1,20x1,20\r\n- Escavação de sapata do muro de arrimo concluído\r\n- Gabarito concluído\r\n- Sapata do muro de arrimo   \r\nLG 400 mm\r\nCp 600 mm\r\nPr 1200 mm\r\n- Pilar moro de arrimo',NULL,'20 sc de cimento nassau','Não estar na obra material solicitado no dia 27/04( ontem)',NULL,NULL,'2026-04-28 23:25:00','2026-04-28 23:25:00',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"bom\", \"14:00\": \"bom\", \"15:00\": \"bom\", \"16:00\": \"-\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"Carpinteiro\", \"quantidade\": \"1\"}, {\"funcao\": \"Pedreiro\", \"quantidade\": \"1\"}, {\"funcao\": \"Ferreiro\", \"quantidade\": \"1\"}, {\"funcao\": \"Servente\", \"quantidade\": \"1\"}, {\"funcao\": \"Encarregado\", \"quantidade\": \"2\"}, {\"funcao\": \"Soldador\", \"quantidade\": \"1\"}]','[{\"item\": \"Máquina de solda\", \"quantidade\": \"1\"}, {\"item\": \"Poli corte\", \"quantidade\": \"1\"}]',0,NULL),(3,1,1,'2026-04-29','trabalhado',NULL,'- Início da concretagem do muro de arrimo\r\n- Radie (gaiola ) do Pilar da cobertura concluído\r\n- Foi cheio duas sapatas,devido a chuva as 14:00 hrs foi impossível encher maís \r\n5 sc de cimento',NULL,'12 Dz de tipão \r\n12 Dz de Tábua',NULL,'Devido ao não cumprimento da entrada conforme estabelecido em contrato, estamos operando com capacidade reduzida. Solicitamos a regularização para normalização dos serviços.',NULL,'2026-04-29 19:47:57','2026-04-29 19:52:50','2026-04-29 19:52:50','{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"bom\", \"14:00\": \"chuva_f\", \"15:00\": \"chuva_l\", \"16:00\": \"chuva_f\", \"17:00\": \"chuva_f\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"Pedreiro\", \"quantidade\": \"1\"}, {\"funcao\": \"Ferreiro\", \"quantidade\": \"1\"}, {\"funcao\": \"Carpinteiro\", \"quantidade\": \"1\"}, {\"funcao\": \"Servente\", \"quantidade\": \"1\"}, {\"funcao\": \"Encarregado\", \"quantidade\": \"2\"}, {\"funcao\": \"Engenheiro Civil\", \"quantidade\": \"1\"}]','[{\"item\": \"Máquina de solda\", \"quantidade\": \"1\"}, {\"item\": \"Poli corte\", \"quantidade\": \"1\"}]',0,1),(4,1,3,'2026-05-04','trabalhado','Escavação Sapatas prédio','- Solo encharcado\r\n- Início da concretagem da viga do murro de arrimo\r\n- Muro de arrimo',NULL,'Banheiro químico',NULL,'Foram usados 7 sc de cimento no muro de arrimo',NULL,'2026-05-04 20:53:53','2026-05-05 15:28:38','2026-05-05 15:28:38','{\"07:00\": \"chuva_f\", \"08:00\": \"chuva_f\", \"09:00\": \"nublado\", \"10:00\": \"nublado\", \"11:00\": \"nublado\", \"12:00\": \"chuva_l\", \"13:00\": \"nublado\", \"14:00\": \"nublado\", \"15:00\": \"nublado\", \"16:00\": \"nublado\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"Pedreiro\", \"quantidade\": \"1\"}, {\"funcao\": \"Servente\", \"quantidade\": \"3\"}, {\"funcao\": \"Encarregado\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,2),(5,1,3,'2026-05-05','meio_expediente',NULL,'- Solo encharcado\n- Concretagem da viga do muro de arrimo\n- Escavação da sapata do prédio',NULL,'05 DZ PERNAMANCA','Período da manhã com ocorrência de chuva, impossibilitando o início das atividades. Serviços iniciados às 12h00 após melhora das condições climáticas.','Foram usados 6 sc de cimento',NULL,'2026-05-05 21:19:03','2026-05-05 21:40:10','2026-05-05 21:40:10','{\"07:00\": \"nublado\", \"08:00\": \"nublado\", \"09:00\": \"chuva_l\", \"10:00\": \"chuva_l\", \"11:00\": \"chuva_l\", \"12:00\": \"nublado\", \"13:00\": \"nublado\", \"14:00\": \"nublado\", \"15:00\": \"nublado\", \"16:00\": \"nublado\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"5\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}, {\"funcao\": \"ENGENHEIRO\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,1),(6,1,3,'2026-05-06','trabalhado',NULL,'- Solo encharcado',NULL,'Caixa d’água 500LT',NULL,NULL,NULL,'2026-05-07 10:36:45','2026-05-07 10:36:45',NULL,'{\"07:00\": \"-\", \"08:00\": \"-\", \"09:00\": \"-\", \"10:00\": \"-\", \"11:00\": \"-\", \"12:00\": \"-\", \"13:00\": \"-\", \"14:00\": \"-\", \"15:00\": \"-\", \"16:00\": \"-\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"FERREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"4\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}, {\"funcao\": \"ENGENHEIRO\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',1,NULL),(7,1,3,'2026-05-07','trabalhado',NULL,'Murro de Arrimo \nSapata do prédio \nSapata da ilha',NULL,NULL,NULL,NULL,NULL,'2026-05-08 13:41:15','2026-05-10 22:08:58','2026-05-10 22:08:58','{\"07:00\": \"-\", \"08:00\": \"-\", \"09:00\": \"-\", \"10:00\": \"-\", \"11:00\": \"-\", \"12:00\": \"-\", \"13:00\": \"-\", \"14:00\": \"-\", \"15:00\": \"-\", \"16:00\": \"-\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"5\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,1),(8,1,3,'2026-05-08','trabalhado',NULL,'- Execução de alvenaria de vedação em blocos de concreto sobre viga\n- Montagem das armaduras',NULL,'50 SC DE CIMENTO',NULL,NULL,NULL,'2026-05-08 21:46:14','2026-05-10 21:57:36','2026-05-10 21:57:36','{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"nublado\", \"14:00\": \"chuva_l\", \"15:00\": \"nublado\", \"16:00\": \"nublado\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"6\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}, {\"funcao\": \"SOLDADOR\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,1),(9,1,3,'2026-05-11','meio_expediente',NULL,'- Escavação da sapata do prédio\n- Escavação da sapata do murro de arrimo\n- Radier e arranque da sapata do prédio',NULL,NULL,'Hoje os serviços foram até as 12:00, havendo paralisação das atividades no período restante devido às condições climáticas desfavoráveis (chuva), impossibilitando a continuidade segura e adequada da execução',NULL,NULL,'2026-05-11 17:40:33','2026-05-11 17:40:33',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"-\", \"14:00\": \"-\", \"15:00\": \"-\", \"16:00\": \"-\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"3\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"1\"}, {\"funcao\": \"ENGENHEIRO\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,NULL),(10,1,3,'2026-05-12','nao_trabalhado',NULL,NULL,NULL,NULL,'Na data de hoje, as atividades da obra foram interrompidas devido à ocorrência de chuva intensa, impossibilitando a continuidade dos serviços programados com segurança e dentro das condições adequadas de execução.',NULL,NULL,'2026-05-12 16:19:39','2026-05-12 16:55:22','2026-05-12 16:55:22','{\"07:00\": \"-\", \"08:00\": \"-\", \"09:00\": \"-\", \"10:00\": \"-\", \"11:00\": \"-\", \"12:00\": \"-\", \"13:00\": \"-\", \"14:00\": \"-\", \"15:00\": \"-\", \"16:00\": \"-\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": null, \"quantidade\": \"1\"}]','[{\"item\": null, \"quantidade\": \"1\"}]',0,1),(11,1,3,'2026-05-13','trabalhado',NULL,'- Ferragem da viga do muro de arrimo\n- Concretagem da sapata do prédio\n- Finalizando a soldagem estrutural da viga da ilha\n- Em andamento armação do painel da viga',NULL,'40 UND DE CIMENTO \n10 DZ DE RIPÃO \n10 DZ DE MADEIRA',NULL,'Foram usados 15 sc de cimento nas sapatas do prédio',NULL,'2026-05-13 20:45:34','2026-05-13 23:37:24','2026-05-13 23:37:24','{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"bom\", \"14:00\": \"bom\", \"15:00\": \"bom\", \"16:00\": \"bom\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"3\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}, {\"funcao\": \"ENGENHEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SOLDADOR\", \"quantidade\": \"1\"}, {\"funcao\": \"Ferreiro\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,1),(12,1,3,'2026-05-14','trabalhado',NULL,'- Montagem de formas e armações em andamento\n- Montagem de formas e armação para viga\n- Ferragem da viga do muro de arrimo\n- Fundação da sapata do muro de arrimo\n- Continuação da armação da viga do muro de arrimo\n- Concretagem da sapata do prédio\n- Muro de arrimo',NULL,'10 m3 de brita\n16 m3 de areia',NULL,'Foram usados 18 sc de cimento na sapata do prédio',NULL,'2026-05-14 20:20:11','2026-05-14 20:20:11',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"bom\", \"14:00\": \"bom\", \"15:00\": \"bom\", \"16:00\": \"bom\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"3\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}, {\"funcao\": \"FERREIRO\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,NULL),(13,1,3,'2026-05-15','meio_expediente',NULL,'- Ferragem pro muro de arrimo\n- Concretagem do muro de arrimo\n- Armação da viga do muro de arrimo\n- Concretagem do caixote da sapata do prédio',NULL,NULL,'Os serviços previstos para o período foram executados em meio expediente devido à ocorrência de chuvas, impactando parcialmente o andamento das atividades em campo','Foram usados 9 sc de cimento no caixote do prédio',NULL,'2026-05-15 23:07:36','2026-05-15 23:07:36',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"chuva_f\", \"13:00\": \"chuva_f\", \"14:00\": \"chuva_f\", \"15:00\": \"chuva_l\", \"16:00\": \"chuva_l\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"4\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}, {\"funcao\": \"ENGENHEIRO\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,NULL),(14,1,3,'2026-05-16','nao_trabalhado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-18 12:17:01','2026-05-18 12:17:01',NULL,'{\"07:00\": \"-\", \"08:00\": \"-\", \"09:00\": \"-\", \"10:00\": \"-\", \"11:00\": \"-\", \"12:00\": \"-\", \"13:00\": \"-\", \"14:00\": \"-\", \"15:00\": \"-\", \"16:00\": \"-\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": null, \"quantidade\": \"1\"}]','[{\"item\": null, \"quantidade\": \"1\"}]',0,NULL),(15,1,3,'2026-05-18','meio_expediente',NULL,'- Caixaria do prédio\n- Viga do muro de arrimo finalizada\n- Ferragem do prédio\n- Muro de arrimo',NULL,NULL,'Serviços realizados em meio expediente devido às condições climáticas (chuva Intensa)','Foram usados 8 sc de cimento no muro de arrimo',NULL,'2026-05-18 17:31:33','2026-05-18 17:31:33',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"-\", \"14:00\": \"-\", \"15:00\": \"-\", \"16:00\": \"-\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"4\"}, {\"funcao\": \"FERREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,NULL),(16,1,3,'2026-05-19','trabalhado',NULL,'- Viga do prédio\n- Bloco no murro de arrimo\n- Concretagem da viga do prédio\n- Material ecológico',NULL,'Matéria ecológico',NULL,'Materiais q foram solicitado \n10 dz de tábua 3m azimbre \n5 dz de ripao 3m azimbre\nEstribo  24x9 C= 71 cm aço 5mm - 280 unidades \nEstribo  34x9 C= 91cm aço 5mm - 150 unidades\nAço 10mm Barra 12m - 40 unidades\nForam usados 21 sc de cimento na viga do prédio e no muro de arrimo',NULL,'2026-05-19 22:50:35','2026-05-19 22:50:35',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"bom\", \"14:00\": \"bom\", \"15:00\": \"bom\", \"16:00\": \"bom\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"4\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}, {\"funcao\": \"ENGENHEIRO\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,NULL),(17,1,3,'2026-05-20','trabalhado',NULL,'- Concretagem da viga do prédio\n- Arranque do muro de arrimo\n- Caixaria do prédio\n- Caixaria da sapata do prédio\n- Muro de arrimo\n- Serviço de execução do sump do tanque',NULL,'10 dz de tábua 3m azimbre \n5 dz de ripao 3m azimbre',NULL,'Foram usados 14 sc de cimento na concretagem da viga do prédio',NULL,'2026-05-20 21:32:22','2026-05-20 21:32:22',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"nublado\", \"14:00\": \"nublado\", \"15:00\": \"nublado\", \"16:00\": \"nublado\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"3\"}, {\"funcao\": \"FERREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"1\"}, {\"funcao\": \"ENGENHEIRO\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,NULL),(18,1,3,'2026-05-21','trabalhado',NULL,'- Viga do prédio\n- Sapata do muro de arrimo\n- Sump do tanque',NULL,'40 sc de cimento',NULL,'Foram usados 11 sc de cimento na viga do prédio',NULL,'2026-05-21 22:10:51','2026-05-21 22:14:25','2026-05-21 22:14:25','{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"nublado\", \"14:00\": \"nublado\", \"15:00\": \"nublado\", \"16:00\": \"nublado\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"PEDREIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"6\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,1),(19,1,3,'2026-05-22','nao_trabalhado',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-22 13:23:41','2026-05-22 13:23:41',NULL,'{\"07:00\": \"-\", \"08:00\": \"-\", \"09:00\": \"-\", \"10:00\": \"-\", \"11:00\": \"-\", \"12:00\": \"-\", \"13:00\": \"-\", \"14:00\": \"-\", \"15:00\": \"-\", \"16:00\": \"-\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": null, \"quantidade\": \"1\"}]','[{\"item\": null, \"quantidade\": \"1\"}]',0,NULL),(20,1,3,'2026-05-27','trabalhado',NULL,'- Execução de impermeabilização em parede de contenção\n- Execução da desforma das vigas baldrame\n- Execução da alvenaria de vedação em tijolo cerâmico\n- Execução de impermeabilização das vigas do prédio\n- impermeabilização das vigas e alvenaria de vedação em tijolo',NULL,'Areia 16m3',NULL,NULL,NULL,'2026-05-27 21:44:18','2026-05-27 21:44:18',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"bom\", \"14:00\": \"bom\", \"15:00\": \"bom\", \"16:00\": \"bom\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"PEDREIRO\", \"quantidade\": \"2\"}, {\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"4\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"1\"}, {\"funcao\": \"ENGENHEIRO\", \"quantidade\": \"1\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,NULL),(21,1,3,'2026-05-28','trabalhado',NULL,'- Impermeabilização da viga do prédio\n- Ferragem da viga do muro\n- Instalação da tubulação\n- Execução da alvenaria',NULL,NULL,NULL,'Foram usados 9 sc de cimento na alvenaria',NULL,'2026-05-28 22:41:20','2026-05-28 22:41:20',NULL,'{\"07:00\": \"bom\", \"08:00\": \"bom\", \"09:00\": \"bom\", \"10:00\": \"bom\", \"11:00\": \"bom\", \"12:00\": \"bom\", \"13:00\": \"bom\", \"14:00\": \"bom\", \"15:00\": \"bom\", \"16:00\": \"bom\", \"17:00\": \"-\", \"18:00\": \"-\", \"19:00\": \"-\", \"20:00\": \"-\"}','[{\"funcao\": \"CARPINTEIRO\", \"quantidade\": \"1\"}, {\"funcao\": \"PEDREIRO\", \"quantidade\": \"2\"}, {\"funcao\": \"SERVENTE\", \"quantidade\": \"4\"}, {\"funcao\": \"ENCARREGADO\", \"quantidade\": \"2\"}, {\"funcao\": \"INSTALADOR\", \"quantidade\": \"2\"}]','[{\"item\": \"Betoneira\", \"quantidade\": \"1\"}]',0,NULL);
/*!40000 ALTER TABLE `diario_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `etapa_obras`
--

DROP TABLE IF EXISTS `etapa_obras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etapa_obras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(15,2) NOT NULL DEFAULT '0.00',
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_inicio_prevista` date DEFAULT NULL,
  `data_fim_prevista` date DEFAULT NULL,
  `data_inicio_real` date DEFAULT NULL,
  `data_fim_real` date DEFAULT NULL,
  `percentual_concluido` int(11) NOT NULL DEFAULT '0',
  `status` enum('pendente','em_progresso','concluida','atrasada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `ordem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `etapa_obras_obra_id_foreign` (`obra_id`),
  CONSTRAINT `etapa_obras_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `etapa_obras`
--

LOCK TABLES `etapa_obras` WRITE;
/*!40000 ALTER TABLE `etapa_obras` DISABLE KEYS */;
INSERT INTO `etapa_obras` VALUES (1,1,'SERVIÇOS PRELIMINARES',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,100,'pendente','1','2026-04-27 18:20:37','2026-05-19 00:56:02'),(2,1,'PRÉDIO DE SERVIÇOS',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2','2026-04-27 18:20:37','2026-05-19 00:56:35'),(3,1,'Fundação Edificação',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,60,'pendente','2.1','2026-04-27 18:20:37','2026-05-28 14:46:22'),(4,1,'Infraestrutura',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,12,'pendente','2.2','2026-04-27 18:20:37','2026-05-28 14:46:44'),(5,1,'Superestrutura',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.3','2026-04-27 18:20:37','2026-04-27 18:20:37'),(6,1,'Pisos',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.4','2026-04-27 18:20:37','2026-04-27 18:20:37'),(7,1,'Paredes',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,18,'pendente','2.5','2026-04-27 18:20:37','2026-05-29 02:27:48'),(8,1,'Teto',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.6','2026-04-27 18:20:37','2026-04-27 18:20:37'),(9,1,'Instalações',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.7','2026-04-27 18:20:37','2026-04-27 18:20:37'),(10,1,'Instalação Elétrica',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.7.1','2026-04-27 18:20:37','2026-04-27 18:20:37'),(11,1,'Instalações Hidráulicas',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.7.2','2026-04-27 18:20:37','2026-04-27 18:20:37'),(12,1,'Climatização',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.7.3','2026-04-27 18:20:37','2026-04-27 18:20:37'),(13,1,'Portas, Marquise e Esquadrias',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.8.4','2026-04-27 18:20:37','2026-04-27 18:20:37'),(14,1,'OBRAS DE PERIFERIA (ÁREA EXTERNA)',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','3','2026-04-27 18:20:37','2026-04-27 18:20:37'),(15,1,'Muro de vedação',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','3.1','2026-04-27 18:20:37','2026-04-27 18:20:37'),(16,1,'Drenagem de águas Pluviais',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,5,'pendente','3.2','2026-04-27 18:20:37','2026-05-29 02:28:19'),(17,1,'Caixa de Retenção de águas pluviais',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','3.3','2026-04-27 18:20:37','2026-04-27 18:20:37'),(18,1,'Sistema de água e óleo',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','3.4','2026-04-27 18:20:37','2026-04-27 18:20:37'),(19,1,'Lixeira, Casa do compressor, pintura ilha e sinalização horizontal',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','3.6','2026-04-27 18:20:37','2026-04-27 18:20:37'),(20,1,'Piso em Concreto Armado/ Calçada externa',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','3.7','2026-04-27 18:20:37','2026-04-27 18:20:37'),(21,1,'SASC- Sistema Abastecimento Subterrâneo de Combustível',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,18,'pendente','3.8','2026-04-27 18:20:37','2026-04-27 19:24:26'),(22,1,'SERVIÇOS PRELIMINARES',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','1','2026-05-28 22:34:52','2026-05-28 22:34:52'),(23,1,'Fundação  Edificação',0.00,'Gerado automaticamente via Proposta',NULL,NULL,NULL,NULL,0,'pendente','2.1','2026-05-28 22:34:52','2026-05-28 22:34:52');
/*!40000 ALTER TABLE `etapa_obras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_04_27_042400_create_obras_table',1),(5,'2026_04_27_042401_create_propostas_table',1),(6,'2026_04_27_042402_create_pagamentos_table',1),(7,'2026_04_27_042403_create_diario_posts_table',1),(8,'2026_04_27_042404_create_diario_reports_table',1),(9,'2026_04_27_042405_create_nota_fiscals_table',1),(10,'2026_04_27_042406_create_etapa_obras_table',1),(11,'2026_04_27_042407_create_obra_user_table',1),(12,'2026_04_27_042455_add_role_to_users_table',1),(13,'2026_04_27_051749_add_address_fields_to_obras_table',1),(14,'2026_04_27_061035_add_details_to_obras_and_reports_tables',1),(15,'2026_04_27_061610_add_cnpjs_to_obras_table',1),(16,'2026_04_27_070842_add_edit_audit_to_diario_reports_table',1),(17,'2026_04_27_072241_add_etapa_id_to_diario_posts_table',1),(18,'2026_04_27_072314_add_details_to_etapa_obras_table',1),(19,'2026_04_27_073712_add_valor_to_etapa_obras_table',1),(20,'2026_04_27_073726_create_contratos_table',1),(21,'2026_04_27_075804_create_proposta_items_table',1),(22,'2026_04_27_075804_update_propostas_table',1),(23,'2026_04_27_080918_fix_propostas_status_enum',1),(24,'2026_04_27_082130_update_nota_fiscals_table',1),(25,'2026_04_27_145056_change_ordem_to_string_in_proposta_items_and_etapa_obras',2),(26,'2026_04_27_230356_add_status_dia_to_diario_reports_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_fiscals`
--

DROP TABLE IF EXISTS `nota_fiscals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nota_fiscals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `numero_nota` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(15,2) NOT NULL DEFAULT '0.00',
  `quem_recebeu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arquivo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci,
  `data_recebimento` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nota_fiscals_obra_id_foreign` (`obra_id`),
  CONSTRAINT `nota_fiscals_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_fiscals`
--

LOCK TABLES `nota_fiscals` WRITE;
/*!40000 ALTER TABLE `nota_fiscals` DISABLE KEYS */;
INSERT INTO `nota_fiscals` VALUES (20,1,'244482','Cimento',880.00,'Carol Silva','notas_fiscais/ysHZs1xTkGQqZOmvUpubBDsqIZSHRgfykO8ZqG4O.jpg',NULL,'2026-04-28','2026-04-28 18:29:43','2026-04-28 18:29:43'),(36,1,'000.016.960','Vergalhão 3/8 vergalhão 5/16',4.05,'Carol Silva','notas_fiscais/dpSHkF1You8RTw1WNdGGsLmfciRGEB2a7BFvmNXb.jpg',NULL,'2026-04-29','2026-04-29 13:29:24','2026-04-29 13:29:24'),(37,1,'119411','Caixa d’água,mangueira, joelho,registro',450.00,'Carol Anjo','notas_fiscais/3Gt5ZE4ill2OiStC5lelzSWnqYmcKBgaXUgOHAEI.pdf',NULL,'2026-05-06','2026-05-06 13:50:02','2026-05-06 13:50:02'),(39,1,'119595','Vedalit,Disco Madeira ,Arame recozido',443.75,'Carol Anjo','notas_fiscais/tyM8cPU4opy2oEv16Jry6tKXcc92oW5JshKvy8Um.jpg',NULL,'2026-05-09','2026-05-09 12:03:32','2026-05-09 12:03:32'),(40,1,'245872','Cimento',1.76,'Carol Anjo','notas_fiscais/xQy6RjcuyjftBPMxfIQjdNx7NcZM2ArYjd82c511.jpg',NULL,'2026-05-13','2026-05-13 16:59:38','2026-05-13 16:59:38'),(41,1,'000.028.545','10 m3 brita',1.65,'Carol Anjo','notas_fiscais/W9eFVg2d5o2ErD9EfzitZqtwKSHijO04C1OI0xAr.jpg',NULL,'2026-05-14','2026-05-14 11:35:30','2026-05-14 11:35:30'),(42,1,'332743','Areia 16 m3',960.00,'Carol Anjo','notas_fiscais/JWUQl1lfxJYEpZhuUT3SFAbkfTwt3rZaZiq7hkYi.jpg',NULL,'2026-05-14','2026-05-14 16:02:26','2026-05-14 16:02:26'),(43,1,'13741','Material ecológico',20.10,'Carol Anjo','notas_fiscais/S5iIgZMpOe3bdP4r4TUsP05UJjSq2YoVi5v5FlM2.jpg',NULL,'2026-05-19','2026-05-19 18:53:44','2026-05-19 18:53:44'),(44,1,'190762','Prego',508.05,'Carol Anjo','notas_fiscais/7P9tHVvbNR9zNXY9aHNQpeUvpwt9eA3I1692Talh.jpg',NULL,'2026-05-20','2026-05-20 17:18:20','2026-05-20 17:18:20'),(45,1,'190761','Impermeabilizantes',214.20,'Carol Anjo','notas_fiscais/4KDGXGmzkoAAWAxKCuxUy0QljQi0dQ4znAslakm3.jpg',NULL,'2026-05-20','2026-05-20 17:20:33','2026-05-20 17:20:33'),(46,1,'115880','Araldite',281.63,'Carol Anjo','notas_fiscais/yL8kddl6vamduqZeykxO4J4rgPZliPvz7drBGs0L.jpg',NULL,'2026-05-27','2026-05-27 18:34:01','2026-05-27 18:34:01'),(47,1,'246925','Tijolo',3.10,'Carol Anjo','notas_fiscais/6tI6GoDfJEEYcKyvkFzemlm3XlvF9oPrrbkO2IOl.jpg',NULL,'2026-05-27','2026-05-27 18:36:33','2026-05-27 18:36:33'),(48,1,'247130','Areia',960.00,'Carol Anjo','notas_fiscais/Im8WAlbfjbCUlmkZ14P4ZTd2ogXXilcKWCl7gvoy.jpg',NULL,'2026-05-27','2026-05-27 18:38:11','2026-05-27 18:38:11');
/*!40000 ALTER TABLE `nota_fiscals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obra_user`
--

DROP TABLE IF EXISTS `obra_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obra_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `obra_user_obra_id_foreign` (`obra_id`),
  KEY `obra_user_user_id_foreign` (`user_id`),
  CONSTRAINT `obra_user_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `obra_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obra_user`
--

LOCK TABLES `obra_user` WRITE;
/*!40000 ALTER TABLE `obra_user` DISABLE KEYS */;
INSERT INTO `obra_user` VALUES (1,1,4,NULL,NULL),(2,1,3,NULL,NULL);
/*!40000 ALTER TABLE `obra_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obras`
--

DROP TABLE IF EXISTS `obras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obras` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `localizacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim_prevista` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'em_andamento',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `contratante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_contratada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engenheiro_responsavel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prazo_dias` int(11) NOT NULL DEFAULT '0',
  `cnpj_contratante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj_empresa_contratada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obras`
--

LOCK TABLES `obras` WRITE;
/*!40000 ALTER TABLE `obras` DISABLE KEYS */;
INSERT INTO `obras` VALUES (1,'Obra Posto EDDY',NULL,'69058-790','Rua Visconde de Sinimbu','Flores','Manaus','AM','2026-04-27','2026-07-20','em_andamento','2026-04-27 12:40:57','2026-04-27 23:55:42','EDDY PETROLEO LTDA','CONSTRUTEC ENGENHARIA E CONSTRUÇÃO LTDA','Ronaldo Souza 24958-AM',120,'61.843.305/0001-50','48.065.175/0001-03');
/*!40000 ALTER TABLE `obras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagamentos`
--

DROP TABLE IF EXISTS `pagamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagamentos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proposta_id` bigint(20) unsigned NOT NULL,
  `valor_pago` decimal(15,2) NOT NULL,
  `data_pagamento` date NOT NULL,
  `comprovante_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagamentos_proposta_id_foreign` (`proposta_id`),
  CONSTRAINT `pagamentos_proposta_id_foreign` FOREIGN KEY (`proposta_id`) REFERENCES `propostas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagamentos`
--

LOCK TABLES `pagamentos` WRITE;
/*!40000 ALTER TABLE `pagamentos` DISABLE KEYS */;
INSERT INTO `pagamentos` VALUES (2,1,12000.00,'2026-04-22',NULL,'Adiantamento','2026-04-27 19:20:53','2026-04-27 19:20:53');
/*!40000 ALTER TABLE `pagamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proposta_items`
--

DROP TABLE IF EXISTS `proposta_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proposta_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proposta_id` bigint(20) unsigned NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantidade` decimal(15,3) NOT NULL DEFAULT '1.000',
  `valor_unitario` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_etapa` tinyint(1) NOT NULL DEFAULT '0',
  `ordem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proposta_items_proposta_id_foreign` (`proposta_id`),
  CONSTRAINT `proposta_items_proposta_id_foreign` FOREIGN KEY (`proposta_id`) REFERENCES `propostas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1249 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proposta_items`
--

LOCK TABLES `proposta_items` WRITE;
/*!40000 ALTER TABLE `proposta_items` DISABLE KEYS */;
INSERT INTO `proposta_items` VALUES (367,1,'SERVIÇOS PRELIMINARES','un',1.000,0.00,0.00,1,'1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(368,1,'Mobilização e Desmobilização','vb',1.000,4000.00,4000.00,0,'1.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(369,1,'Tapume cercamento da obra/Barracão e instalações provisórias','vb',1.000,2500.00,2500.00,0,'1.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(370,1,'Execução de Gabarito e locação da obra( Prédio, Ilha de abastecimento e tanques)','m2',191.420,30.00,5742.60,0,'1.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(371,1,'Aluguel de retroescavadeira para serviços gerais da obra','mês',1.000,15000.00,15000.00,0,'1.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(372,1,'Locação de Contêiner','mês',5.000,750.00,3750.00,0,'1.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(373,1,'PRÉDIO DE SERVIÇOS','un',1.000,0.00,0.00,1,'2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(374,1,'Fundação Edificação','un',1.000,0.00,0.00,1,'2.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(375,1,'Sapatas 25und. (80x80cm) e Arranque de Pilar','un',1.000,0.00,0.00,0,'2.1.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(376,1,'Escavação manual sapatas','m3',31.200,200.00,6240.00,0,'2.1.1.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(377,1,'Aço Sapatas/Arranque de Pilar (armaduras)','kg',297.700,8.00,2381.60,0,'2.1.1.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(378,1,'Concreto Sapatas e Arranque','m3',5.280,300.00,1584.00,0,'2.1.1.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(379,1,'Viga Baldrame Concreto Armado 40x15cm','un',1.000,0.00,0.00,0,'2.1.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(380,1,'Escavação manual viga baldrame','m3',10.190,200.00,2038.00,0,'2.1.2.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(381,1,'Aço Viga baldrame (armaduras)','kg',622.720,8.00,4981.76,0,'2.1.2.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(382,1,'Confecção e montagem de Forma','m2',104.800,80.00,8384.00,0,'2.1.2.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(383,1,'Concreto Viga Baldrame','m3',9.400,300.00,2820.00,0,'2.1.2.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(384,1,'Desforma','m2',104.800,15.00,1572.00,0,'2.1.2.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(385,1,'Impermeabilização Viga baldrame','m2',124.450,10.00,1244.50,0,'2.1.2.6','2026-04-29 00:47:06','2026-04-29 00:47:06'),(386,1,'Aterro e compactação do interno e ao entorno','vb',1.000,1500.00,1500.00,0,'2.1.2.7','2026-04-29 00:47:06','2026-04-29 00:47:06'),(387,1,'Infraestrutura','un',1.000,0.00,0.00,1,'2.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(388,1,'Pilares e vigas superiores em concreto armado','un',1.000,0.00,0.00,0,'2.2.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(389,1,'Forma e Desforma Pilares/Vigas','m2',82.500,95.00,7837.50,0,'2.2.1.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(390,1,'Aço Armação','kg',931.200,8.00,7449.60,0,'2.2.1.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(391,1,'Concreto manual Pilares e viga','m3',5.400,300.00,1620.00,0,'2.2.1.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(392,1,'Forma laje Escritório','m2',23.040,95.00,2188.80,0,'2.2.1.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(393,1,'Aço laje escritório','kg',120.000,10.00,1200.00,0,'2.2.1.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(394,1,'Concreto Laje Escritório','vb',700.000,1.00,700.00,0,'2.2.1.6','2026-04-29 00:47:06','2026-04-29 00:47:06'),(395,1,'Superestrutura','un',1.000,0.00,0.00,1,'2.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(396,1,'Vedação Prédio','un',1.000,0.00,0.00,0,'2.3.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(397,1,'Assentamento de paredes - Alvenaria','m2',406.560,35.00,14229.60,0,'2.3.1.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(398,1,'Chapisco, Reboco Externo e Interno','m2',813.120,50.00,40656.00,0,'2.3.1.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(399,1,'Acabamento de Argamassa em portas e janelas','m',109.870,25.00,2746.75,0,'2.3.1.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(400,1,'Instalação de Pingadeira','m',82.160,15.00,1232.40,0,'2.3.1.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(401,1,'Pisos','un',1.000,0.00,0.00,1,'2.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(402,1,'Regularização e execução de contrapiso','m2',163.220,35.00,5712.70,0,'2.4.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(403,1,'Impermeabilização de área molhadas com argamassa polimérica','m2',26.600,7.00,186.20,0,'2.4.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(404,1,'Execução de revestimento porcelanato piso','m2',163.220,77.00,12567.94,0,'2.4.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(405,1,'Execução de rodapé','m',65.590,30.00,1967.70,0,'2.4.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(406,1,'Execução de rejunte em Porcelanato','m2',163.220,8.00,1305.76,0,'2.4.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(407,1,'Execução de calçada externa ao redor do prédio','m2',80.490,37.50,3018.38,0,'2.4.6','2026-04-29 00:47:06','2026-04-29 00:47:06'),(408,1,'Execução de revestimento cerâmico piso da calçada','m2',80.490,90.00,7244.10,0,'2.4.7','2026-04-29 00:47:06','2026-04-29 00:47:06'),(409,1,'Paredes','un',1.000,0.00,0.00,1,'2.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(410,1,'Execução de revestimento em parede dos banheiros','m2',85.610,35.00,2996.35,0,'2.5.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(411,1,'Execução de revestimento fachada','m2',80.430,60.00,4825.80,0,'2.5.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(412,1,'Execução de selador, massa acrilica/pva, Pintura prédio','m2',646.960,31.00,20055.76,0,'2.5.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(413,1,'Teto','un',1.000,0.00,0.00,1,'2.6','2026-04-29 00:47:06','2026-04-29 00:47:06'),(414,1,'Execução de forro acartonado','m2',163.220,45.00,7344.90,0,'2.6.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(415,1,'Pintura de forro','m2',163.220,35.00,5712.70,0,'2.6.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(416,1,'Instalações','un',1.000,0.00,0.00,1,'2.7','2026-04-29 00:47:06','2026-04-29 00:47:06'),(417,1,'Instalação Elétrica','un',1.000,0.00,0.00,1,'2.7.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(418,1,'Execução de Mureta - (Entrada padrão e medição).','un',1.000,0.00,0.00,0,'2.7.1.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(419,1,'Infraestrutura elétrica/CFTV','m2',190.080,90.00,17107.20,0,'2.7.1.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(420,1,'Instalações Hidráulicas','un',1.000,0.00,0.00,1,'2.7.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(421,1,'Água Fria','pt.',17.000,280.00,4760.00,0,'2.7.2.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(422,1,'Esgoto sanitário','pt.',16.000,350.00,5600.00,0,'2.7.2.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(423,1,'Execução de caixas de inspeção - Esgoto','und.',4.000,250.00,1000.00,0,'2.7.2.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(424,1,'Execução de Fossa e Sumidouro','un',1.000,8000.00,8000.00,0,'2.7.2.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(425,1,'Assentamento de louças e metais','un',1.000,800.00,800.00,0,'2.7.2.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(426,1,'Climatização','un',1.000,0.00,0.00,1,'2.7.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(427,1,'Infra de Ar-condicionado','und.',6.000,350.00,2100.00,0,'2.7.3.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(428,1,'Dreno de ar-condicionado','Und.',6.000,300.00,1800.00,0,'2.7.3.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(429,1,'Instalação ar-condicionado','Und.',6.000,400.00,2400.00,0,'2.7.3.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(430,1,'Portas, Marquise e Esquadrias','un',1.000,0.00,0.00,1,'2.8.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(431,1,'Instalação de Portas em MDF ou Alumínio','und.',10.000,300.00,3000.00,0,'2.8.4.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(432,1,'Execução de Marquise ACM + Estrutura','m2',68.250,70.00,4777.50,0,'2.8.4.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(433,1,'OBRAS DE PERIFERIA (ÁREA EXTERNA)','un',1.000,0.00,0.00,1,'3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(434,1,'Muro de vedação','un',1.000,0.00,0.00,1,'3.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(435,1,'Fundação - Sapatas, Viga Baldrame e Impermeabilização','m',89.500,30.00,2685.00,0,'3.1.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(436,1,'Estrutura - Pilares e vigas superiores','m',169.500,25.00,4237.50,0,'3.1.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(437,1,'Alvenaria em tijoso cerâmico','m2',223.750,25.00,5593.75,0,'3.1.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(438,1,'Chapisco, Reboco, Pingadeira','m2',223.750,30.00,6712.50,0,'3.1.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(439,1,'Drenagem de águas Pluviais','un',1.000,0.00,0.00,1,'3.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(440,1,'Lançamento de tubo pvc, Diâmetro = 100mm com lançamento de colchão de areia e reaterro.','m',65.000,30.00,1950.00,0,'3.2.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(441,1,'Lançamento de tubo pvc, Diâmetro 150mm com lançamento de colchão de areia e reaterro.','m',42.000,40.00,1680.00,0,'3.2.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(442,1,'Lançamento de tubo pvc, Diâmetro 200mm com lançamento de colchão de areia e reaterro.','m',70.000,50.00,3500.00,0,'3.2.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(443,1,'Lançamento de tubo pvc, Diâmetro = 250mm com lançamento de colchão de areia e reaterro.','m',13.000,80.00,1040.00,0,'3.2.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(444,1,'Lançamento de tubo pvc, Diâmetro 300mm com lançamento de colchão de areia e reaterro em via pública','m',132.000,160.00,21120.00,0,'3.2.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(445,1,'Caixa de drenagem em manilha diâmetro 600mm','caixas',9.000,250.00,2250.00,0,'3.2.6','2026-04-29 00:47:06','2026-04-29 00:47:06'),(446,1,'Canaleta em bloco estrutural (14x19x39cm)com vão interno de 20cm grauteado','m',24.000,80.00,1920.00,0,'3.2.7','2026-04-29 00:47:06','2026-04-29 00:47:06'),(447,1,'Grelha para canaleta de drenagem com cantoneira e barra chata','m',24.000,150.00,3600.00,0,'3.2.8','2026-04-29 00:47:06','2026-04-29 00:47:06'),(448,1,'Caixa de Inspeção 60x60cm em manilha','und.',7.000,250.00,1750.00,0,'3.2.9','2026-04-29 00:47:06','2026-04-29 00:47:06'),(449,1,'Aro e tampa - Instalação das caixas de inspeção','und.',7.000,250.00,1750.00,0,'3.2.10','2026-04-29 00:47:06','2026-04-29 00:47:06'),(450,1,'Caixa de Retenção de águas pluviais','un',1.000,0.00,0.00,1,'3.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(451,1,'Fundação - Sapatas e Radier (Fundo Caixa)','un',1.000,0.00,0.00,0,'3.3.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(452,1,'Escavação manual para sapatas','m3',3.180,200.00,636.00,0,'3.3.1.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(453,1,'Aço das Sapatas','kg',16.920,8.00,135.36,0,'3.3.1.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(454,1,'Concreto das Sapatas','m3',0.540,300.00,162.00,0,'3.3.1.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(455,1,'Estrutura Caixa de Retenção','un',1.000,0.00,0.00,0,'3.3.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(456,1,'Escavação manual de viga baldrame','m3',2.430,200.00,486.00,0,'3.3.2.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(457,1,'Aço Viga baldrame, pilares, arranque da estrutura, viga superior e laje.','kg',710.880,8.00,5687.04,0,'3.3.2.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(458,1,'Forma e desforma Estrutura','m2',74.450,60.00,4467.00,0,'3.3.2.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(459,1,'Alvenaria em bloco estrutural grauteado','m2',55.800,70.00,3906.00,0,'3.3.2.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(460,1,'Impermeabilização da caixa de retenção','m2',111.000,6.00,666.00,0,'3.3.2.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(461,1,'Concretagem das vigas, pilares e laje','m3',8.380,300.00,2514.00,0,'3.3.2.6','2026-04-29 00:47:06','2026-04-29 00:47:06'),(462,1,'Sistema de água e óleo','un',1.000,0.00,0.00,1,'3.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(463,1,'Caixa de Inspeção 60x60cm em manilha','und.',8.000,250.00,2000.00,0,'3.4.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(464,1,'Aro e tampa - Instalação das caixas de inspeção','und.',8.000,250.00,2000.00,0,'3.4.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(465,1,'Lançamento da rede de residuos oleosos tubo 100mm','m',95.000,30.00,2850.00,0,'3.4.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(466,1,'Conteção para caixa separadora de água e óleo','und.',1.000,400.00,400.00,0,'3.4.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(467,1,'Instalação CSAO','und.',1.000,800.00,800.00,0,'3.4.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(468,1,'Lixeira, Casa do compressor, pintura ilha e sinalização horizontal','un',1.000,0.00,0.00,1,'3.6','2026-04-29 00:47:06','2026-04-29 00:47:06'),(469,1,'Execução de lixeira (base concreto, fechamento alvenaria, laje em concreto armado, grade de fechamento.','und.',1.000,1500.00,1500.00,0,'3.6.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(470,1,'Execução de compressor (base concreto, fechamento alvenaria, laje em concreto armado, grade de fechamento.','und.',1.000,1500.00,1500.00,0,'3.6.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(471,1,'Execução de alvenaria para jardim','m',69.200,25.00,1730.00,0,'3.6.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(472,1,'Execução de sinalização horinzontal e vertical','vb',1.000,800.00,800.00,0,'3.6.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(473,1,'Pintura de piso da ilha','m2',451.350,8.00,3610.80,0,'3.6.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(474,1,'Piso em Concreto Armado/ Calçada externa','un',1.000,0.00,0.00,1,'3.7','2026-04-29 00:47:06','2026-04-29 00:47:06'),(475,1,'Lançamento de colchão de areia, montagem armação de treliças/barras de transição e telas. Desempenamento e polimento (caso necessário). Junta de dilatação','m2',1245.450,26.00,32381.70,0,'3.7.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(476,1,'Calçada externas','m2',300.110,20.00,6002.20,0,'3.7.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(477,1,'SASC- Sistema Abastecimento Subterrâneo de Combustível','un',1.000,0.00,0.00,1,'3.8','2026-04-29 00:47:06','2026-04-29 00:47:06'),(478,1,'Instalação dos Tanques','und.',2.000,0.00,0.00,0,'3.8.1','2026-04-29 00:47:06','2026-04-29 00:47:06'),(479,1,'Instalação das Ilhas e concretagem das bases das ilhas de abastecimento','und.',3.000,0.00,0.00,0,'3.8.2','2026-04-29 00:47:06','2026-04-29 00:47:06'),(480,1,'Instalação dos Sumps de Bomba','Und.',3.000,0.00,0.00,0,'3.8.3','2026-04-29 00:47:06','2026-04-29 00:47:06'),(481,1,'Escavação e extensão das linhas de combustíveis ( PEAD)','un',1.000,0.00,0.00,0,'3.8.4','2026-04-29 00:47:06','2026-04-29 00:47:06'),(482,1,'Instalação de Flexíveis e Conexões','un',1.000,0.00,0.00,0,'3.8.5','2026-04-29 00:47:06','2026-04-29 00:47:06'),(483,1,'Instalação de Descarga a distância','un',1.000,0.00,0.00,0,'3.8.6','2026-04-29 00:47:06','2026-04-29 00:47:06'),(484,1,'Limpeza dos Tanques para recebimento de combustíveis','und.',2.000,35000.00,70000.00,0,'3.8.7','2026-04-29 00:47:06','2026-04-29 00:47:06'),(485,1,'Instalação das Bombas','und.',3.000,0.00,0.00,0,'3.8.8','2026-04-29 00:47:06','2026-04-29 00:47:06'),(486,1,'Infra - Automação','un',1.000,0.00,0.00,0,'3.8.9','2026-04-29 00:47:06','2026-04-29 00:47:06'),(487,1,'BDI-5%','un',1.000,23481.93,23481.93,0,'122','2026-04-29 00:47:06','2026-04-29 00:47:06'),(488,1,'Desconto','un',1.000,-23486.88,-23486.88,0,'122','2026-04-29 00:47:06','2026-04-29 00:47:06'),(489,1,'Ajuste Temporario de Revisao','un',1.000,100.01,100.01,0,'123','2026-04-29 00:47:06','2026-04-29 00:47:06'),(1138,2,'SERVIÇOS PRELIMINARES','un',1.000,0.00,0.00,1,'1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1139,2,'Barracão e instalações provisórias','un',1.000,5000.00,5000.00,0,'1.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1140,2,'Execução de Gabarito e locação da obra( Prédio, Ilha de abastecimento e tanques)','m2',231.000,35.00,8085.00,0,'1.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1141,2,'Fundação  Edificação','un',1.000,0.00,0.00,1,'2.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1142,2,'Escavação manual sapatas','m3',36.920,200.00,7384.00,0,'2.1.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1143,2,'Aço Sapatas/Arranque de Pilar (armaduras)','kç',80.580,9.00,725.22,0,'2.1.1.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1144,2,'Concreto Sapatas e Arranque','m3',8.800,300.00,2640.00,0,'2.1.1.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1145,2,'Viga Baldrame Concreto Armado 40x15cm','un',1.000,0.00,0.00,1,'2.1.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1146,2,'Escavação manual viga baldrame','m³',18.210,200.00,3642.00,0,'2.1.2.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1147,2,'Aço Viga baldrame  (armaduras','kg',408.320,8.00,3266.56,0,'2.1.2.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1148,2,'Confecção e montagem de Forma','un',97.160,80.00,7772.80,0,'2.1.2.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1149,2,'Concreto Viga Baldrame','un',10.930,300.00,3279.00,0,'2.1.2.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1150,2,'Desforma','un',97.160,15.00,1457.40,0,'2.1.2.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1151,2,'Impermeabilização Viga baldrame','un',115.370,10.00,1153.70,0,'2.1.2.6','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1152,2,'Aterro e compactação do interno e ao entorno','un',1.000,1500.00,1500.00,0,'2.1.2.7','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1153,2,'Infraestrutura','un',1.000,0.00,0.00,1,'2.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1154,2,'Forma e Desforma Pilares/Vigas','m²',140.100,95.00,13309.50,0,'2.2.1.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1155,2,'Aço Armação','kg',1042.420,8.00,8339.36,0,'2.2.1.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1156,2,'Concreto manual Pilares e viga','m³',10.330,300.00,3099.00,0,'2.2.1.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1157,2,'Forma laje Escritório','m²',59.200,95.00,5624.00,0,'2.2.1.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1158,2,'Concreto Laje Escritório','m²',5.900,300.00,1770.00,0,'2.2.1.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1159,2,'Superestrutura','un',1.000,0.00,0.00,1,'2.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1160,2,'Assentamento de paredes - Alvenaria','m²',324.650,45.00,14609.25,0,'2.3.1.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1161,2,'Chapisco, Reboco Externo e Interno','m²',649.300,60.00,38958.00,0,'2.3.1.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1162,2,'Acabamento de Argamassa em portas e janela','m',133.360,25.00,3334.00,0,'2.3.1.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1163,2,'Instalação de Pingadeira','m',83.500,15.00,1252.50,0,'2.3.1.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1164,2,'Pisos','un',1.000,0.00,0.00,1,'2.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1165,2,'Regularização e execução de contrapiso','m²',184.370,45.00,8296.65,0,'2.4.18','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1166,2,'Impermeabilização de área molhadas com argamassa polimérica','m²',21.000,25.00,525.00,0,'2.4.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1167,2,'Execução de revestimento porcelanato piso','m²',184.370,80.00,14749.60,0,'2.4.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1168,2,'Execução de rodapé','m',108.300,20.00,2166.00,0,'2.4.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1169,2,'Execução de rejunte em Porcelanato','m²',184.370,8.00,1474.96,0,'2.4.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1170,2,'Execução de calçada externa ao redor do prédio','m²',109.910,50.00,5495.50,0,'2.4.6','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1171,2,'Execução de revestimento cerâmico piso da calçada','m²',56.000,90.00,5040.00,0,'2.4.7','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1172,2,'Paredes','un',1.000,0.00,0.00,1,'2.55','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1173,2,'Execução de revestimento em parede dos banheiros','vb',4.000,2000.00,8000.00,0,'2.5.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1174,2,'Execuçaõ de revestimento fachada','vb',1.000,5000.00,5000.00,0,'2.5.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1175,2,'Execuçaõ de selador, massa acrilica/pva, Pintura prédio Execuçaõ de selador, massa acrilica/pva, Pintura prédio','m²',714.230,31.00,22141.13,0,'2.5.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1176,2,'Teto','un',1.000,0.00,0.00,1,'2.6','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1177,2,'Execução de forro acartonado','m²',184.370,45.00,8296.65,0,'2.6.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1178,2,'Pintura de forro','m²',184.370,31.00,5715.47,0,'2.6.21','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1179,2,'Instalações','m²',201.190,90.00,18107.10,1,'2.7','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1180,2,'Execução de Mureta - (Entrada padrão e medição).','un',1.000,0.00,0.00,0,'2.7.1.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1181,2,'Infraestrutura elétrica/CFTV','un',1.000,0.00,0.00,0,'2.7.1.2.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1182,2,'Fiação e cabeamento elétrico','un',1.000,0.00,0.00,0,'2.7.1.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1183,2,'Tomadas, iluminação, Montagem de Quadros elétrico','un',1.000,0.00,0.00,0,'2.7.1.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1184,2,'Instalações Hidráulicas','un',1.000,0.00,0.00,1,'2.7.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1185,2,'Água Fria','pt',16.000,280.00,4480.00,0,'2.7.2.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1186,2,'Esgoto sanitário','pt',19.000,350.00,6650.00,0,'2.7.2.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1187,2,'Execução de caixas de inspeção - Esgoto','und.',4.000,250.00,1000.00,0,'2.7.2.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1188,2,'Execução de caixas de inspeção - Esgoto','vb',1.000,12000.00,12000.00,0,'2.7.2.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1189,2,'Assentamento de louças e metais','vb',1.000,1300.00,1300.00,0,'2.7.2.52','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1190,2,'Climatização','un',1.000,0.00,0.00,1,'2.7.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1191,2,'Climatização','und.',5.000,350.00,1750.00,0,'2.7.33.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1192,2,'Dreno de ar condicionado','und.',5.000,300.00,1500.00,0,'2.7.3.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1193,2,'Dreno de ar condicionado','und.',5.000,400.00,2000.00,0,'2.7.3.36','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1194,2,'Portas, Marquise e Esquadrias','un',1.000,0.00,0.00,1,'2.8.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1195,2,'Instalação de Portas em MDF ou Aluminio','und.',7.000,350.00,2450.00,0,'2.8.4.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1196,2,'Muro de vedação','un',1.000,0.00,0.00,1,'3.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1197,2,'Fundação - Sapatas, Viga Baldrame e Impermeabilização','m',78.300,40.00,3132.00,0,'3..1.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1198,2,'Estrutura - Pilares e vigas superiores','m',169.500,25.00,4237.50,0,'3.1.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1199,2,'Alvenaria em tijoso cerâmico','m²',195.750,30.00,5872.50,0,'3.1.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1200,2,'Chapisco, Reboco, Pingadeira','m²',195.750,30.00,5872.50,0,'3.1.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1201,2,'Drenagem de águas Pluviais','un',1.000,0.00,0.00,1,'3.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1202,2,'Lançamento de tubo pvc, Diâmetro = 100mm com lançamento de colchão de areia e reaterro.','m',42.000,30.00,1260.00,0,'3.2.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1203,2,'Lançamento de tubo pvc, Diâmetro = 150mm com lançamento de colchão de areia e reaterro.','m',50.060,40.00,2002.40,0,'3.2.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1204,2,'Lançamento de tubo pvc, Diâmetro = 200mm com lançamento de colchão de areia e reaterro.','m',75.120,50.00,3756.00,0,'3.2.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1205,2,'Lançamento de tubo pvc, Diâmetro = 250mm com lançamento de colchão de areia e reaterro.','m',23.650,80.00,1892.00,0,'3.2.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1206,2,'Caixa de drenagem em manilha diametro 600mm','cx',9.000,250.00,2250.00,0,'3.2.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1207,2,'Canaleta em bloco estrutural (14x19x39cm)com vão interno de 20cm grauteado','m',38.000,80.00,3040.00,0,'3.2.6','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1208,2,'Grelha para canaleta de drenagem com cantoneira e barra chata','m',38.000,150.00,5700.00,0,'3.2.7','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1209,2,'Aro e tampa - Instalação das caixas de inspeção','und.',9.000,250.00,2250.00,0,'3.2.8','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1210,2,'Caixa de Retenção de águas pluviais','vb',1.000,20659.00,20659.00,1,'3.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1211,2,'Fundação - Sapatas e Radier (Fundo Caixa)','un',1.000,0.00,0.00,0,'3.3.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1212,2,'Escavação manual para sapatas','un',1.000,0.00,0.00,0,'3.3.1.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1213,2,'Aço das Sapatas','un',1.000,0.00,0.00,0,'3.3.1.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1214,2,'Concreto das Sapatas','un',1.000,0.00,0.00,0,'3.3.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1215,2,'Estrutura Caixa de Retenção','un',1.000,0.00,0.00,0,'3.3.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1216,2,'Escavação manual de viga baldrame','un',1.000,0.00,0.00,0,'3.3.2.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1217,2,'Aço Viga baldrame, pilares, arranque da estrutura, viga superior e laje.','un',1.000,0.00,0.00,0,'3.3.2.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1218,2,'Forma e desforma Estrutura','un',1.000,0.00,0.00,0,'3.3.2.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1219,2,'Alvenaria em bloco estrutural grauteado','un',1.000,0.00,0.00,0,'3.3.2.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1220,2,'Impermeabilização da caixa de retenção','un',1.000,0.00,0.00,0,'3.3.2.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1221,2,'Concretagem das vigas, pilares e laje','un',1.000,0.00,0.00,0,'3.3.2.6','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1222,2,'Sistema de água e óleo','un',1.000,0.00,0.00,1,'3.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1223,2,'Caixa de Inspeção 60x60cm em manilha','und.',6.000,250.00,1500.00,0,'3.4.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1224,2,'Aro e tampa - Instalação das caixas de inspeção','und.',6.000,250.00,1500.00,0,'3.4.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1225,2,'Lançamento da rede de residuos oleosos tubo 100mm','m',73.000,30.00,2190.00,0,'3.4.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1226,2,'Conteção para caixa separadora de água e óleo','und.',1.000,700.00,700.00,0,'3.4.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1227,2,'Instalação CSAO','und.',1.000,800.00,800.00,0,'3.4.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1228,2,'Fundação Ilha abastecimetno','und.',3.000,1200.00,3600.00,1,'3.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1229,2,'Execução de fundação ilha abastecimento - Escavação, execução de magro, Armação e Concretagem  (Usinado)','un',1.000,0.00,0.00,0,'3.5.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1230,2,'Lixeira, Casa do compressor, pintura ilha e sinalização horizontal','un',1.000,0.00,0.00,1,'3.6','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1231,2,'Execução de lixeira (base concreto, fechamento alvenaria, laje em concreto armado, grade de fechamento.','und.',1.000,1500.00,1500.00,0,'3.6.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1232,2,'Execução de compressor (base concreto, fechamento alvenaria, laje em concreto armado, grade de fechamento.','und.',1.000,1500.00,1500.00,0,'3.6.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1233,2,'Lixeira, Casa do compressor, pintura ilha e sinalização horizontal','m',35.000,40.00,1400.00,0,'3.6.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1234,2,'Execução de alvenaria para jardim','vb',1.000,800.00,800.00,0,'3.6.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1235,2,'Pintura de piso da ilha','m²',244.300,26.00,6351.80,0,'3.6.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1236,2,'Piso em Concreto Armado/ Calçada externa','un',1.000,0.00,0.00,1,'3.7','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1237,2,'Lançamento de colchão de areia, montagem armação de treliças/barras de transição e telas. Desempenamento e polimento (caso necessário). Junta de dilatação','m²',922.490,35.00,32287.15,0,'3.7.1','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1238,2,'SASC - Sistema Abastecimento Subterrâneo de Combustível','un',1.000,45000.00,45000.00,1,'3.8','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1239,2,'Instalação dos Tanques','und.',2.000,0.00,0.00,0,'3.81','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1240,2,'Instalação das Ilhas e concretagem das bases das ilhas de abastecimento','und.',3.000,0.00,0.00,0,'3.8.2','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1241,2,'Instalação dos Sumps de Bomba','und.',3.000,0.00,0.00,0,'3.8.3','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1242,2,'Escavação e extensão das linhas de combustiveis  (PEAD)','vb',1.000,0.00,0.00,0,'3.8.4','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1243,2,'Instalação de Flexiveis e Conexões','vb',1.000,0.00,0.00,0,'3.8.5','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1244,2,'Instalação de Descarga a distância','und.',4.000,0.00,0.00,0,'3.8.6','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1245,2,'Limpeza dos Tanques para recebimento de combustíveis','und.',2.000,0.00,0.00,0,'3.8.7','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1246,2,'Limpeza dos Tanques para recebimento de combustíveis','und.',3.000,0.00,0.00,0,'3.8.8','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1247,2,'Instalação das Bombas','vb',1.000,0.00,0.00,0,'3.8.9','2026-05-29 02:22:46','2026-05-29 02:22:46'),(1248,2,'BDI','%',1.000,74016.00,74016.00,0,'111','2026-05-29 02:22:46','2026-05-29 02:22:46');
/*!40000 ALTER TABLE `proposta_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `propostas`
--

DROP TABLE IF EXISTS `propostas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `propostas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `obra_id` bigint(20) unsigned NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `escopo` longtext COLLATE utf8mb4_unicode_ci,
  `data_proposta` date DEFAULT NULL,
  `valor_total` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rascunho',
  `arquivo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `propostas_obra_id_foreign` (`obra_id`),
  CONSTRAINT `propostas_obra_id_foreign` FOREIGN KEY (`obra_id`) REFERENCES `obras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `propostas`
--

LOCK TABLES `propostas` WRITE;
/*!40000 ALTER TABLE `propostas` DISABLE KEYS */;
INSERT INTO `propostas` VALUES (1,1,'Construção do Posto','Escopo.','2026-04-27',472000.01,'aceita',NULL,'2026-04-27 18:20:37','2026-04-29 00:47:06'),(2,1,'OBRA POSTO LIMA E NOGUEIRA','Contrução de um Posto de combustivel','2026-05-28',509408.20,'aceita',NULL,'2026-05-28 22:34:52','2026-05-29 02:22:46');
/*!40000 ALTER TABLE `propostas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('6n0R9bT4j7YXGXu4BcT11PkXnmeetkfhfbide55K',5,'190.2.70.163','Mozilla/5.0 (iPhone; CPU iPhone OS 18_6_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Mobile/15E148 Safari/604.1','eyJfdG9rZW4iOiJ3cUp4b0d3U0Q4eWd0UEhOYllIcUtuTkFoY2dxYmU2STI1ZVZmVEtGIiwidXJsIjpbXSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9nZG9pc20uY29tLmJyXC9jb25zdHJ1dGVjXC9kaWFyaW9fb2JyYXNcL29icmFzXC9jcmVhdGUiLCJyb3V0ZSI6Im9icmFzLnNob3cifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6NX0=',1780025173),('dJc30hVh6wz4apGnZFSXCr2nGbwPV5OAbdRKqziS',1,'191.189.2.6','Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.5 Mobile/15E148 Safari/604.1','eyJfdG9rZW4iOiJya0hhc0tJbFhHb2tuYVMzYURXSjYyNVRiTXlFQXlMTmJkMTdMWHRoIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9nZG9pc20uY29tLmJyXC9jb25zdHJ1dGVjXC9kaWFyaW9fb2JyYXNcL29icmFzXC9jcmVhdGUiLCJyb3V0ZSI6Im9icmFzLnNob3cifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJ1cmwiOltdLCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiYWN0aXZlX29icmFfaWQiOjF9',1780025282),('Negy1j1P6b868AgfCYFCL094fyMWDfkr6b3ZgVp2',1,'190.2.70.163','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJqOXA3ZnJhRHQ1NUlrYWRhMmp6QzFKcU5aSzBxRWRjWk5MYXRUbzN6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9nZG9pc20uY29tLmJyXC9jb25zdHJ1dGVjXC9kaWFyaW9fb2JyYXNcL3Byb3Bvc3RhcyIsInJvdXRlIjoicHJvcG9zdGFzLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwidXJsIjpbXSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEsImFjdGl2ZV9vYnJhX2lkIjoxfQ==',1780025332);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('chefe','operador','cliente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cliente',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Cassio Reis','cassio@obrasconstrutec.com','chefe',NULL,'$2y$12$VsbOX9hOVfOIkXak8icf8.sYcblELKg2HME4JxNQOJub4ClE8AIsa','mWsoC6K8vWp9lN4oZVigVeezpU3Lm6bnaSdsgKKX8WUY4v5L6ZYDvHsX0HUV','2026-04-27 12:12:26','2026-04-27 12:12:26'),(2,'Ronaldo Souza','Ronaldosouza.eng@hotmail.com','chefe',NULL,'$2y$12$7NOPzonurzdG43exWzhYFehZjGI7vMPLCgoAK.rKR1C5U6D9FZ7De','Zh6tZw4PChHvKJShKazB7mqNIzKrskyNr1GYZ9AgrL9bhuD9SlDb1DCzxpGR','2026-04-27 18:01:59','2026-04-27 18:01:59'),(3,'Carol Anjo','carol@obrasconstrutec.com','operador',NULL,'$2y$12$8YTcOrMowFSKjRYcTt9i4egAk2nRfhRwYkVxaCBm07gwkQeEaob1K',NULL,'2026-04-28 01:07:11','2026-04-29 14:07:03'),(4,'Edilson','edy@obra.com','cliente',NULL,'$2y$12$Zi1pCNZUfE0btcumnM1NkOjjA2BRy5uIE3Os4dTISm2jb5wrjuH56','qibKNfNC1QE9LcgJMNOpr6Q7KUqveCnSZZrDtJsIJ28DGSDMwRTdiM1mR3uq','2026-04-28 01:08:24','2026-04-28 01:08:24'),(5,'ELISSANDRA','elissandra@obrasconstrutec.com','operador',NULL,'$2y$12$M0x96wuefYPG26XZAjlSiewquOg01bNLSaSqmAcSzX/H62EuSY6yW',NULL,'2026-05-28 22:14:52','2026-05-28 22:14:52');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!50112 SET @disable_bulk_load = IF (@is_rocksdb_supported, 'SET SESSION rocksdb_bulk_load = @old_rocksdb_bulk_load', 'SET @dummy_rocksdb_bulk_load = 0') */;
/*!50112 PREPARE s FROM @disable_bulk_load */;
/*!50112 EXECUTE s */;
/*!50112 DEALLOCATE PREPARE s */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-29  0:46:55
