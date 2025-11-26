<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('exercise_types', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attempts', function (Blueprint $t) { $t->dropSoftDeletes(); });
        Schema::table('lessons', function (Blueprint $t) { $t->dropSoftDeletes(); });
        Schema::table('exercises', function (Blueprint $t) { $t->dropSoftDeletes(); });
        Schema::table('exercise_types', function (Blueprint $t) { $t->dropSoftDeletes(); });
        Schema::table('questions', function (Blueprint $t) { $t->dropSoftDeletes(); });
        Schema::table('users', function (Blueprint $t) { $t->dropSoftDeletes(); });
    }
}
