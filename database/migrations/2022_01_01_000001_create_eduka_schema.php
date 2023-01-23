<?php

use Eduka\Database\Seeders\InitialSchemaSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEdukaSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('link')
                  ->comment('The link for the paddle gate');

            $table->foreignId('user_id')
                  ->comment('The relatable user id');

            $table->foreignId('course_id')
                  ->comment('The relatable course id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The chapter name');

            $table->string('details')
                  ->nullable()
                  ->comment('Some extra details about this chapter subject');

            $table->unsignedInteger('index')
                  ->comment('Chapter index related to the course that it belongs to');

            $table->foreignId('course_id')
                  ->nullable()
                  ->comment('Related course id');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['id', 'index']);
        });

        Schema::create('series', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The series name');

            $table->string('details')
                  ->nullable()
                  ->comment('Some extra details about this series subject');

            $table->foreignId('course_id')
                  ->nullable()
                  ->comment('Related course id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The tag name');

            $table->foreignId('course_id')
                  ->nullable()
                  ->comment('Related course id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('links', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The link name');

            $table->string('url')
                  ->comment('The link url');

            $table->foreignId('video_id')
                  ->nullable()
                  ->comment('Related video id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('videos_completed', function (Blueprint $table) {
            $table->id();

            $table->foreignId('video_id')
                  ->nullable()
                  ->comment('Related video id');

            $table->foreignId('user_id')
                  ->nullable()
                  ->comment('Related video id');

            $table->index(['video_id', 'user_id']);
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The title of the video');

            $table->longText('details')
                  ->nullable()
                  ->comment('More information about this video');

            $table->unsignedInteger('vimeo_id')
                  ->nullable()
                  ->comment('Vimeo video related id');

            $table->unsignedInteger('duration')
                  ->nullable()
                  ->comment('Video duration, in seconds');

            $table->uuid('uuid')
                  ->nullable()
                  ->unique()
                  ->comment('The url suffix to direct link this video. Unique identifier');

            $table->boolean('is_visible')
                  ->default(false)
                  ->comment('If the video can be presented on screen (doesnt mean is clickable)');

            $table->boolean('is_active')
                  ->default(false)
                  ->comment('If when the video appears, it can be clickable, interactable, etc');

            $table->boolean('is_free')
                  ->default(false)
                  ->comment('When a video is free it doesnt need to be accessible via a logged/paid in page');

            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * Schema table creation of the visits table.
         */
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            $table->string('session_id')
                  ->nullable()
                  ->comment('Session id (php session id) from the visit source');

            $table->foreignId('user_id')
                  ->nullable()
                  ->comment('Relatable user id, if existing');

            $table->foreignId('course_id')
                  ->nullable()
                  ->default(null)
                  ->comment('Related course id in case it exists');

            $table->foreignId('goal_id')
                  ->nullable()
                  ->comment('If a goal (E.g.: "course bought") is achieved, is it written here');

            $table->foreignId('affiliate_id')
                  ->nullable()
                  ->comment('Affilate related id, if exists and is connected to via referrer data. Session persisted');

            $table->string('url')
                  ->comment('The full qualified url path, with querystrings');

            $table->string('path')
                  ->comment('The url path, not including the full url');

            $table->string('route_name')
                  ->nullable()
                  ->comment('Route name in case it exists');

            $table->string('referrer_utm_source')
                  ->nullable()
                  ->comment('Referrer utm_source querystring, e.g.: ?utm_source=xxx, if it exists');

            $table->string('referrer_domain')
                  ->nullable()
                  ->comment('Referrer url http header if it is present');

            $table->string('referrer_campaign')
                  ->nullable()
                  ->comment('Querystring ?cmpg=xxx if it is present');

            $table->boolean('is_bot')
                  ->default(false);

            $table->ipAddress('ip')
                  ->nullable();

            $table->string('hash') // GDPR reasons. Identifies a visit source.
                  ->nullable()
                  ->comment('The visit hashable identity. GDPR, is encrypted. Created as md5(request()->ip().Agent::platform().Agent::device())');

            $table->string('continent')
                  ->nullable();

            $table->string('continentCode')
                  ->nullable();

            $table->string('country')
                  ->nullable();

            $table->string('countryCode')
                  ->nullable();

            $table->string('region')
                  ->nullable();

            $table->string('regionName')
                  ->nullable();

            $table->string('city')
                  ->nullable();

            $table->string('district')
                  ->nullable();

            $table->string('zip')
                  ->nullable();

            $table->decimal('latitude', 11, 7)
                  ->nullable();

            $table->decimal('longitude', 11, 7)
                  ->nullable();

            $table->string('timezone')
                  ->nullable();

            $table->string('currency')
                  ->nullable();

            $table->timestamps();
        });

        /**
         * Changes to the default users table.
         */
        Schema::table('users', function (Blueprint $table) {

            // No need to use the email verification.
            $table->dropColumn(['email_verified_at']);

            // Name and password are not mandatory. Only email is.
            $table->string('name')
                  ->nullable()
                  ->change();

            $table->string('password')
                  ->nullable()
                  ->change();

            $table->uuid('uuid')
                  ->nullable()
                  ->after('remember_token')
                  ->comment('Used for e.g. view user data');

            $table->boolean('receives_notifications')
                  ->default(true)
                  ->after('remember_token')
                  ->comment('Global flag that enables or disables notifications sent to the user');

            $table->unsignedInteger('old_id')
                  ->nullable()
                  ->after('id');

            $table->softDeletes();
        });

        /**
         * Courses are the heart instance of eduka. They are contextualized
         * via a domain url, and then used via product paylinks for purchases.
         */
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('Course marketing name');

            $table->string('meta_description')
                  ->nullable()
                  ->comment('A bit more information about the course');

            $table->string('meta_twitter_alias')
                  ->nullable()
                  ->comment('Meta tag twitter:site');

            $table->string('meta_title')
                  ->nullable()
                  ->comment('Meta tag *:title and <title> tags');

            $table->string('admin_name')
                  ->nullable()
                  ->comment('Admin name, for notifications');

            $table->string('admin_email')
                  ->nullable()
                  ->comment('Admin email, for notifications');

            $table->string('twitter_handle')
                  ->nullable()
                  ->comment('Twitter handle, for email signatures, without the (at) symbol and without url prefix');

            $table->string('provider_namespace')
                  ->nullable()
                  ->comment("Service provider namespace. E.g.: 'MasteringNova\\MasteringNovaServiceProvider'");

            $table->boolean('is_decommissioned')
                  ->default(false)
                  ->comment('Global flag to disable a course. When a course is decommissioned, it cannot be purchased');

            $table->dateTime('launched_at')
                  ->nullable()
                  ->comment('The date where the course was/will be launched');

            $table->softDeletes();
            $table->timestamps();
        });

        /**
         * Domains are used to connect an url to a respective course.
         * We can have 1-N relationships, meaning several domains pointing
         * to the same course. Each entry will always be a course
         * domain, and not the backend domain.
         */
        Schema::create('domains', function (Blueprint $table) {
            $table->id();

            $table->string('suffix')
                  ->comment('Domain url suffix, without HTTP preffix neither "www.". E.g.: "cnn.com"');

            $table->foreignId('course_id')
                  ->nullable()
                  ->comment('The related course instance');

            $table->softDeletes();
            $table->timestamps();
        });

        /**
         * N-N tables.
         */
        Schema::create('chapter_video', function (Blueprint $table) {
            $table->id();

            $table->foreignId('video_id');
            $table->foreignId('chapter_id');
            $table->unsignedInteger('index');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('series_video', function (Blueprint $table) {
            $table->id();

            $table->foreignId('video_id');
            $table->foreignId('series_id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tag_video', function (Blueprint $table) {
            $table->id();

            $table->foreignId('video_id');
            $table->foreignId('tag_id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id');
            $table->foreignId('user_id');

            $table->timestamps();
            $table->softDeletes();
        });

        // Run initial framework schema seeder.
        $seeder = new InitialSchemaSeeder();
        $seeder->run();
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
