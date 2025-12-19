<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\{
    User,
    Lesson,
    Exercise,
    ExerciseType,
    Question,
    Attempt,
    Group
};

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Clean up old data first (if exists)
        // Delete old lessons that don't match new structure
        $oldLessonTitles = [
            'Bài 1: Giới thiệu bản thân',
            'Bài 2: Hoạt động hàng ngày',
            'Bài 3: Môi trường',
            'Bài 4',
            'Bài 5'
        ];
        
        foreach ($oldLessonTitles as $oldTitle) {
            $oldLesson = Lesson::where('title', $oldTitle)->first();
            if ($oldLesson) {
                $this->command->info("Deleting old lesson: {$oldTitle}");
                $oldLesson->delete();
            }
        }
        
        //USERS - Delete old default users first, then create fresh ones to ensure consistent IDs
        // This ensures IDs are always 1, 2, 3 for default users
        $defaultEmails = ['admin@example.com', 'user1@example.com', 'user2@example.com'];
        User::whereIn('email', $defaultEmails)->delete();
        
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
            ExerciseType::firstOrCreate(['code' => 'SPW'], ['name' => 'Speaking - Word']),
            ExerciseType::firstOrCreate(['code' => 'SPS'], ['name' => 'Speaking - Sentence']),
            ExerciseType::firstOrCreate(['code' => 'WAQ'], ['name' => 'Writing - Answer the question']),
            ExerciseType::firstOrCreate(['code' => 'WCS'], ['name' => 'Writing - Complete the sentence']),
            ExerciseType::firstOrCreate(['code' => 'WSG'], ['name' => 'Writing - Write sentence using the given word']),
        ]);

        //LESSONS - 5 lessons: 2 Speaking, 3 Writing
        $lessons = collect([
            Lesson::firstOrCreate(
                ['title' => 'Bài 1: Luyện phát âm từ vựng'],
                ['description' => 'Luyện phát âm các từ vựng cơ bản', 'img_url' => null, 'level' => 'Easy']
            ),
            Lesson::firstOrCreate(
                ['title' => 'Bài 2: Luyện phát âm câu'],
                ['description' => 'Luyện phát âm các câu hoàn chỉnh', 'img_url' => null, 'level' => 'Medium']
            ),
            Lesson::firstOrCreate(
                ['title' => 'Bài 3: Luyện viết trả lời câu hỏi'],
                ['description' => 'Trả lời các câu hỏi bằng câu hoàn chỉnh', 'img_url' => null, 'level' => 'Medium']
            ),
            Lesson::firstOrCreate(
                ['title' => 'Bài 4: Luyện viết hoàn thành câu'],
                ['description' => 'Hoàn thành các câu với từ cho sẵn', 'img_url' => null, 'level' => 'Easy']
            ),
            Lesson::firstOrCreate(
                ['title' => 'Bài 5: Luyện viết từ thành câu'],
                ['description' => 'Sắp xếp từ thành câu hoàn chỉnh', 'img_url' => null, 'level' => 'Easy']
            ),
        ]);

        // GROUPS - Create groups first
        $groupsData = [
            'Từ vựng cơ bản',
            'Ngữ pháp cơ bản',
            'Giao tiếp hàng ngày',
            'Câu hỏi mở rộng',
            'Luyện tập tổng hợp',
        ];

        $groups = [];
        foreach ($groupsData as $groupName) {
            $group = Group::firstOrCreate(['name' => $groupName]);
            $groups[] = $group;
        }

        $typeToGroupMap = [
            'SPW' => 0, // Từ vựng cơ bản
            'SPS' => 2, // Giao tiếp hàng ngày
            'WAQ' => 3, // Câu hỏi mở rộng
            'WCS' => 1, // Ngữ pháp cơ bản
            'WSG' => 4, // Luyện tập tổng hợp
        ];

        // LESSON 1: Speaking - Word (SPW) - 2 exercises, 20 questions
        $lesson1 = $lessons->where('title', 'Bài 1: Luyện phát âm từ vựng')->first();
        
        // Exercise 1.1: SPW - 10 questions
        $ex1_1 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson1->id, 'title' => 'Phát âm từ vựng cơ bản - Phần 1'],
            [
                'type_id' => $types->where('code', 'SPW')->first()->id,
                'instruction' => 'Hãy đọc to và rõ ràng các từ vựng sau đây.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 1
            ]
        );
        
        $spwWords1 = ['Hello', 'Name', 'School', 'Teacher', 'Friend', 'Family', 'Apple', 'Book', 'Pencil', 'Table'];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $spwWords1[$i], 'target_text' => $spwWords1[$i]],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => null
                ]
            );
            $ex1_1->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['SPW']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // Exercise 1.2: SPW - 10 questions
        $ex1_2 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson1->id, 'title' => 'Phát âm từ vựng cơ bản - Phần 2'],
            [
                'type_id' => $types->where('code', 'SPW')->first()->id,
                'instruction' => 'Hãy đọc to và rõ ràng các từ vựng sau đây.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 2
            ]
        );
        
        $spwWords2 = ['Chair', 'Window', 'Door', 'Desk', 'Pen', 'Bag', 'Water', 'Milk', 'Bread', 'Rice'];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $spwWords2[$i], 'target_text' => $spwWords2[$i]],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => null
                ]
            );
            $ex1_2->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['SPW']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // LESSON 2: Speaking - Sentence (SPS) - 2 exercises, 20 questions
        $lesson2 = $lessons->where('title', 'Bài 2: Luyện phát âm câu')->first();
        
        // Exercise 2.1: SPS - 10 questions
        $ex2_1 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson2->id, 'title' => 'Phát âm câu hoàn chỉnh - Phần 1'],
            [
                'type_id' => $types->where('code', 'SPS')->first()->id,
                'instruction' => 'Hãy đọc to và rõ ràng các câu sau đây với ngữ điệu phù hợp.',
                'difficulty' => 'Trung bình',
                'img_url' => null,
                'order_index' => 1
            ]
        );
        
        $spsSentences1 = [
            'What is your name?',
            'How are you?',
            'Nice to meet you.',
            'I am fine, thank you.',
            'Good morning, teacher.',
            'See you later.',
            'Have a nice day.',
            'I love my family.',
            'This is my school.',
            'Thank you very much.'
        ];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $spsSentences1[$i], 'target_text' => $spsSentences1[$i]],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => null
                ]
            );
            $ex2_1->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['SPS']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // Exercise 2.2: SPS - 10 questions
        $ex2_2 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson2->id, 'title' => 'Phát âm câu hoàn chỉnh - Phần 2'],
            [
                'type_id' => $types->where('code', 'SPS')->first()->id,
                'instruction' => 'Hãy đọc to và rõ ràng các câu sau đây với ngữ điệu phù hợp.',
                'difficulty' => 'Trung bình',
                'img_url' => null,
                'order_index' => 2
            ]
        );
        
        $spsSentences2 = [
            'I go to school every day.',
            'I like playing soccer.',
            'My favorite color is blue.',
            'I have breakfast at 7 AM.',
            'I live in Hanoi.',
            'I am 8 years old.',
            'I have two brothers.',
            'I eat an apple for lunch.',
            'I play soccer with my friends.',
            'My teacher is very kind.'
        ];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $spsSentences2[$i], 'target_text' => $spsSentences2[$i]],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => null
                ]
            );
            $ex2_2->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['SPS']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // LESSON 3: Writing - Answer Question (WAQ) - 2 exercises, 20 questions
        $lesson3 = $lessons->where('title', 'Bài 3: Luyện viết trả lời câu hỏi')->first();
        
        // Exercise 3.1: WAQ - 10 questions
        $ex3_1 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson3->id, 'title' => 'Trả lời câu hỏi bằng câu hoàn chỉnh - Phần 1'],
            [
                'type_id' => $types->where('code', 'WAQ')->first()->id,
                'instruction' => 'Hãy đọc câu hỏi và trả lời bằng một câu hoàn chỉnh, có chủ ngữ và vị ngữ.',
                'difficulty' => 'Trung bình',
                'img_url' => null,
                'order_index' => 1
            ]
        );
        
        $waqQuestions1 = [
            ['prompt' => 'What is your name?', 'target' => 'My name is Anna.'],
            ['prompt' => 'How old are you?', 'target' => 'I am 8 years old.'],
            ['prompt' => 'Where do you live?', 'target' => 'I live in Hanoi.'],
            ['prompt' => 'What is your favorite sport?', 'target' => 'I like playing soccer.'],
            ['prompt' => 'What time do you have breakfast?', 'target' => 'I have breakfast at 7 AM.'],
            ['prompt' => 'What is your favorite color?', 'target' => 'My favorite color is blue.'],
            ['prompt' => 'How do you go to school?', 'target' => 'I go to school by bus.'],
            ['prompt' => 'What time is it? Answer using the format \'It is [number] o\'clock\'.', 'target' => "It is 7 o'clock."],
            ['prompt' => 'Describe the weather today using the format "Today is ...".', 'target' => 'Today is sunny.'],
            ['prompt' => 'Say hello to your teacher.', 'target' => 'Hello, teacher!']
        ];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $waqQuestions1[$i]['prompt'], 'target_text' => $waqQuestions1[$i]['target']],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => null
                ]
            );
            $ex3_1->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['WAQ']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // Exercise 3.2: WAQ - 10 questions
        $ex3_2 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson3->id, 'title' => 'Trả lời câu hỏi bằng câu hoàn chỉnh - Phần 2'],
            [
                'type_id' => $types->where('code', 'WAQ')->first()->id,
                'instruction' => 'Hãy đọc câu hỏi và trả lời bằng một câu hoàn chỉnh, có chủ ngữ và vị ngữ.',
                'difficulty' => 'Trung bình',
                'img_url' => null,
                'order_index' => 2
            ]
        );
        
        $waqQuestions2 = [
            ['prompt' => 'What is your favorite hobby?', 'target' => 'My favorite hobby is reading books.'],
            ['prompt' => 'What do you do every morning?', 'target' => 'I brush my teeth every morning.'],
            ['prompt' => 'What time do you have lunch?', 'target' => 'I have lunch at 12 o\'clock.'],
            ['prompt' => 'When do you do your homework?', 'target' => 'I do my homework in the afternoon.'],
            ['prompt' => 'What do you do in the evening?', 'target' => 'I watch TV in the evening.'],
            ['prompt' => 'What time do you go to bed?', 'target' => 'I go to bed at 9 PM.'],
            ['prompt' => 'How do you help your family?', 'target' => 'I help my mom cook dinner.'],
            ['prompt' => 'What should we do to help the Earth?', 'target' => 'We should plant more trees.'],
            ['prompt' => 'How can we protect the environment?', 'target' => 'We should recycle to protect the environment.'],
            ['prompt' => 'How can you help protect the environment in your community?', 'target' => 'We can help by cleaning the park.']
        ];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $waqQuestions2[$i]['prompt'], 'target_text' => $waqQuestions2[$i]['target']],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => null
                ]
            );
            $ex3_2->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['WAQ']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // LESSON 4: Writing - Complete Sentence (WCS) - 2 exercises, 20 questions
        $lesson4 = $lessons->where('title', 'Bài 4: Luyện viết hoàn thành câu')->first();
        
        // Exercise 4.1: WCS - 10 questions
        $ex4_1 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson4->id, 'title' => 'Hoàn thành câu với từ cho sẵn - Phần 1'],
            [
                'type_id' => $types->where('code', 'WCS')->first()->id,
                'instruction' => 'Hãy hoàn thành các câu sau bằng cách điền từ thích hợp vào chỗ trống.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 1
            ]
        );
        
        $wcsQuestions1 = [
            ['prompt' => 'Complete the sentence about your favorite hobby.', 'target' => 'My favorite hobby is playing the piano.', 'starter' => 'My favorite hobby is'],
            ['prompt' => 'Complete the sentence to tell where you live.', 'target' => 'I live in Hanoi.', 'starter' => 'I live in'],
            ['prompt' => 'Complete the sentence about what time you go to school.', 'target' => 'I go to school at 7 o\'clock.', 'starter' => 'I go to school at'],
            ['prompt' => 'Complete the sentence about your favorite color.', 'target' => 'My favorite color is blue.', 'starter' => 'My favorite color is'],
            ['prompt' => 'Complete the sentence about what you like to do.', 'target' => 'I like to read books.', 'starter' => 'I like to'],
            ['prompt' => 'Complete the sentence about what day it is today.', 'target' => 'Today is Monday.', 'starter' => 'Today is'],
            ['prompt' => 'Complete the sentence about your family.', 'target' => 'I have two brothers.', 'starter' => 'I have'],
            ['prompt' => 'Complete the sentence about when you eat breakfast.', 'target' => 'I eat breakfast in the morning.', 'starter' => 'I eat breakfast'],
            ['prompt' => 'Complete the sentence about playing sports.', 'target' => 'I play soccer with my friends.', 'starter' => 'I play soccer'],
            ['prompt' => 'Complete the sentence about your teacher.', 'target' => 'My teacher is very kind.', 'starter' => 'My teacher is']
        ];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $wcsQuestions1[$i]['prompt'], 'target_text' => $wcsQuestions1[$i]['target']],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => $wcsQuestions1[$i]['starter']
                ]
            );
            $ex4_1->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['WCS']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // Exercise 4.2: WCS - 10 questions
        $ex4_2 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson4->id, 'title' => 'Hoàn thành câu với từ cho sẵn - Phần 2'],
            [
                'type_id' => $types->where('code', 'WCS')->first()->id,
                'instruction' => 'Hãy hoàn thành các câu sau bằng cách điền từ thích hợp vào chỗ trống.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 2
            ]
        );
        
        $wcsQuestions2 = [
            ['prompt' => 'Complete: I go to bed', 'target' => 'I go to bed at 9 PM.', 'starter' => 'I go to bed'],
            ['prompt' => 'Complete: My favorite food is', 'target' => 'My favorite food is pizza.', 'starter' => 'My favorite food is'],
            ['prompt' => 'Complete: I wake up', 'target' => 'I wake up at 6 o\'clock.', 'starter' => 'I wake up'],
            ['prompt' => 'Complete: I study', 'target' => 'I study in the library.', 'starter' => 'I study'],
            ['prompt' => 'Complete: My best friend is', 'target' => 'My best friend is very nice.', 'starter' => 'My best friend is'],
            ['prompt' => 'Complete: I play', 'target' => 'I play in the park.', 'starter' => 'I play'],
            ['prompt' => 'Complete: I eat', 'target' => 'I eat lunch at school.', 'starter' => 'I eat'],
            ['prompt' => 'Complete: My school is', 'target' => 'My school is very big.', 'starter' => 'My school is'],
            ['prompt' => 'Complete: I like', 'target' => 'I like to draw pictures.', 'starter' => 'I like'],
            ['prompt' => 'Complete: I have', 'target' => 'I have a pet cat.', 'starter' => 'I have']
        ];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $wcsQuestions2[$i]['prompt'], 'target_text' => $wcsQuestions2[$i]['target']],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => $wcsQuestions2[$i]['starter']
                ]
            );
            $ex4_2->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['WCS']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // LESSON 5: Writing - Word to Sentence (WSG) - 2 exercises, 20 questions
        $lesson5 = $lessons->where('title', 'Bài 5: Luyện viết từ thành câu')->first();
        
        // Exercise 5.1: WSG - 10 questions
        $ex5_1 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson5->id, 'title' => 'Sắp xếp từ thành câu - Phần 1'],
            [
                'type_id' => $types->where('code', 'WSG')->first()->id,
                'instruction' => 'Hãy sắp xếp các từ cho sẵn thành một câu hoàn chỉnh và có nghĩa.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 1
            ]
        );
        
        $wsgWords1 = [
            ['prompt' => 'sunny', 'target' => 'Today is sunny.'],
            ['prompt' => 'friend', 'target' => 'My friend is very kind.'],
            ['prompt' => 'family', 'target' => 'I love my family.'],
            ['prompt' => 'school', 'target' => 'I go to school every day.'],
            ['prompt' => 'teacher', 'target' => 'My teacher is nice.'],
            ['prompt' => 'books', 'target' => 'I like to read books.'],
            ['prompt' => 'apple', 'target' => 'I eat an apple for lunch.'],
            ['prompt' => 'soccer', 'target' => 'I play soccer in the park.'],
            ['prompt' => 'dog', 'target' => 'I have a pet dog.'],
            ['prompt' => 'TV', 'target' => 'I watch TV in the evening.']
        ];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $wsgWords1[$i]['prompt'], 'target_text' => $wsgWords1[$i]['target']],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => null
                ]
            );
            $ex5_1->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['WSG']]->questions()->syncWithoutDetaching([$q->id]);
        }

        // Exercise 5.2: WSG - 10 questions
        $ex5_2 = Exercise::firstOrCreate(
            ['lesson_id' => $lesson5->id, 'title' => 'Sắp xếp từ thành câu - Phần 2'],
            [
                'type_id' => $types->where('code', 'WSG')->first()->id,
                'instruction' => 'Hãy sắp xếp các từ cho sẵn thành một câu hoàn chỉnh và có nghĩa.',
                'difficulty' => 'Dễ',
                'img_url' => null,
                'order_index' => 2
            ]
        );
        
        $wsgWords2 = [
            ['prompt' => 'morning', 'target' => 'Good morning, teacher.'],
            ['prompt' => 'breakfast', 'target' => 'I have breakfast at 7 AM.'],
            ['prompt' => 'homework', 'target' => 'I do my homework after school.'],
            ['prompt' => 'lunch', 'target' => 'I eat lunch at 12 o\'clock.'],
            ['prompt' => 'dinner', 'target' => 'I have dinner with my family.'],
            ['prompt' => 'bed', 'target' => 'I go to bed at 9 PM.'],
            ['prompt' => 'park', 'target' => 'I play in the park.'],
            ['prompt' => 'library', 'target' => 'I study in the library.'],
            ['prompt' => 'cat', 'target' => 'I have a pet cat.'],
            ['prompt' => 'bike', 'target' => 'I ride my bike to school.']
        ];
        for ($i = 0; $i < 10; $i++) {
            $q = Question::firstOrCreate(
                ['prompt_text' => $wsgWords2[$i]['prompt'], 'target_text' => $wsgWords2[$i]['target']],
                [
                    'img_url' => null,
                    'audio_url' => null,
                    'order_index' => $i + 1,
                    'starter_text' => null
                ]
            );
            $ex5_2->questions()->syncWithoutDetaching([$q->id => ['order_index' => $i + 1]]);
            $groups[$typeToGroupMap['WSG']]->questions()->syncWithoutDetaching([$q->id]);
        }

        $this->command->info('All tables seeded successfully!');
        $this->command->info('Lessons: ' . Lesson::count());
        $this->command->info('Exercises: ' . Exercise::count());
        $this->command->info('Questions: ' . Question::count());
        $this->command->info('Groups: ' . Group::count());
        $this->command->info('Questions linked to groups: ' . DB::table('group_question')->count());
    }
}
