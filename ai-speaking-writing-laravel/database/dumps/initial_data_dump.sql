-- Initial data dump generated on 2025-11-21
-- This dump contains the current production data being used
SET FOREIGN_KEY_CHECKS=0;

TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `name`, `email`, `avatar`, `dob`, `gender`, `password`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'admin@example.com', NULL, '2000-01-01', NULL, '$2y$10$VYowfZoNOa3oJHewI7zJqePFra98QzJ0PQunArIsO2MN02AmX/LsC', 'admin', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(2, 'Học sinh A', 'student1@example.com', NULL, '2010-05-05', NULL, '$2y$10$rweXraasJlSN72NGDPGpHes/Hotk4noSh.SOVGfg33df231hnDzIu', 'user', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(3, 'Học sinh B', 'student2@example.com', NULL, '2011-08-12', NULL, '$2y$10$uR6/hZXvhTPtRefVJR0Z3e2UeB30vzShjPy9MTwm1F2aQkxvhuutK', 'user', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL);

TRUNCATE TABLE `exercise_types`;
INSERT INTO `exercise_types` (`id`, `name`, `code`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Speaking - Word', 'SPW', NULL, NULL, NULL),
(2, 'Speaking - Sentence', 'SPS', NULL, NULL, NULL),
(3, 'Writing - Answer the question', 'WAQ', NULL, NULL, NULL),
(4, 'Writing - Complete the sentence', 'WCS', NULL, NULL, NULL),
(5, 'Writing - Word to sentence', 'WSG', NULL, NULL, NULL);

TRUNCATE TABLE `lessons`;
INSERT INTO `lessons` (`id`, `title`, `description`, `img_url`, `level`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Speaking: Word Practice', 'Luyện nói từng từ rõ ràng, trọng âm chuẩn.', NULL, 'Easy', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(2, 'Speaking: Sentence Practice', 'Luyện nói câu hoàn chỉnh, diễn đạt tự nhiên.', NULL, 'Medium', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(3, 'Writing: Answer the Question', 'Trả lời câu hỏi mở bằng câu hoàn chỉnh.', NULL, 'Medium', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(4, 'Writing: Complete the Sentence', 'Hoàn thành câu dựa trên gợi ý cho trước.', NULL, 'Easy', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(5, 'Writing: Word to Sentence', 'Nhìn từ gợi ý và đặt câu hoàn chỉnh.', NULL, 'Easy', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL);

TRUNCATE TABLE `exercises`;
INSERT INTO `exercises` (`id`, `lesson_id`, `type_id`, `title`, `instruction`, `difficulty`, `img_url`, `order_index`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Speaking - Word Practice', 'Phát âm rõ từng từ, giữ tốc độ ổn định.', 'Easy', NULL, 1, '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(2, 2, 2, 'Speaking - Sentence Practice', 'Đọc câu trọn vẹn với ngữ điệu tự nhiên.', 'Medium', NULL, 1, '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(3, 3, 3, 'Writing - Answer the Question', 'Trả lời câu hỏi bằng câu rõ ràng, có chủ ngữ vị ngữ.', 'Medium', NULL, 1, '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(4, 4, 4, 'Writing - Complete the Sentence', 'Hoàn chỉnh câu dựa trên gợi ý đầu câu cho trước.', 'Easy', NULL, 1, '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(5, 5, 5, 'Writing - Word to Sentence', 'Nhìn từ khóa và viết thành câu hoàn chỉnh.', 'Easy', NULL, 1, '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL);

TRUNCATE TABLE `questions`;
INSERT INTO `questions` (`id`, `exercise_id`, `img_url`, `audio_url`, `order_index`, `target_text`, `prompt_text`, `starter_text`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRM8TDSjkfhlJUk44HqxJtTsEBJD1eW2uOV4A&s', NULL, 1, 'apple', NULL, 'apple', '2025-11-19 09:15:23', '2025-11-20 09:42:24', NULL),
(2, 1, NULL, NULL, 2, 'family', NULL, 'family', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(3, 1, NULL, NULL, 3, 'school', NULL, 'school', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(4, 1, NULL, NULL, 4, 'holiday', NULL, 'holiday', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(5, 1, NULL, NULL, 5, 'teacher', NULL, 'teacher', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(6, 1, NULL, NULL, 6, 'ocean', NULL, 'ocean', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(7, 1, NULL, NULL, 7, 'healthy', NULL, 'healthy', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(8, 1, NULL, NULL, 8, 'future', NULL, 'future', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(9, 1, NULL, NULL, 9, 'sunshine', NULL, 'sunshine', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(10, 1, NULL, NULL, 10, 'friendship', NULL, 'friendship', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(11, 2, NULL, NULL, 1, 'I like studying English every day.', NULL, 'I like studying English every day.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(12, 2, NULL, NULL, 2, 'My best friend lives near my house.', NULL, 'My best friend lives near my house.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(13, 2, NULL, NULL, 3, 'We usually have dinner at seven.', NULL, 'We usually have dinner at seven.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(14, 2, NULL, NULL, 4, 'Please bring your notebook to class.', NULL, 'Please bring your notebook to class.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(15, 2, NULL, NULL, 5, 'Reading books makes me happy.', NULL, 'Reading books makes me happy.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(16, 2, NULL, NULL, 6, 'I want to be a doctor in the future.', NULL, 'I want to be a doctor in the future.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(17, 2, NULL, NULL, 7, 'My parents always support me.', NULL, 'My parents always support me.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(18, 2, NULL, NULL, 8, 'The weather is beautiful today.', NULL, 'The weather is beautiful today.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(19, 2, NULL, NULL, 9, 'Learning new words is exciting.', NULL, 'Learning new words is exciting.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(20, 2, NULL, NULL, 10, 'I practice speaking English loudly.', NULL, 'I practice speaking English loudly.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(21, 3, NULL, NULL, 1, 'My favorite hobby is reading comics.', NULL, 'What is your favorite hobby?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(22, 3, NULL, NULL, 2, 'I live with my parents and my brother.', NULL, 'Who do you live with?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(23, 3, NULL, NULL, 3, 'I usually wake up at six thirty.', NULL, 'When do you usually wake up?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(24, 3, NULL, NULL, 4, 'I often eat bread and drink milk for breakfast.', NULL, 'What do you eat for breakfast?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(25, 3, NULL, NULL, 5, 'I go to school by bicycle every day.', NULL, 'How do you go to school?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(26, 3, NULL, NULL, 6, 'I like English the most because it is fun.', NULL, 'What subject do you like the most?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(27, 3, NULL, NULL, 7, 'My classroom is bright and has twenty desks.', NULL, 'Describe your classroom.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(28, 3, NULL, NULL, 8, 'After school I do homework then play sports.', NULL, 'What do you do after school?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(29, 3, NULL, NULL, 9, 'My favorite teacher is Ms. Hoa because she is kind.', NULL, 'Who is your favorite teacher?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(30, 3, NULL, NULL, 10, 'This year I want to learn better pronunciation.', NULL, 'What do you want to learn this year?', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(31, 4, NULL, NULL, 1, 'Every morning I brush my teeth and eat breakfast.', 'Every morning I', 'Complete the sentence about your morning routine.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(32, 4, NULL, NULL, 2, 'In the future I want to travel around the world.', 'In the future I want to', 'Complete to talk about your dream.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(33, 4, NULL, NULL, 3, 'My family is small but very happy.', 'My family is', 'Complete about your family.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(34, 4, NULL, NULL, 4, 'My best friend is friendly and helpful.', 'My best friend is', 'Describe your best friend.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(35, 4, NULL, NULL, 5, 'On the weekend I visit my grandparents.', 'On the weekend I', 'Complete about your weekend.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(36, 4, NULL, NULL, 6, 'My favorite food is pho because it tastes great.', 'My favorite food is', 'Complete about your favorite food.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(37, 4, NULL, NULL, 7, 'Today the weather is cool and windy.', 'Today the weather is', 'Complete about the weather.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(38, 4, NULL, NULL, 8, 'This week I will finish all my homework early.', 'This week I will', 'Complete about your study plan.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(39, 4, NULL, NULL, 9, 'My city is modern with many tall buildings.', 'My city is', 'Complete about your city.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(40, 4, NULL, NULL, 10, 'My school has a big library and a garden.', 'My school has', 'Complete about your school.', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(41, 5, NULL, NULL, 1, 'library', NULL, 'library', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(42, 5, NULL, NULL, 2, 'basketball', NULL, 'basketball', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(43, 5, NULL, NULL, 3, 'birthday', NULL, 'birthday', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(44, 5, NULL, NULL, 4, 'holiday', NULL, 'holiday', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(45, 5, NULL, NULL, 5, 'computer', NULL, 'computer', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(46, 5, NULL, NULL, 6, 'music', NULL, 'music', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(47, 5, NULL, NULL, 7, 'garden', NULL, 'garden', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(48, 5, NULL, NULL, 8, 'river', NULL, 'river', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(49, 5, NULL, NULL, 9, 'picture', NULL, 'picture', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL),
(50, 5, NULL, NULL, 10, 'friendship', NULL, 'friendship', '2025-11-19 09:15:23', '2025-11-19 09:15:23', NULL);

SET FOREIGN_KEY_CHECKS=1;