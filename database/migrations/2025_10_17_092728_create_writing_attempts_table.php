<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWritingAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('writing_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Polymorphic relationship để link với 3 loại writing exercises
            $table->string('writing_exercise_type'); // writing_qa_exercise, writing_sentence_building_exercise, writing_complete_sentence_exercise
            $table->unsignedBigInteger('writing_exercise_id'); // ID của exercise cụ thể
            
            $table->integer('attempt_number')->default(1);
            $table->text('user_answer'); // Câu trả lời của user
            
            // Thống kê câu trả lời
            $table->integer('word_count')->default(0); // Số từ trong câu trả lời
            $table->integer('character_count')->default(0); // Số ký tự
            $table->integer('time_spent')->nullable(); // Thời gian làm bài (giây)
            
            // Trạng thái
            $table->boolean('is_submitted')->default(false); // Đã nộp bài chưa
            $table->boolean('is_correct')->default(false); // Câu trả lời đúng/sai
            $table->text('feedback')->nullable(); // Phản hồi từ AI/human
            
            // Điểm số
            $table->decimal('score', 5, 2)->nullable(); // Điểm số (0-100)
            
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            // Index cho polymorphic relationship
            $table->index(['writing_exercise_type', 'writing_exercise_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('writing_attempts');
    }
}