<?php

use Eduka\Cube\Models\Subscriber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema8 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            if (! Schema::hasColumn('subscribers', 'course_id')) {
                $table->foreignId('course_id')
                      ->nullable()
                      ->comment('The related course id for this subscriber')
                      ->after('uuid');
            }

            if (! Schema::hasColumn('subscribers', 'user_id')) {
                $table->foreignId('user_id')
                  ->nullable()
                  ->comment('The related user id for this subscriber')
                  ->after('course_id');
            }
        });

        Subscriber::query()->update(['course_id' => 1]);
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
