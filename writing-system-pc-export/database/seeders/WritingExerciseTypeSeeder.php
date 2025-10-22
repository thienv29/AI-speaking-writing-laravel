<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExerciseType;

class WritingExerciseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $writingTypes = [
            [
                'name' => 'Q&A Writing',
                'code' => 'qa_writing'
            ],
            [
                'name' => 'Sentence Building',
                'code' => 'sentence_building'
            ],
            [
                'name' => 'Complete Sentence',
                'code' => 'complete_sentence'
            ]
        ];

        foreach ($writingTypes as $type) {
            ExerciseType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}