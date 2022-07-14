<?php

use Eduka\Cube\Models\Subscriber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema4 extends Migration
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
                      ->default(1)
                      ->comment('The related course id from this subscription')
                      ->constrained()
                      ->after('email');
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
