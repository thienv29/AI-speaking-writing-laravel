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
                'type_id' => $types->where('name', 'Speaking - Word')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 1: Bắt đầu nào!',
                'instruction' => 'Nói thật to và rõ các từ sau.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 1
            ]),
            Exercise::create([
                'type_id' => $types->where('name', 'Speaking - Sentence')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 2: Bạn là ai?',
                'instruction' => 'Nói thật to và rõ các câu sau.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 2
            ]),
            Exercise::create([
                'type_id' => $types->where('name', 'Writing - Complete the sentence')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 3: Hãy giới thiệu nào',
                'instruction' => 'Hoàn thành các câu sau.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 3
            ]),
            Exercise::create([
                'type_id' => $types->where('name', 'Writing - Answer the question')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 4: Làm quen nha',
                'instruction' => 'Trả lời các câu hỏi sau',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 4
            ]),
            Exercise::create([
                'type_id' => $types->where('name', 'Writing - Complete the sentence')->first()->id,
                'lesson_id' => $lessons->where('title', 'Bài 1: Giới thiệu bản thân')->first()->id,
                'title' => 'Bài tập 3: Hãy giới thiệu nào?',
                'instruction' => 'Hoàn thành các câu sau.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 3
            ]),
        ]);

        // ❓ QUESTIONS
        $questions = collect();
        foreach ($exercises as $exercise) {
            for ($i = 1; $i <= 3; $i++) {
                $questions->push(Question::create([
                    'exercise_id' => $exercise->id,
                    'order_index' => $i,
                    'prompt_text' => "Question {$i} for {$exercise->title}",
                    'target_text' => "Expected answer {$i}",
                    'starter_text' => "Starter {$i}",
                    'img_url' => null,
                    'audio_url' => null
                ]));
            }
        }

        // 🎙️ ATTEMPTS
        foreach ($users as $user) {
            foreach ($questions->take(5) as $question) {
                Attempt::create([
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                    'attempt_number' => 1,
                    'user_answer' => "Answer by {$user->name}",
                    'is_correct' => rand(0, 1),
                    'feedback' => 'Good try!'
                ]);
            }
        }

        // 📈 PROGRESS
        foreach ($users as $user) {
            foreach ($lessons as $lesson) {
                Progress::create([
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                    'status' => rand(0, 1) ? 'completed' : 'in_progress',
                    'complete_at' => now()->subDays(rand(0, 5)),
                    'time_spent' => rand(10, 60)
                ]);
            }
        }

        //VOCABULARY
        $words = [
            ['word' => 'hello', 'phonetic' => '/həˈləʊ/', 'meaning' => 'xin chào', 'note' => 'used for greeting'],
            ['word' => 'study', 'phonetic' => '/ˈstʌdi/', 'meaning' => 'học', 'note' => 'used in education context'],
            ['word' => 'practice', 'phonetic' => '/ˈpræktɪs/', 'meaning' => 'luyện tập', 'note' => 'use in speaking/writing'],
            ['word' => 'teacher', 'phonetic' => '/ˈtiːtʃə(r)/', 'meaning' => 'giáo viên', 'note' => 'person who teaches'],
            ['word' => 'lesson', 'phonetic' => '/ˈlesn/', 'meaning' => 'bài học', 'note' => 'part of a course']
        ];

        foreach ($words as $w) {
            Vocabulary::create($w);
        }

        $this->command->info('All tables seeded successfully!');
    }
}
