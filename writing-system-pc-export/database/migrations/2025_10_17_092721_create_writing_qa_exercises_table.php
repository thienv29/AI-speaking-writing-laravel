<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWritingQaExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('writing_qa_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->string('title');
            $table->text('question'); // Câu hỏi chính
            $table->text('instructions')->nullable(); // Hướng dẫn cách trả lời
            $table->json('sample_answers')->nullable(); // Các câu trả lời mẫu
            $table->integer('expected_word_count')->default(20); // Số từ mong đợi cho câu trả lời
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
        Schema::dropIfExists('writing_qa_exercises');
    }
}