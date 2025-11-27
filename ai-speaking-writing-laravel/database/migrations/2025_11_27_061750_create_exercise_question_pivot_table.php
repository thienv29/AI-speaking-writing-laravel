<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateExerciseQuestionPivotTable extends Migration
{
    /**
     * Run the migrations.
     * Convert Exercise-Question relationship from One-to-Many to Many-to-Many
     *
     * @return void
     */
    public function up()
    {
        // Create pivot table
        Schema::create('exercise_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('order_index')->default(0);
            $table->timestamps();
            
            // Unique constraint: one question can only appear once per exercise
            $table->unique(['exercise_id', 'question_id']);
            
            // Index for faster queries
            $table->index('exercise_id');
            $table->index('question_id');
        });
        
        // Migrate existing data from questions.exercise_id to pivot table
        DB::statement("
            INSERT INTO exercise_question (exercise_id, question_id, order_index, created_at, updated_at)
            SELECT exercise_id, id, order_index, created_at, updated_at
            FROM questions
            WHERE exercise_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore exercise_id in questions table before dropping pivot
        // Note: This assumes questions can only belong to one exercise (original One-to-Many)
        // If a question belongs to multiple exercises, we'll take the first one
        DB::statement("
            UPDATE questions q
            INNER JOIN (
                SELECT question_id, MIN(exercise_id) as exercise_id
                FROM exercise_question
                GROUP BY question_id
            ) eq ON q.id = eq.question_id
            SET q.exercise_id = eq.exercise_id
        ");
        
        Schema::dropIfExists('exercise_question');
    }
}
