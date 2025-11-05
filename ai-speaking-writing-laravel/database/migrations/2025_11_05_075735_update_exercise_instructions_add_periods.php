<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update instructions that end with ':' to end with '.'
        DB::table('exercises')
            ->where('instruction', 'LIKE', '%:')
            ->where('instruction', 'NOT LIKE', '%.')
            ->update([
                'instruction' => DB::raw("CONCAT(TRIM(TRAILING ':' FROM instruction), '.')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert instructions that end with '.' back to ':'
        DB::table('exercises')
            ->whereIn('instruction', [
                'Hoàn thành các câu sau.',
                'Trả lời các câu hỏi sau.',
                'Sử dụng từ cho sẵn để đặt câu.',
                'Trả lời các câu hỏi về thói quen hàng ngày của bạn.',
                'Trả lời các câu hỏi về môi trường.'
            ])
            ->update([
                'instruction' => DB::raw("CONCAT(TRIM(TRAILING '.' FROM instruction), ':')")
            ]);
    }
};
