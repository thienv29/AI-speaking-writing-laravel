<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropProgressTableAndVocabulariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists("progress");
        Schema::dropIfExists("vocabularies");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->string('status');
            $table->timestamp('complete_at');
            $table->integer('time_spent');
            $table->timestamps();
        });

        Schema::create('vocabulary', function (Blueprint $table) {
            $table->id('id');
            $table->string('word');
            $table->string('phonetic');
            $table->string('meaning');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
}
