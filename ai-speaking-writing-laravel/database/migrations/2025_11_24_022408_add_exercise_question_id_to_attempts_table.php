<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExerciseQuestionIdToAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->unsignedBigInteger('exercise_question_id');

            $table->foreign('exercise_question_id')
                  ->references('id')
                  ->on('exercise_question')
                  ->onDelete('cascade');

            $table->dropForeign(['question_id']);
            $table->dropColumn('question_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->dropForeign(['exercise_question_id']); 
            $table->dropColumn('exercise_question_id');

            $table->unsignedBigInteger('question_id')->after('id');

            $table->foreign('question_id')
                  ->references('id')
                  ->on('questions')
                  ->onDelete('cascade');
        });
    }
}
