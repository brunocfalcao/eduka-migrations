<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsSequence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dateTime('published_at')
                  ->after('created_at')
                  ->nullable()
                  ->comment('When will this video be available. Earlier than this date it will be not visible');

            $table->dateTime('archived_at')
                  ->after('published_at')
                  ->nullable()
                  ->comment('When will this video be is archived. After this date will not be visible');

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
        //
    }
}
