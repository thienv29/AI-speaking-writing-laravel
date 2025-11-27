<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConvertGroupQuestionToManyToMany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create pivot table
        Schema::create('group_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->timestamps();
            
            // Prevent duplicate entries
            $table->unique(['group_id', 'question_id']);
        });

        // Migrate existing data from group_id to pivot table
        DB::statement('
            INSERT INTO group_question (group_id, question_id, created_at, updated_at)
            SELECT group_id, id, NOW(), NOW()
            FROM questions
            WHERE group_id IS NOT NULL
        ');

        // Drop group_id column from questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add group_id column back
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('set null');
        });

        // Migrate data back from pivot table (take first group if multiple)
        DB::statement('
            UPDATE questions q
            INNER JOIN (
                SELECT question_id, MIN(group_id) as group_id
                FROM group_question
                GROUP BY question_id
            ) gq ON q.id = gq.question_id
            SET q.group_id = gq.group_id
        ');

        // Drop pivot table
        Schema::dropIfExists('group_question');
    }
}

