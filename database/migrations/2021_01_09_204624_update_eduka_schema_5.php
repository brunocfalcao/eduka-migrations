<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (! Schema::hasColumn('subscribers', 'can_receive_emails')) {
                $table->boolean('can_receive_emails')
                      ->default(true)
                      ->comment('Flag to send emails, or not')
                      ->after('email');
            }

            if (! Schema::hasColumn('subscribers', 'uuid')) {
                $table->string('uuid')
                      ->nullable()
                      ->comment('The uuid used to stop receiving emails')
                      ->after('can_receive_emails');
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
