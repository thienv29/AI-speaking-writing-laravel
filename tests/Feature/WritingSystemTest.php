<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Exercise;
use App\Models\WritingQaExercise;
use App\Models\WritingSentenceBuildingExercise;
use App\Models\WritingCompleteSentenceExercise;
use App\Models\WritingAttempt;

class WritingSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $exercise;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

        // Create test exercise
        $this->exercise = Exercise::create([
            'type_id' => 1,
            'lesson_id' => 1,
            'title' => 'Test Exercise',
            'instruction' => 'Test instruction',
            'difficulty' => 'easy',
            'order_index' => 1
        ]);
    }

    public function test_can_create_qa_exercise()
    {
        $qaExercise = WritingQaExercise::create([
            'exercise_id' => $this->exercise->id,
            'title' => 'Test Q&A',
            'question' => 'What is your name?',
            'expected_word_count' => 10,
            'difficulty' => 'easy'
        ]);

        $this->assertDatabaseHas('writing_qa_exercises', [
            'title' => 'Test Q&A',
            'question' => 'What is your name?'
        ]);
    }

    public function test_can_create_sentence_building_exercise()
    {
        $sentenceExercise = WritingSentenceBuildingExercise::create([
            'exercise_id' => $this->exercise->id,
            'title' => 'Test Sentence Building',
            'target_word' => 'happy',
            'word_type' => 'adjective',
            'word_meaning' => 'feeling joy',
            'expected_sentence_count' => 2,
            'difficulty' => 'medium'
        ]);

        $this->assertDatabaseHas('writing_sentence_building_exercises', [
            'title' => 'Test Sentence Building',
            'target_word' => 'happy'
        ]);
    }

    public function test_can_create_complete_sentence_exercise()
    {
        $completeExercise = WritingCompleteSentenceExercise::create([
            'exercise_id' => $this->exercise->id,
            'title' => 'Test Complete Sentence',
            'sentence_start' => 'I enjoy',
            'expected_word_count' => 5,
            'sentence_type' => 'simple',
            'difficulty' => 'easy'
        ]);

        $this->assertDatabaseHas('writing_complete_sentence_exercises', [
            'title' => 'Test Complete Sentence',
            'sentence_start' => 'I enjoy'
        ]);
    }

    public function test_can_create_writing_attempt()
    {
        $qaExercise = WritingQaExercise::create([
            'exercise_id' => $this->exercise->id,
            'title' => 'Test Q&A',
            'question' => 'What is your name?',
            'expected_word_count' => 10,
            'difficulty' => 'easy'
        ]);

        $attempt = WritingAttempt::create([
            'user_id' => $this->user->id,
            'writing_exercise_type' => 'writing_qa_exercise',
            'writing_exercise_id' => $qaExercise->id,
            'attempt_number' => 1,
            'user_answer' => 'My name is John.',
            'time_spent' => 120
        ]);

        $this->assertDatabaseHas('writing_attempts', [
            'user_id' => $this->user->id,
            'writing_exercise_type' => 'writing_qa_exercise',
            'user_answer' => 'My name is John.'
        ]);
    }

    public function test_word_count_calculation()
    {
        $attempt = WritingAttempt::create([
            'user_id' => $this->user->id,
            'writing_exercise_type' => 'writing_qa_exercise',
            'writing_exercise_id' => 1,
            'attempt_number' => 1,
            'user_answer' => 'Hello world this is a test.'
        ]);

        $wordCount = $attempt->calculateWordCount();
        $this->assertEquals(6, $wordCount);

        $charCount = $attempt->calculateCharacterCount();
        $this->assertEquals(28, $charCount);
    }

    public function test_qa_exercise_api_endpoint()
    {
        WritingQaExercise::create([
            'exercise_id' => $this->exercise->id,
            'title' => 'Test Q&A',
            'question' => 'What is your name?',
            'expected_word_count' => 10,
            'difficulty' => 'easy'
        ]);

        $response = $this->getJson('/api/writing/qa-exercises');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'question',
                            'difficulty'
                        ]
                    ]
                ]);
    }

    public function test_sentence_building_api_endpoint()
    {
        WritingSentenceBuildingExercise::create([
            'exercise_id' => $this->exercise->id,
            'title' => 'Test Sentence Building',
            'target_word' => 'happy',
            'word_type' => 'adjective',
            'expected_sentence_count' => 2,
            'difficulty' => 'medium'
        ]);

        $response = $this->getJson('/api/writing/sentence-building-exercises');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'target_word',
                            'word_type'
                        ]
                    ]
                ]);
    }

    public function test_complete_sentence_api_endpoint()
    {
        WritingCompleteSentenceExercise::create([
            'exercise_id' => $this->exercise->id,
            'title' => 'Test Complete Sentence',
            'sentence_start' => 'I enjoy',
            'expected_word_count' => 5,
            'sentence_type' => 'simple',
            'difficulty' => 'easy'
        ]);

        $response = $this->getJson('/api/writing/complete-sentence-exercises');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'sentence_start',
                            'sentence_type'
                        ]
                    ]
                ]);
    }
}