<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveActiveColumnFromTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'active')) {
                $table->dropColumn('active');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'active')) {
                $table->dropColumn('active');
            }
        });

        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'active')) {
                $table->dropColumn('active');
            }
        });

        Schema::table('exercise_types', function (Blueprint $table) {
            if (Schema::hasColumn('exercise_types', 'active')) {
                $table->dropColumn('active');
            }
        });

        Schema::table('exercises', function (Blueprint $table) {
            if (Schema::hasColumn('exercises', 'active')) {
                $table->dropColumn('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->boolean('active')->default(1);
        });

        Schema::table('exercises', function (Blueprint $table) {
            $table->boolean('active')->default(1);
        });

        Schema::table('exercises_types', function (Blueprint $table) {
            $table->boolean('active')->default(1);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('active')->default(1);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(1);
        });
    }
}
