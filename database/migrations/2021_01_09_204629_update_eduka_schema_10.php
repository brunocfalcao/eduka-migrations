<?php

use Eduka\Cube\Models\Subscriber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class UpdateEdukaSchema10 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (Subscriber::all() as $subscriber) {
            $subscriber->uuid = Str::random(40);
            $subscriber->save();
        }
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
