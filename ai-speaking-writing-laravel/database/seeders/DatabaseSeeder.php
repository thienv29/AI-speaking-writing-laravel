<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\{
    User,
    Lesson,
    Exercise,
    ExerciseType,
    Question,
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
                'type_id' => $types->where('code', 'SPW')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 1',
                'instruction' => 'Đọc to và rõ ràng các từ sau:',
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
                'instruction' => 'Hoàn thành các câu sau:',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 3
            ]),
            Exercise::create([
                'type_id' => $types->where('code', 'WAQ')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 4',
                'instruction' => 'Trả lời các câu hỏi sau:',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 4
            ]),
            Exercise::create([
                'type_id' => $types->where('code', 'WSG')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 5',
                'instruction' => 'Sử dụng từ cho sẵn để đặt câu:',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 5
            ]),
        ]);

        //QUESTIONS
        $lessonId = $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id;
        $questions = collect([
            //Bài 1
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 1')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 1,
                'target_text' => 'Hello',
                'prompt_text' => 'Hello',
                'starter_text' => null
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 1')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 2,
                'target_text' => 'Name',
                'prompt_text' => 'Name',
                'starter_text' => null
            ]),

            //Bài 2
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 2')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 1,
                'target_text' => 'What is your name?',
                'prompt_text' => 'What is your name?',
                'starter_text' => null
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 2')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 2,
                'target_text' => 'How are you?',
                'prompt_text' => 'How are you?',
                'starter_text' => null
            ]),

            //Bài 3
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 3')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 1,
                'target_text' => null,
                'prompt_text' => 'I am...',
                'starter_text' => 'I am',
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 3')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 2,
                'target_text' => null,
                'prompt_text' => 'My name...',
                'starter_text' => 'My name',
            ]),

            //Bài 4
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 4')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 1,
                'target_text' => 'Where do you live?',
                'prompt_text' => 'Where do you live?',
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 4')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 2,
                'target_text' => 'How old are you?',
                'prompt_text' => 'How old are you?',
                'starter_text' => null,
            ]),

            //Bài 5
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 5')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 1,
                'target_text' => "Vietnam",
                'prompt_text' => 'Vietnam',
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 5')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 1,
                'target_text' => "love",
                'prompt_text' => 'Love',
                'starter_text' => null,
            ]),
        ]);

        $this->command->info('All tables seeded successfully!');
    }
}
