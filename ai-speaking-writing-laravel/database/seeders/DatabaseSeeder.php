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
        
        // Get exercises for Bài 1 - use fresh query to ensure we get the models with relationships loaded
        $exercise1 = Exercise::where('lesson_id', $lessonId)->where('title', 'Bài tập 1')->first();
        $exercise2 = Exercise::where('lesson_id', $lessonId)->where('title', 'Bài tập 2')->first();
        $exercise3 = Exercise::where('lesson_id', $lessonId)->where('title', 'Bài tập 3')->first();
        $exercise4 = Exercise::where('lesson_id', $lessonId)->where('title', 'Bài tập 4')->first();
        $exercise5 = Exercise::where('lesson_id', $lessonId)->where('title', 'Bài tập 5')->first();
        
        // Validate exercises exist
        if (!$exercise1 || !$exercise2 || !$exercise3 || !$exercise4 || !$exercise5) {
            throw new \Exception('One or more exercises not found. Exercise IDs: ' . 
                ($exercise1 ? $exercise1->id : 'null') . ', ' .
                ($exercise2 ? $exercise2->id : 'null') . ', ' .
                ($exercise3 ? $exercise3->id : 'null') . ', ' .
                ($exercise4 ? $exercise4->id : 'null') . ', ' .
                ($exercise5 ? $exercise5->id : 'null'));
        }
        
        $questions = collect([]);
        
        // Bài tập 1 - Questions (SPW - Speaking Word) - 10 questions
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
        
        $q1_3 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 3,
            'target_text' => 'School',
            'prompt_text' => 'School',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_3->id, ['order_index' => 3]);
        $questions->push($q1_3);
        
        $q1_4 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 4,
            'target_text' => 'Teacher',
            'prompt_text' => 'Teacher',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_4->id, ['order_index' => 4]);
        $questions->push($q1_4);
        
        $q1_5 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 5,
            'target_text' => 'Friend',
            'prompt_text' => 'Friend',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_5->id, ['order_index' => 5]);
        $questions->push($q1_5);
        
        $q1_6 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 6,
            'target_text' => 'Family',
            'prompt_text' => 'Family',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_6->id, ['order_index' => 6]);
        $questions->push($q1_6);
        
        $q1_7 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 7,
            'target_text' => 'Apple',
            'prompt_text' => 'Apple',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_7->id, ['order_index' => 7]);
        $questions->push($q1_7);
        
        $q1_8 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 8,
            'target_text' => 'Book',
            'prompt_text' => 'Book',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_8->id, ['order_index' => 8]);
        $questions->push($q1_8);
        
        $q1_9 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 9,
            'target_text' => 'Pencil',
            'prompt_text' => 'Pencil',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_9->id, ['order_index' => 9]);
        $questions->push($q1_9);
        
        $q1_10 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 10,
            'target_text' => 'Table',
            'prompt_text' => 'Table',
            'starter_text' => null
        ]);
        $exercise1->questions()->attach($q1_10->id, ['order_index' => 10]);
        $questions->push($q1_10);

        // Bài tập 2 - Questions (SPS - Speaking Sentence) - 10 questions
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
        
        $q2_3 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 3,
            'target_text' => 'Nice to meet you.',
            'prompt_text' => 'Nice to meet you.',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_3->id, ['order_index' => 3]);
        $questions->push($q2_3);
        
        $q2_4 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 4,
            'target_text' => 'I am fine, thank you.',
            'prompt_text' => 'I am fine, thank you.',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_4->id, ['order_index' => 4]);
        $questions->push($q2_4);
        
        $q2_5 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 5,
            'target_text' => 'Good morning, teacher.',
            'prompt_text' => 'Good morning, teacher.',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_5->id, ['order_index' => 5]);
        $questions->push($q2_5);
        
        $q2_6 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 6,
            'target_text' => 'See you later.',
            'prompt_text' => 'See you later.',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_6->id, ['order_index' => 6]);
        $questions->push($q2_6);
        
        $q2_7 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 7,
            'target_text' => 'Have a nice day.',
            'prompt_text' => 'Have a nice day.',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_7->id, ['order_index' => 7]);
        $questions->push($q2_7);
        
        $q2_8 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 8,
            'target_text' => 'I love my family.',
            'prompt_text' => 'I love my family.',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_8->id, ['order_index' => 8]);
        $questions->push($q2_8);
        
        $q2_9 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 9,
            'target_text' => 'This is my school.',
            'prompt_text' => 'This is my school.',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_9->id, ['order_index' => 9]);
        $questions->push($q2_9);
        
        $q2_10 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 10,
            'target_text' => 'Thank you very much.',
            'prompt_text' => 'Thank you very much.',
            'starter_text' => null
        ]);
        $exercise2->questions()->attach($q2_10->id, ['order_index' => 10]);
        $questions->push($q2_10);

        // Bài tập 3 - Questions (WCS - Writing Complete Sentence) - 10 questions
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
        
        $q3_3 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 3,
            'target_text' => 'I go to school at 7 o\'clock.',
            'prompt_text' => 'Complete the sentence about what time you go to school.',
            'starter_text' => 'I go to school at',
        ]);
        $exercise3->questions()->attach($q3_3->id, ['order_index' => 3]);
        $questions->push($q3_3);
        
        $q3_4 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 4,
            'target_text' => 'My favorite color is blue.',
            'prompt_text' => 'Complete the sentence about your favorite color.',
            'starter_text' => 'My favorite color is',
        ]);
        $exercise3->questions()->attach($q3_4->id, ['order_index' => 4]);
        $questions->push($q3_4);
        
        $q3_5 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 5,
            'target_text' => 'I like to read books.',
            'prompt_text' => 'Complete the sentence about what you like to do.',
            'starter_text' => 'I like to',
        ]);
        $exercise3->questions()->attach($q3_5->id, ['order_index' => 5]);
        $questions->push($q3_5);
        
        $q3_6 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 6,
            'target_text' => 'Today is Monday.',
            'prompt_text' => 'Complete the sentence about what day it is today.',
            'starter_text' => 'Today is',
        ]);
        $exercise3->questions()->attach($q3_6->id, ['order_index' => 6]);
        $questions->push($q3_6);
        
        $q3_7 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 7,
            'target_text' => 'I have two brothers.',
            'prompt_text' => 'Complete the sentence about your family.',
            'starter_text' => 'I have',
        ]);
        $exercise3->questions()->attach($q3_7->id, ['order_index' => 7]);
        $questions->push($q3_7);
        
        $q3_8 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 8,
            'target_text' => 'I eat breakfast in the morning.',
            'prompt_text' => 'Complete the sentence about when you eat breakfast.',
            'starter_text' => 'I eat breakfast',
        ]);
        $exercise3->questions()->attach($q3_8->id, ['order_index' => 8]);
        $questions->push($q3_8);
        
        $q3_9 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 9,
            'target_text' => 'I play soccer with my friends.',
            'prompt_text' => 'Complete the sentence about playing sports.',
            'starter_text' => 'I play soccer',
        ]);
        $exercise3->questions()->attach($q3_9->id, ['order_index' => 9]);
        $questions->push($q3_9);
        
        $q3_10 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 10,
            'target_text' => 'My teacher is very kind.',
            'prompt_text' => 'Complete the sentence about your teacher.',
            'starter_text' => 'My teacher is',
        ]);
        $exercise3->questions()->attach($q3_10->id, ['order_index' => 10]);
        $questions->push($q3_10);

        // Bài tập 4 - Questions (WAQ - Writing Answer Question) - 10 questions
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
        
        $q4_7 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 7,
            'target_text' => 'I like playing soccer.',
            'prompt_text' => 'What is your favorite sport?',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_7->id, ['order_index' => 7]);
        $questions->push($q4_7);
        
        $q4_8 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 8,
            'target_text' => 'I go to school by bus.',
            'prompt_text' => 'How do you go to school?',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_8->id, ['order_index' => 8]);
        $questions->push($q4_8);
        
        $q4_9 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 9,
            'target_text' => 'I have breakfast at 7 AM.',
            'prompt_text' => 'What time do you have breakfast?',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_9->id, ['order_index' => 9]);
        $questions->push($q4_9);
        
        $q4_10 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 10,
            'target_text' => 'My favorite color is blue.',
            'prompt_text' => 'What is your favorite color?',
            'starter_text' => null,
        ]);
        $exercise4->questions()->attach($q4_10->id, ['order_index' => 10]);
        $questions->push($q4_10);

        // Bài tập 5 - Questions (WSG - Writing Word to Sentence) - 10 questions
        $q5_1 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 1,
            'target_text' => 'Today is sunny.',
            'prompt_text' => 'sunny',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_1->id, ['order_index' => 1]);
        $questions->push($q5_1);
        
        $q5_2 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 2,
            'target_text' => 'My friend is very kind.',
            'prompt_text' => 'friend',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_2->id, ['order_index' => 2]);
        $questions->push($q5_2);
        
        $q5_3 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 3,
            'target_text' => 'I love my family.',
            'prompt_text' => 'family',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_3->id, ['order_index' => 3]);
        $questions->push($q5_3);
        
        $q5_4 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 4,
            'target_text' => 'I go to school every day.',
            'prompt_text' => 'school',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_4->id, ['order_index' => 4]);
        $questions->push($q5_4);
        
        $q5_5 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 5,
            'target_text' => 'My teacher is nice.',
            'prompt_text' => 'teacher',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_5->id, ['order_index' => 5]);
        $questions->push($q5_5);
        
        $q5_6 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 6,
            'target_text' => 'I like to read books.',
            'prompt_text' => 'books',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_6->id, ['order_index' => 6]);
        $questions->push($q5_6);
        
        $q5_7 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 7,
            'target_text' => 'I eat an apple for lunch.',
            'prompt_text' => 'apple',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_7->id, ['order_index' => 7]);
        $questions->push($q5_7);
        
        $q5_8 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 8,
            'target_text' => 'I play soccer in the park.',
            'prompt_text' => 'soccer',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_8->id, ['order_index' => 8]);
        $questions->push($q5_8);
        
        $q5_9 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 9,
            'target_text' => 'I have a pet dog.',
            'prompt_text' => 'dog',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_9->id, ['order_index' => 9]);
        $questions->push($q5_9);
        
        $q5_10 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 10,
            'target_text' => 'I watch TV in the evening.',
            'prompt_text' => 'TV',
            'starter_text' => null,
        ]);
        $exercise5->questions()->attach($q5_10->id, ['order_index' => 10]);
        $questions->push($q5_10);

        // Add exercises and questions for Bài 2: Hoạt động hàng ngày
        $lesson2Id = $lessons->where('title', 'Bài 2: Hoạt động hàng ngày')->first()->id;
        
        $exerciseLesson2 = Exercise::where('lesson_id', $lesson2Id)->where('title', 'Luyện viết về hoạt động hàng ngày')->first();
        
        if (!$exerciseLesson2) {
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
        }

        // Questions for Bài 2 (WAQ) - 10 questions
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
        
        $qLesson2_5 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 5,
            'target_text' => 'I brush my teeth every morning.',
            'prompt_text' => 'What do you do every morning?',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_5->id, ['order_index' => 5]);
        $questions->push($qLesson2_5);
        
        $qLesson2_6 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 6,
            'target_text' => 'I have lunch at 12 o\'clock.',
            'prompt_text' => 'What time do you have lunch?',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_6->id, ['order_index' => 6]);
        $questions->push($qLesson2_6);
        
        $qLesson2_7 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 7,
            'target_text' => 'I do my homework in the afternoon.',
            'prompt_text' => 'When do you do your homework?',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_7->id, ['order_index' => 7]);
        $questions->push($qLesson2_7);
        
        $qLesson2_8 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 8,
            'target_text' => 'I watch TV in the evening.',
            'prompt_text' => 'What do you do in the evening?',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_8->id, ['order_index' => 8]);
        $questions->push($qLesson2_8);
        
        $qLesson2_9 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 9,
            'target_text' => 'I go to bed at 9 PM.',
            'prompt_text' => 'What time do you go to bed?',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_9->id, ['order_index' => 9]);
        $questions->push($qLesson2_9);
        
        $qLesson2_10 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 10,
            'target_text' => 'I help my mom cook dinner.',
            'prompt_text' => 'How do you help your family?',
            'starter_text' => null,
        ]);
        $exerciseLesson2->questions()->attach($qLesson2_10->id, ['order_index' => 10]);
        $questions->push($qLesson2_10);

        // Add exercises and questions for Bài 3: Môi trường
        $lesson3Id = $lessons->where('title', 'Bài 3: Môi trường')->first()->id;
        
        $exerciseLesson3 = Exercise::where('lesson_id', $lesson3Id)->where('title', 'Luyện viết về môi trường')->first();
        
        if (!$exerciseLesson3) {
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
        }

        // Questions for Bài 3 (WAQ) - 10 questions
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
            'target_text' => 'We should recycle to protect the environment.',
            'prompt_text' => 'How can we protect the environment?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_5->id, ['order_index' => 5]);
        $questions->push($qLesson3_5);
        
        $qLesson3_6 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 6,
            'target_text' => 'We should plant more trees.',
            'prompt_text' => 'What should we do to help the Earth?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_6->id, ['order_index' => 6]);
        $questions->push($qLesson3_6);
        
        $qLesson3_7 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 7,
            'target_text' => 'We should save water and electricity.',
            'prompt_text' => 'How can we save natural resources?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_7->id, ['order_index' => 7]);
        $questions->push($qLesson3_7);
        
        $qLesson3_8 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 8,
            'target_text' => 'We should not throw trash on the ground.',
            'prompt_text' => 'What should we not do to keep our environment clean?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_8->id, ['order_index' => 8]);
        $questions->push($qLesson3_8);
        
        $qLesson3_9 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 9,
            'target_text' => 'I love animals and nature.',
            'prompt_text' => 'What do you love about nature?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_9->id, ['order_index' => 9]);
        $questions->push($qLesson3_9);
        
        $qLesson3_10 = Question::create([
            'img_url' => null,
            'audio_url' => null,
            'order_index' => 10,
            'target_text' => 'We can help by cleaning the park.',
            'prompt_text' => 'How can you help protect the environment in your community?',
            'starter_text' => null,
        ]);
        $exerciseLesson3->questions()->attach($qLesson3_10->id, ['order_index' => 10]);
        $questions->push($qLesson3_10);

        $this->command->info('All tables seeded successfully!');
    }
}
