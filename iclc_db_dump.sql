-- MySQL dump 10.13  Distrib 8.0.43, for Linux (aarch64)
--
-- Host: localhost    Database: iclc_db
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `attempts`
--

DROP TABLE IF EXISTS `attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attempts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `question_id` bigint unsigned NOT NULL,
  `user_answer` text COLLATE utf8mb4_unicode_ci,
  `user_audio_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `score` int DEFAULT NULL,
  `evaluation_meta` json DEFAULT NULL,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `attempts_user_id_foreign` (`user_id`),
  KEY `attempts_question_id_foreign` (`question_id`),
  CONSTRAINT `attempts_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attempts`
--

LOCK TABLES `attempts` WRITE;
/*!40000 ALTER TABLE `attempts` DISABLE KEYS */;
INSERT INTO `attempts` VALUES (1,1,1,'Hello, my name is Bảo.',NULL,1,NULL,NULL,'Xin lỗi con, hệ thống gặp sự cố khi chấm bài. Con thử lại sau nhé.','2025-11-06 03:38:27'),(2,1,1,'Hello, Bảo.',NULL,1,NULL,NULL,'Xin lỗi con, hệ thống gặp sự cố khi chấm bài. Con thử lại sau nhé.','2025-11-06 03:41:31'),(3,1,1,'Hello, Bảo.',NULL,1,NULL,NULL,'Xin lỗi con, hệ thống gặp sự cố khi chấm bài. Con thử lại sau nhé.','2025-11-06 03:41:40'),(4,1,1,'Hello, Bảo.',NULL,1,NULL,NULL,'Xin lỗi con, hệ thống gặp sự cố khi chấm bài. Con thử lại sau nhé.','2025-11-06 03:41:45'),(5,1,1,'Hello,Bảo.',NULL,1,NULL,NULL,'Xin lỗi con, hệ thống gặp sự cố khi chấm bài. Con thử lại sau nhé.','2025-11-06 03:42:06'),(6,1,1,'Hello',NULL,1,NULL,NULL,'Xin lỗi con, hệ thống gặp sự cố khi chấm bài. Con thử lại sau nhé.','2025-11-06 03:44:31'),(7,1,1,'Hello',NULL,1,NULL,NULL,'Con làm rất tốt! Câu trả lời của con đúng rồi. Tiếp tục cố gắng nhé con! 🌟','2025-11-06 03:47:15'),(8,1,1,'Hello, My name is bảo.',NULL,0,NULL,NULL,'Con làm tốt lắm! Con đã nói \'Hello\' rất đúng. Tên \'bảo\' thì mình cần viết hoa chữ cái đầu tiên nhé, thành \'Bảo\' đó con. Tiếp tục cố gắng nha! ✨','2025-11-06 03:47:39'),(9,1,7,'Hello, my name is Bao.',NULL,1,NULL,NULL,'Làm tốt lắm con! 🌟 Câu trả lời của con rất đúng và hay. Tiếp tục phát huy nhé con!','2025-11-06 03:51:17'),(10,1,8,'I am 12 years old.',NULL,1,NULL,NULL,'Con làm tốt lắm! 🌟 Câu trả lời của con rất đúng ngữ pháp và rõ ràng. Con đã trả lời đúng câu hỏi rồi đó!','2025-11-06 03:51:58'),(11,1,1,'Hello',NULL,1,NULL,NULL,'Con làm rất tốt! Câu trả lời \'Hello\' của con hoàn toàn đúng rồi. Tiếp tục cố gắng nhé con! 🌟','2025-11-07 02:24:27');
/*!40000 ALTER TABLE `attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercise_types`
--

DROP TABLE IF EXISTS `exercise_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercise_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exercise_type_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise_types`
--

LOCK TABLES `exercise_types` WRITE;
/*!40000 ALTER TABLE `exercise_types` DISABLE KEYS */;
INSERT INTO `exercise_types` VALUES (1,'Speaking - Word','SPW','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(2,'Speaking - Sentence','SPS','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(3,'Writing - Answer the question','WAQ','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(4,'Writing - Complete the sentence','WCS','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(5,'Writing - Write sentence using the given word','WSG','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL);
/*!40000 ALTER TABLE `exercise_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercises`
--

DROP TABLE IF EXISTS `exercises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercises` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type_id` bigint unsigned NOT NULL,
  `lesson_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instruction` text COLLATE utf8mb4_unicode_ci,
  `difficulty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_index` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercises_type_id_foreign` (`type_id`),
  KEY `exercises_lesson_id_foreign` (`lesson_id`),
  CONSTRAINT `exercises_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `exercises_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `exercise_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercises`
--

LOCK TABLES `exercises` WRITE;
/*!40000 ALTER TABLE `exercises` DISABLE KEYS */;
INSERT INTO `exercises` VALUES (1,3,1,'Bài tập 1','Điền tên vào chỗ trống.','Dễ',NULL,1,'2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(2,2,1,'Bài tập 2','Đọc to và rõ ràng các câu sau:','Dễ',NULL,2,'2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(3,4,1,'Bài tập 3','Hoàn thành các câu sau.','Dễ',NULL,3,'2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(4,3,1,'Bài tập 4','Trả lời các câu hỏi sau.','Dễ',NULL,4,'2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(5,5,1,'Bài tập 5','Sử dụng từ cho sẵn để đặt câu.','Dễ',NULL,5,'2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(6,3,2,'Luyện viết về hoạt động hàng ngày','Trả lời các câu hỏi về thói quen hàng ngày của bạn.','Trung bình',NULL,1,'2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(7,3,3,'Luyện viết về môi trường','Trả lời các câu hỏi về môi trường.','Khó',NULL,1,'2025-11-06 03:34:32','2025-11-06 03:34:32',NULL);
/*!40000 ALTER TABLE `exercises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lessons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `img_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lessons`
--

LOCK TABLES `lessons` WRITE;
/*!40000 ALTER TABLE `lessons` DISABLE KEYS */;
INSERT INTO `lessons` VALUES (1,'Bài 1: Giới thiệu bản thân','Giới thiệu bản thân và chào hỏi cơ bản',NULL,'Easy','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(2,'Bài 2: Hoạt động hàng ngày','Nói về thói quen hàng ngày của bạn',NULL,'Medium','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(3,'Bài 3: Môi trường','Thảo luận về môi trường và thiên nhiên',NULL,'Hard','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL);
/*!40000 ALTER TABLE `lessons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1),(2,'2025_10_17_020311_create_users_table',1),(3,'2025_10_17_020316_create_lessons_table',1),(4,'2025_10_17_020320_create_exercise_type_table',1),(5,'2025_10_17_020328_create_exercises_table',1),(6,'2025_10_17_020336_create_questions_table',1),(7,'2025_10_17_020345_create_attempts_table',1),(8,'2025_10_17_020354_create_progress_table',1),(9,'2025_10_17_020400_create_vocabulary_table',1),(10,'2025_10_17_065255_alter_users_set_default_avatar',1),(11,'2025_10_17_095913_add_timestamps_to_exercise_type_table',1),(12,'2025_10_17_100340_rename_vocabulary_to_vocabularies_table',1),(13,'2025_10_20_034744_rename_exercise_type_to_exercise_types',1),(14,'2025_10_20_041912_drop_prompt_text_from_questions_table',1),(15,'2025_10_20_062346_add_is_active_flags',1),(16,'2025_10_22_064834_add_prompt_text_to_questions_table',1),(17,'2025_10_22_085108_delete_attempt_number_from_attempts_table',1),(18,'2025_10_22_093742_drop_progress_table_and_vocabularies_table',1),(19,'2025_10_27_021755_add_deleted_at_to_tables',1),(20,'2025_10_27_031222_remove_active_column_from_tables',1),(21,'2025_11_05_075735_update_exercise_instructions_add_periods',1),(22,'2025_11_05_081347_update_exercise_1_to_writing_type',1),(23,'2025_11_06_000000_add_score_and_metadata_to_attempts_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exercise_id` bigint unsigned NOT NULL,
  `img_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `audio_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_index` int NOT NULL DEFAULT '0',
  `target_text` text COLLATE utf8mb4_unicode_ci,
  `starter_text` text COLLATE utf8mb4_unicode_ci,
  `prompt_text` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questions_exercise_id_foreign` (`exercise_id`),
  CONSTRAINT `questions_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` VALUES (1,1,NULL,NULL,1,'Hello',NULL,'Hello','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(2,1,NULL,NULL,2,'Name',NULL,'Name','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(3,2,NULL,NULL,1,'What is your name?',NULL,'What is your name?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(4,2,NULL,NULL,2,'How are you?',NULL,'How are you?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(5,3,NULL,NULL,1,'My favorite hobby is playing the piano.','My favorite hobby is','Complete the sentence about your favorite hobby.','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(6,3,NULL,NULL,2,'I live in Hanoi.','I live in','Complete the sentence to tell where you live.','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(7,4,NULL,NULL,1,'My name is Anna.',NULL,'What is your name?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(8,4,NULL,NULL,2,'I am 8 years old.',NULL,'How old are you?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(9,4,NULL,NULL,3,'I live in Hanoi.',NULL,'Where do you live?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(10,4,NULL,NULL,4,'Hello, teacher!',NULL,'Say hello to your teacher.','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(11,4,NULL,NULL,5,'It is 7 o\'clock.',NULL,'What time is it? Answer using the format \'It is [number] o\'clock\'.','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(12,4,NULL,NULL,6,'Today is sunny.',NULL,'Describe the weather today using the format \"Today is ...\".','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(13,5,NULL,NULL,1,'sunny',NULL,'sunny','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(14,5,NULL,NULL,2,'friend',NULL,'friend','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(15,5,NULL,NULL,3,'family',NULL,'family','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(16,6,NULL,NULL,1,'My favorite hobby is reading books.',NULL,'What is your favorite hobby?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(17,6,NULL,NULL,2,'I live in Ho Chi Minh City.',NULL,'Where do you live?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(18,6,NULL,NULL,3,'It is 9 o\'clock.',NULL,'What time do you usually wake up? Answer using the format \'It is [number] o\'clock\'.','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(19,6,NULL,NULL,4,'Today is rainy.',NULL,'Describe the weather today using the format \"Today is ...\".','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(20,7,NULL,NULL,1,'My name is Emma.',NULL,'What is your name?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(21,7,NULL,NULL,2,'I am 10 years old.',NULL,'How old are you?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(22,7,NULL,NULL,3,'My favorite hobby is planting trees.',NULL,'What is your favorite hobby?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(23,7,NULL,NULL,4,'I live in Da Nang.',NULL,'Where do you live?','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(24,7,NULL,NULL,5,'Hello, teacher!',NULL,'Say hello to your teacher.','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(25,7,NULL,NULL,6,'Today is sunny.',NULL,'Describe the weather today using the format \"Today is ...\".','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `avatar_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'avatars/default-avatar.png',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@example.com',NULL,'2000-01-01',NULL,'$2y$10$NnUPry3EVWO5YbFlupNhf.Sc.z0vOV1gloosdHWOQ3DCV0EO/yJV2','admin','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(2,'User 1','user1@example.com',NULL,'2007-05-15',NULL,'$2y$10$sgE1.B37iS7skT3lmW2o/.Z.NHxLAzhkEMDFVOr67WXUb01Ep25rG','user','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL),(3,'User 2','user2@example.com',NULL,'2009-08-22',NULL,'$2y$10$98iql.JzE5X.J0tboZvU9OlSL0MdgrJj6cCU7XL4L7hl1c1O7EE6G','user','2025-11-06 03:34:32','2025-11-06 03:34:32',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-07  3:09:44
