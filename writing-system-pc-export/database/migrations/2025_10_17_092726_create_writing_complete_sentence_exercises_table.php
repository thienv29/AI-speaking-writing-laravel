<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWritingCompleteSentenceExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('writing_complete_sentence_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->string('title');
            $table->text('sentence_start'); // Phần đầu câu cần hoàn thành
            $table->text('instructions')->nullable(); // Hướng dẫn hoàn thành câu
            $table->json('hint_words')->nullable(); // Từ gợi ý (optional)
            $table->json('sample_completions')->nullable(); // Các cách hoàn thành mẫu
            $table->integer('expected_word_count')->default(5); // Số từ mong đợi để hoàn thành
            $table->string('sentence_type')->default('simple'); // simple, compound, complex
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('writing_complete_sentence_exercises');
    }
}