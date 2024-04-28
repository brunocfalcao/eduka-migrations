<?php

use Eduka\Database\Seeders\InitialSchemaSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEdukaProgressiveSchema extends Migration
{
    public function up()
    {
        if (!Schema::connection(env('PROGRESSIVE_DB_CONNECTION'))->hasTable('subscribers')) {
            Schema::connection(env('PROGRESSIVE_DB_CONNECTION'))
            ->create('subscribers', function (Blueprint $table) {
                $table->id();

                $table->unsignedInteger('course_id')
                      ->constrained();

                $table->string('name')
                      ->nullable();

                $table->text('email');

                $table->timestamps();
            });
        }
    }

    public function down()
    {
        //
    }
}
