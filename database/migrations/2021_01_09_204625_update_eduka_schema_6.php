<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'subscriber_id')) {
                $table->dropForeign('users_subscriber_id_foreign');
                $table->dropColumn('subscriber_id');
            }
        });

        Schema::table('subscribers', function (Blueprint $table) {
            if (! Schema::hasColumn('subscribers', 'user_id')) {
                $table->foreignId('user_id')
                      ->comment('The related user id, can have multiple subscribers for the same user id')
                      ->nullable()
                      ->after('uuid');
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
