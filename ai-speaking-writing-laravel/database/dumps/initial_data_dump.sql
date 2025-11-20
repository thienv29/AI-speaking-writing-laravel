-- Initial data dump generated on $(date +%Y-%m-%d)
SET FOREIGN_KEY_CHECKS=0;

TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `dob`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$lus1FXc22x30edZtCByDnO1St1m3RGz9m7VLx5UjmO3NBRR8r4SJq', 'admin', '2000-01-01', NOW(), NOW()),
(2, 'User 1', 'user1@example.com', '$2y$10$lus1FXc22x30edZtCByDnO1St1m3RGz9m7VLx5UjmO3NBRR8r4SJq', 'user', '2007-05-15', NOW(), NOW()),
(3, 'User 2', 'user2@example.com', '$2y$10$lus1FXc22x30edZtCByDnO1St1m3RGz9m7VLx5UjmO3NBRR8r4SJq', 'user', '2009-08-22', NOW(), NOW());

TRUNCATE TABLE `exercise_types`;
INSERT INTO `exercise_types` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Speaking - Word', 'SPW', NOW(), NOW()),
(2, 'Speaking - Sentence', 'SPS', NOW(), NOW()),
(3, 'Writing - Answer the question', 'WAQ', NOW(), NOW()),
(4, 'Writing - Complete the sentence', 'WCS', NOW(), NOW()),
(5, 'Writing - Write sentence using the given word', 'WSG', NOW(), NOW());

TRUNCATE TABLE `lessons`;
INSERT INTO `lessons` (`id`, `title`, `description`, `img_url`, `level`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Bài 1: Giới thiệu bản thân', 'Giới thiệu bản thân và chào hỏi cơ bản', NULL, 'Easy', NOW(), NOW(), NULL),
(2, 'Bài 2: Hoạt động hàng ngày', 'Nói về thói quen hàng ngày của bạn', NULL, 'Medium', NOW(), NOW(), NULL),
(3, 'Bài 3: Môi trường', 'Thảo luận về môi trường và thiên nhiên', NULL, 'Hard', NOW(), NOW(), NULL);

TRUNCATE TABLE `exercises`;
INSERT INTO `exercises` (`id`, `lesson_id`, `type_id`, `title`, `instruction`, `difficulty`, `img_url`, `order_index`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Bài tập 1', 'Điền tên vào chỗ trống.', 'Dễ', NULL, 1, NOW(), NOW()),
(2, 1, 2, 'Bài tập 2', 'Đọc to và rõ ràng các câu sau:', 'Dễ', NULL, 2, NOW(), NOW()),
(3, 1, 4, 'Bài tập 3', 'Hoàn thành các câu sau.', 'Dễ', NULL, 3, NOW(), NOW()),
(4, 1, 3, 'Bài tập 4', 'Trả lời các câu hỏi sau.', 'Dễ', NULL, 4, NOW(), NOW()),
(5, 1, 5, 'Bài tập 5', 'Sử dụng từ cho sẵn để đặt câu.', 'Dễ', NULL, 5, NOW(), NOW());

TRUNCATE TABLE `questions`;
INSERT INTO `questions` (`id`, `exercise_id`, `img_url`, `audio_url`, `order_index`, `target_text`, `prompt_text`, `starter_text`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 1, 'Hello', 'Hello', NULL, NOW(), NOW()),
(2, 1, NULL, NULL, 2, 'Name', 'Name', NULL, NOW(), NOW()),
(3, 2, NULL, NULL, 1, 'What is your name?', 'What is your name?', NULL, NOW(), NOW()),
(4, 2, NULL, NULL, 2, 'How are you?', 'How are you?', NULL, NOW(), NOW()),
(5, 3, NULL, NULL, 1, 'My favorite hobby is playing the piano.', 'Complete the sentence about your favorite hobby.', 'My favorite hobby is', NOW(), NOW()),
(6, 3, NULL, NULL, 2, 'I like to read books in my free time.', 'Complete the sentence about your free time activity.', 'I like to', NOW(), NOW()),
(7, 4, NULL, NULL, 1, 'I live in Ho Chi Minh City.', 'Where do you live?', 'I live in', NOW(), NOW()),
(8, 4, NULL, NULL, 2, 'I have one younger brother.', 'How many siblings do you have?', 'I have', NOW(), NOW()),
(9, 5, NULL, NULL, 1, 'I like singing', 'Use the word "singing" in a sentence.', NULL, NOW(), NOW()),
(10, 5, NULL, NULL, 2, 'I like dancing', 'Use the word "dancing" in a sentence.', NULL, NOW(), NOW());

SET FOREIGN_KEY_CHECKS=1;
