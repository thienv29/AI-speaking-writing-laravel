<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWritingSentenceBuildingExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('writing_sentence_building_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->string('title');
            $table->string('target_word'); // Từ mới cần sử dụng
            $table->string('word_type')->nullable(); // noun, verb, adjective, adverb
            $table->text('word_meaning')->nullable(); // Ý nghĩa của từ
            $table->text('word_example')->nullable(); // Ví dụ sử dụng từ
            $table->text('instructions')->nullable(); // Hướng dẫn đặt câu
            $table->json('sample_sentences')->nullable(); // Các câu mẫu sử dụng từ
            $table->integer('expected_sentence_count')->default(1); // Số câu cần đặt
            $table->integer('expected_word_count')->default(10); // Số từ tối thiểu mỗi câu
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
        Schema::dropIfExists('writing_sentence_building_exercises');
    }
}