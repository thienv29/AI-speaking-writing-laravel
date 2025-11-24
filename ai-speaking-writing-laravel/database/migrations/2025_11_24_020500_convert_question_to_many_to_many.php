<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConvertQuestionToManyToMany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // 1. Tạo bảng pivot
        Schema::create('exercise_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });

        // 2. Chuyển dữ liệu cũ từ questions sang pivot
        $rows = DB::table('questions')
            ->select('id as question_id', 'exercise_id', 'order_index')
            ->whereNotNull('exercise_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('exercise_question')->insert([
                'exercise_id' => $row->exercise_id,
                'question_id' => $row->question_id,
                'order_index' => $row->order_index ?? 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // 3. Xóa foreign key + cột trong bảng questions
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'exercise_id')) {
                $table->dropForeign(['exercise_id']);
                $table->dropColumn('exercise_id');
            }

            if (Schema::hasColumn('questions', 'order_index')) {
                $table->dropColumn('order_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // 1. Thêm lại các cột cũ
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('exercise_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('order_index')->default(0);
        });

        // 2. Khôi phục dữ liệu từ pivot (chỉ lấy exercise đầu tiên nếu có nhiều)
        $rows = DB::table('exercise_question')
            ->select('exercise_id', 'question_id', 'order_index')
            ->get();

        foreach ($rows as $row) {
            DB::table('questions')
                ->where('id', $row->question_id)
                ->update([
                    'exercise_id' => $row->exercise_id,
                    'order_index' => $row->order_index,
                ]);
        }

        // 3. Xóa bảng pivot
        Schema::dropIfExists('exercise_question');
    }
}
