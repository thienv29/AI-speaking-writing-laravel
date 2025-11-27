<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\Exercise;
use App\Models\Group;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class UpdateLessonsExercisesAndGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // Update Lesson names
            $lessonsData = [
                1 => [
                    'title' => 'Luyện phát âm từ vựng',
                    'level' => 'Easy',
                ],
                2 => [
                    'title' => 'Luyện phát âm câu',
                    'level' => 'Medium',
                ],
                3 => [
                    'title' => 'Luyện viết trả lời câu hỏi',
                    'level' => 'Medium',
                ],
                4 => [
                    'title' => 'Luyện viết hoàn thành câu',
                    'level' => 'Easy',
                ],
                5 => [
                    'title' => 'Luyện viết từ thành câu',
                    'level' => 'Easy',
                ],
            ];

            foreach ($lessonsData as $lessonId => $data) {
                Lesson::where('id', $lessonId)->update($data);
                echo "Updated Lesson {$lessonId}: {$data['title']}\n";
            }

            // Update Exercise names
            $exercisesData = [
                1 => [
                    'title' => 'Phát âm từ vựng cơ bản',
                    'instruction' => 'Hãy đọc to và rõ ràng các từ vựng sau đây.',
                ],
                2 => [
                    'title' => 'Phát âm câu hoàn chỉnh',
                    'instruction' => 'Hãy đọc to và rõ ràng các câu sau đây với ngữ điệu phù hợp.',
                ],
                3 => [
                    'title' => 'Trả lời câu hỏi bằng câu hoàn chỉnh',
                    'instruction' => 'Hãy đọc câu hỏi và trả lời bằng một câu hoàn chỉnh, có chủ ngữ và vị ngữ.',
                ],
                4 => [
                    'title' => 'Hoàn thành câu với từ cho sẵn',
                    'instruction' => 'Hãy hoàn thành các câu sau bằng cách điền từ thích hợp vào chỗ trống.',
                ],
                5 => [
                    'title' => 'Sắp xếp từ thành câu',
                    'instruction' => 'Hãy sắp xếp các từ cho sẵn thành một câu hoàn chỉnh và có nghĩa.',
                ],
            ];

            foreach ($exercisesData as $exerciseId => $data) {
                Exercise::where('id', $exerciseId)->update($data);
                echo "Updated Exercise {$exerciseId}: {$data['title']}\n";
            }

            // Create Groups
            $groupsData = [
                'Từ vựng cơ bản',
                'Ngữ pháp cơ bản',
                'Giao tiếp hàng ngày',
                'Câu hỏi mở rộng',
                'Luyện tập tổng hợp',
            ];

            $groups = [];
            foreach ($groupsData as $groupName) {
                $group = Group::create(['name' => $groupName]);
                $groups[] = $group;
                echo "Created Group: {$group->name}\n";
            }

            // Assign questions to groups based on exercise type
            $lessons = Lesson::with(['exercises.type', 'exercises.questions'])->get();
            
            // Map exercise types to group indices
            // SPW (Speaking Word) -> Từ vựng cơ bản (0)
            // SPS (Speaking Sentence) -> Giao tiếp hàng ngày (2)
            // WAQ (Writing Answer Question) -> Câu hỏi mở rộng (3)
            // WCS (Writing Complete Sentence) -> Ngữ pháp cơ bản (1)
            // WSG (Writing Word to Sentence) -> Luyện tập tổng hợp (4)
            
            $typeToGroupMap = [
                'SPW' => 0, // Từ vựng cơ bản
                'SPS' => 2, // Giao tiếp hàng ngày
                'WAQ' => 3, // Câu hỏi mở rộng
                'WCS' => 1, // Ngữ pháp cơ bản
                'WSG' => 4, // Luyện tập tổng hợp
            ];
            
            foreach ($lessons as $lesson) {
                foreach ($lesson->exercises as $exercise) {
                    $typeCode = $exercise->type->code ?? null;
                    $groupIndex = $typeToGroupMap[$typeCode] ?? 4; // Default to last group
                    
                    $questions = $exercise->questions;
                    $assignedCount = 0;
                    
                    foreach ($questions as $question) {
                        $question->group_id = $groups[$groupIndex]->id;
                        $question->save();
                        $assignedCount++;
                    }
                    
                    echo "Assigned {$assignedCount} questions from Exercise {$exercise->id} ({$typeCode}) to group: {$groups[$groupIndex]->name}\n";
                }
            }

            echo "\n✅ All updates completed!\n";
        });
    }
}

