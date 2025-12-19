<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeUserIdNullableInAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if table exists
        if (!Schema::hasTable('attempts')) {
            return;
        }

        // Check if column exists
        if (!Schema::hasColumn('attempts', 'user_id')) {
            return;
        }

        // Get the foreign key constraint name
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'attempts' 
            AND COLUMN_NAME = 'user_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        $constraintName = null;
        if (!empty($foreignKeys)) {
            $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
        }

        // Drop foreign key constraint if exists
        if ($constraintName) {
            DB::statement("ALTER TABLE `attempts` DROP FOREIGN KEY `{$constraintName}`");
        }

        // Modify column to nullable using raw SQL
        DB::statement("ALTER TABLE `attempts` MODIFY COLUMN `user_id` BIGINT UNSIGNED NULL");

        // Re-add foreign key constraint (nullable)
        if ($constraintName) {
            // Try to recreate with same name or generate new one
            $newConstraintName = 'attempts_user_id_foreign';
            DB::statement("
                ALTER TABLE `attempts` 
                ADD CONSTRAINT `{$newConstraintName}` 
                FOREIGN KEY (`user_id`) 
                REFERENCES `users` (`id`) 
                ON DELETE CASCADE
            ");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('attempts')) {
            return;
        }

        // Get the foreign key constraint name
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'attempts' 
            AND COLUMN_NAME = 'user_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        $constraintName = null;
        if (!empty($foreignKeys)) {
            $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
        }

        // Drop foreign key constraint if exists
        if ($constraintName) {
            DB::statement("ALTER TABLE `attempts` DROP FOREIGN KEY `{$constraintName}`");
        }

        // Set any NULL values to a default user (user_id = 1) before making NOT NULL
        // Only if user_id = 1 exists
        $userExists = DB::table('users')->where('id', 1)->exists();
        if ($userExists) {
            DB::table('attempts')
                ->whereNull('user_id')
                ->update(['user_id' => 1]);
        }

        // Make column NOT NULL again using raw SQL
        DB::statement("ALTER TABLE `attempts` MODIFY COLUMN `user_id` BIGINT UNSIGNED NOT NULL");

        // Re-add foreign key constraint (NOT NULL)
        if ($constraintName || $userExists) {
            $newConstraintName = 'attempts_user_id_foreign';
            DB::statement("
                ALTER TABLE `attempts` 
                ADD CONSTRAINT `{$newConstraintName}` 
                FOREIGN KEY (`user_id`) 
                REFERENCES `users` (`id`) 
                ON DELETE CASCADE
            ");
        }
    }
}
