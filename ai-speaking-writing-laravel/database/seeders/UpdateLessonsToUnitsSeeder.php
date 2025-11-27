<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;

class UpdateLessonsToUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Option 1: Simple Unit numbers
        $lessonsData = [
            1 => [
                'title' => 'Unit 1',
                'level' => 'Easy',
            ],
            2 => [
                'title' => 'Unit 2',
                'level' => 'Medium',
            ],
            3 => [
                'title' => 'Unit 3',
                'level' => 'Medium',
            ],
            4 => [
                'title' => 'Unit 4',
                'level' => 'Easy',
            ],
            5 => [
                'title' => 'Unit 5',
                'level' => 'Easy',
            ],
        ];

        // Option 2: Unit with topics (uncomment to use)
        /*
        $lessonsData = [
            1 => [
                'title' => 'Unit 1: Gia đình',
                'level' => 'Easy',
            ],
            2 => [
                'title' => 'Unit 2: Trường học',
                'level' => 'Medium',
            ],
            3 => [
                'title' => 'Unit 3: Động vật',
                'level' => 'Medium',
            ],
            4 => [
                'title' => 'Unit 4: Thực phẩm',
                'level' => 'Easy',
            ],
            5 => [
                'title' => 'Unit 5: Giao thông',
                'level' => 'Easy',
            ],
        ];
        */

        foreach ($lessonsData as $lessonId => $data) {
            Lesson::where('id', $lessonId)->update($data);
            echo "Updated Lesson {$lessonId}: {$data['title']}\n";
        }

        echo "\n✅ All lessons updated to Unit format!\n";
    }
}

