<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    User,
    Lesson,
    Exercise,
    ExerciseType,
    Question,
    Attempt
};

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        //USERS
        $users = collect([
            User::create([
                'name' => 'Admin', 
                'email' => 'admin@example.com', 
                'password' => Hash::make('12345678'), 
                'dob' => '2000-01-01',
                'role' => 'admin'
            ]),
            User::create([
                'name' => 'User 1', 
                'email' => 'user1@example.com', 
                'password' => Hash::make('12345678'), 
                'role' => 'user',
                'dob' => '2007-05-15'
            ]),
            User::create([
                'name' => 'User 2', 
                'email' => 'user2@example.com', 
                'password' => Hash::make('12345678'), 
                'role' => 'user',
                'dob'=> '2009-08-22'
            ]),
        ]);

        //EXERCISE TYPES
        $types = collect([
            ExerciseType::create([
                'name' => 'Speaking - Word',
                'code' => 'SPW'
            ]),
            ExerciseType::create([
                'name' => 'Speaking - Sentence', 
                'code' => 'SPS'
            ]),
            ExerciseType::create([
                'name' => 'Writing - Answer the question', 
                'code' => 'WAQ'
            ]),
            ExerciseType::create([
                'name' => 'Writing - Complete the sentence', 
                'code' => 'WCS'
            ]),
            ExerciseType::create([
                'name' => 'Writing - Write sentence using the given word',
                'code' => 'WSG'
            ]),
        ]);

        //LESSONS
        $lessons = collect([
            Lesson::create([
                'title' => 'Bài 1: Giới thiệu bản thân',
                'description' => 'Giới thiệu bản thân và chào hỏi cơ bản',
                'img_url' => null,
                'level' => 'Easy'
            ]),
            Lesson::create([
                'title' => 'Bài 2: Hoạt động hàng ngày',
                'description' => 'Nói về thói quen hàng ngày của bạn',
                'img_url' => null,
                'level' => 'Medium'
            ]),
            Lesson::create([
                'title' => 'Bài 3: Môi trường',
                'description' => 'Thảo luận về môi trường và thiên nhiên',
                'img_url' => null,
                'level' => 'Hard'
            ]),
        ]);

        //EXERCISES
        $exercises = collect([
            Exercise::create([
                'type_id' => $types->where('code', 'WAQ')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 1',
                'instruction' => 'Điền tên vào chỗ trống.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 1
            ]),
            Exercise::create([
                'type_id' => $types->where('code', 'SPS')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 2',
                'instruction' => 'Đọc to và rõ ràng các câu sau:',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 2
            ]),
            Exercise::create([
                'type_id' => $types->where('code', 'WCS')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 3',
                'instruction' => 'Hoàn thành các câu sau.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 3
            ]),
            Exercise::create([
                'type_id' => $types->where('code', 'WAQ')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 4',
                'instruction' => 'Trả lời các câu hỏi sau.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 4
            ]),
            Exercise::create([
                'type_id' => $types->where('code', 'WSG')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 5',
                'instruction' => 'Sử dụng từ cho sẵn để đặt câu.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 5
            ]),
        ]);

        //QUESTIONS - Using many-to-many relationship (pivot table)
        $lessonId = $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id;
        
        // Get exercises for Bài 1
        $exercise1 = $exercises->where('lesson_id', $lessonId)->where('title', 'Bài tập 1')->first();
        $exercise2 = $exercises->where('lesson_id', $lessonId)->where('title', 'Bài tập 2')->first();
        $exercise3 = $exercises->where('lesson_id', $lessonId)->where('title', 'Bài tập 3')->first();
        $exercise4 = $exercises->where('lesson_id', $lessonId)->where('title', 'Bài tập 4')->first();
        $exercise5 = $exercises->where('lesson_id', $lessonId)->where('title', 'Bài tập 5')->first();
        
        $questions = collect([]);
        
        // Bài tập 1 - Questions
        $q1_1 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 1,
            'target_text' => 'Hello',
            'prompt_text' => 'Hello',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_1->id, ['order_index' => 1]);
        $questions->push($q1_1);
        
        $q1_2 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 2,
            'target_text' => 'Name',
            'prompt_text' => 'Name',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_2->id, ['order_index' => 2]);
        $questions->push($q1_2);

        // Bài tập 2 - Questions
        $q2_1 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 1,
            'target_text' => 'What is your name?',
            'prompt_text' => 'What is your name?',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_1->id, ['order_index' => 1]);
        $questions->push($q2_1);
        
        $q2_2 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 2,
            'target_text' => 'How are you?',
            'prompt_text' => 'How are you?',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_2->id, ['order_index' => 2]);
        $questions->push($q2_2);

        // Bài tập 3 - Questions
        $q3_1 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 1,
            'target_text' => 'My favorite hobby is playing the piano.',
            'prompt_text' => 'Complete the sentence about your favorite hobby.',
            'starter_text' => 'My favorite hobby is',
        ]);
        $exercise3->questions()->attach($q3_1->id, ['order_index' => 1]);
        $questions->push($q3_1);
        
        $q3_2 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 2,
            'target_text' => 'I live in Hanoi.',
            'prompt_text' => 'Complete the sentence to tell where you live.',
            'starter_text' => 'I live in',
        ]);
        $exercise3->questions()->attach($q3_2->id, ['order_index' => 2]);
        $questions->push($q3_2);

        // Bài tập 4 - Questions
        $q4_1 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 1,
            'target_text' => 'My name is Anna.',
            'prompt_text' => 'What is your name?',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_1->id, ['order_index' => 1]);
        $questions->push($q4_1);
        
        $q4_2 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 2,
            'target_text' => 'I am 8 years old.',
            'prompt_text' => 'How old are you?',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_2->id, ['order_index' => 2]);
        $questions->push($q4_2);
        
        $q4_3 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 3,
            'target_text' => 'I live in Hanoi.',
            'prompt_text' => 'Where do you live?',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_3->id, ['order_index' => 3]);
        $questions->push($q4_3);
        
        $q4_4 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 4,
            'target_text' => 'Hello, teacher!',
            'prompt_text' => 'Say hello to your teacher.',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_4->id, ['order_index' => 4]);
        $questions->push($q4_4);
        
        $q4_5 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 5,
            'target_text' => "It is 7 o'clock.",
            'prompt_text' => "What time is it? Answer using the format 'It is [number] o'clock'.",
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_5->id, ['order_index' => 5]);
        $questions->push($q4_5);
        
        $q4_6 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 6,
            'target_text' => 'Today is sunny.',
            'prompt_text' => 'Describe the weather today using the format "Today is ...".',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_6->id, ['order_index' => 6]);
        $questions->push($q4_6);

        // Bài tập 5 - Questions
        $q5_1 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 1,
            'target_text' => 'sunny',
            'prompt_text' => 'sunny',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_1->id, ['order_index' => 1]);
        $questions->push($q5_1);
        
        $q5_2 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 2,
            'target_text' => 'friend',
            'prompt_text' => 'friend',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_2->id, ['order_index' => 2]);
        $questions->push($q5_2);
        
        $q5_3 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 3,
            'target_text' => 'family',
            'prompt_text' => 'family',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_3->id, ['order_index' => 3]);
        $questions->push($q5_3);

        // Add exercises and questions for Bài 2: Hoạt động hàng ngày
        $lesson2Id = $lessons->where('title', 'Bài 2: Hoạt động hàng ngày')->first()->id;
        
        $exerciseLesson2 = Exercise::create([
            'type_id' => $types->where('code', 'WAQ')->first()->id,
            'lesson_id' => $lesson2Id,
            'title' => 'Luyện viết về hoạt động hàng ngày',
            'instruction' => 'Trả lời các câu hỏi về thói quen hàng ngày của bạn.',
            'difficulty' => 'Trung bình',
            'img_url' => null,
            'order_index' => 1
        ]);

        $exercises->push($exerciseLesson2);

        // Questions for Bài 2
        $qLesson2_1 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 1,
            'target_text' => 'My favorite hobby is reading books.',
            'prompt_text' => 'What is your favorite hobby?',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_1->id, ['order_index' => 1]);
        $questions->push($qLesson2_1);
        
        $qLesson2_2 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 2,
            'target_text' => 'I live in Ho Chi Minh City.',
            'prompt_text' => 'Where do you live?',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_2->id, ['order_index' => 2]);
        $questions->push($qLesson2_2);
        
        $qLesson2_3 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 3,
            'target_text' => "It is 9 o'clock.",
            'prompt_text' => "What time do you usually wake up? Answer using the format 'It is [number] o'clock'.",
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_3->id, ['order_index' => 3]);
        $questions->push($qLesson2_3);
        
        $qLesson2_4 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 4,
            'target_text' => 'Today is rainy.',
            'prompt_text' => 'Describe the weather today using the format "Today is ...".',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_4->id, ['order_index' => 4]);
        $questions->push($qLesson2_4);

        // Add exercises and questions for Bài 3: Môi trường
        $lesson3Id = $lessons->where('title', 'Bài 3: Môi trường')->first()->id;
        
        $exerciseLesson3 = Exercise::create([
            'type_id' => $types->where('code', 'WAQ')->first()->id,
            'lesson_id' => $lesson3Id,
            'title' => 'Luyện viết về môi trường',
            'instruction' => 'Trả lời các câu hỏi về môi trường.',
            'difficulty' => 'Khó',
            'img_url' => null,
            'order_index' => 1
        ]);

        $exercises->push($exerciseLesson3);

        // Questions for Bài 3
        $qLesson3_1 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 1,
            'target_text' => 'My name is Emma.',
            'prompt_text' => 'What is your name?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_1->id, ['order_index' => 1]);
        $questions->push($qLesson3_1);
        
        $qLesson3_2 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 2,
            'target_text' => 'I am 10 years old.',
            'prompt_text' => 'How old are you?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_2->id, ['order_index' => 2]);
        $questions->push($qLesson3_2);
        
        $qLesson3_3 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 3,
            'target_text' => 'My favorite hobby is planting trees.',
            'prompt_text' => 'What is your favorite hobby?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_3->id, ['order_index' => 3]);
        $questions->push($qLesson3_3);
        
        $qLesson3_4 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 4,
            'target_text' => 'I live in Da Nang.',
            'prompt_text' => 'Where do you live?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_4->id, ['order_index' => 4]);
        $questions->push($qLesson3_4);
        
        $qLesson3_5 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 5,
            'target_text' => 'Hello, teacher!',
            'prompt_text' => 'Say hello to your teacher.',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_5->id, ['order_index' => 5]);
        $questions->push($qLesson3_5);
        
        $qLesson3_6 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 6,
            'target_text' => 'Today is sunny.',
            'prompt_text' => 'Describe the weather today using the format "Today is ...".',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_6->id, ['order_index' => 6]);
        $questions->push($qLesson3_6);

        $this->command->info('All tables seeded successfully!');
    }
}
