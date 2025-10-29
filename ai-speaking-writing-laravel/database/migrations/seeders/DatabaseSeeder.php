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
    Attempt,
    Progress,
    Vocabulary
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
                'target_text' => 'My favorite hobby is playing the piano.',
                'prompt_text' => 'Complete the sentence about your favorite hobby.',
                'starter_text' => 'My favorite hobby is',
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 3')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 2,
                'target_text' => 'I live in Hanoi.',
                'prompt_text' => 'Complete the sentence to tell where you live.',
                'starter_text' => 'I live in',
            ]),

            //Bài 4
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 4')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 1,
                'target_text' => 'My name is Anna.',
                'prompt_text' => 'What is your name?',
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 4')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 2,
                'target_text' => 'I am 8 years old.',
                'prompt_text' => 'How old are you?',
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 4')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 3,
                'target_text' => 'I live in Hanoi.',
                'prompt_text' => 'Where do you live?',
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 4')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 4,
                'target_text' => 'Hello, teacher!',
                'prompt_text' => 'Say hello to your teacher.',
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 4')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 5,
                'target_text' => "It is 7 o'clock.",
                'prompt_text' => "What time is it? Answer using the format 'It is [number] o'clock'.",
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 4')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 6,
                'target_text' => 'Today is sunny.',
                'prompt_text' => 'Describe the weather today using the format "Today is ...".',
                'starter_text' => null,
            ]),

            //Bài 5
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 5')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 1,
                'target_text' => 'sunny',
                'prompt_text' => 'sunny',
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 5')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 2,
                'target_text' => 'friend',
                'prompt_text' => 'friend',
                'starter_text' => null,
            ]),
            Question::create([
                'exercise_id' => $exercises->where('lesson_id', $lessonId)
                    ->where('title', 'Bài tập 5')->first()->id,
                'img_url' => null,
                'audio_url' => null,
                'order_index' => 3,
                'target_text' => 'family',
                'prompt_text' => 'family',
                'starter_text' => null,
            ]),
        ]);

        //VOCABULARY
        $words = [
            ['word' => 'hello', 'phonetic' => '/həˈləʊ/', 'meaning' => 'xin chào', 'note' => 'Một câu chào hỏi cơ bản.'],
            ['word' => 'name', 'phonetic' => '/neɪm/', 'meaning' => 'tên', 'note' => null],
            ['word' => 'Vietnam', 'phonetic' => '/ˈviːɛtnæm/', 'meaning' => 'Việt Nam', 'note' => null],
            ['word' => 'love', 'phonetic' => '/lʌv/', 'meaning' => 'yêu', 'note' => null],
        ];

        foreach ($words as $w) {
            Vocabulary::create($w);
        }

        $this->command->info('All tables seeded successfully!');
    }
}
