<?php

use Eduka\Database\Seeders\InitialSchemaSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEdukaProgressiveSchema extends Migration
{
    public function up()
    {
        if (!Schema::connection(env('PROGRESSIVE_DB_CONNECTION'))
                   ->hasTable('subscribers')) {
            Schema::connection(env('PROGRESSIVE_DB_CONNECTION'))
                  ->create('subscribers', function (Blueprint $table) {
                    $table->id();

                    $table->foreignId('course_id')
                      ->constrained();

                    $table->string('name')
                      ->nullable();

                    $table->text('email');

                    $table->timestamps();
                  });
        };

        if (!Schema::connection(env('PROGRESSIVE_DB_CONNECTION'))->hasTable('backends')) {
            Schema::connection(env('PROGRESSIVE_DB_CONNECTION'))->create('backends', function (Blueprint $table) {
                $table->id();

                $table->string('name')
                ->comment('Backend name, it aggregates courses');

                $table->text('description')
                ->nullable();

                $table->string('domain')
                ->comment('The backend domain where students will log in');

                $table->string('provider_namespace')
                ->comment('Class for the backend management package');

                $table->timestamps();
            });
        };

        if (!Schema::connection(env('PROGRESSIVE_DB_CONNECTION'))->hasTable('courses')) {
            Schema::connection(env('PROGRESSIVE_DB_CONNECTION'))->create('courses', function (Blueprint $table) {
                $table->id();

                $table->string('name')
                ->nullable()
                ->comment('Course marketing name');

                $table->text('description')
                ->nullable()
                ->comment('A more detailed description of this course, what is it about');

                $table->foreignId('student_admin_id')
                ->nullable()
                ->comment('Related student (user) admin acting as admin');

                $table->uuid('uuid')
                ->nullable();

                $table->string('canonical')
                ->nullable()
                ->unique();

                $table->string('twitter_handle')
                ->nullable();

                $table->string('filename_twitter')
                ->nullable()
                ->comment('Twitter image (downloaded directly from twitter)');

                $table->string('filename_logo')
                ->nullable()
                ->comment('Logo image for social integration');

                $table->string('theme_color')
                ->default('#000000')
                ->comment('Course primary theme color (used for theme coloring purposes, like newsletters, etc)');

                $table->string('domain')
                ->nullable()
                ->unique()
                ->comment('The domain where this course is shown (e.g.: the course landing page)');

                $table->string('provider_namespace')
                ->nullable()
                ->comment("Course provider namespace. E.g.: 'MasteringNova\\MasteringNovaServiceProvider'");

                $table->dateTime('prelaunched_at')
                ->nullable()
                ->comment('The date where the course was/will be prelaunched');

                $table->dateTime('launched_at')
                ->nullable()
                ->comment('The date where the course was/will be launched');

                $table->dateTime('retired_at')
                ->nullable()
                ->comment('The date where the course was/will be retired');

                $table->boolean('is_active')
                ->default(true)
                ->comment('Defines if course is active and viewable. If active and launched_at in the future, then it is in prelaunch mode');

                $table->string('clarity_code')
                ->nullable()
                ->comment('Microsoft clarity code for analytics');

                $table->foreignId('backend_id')
                ->nullable()
                ->constrained()
                ->comment('The related backend');

                $table->unsignedInteger('progress')
                ->default(0)
                ->comment('The current course completion progress, for release');

                $table->boolean('is_ppp_enabled')
                ->default(true)
                ->comment('Does the course enables PPP capability');

                $table->string('lemon_squeezy_store_id')
                ->nullable()
                ->comment('The LS store id, even if they are multiple variants, they will all belong to the same store');

                $table->text('lemon_squeezy_api_key')
                ->nullable()
                ->comment('The LS api key, for checkout generation scope');

                $table->text('lemon_squeezy_hash')
                ->nullable()
                ->comment('The LS hash key, for webhook calls verification');

                $table->string('vimeo_uri')
                ->nullable()
                ->comment('The Vimeo folder URI, for sub-folders creation. Please refer to the Vimeo API reference');

                $table->string('vimeo_folder_id')
                ->nullable()
                ->comment('The Vimeo folder ID, for folder renaming. Please refer to the Vimeo API reference');

                $table->timestamps();
            });
        };
    }

    public function down()
    {
        //
    }
}
