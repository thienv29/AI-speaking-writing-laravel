<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveFlags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'active')) {
                $table->boolean('active')->default(true)->index()->after('password');
            }
        });

        Schema::table('exercise_types', function (Blueprint $table) {
            if (!Schema::hasColumn('exercise_types', 'active')) {
                $table->boolean('active')->default(true)->index()->after('code');
            }
        });

        Schema::table('lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('lessons', 'active')) {
                $table->boolean('active')->default(true)->index()->after('level');
            }
        });

        Schema::table('exercises', function (Blueprint $table) {
            if (!Schema::hasColumn('exercises', 'active')) {
                $table->boolean('active')->default(true)->index()->after('order_index');
            }
        });

        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'active')) {
                $table->boolean('active')->default(true)->index()->after('starter_text');
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
        //
    }
}
