<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterUsersSetDefaultAvatar extends Migration
{
    private string $defaultAvatar = 'avatars/default-avatar.png';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('users', 'avatar_url')) {
            DB::table('users')
                ->whereNull('avatar_url')
                ->update(['avatar_url' => $this->defaultAvatar]);

            DB::statement("
                ALTER TABLE `users`
                MODIFY `avatar_url` VARCHAR(255)
                NULL DEFAULT '{$this->defaultAvatar}'
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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'avatar_url')) {
                DB::statement("
                    ALTER TABLE `users`
                    MODIFY `avatar_url` VARCHAR(255)
                    NULL DEFAULT NULL
                ");
            }
        });
    }
}
