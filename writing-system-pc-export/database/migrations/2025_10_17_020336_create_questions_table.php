<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('exercise_id')->constrained('exercises')->onDelete('cascade');
            $table->string('img_url')->nullable();
            $table->string('audio_url')->nullable();
            $table->integer('order_index')->default(0);
            $table->text('prompt_text')->nullable();
            $table->text('target_text')->nullable();
            $table->text('starter_text')->nullable();
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
        Schema::dropIfExists('questions');
    }
}
