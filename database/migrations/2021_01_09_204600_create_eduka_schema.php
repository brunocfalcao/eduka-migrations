<?php

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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The affiliate name');

            $table->string('description')
                  ->comment('The affiliate description')
                  ->nullable();

            $table->string('domain')
                        ->nullable()
                        ->comment('The affiliate domain url hostname for the respective affiliate');

            $table->string('utm_source')
                  ->nullable()
                  ->comment('If there is no HTTP header, then we can use the utm_source value to identify this affiliate');

            $table->foreignId('course_id')
                  ->nullable()
                  ->comment('The related course id');

            $table->unsignedInteger('paddle_vendor_id')
                  ->comment('The affiliate paddle vendor id, not unique since we can have the same vendor id for multiple products');

            $table->unsignedInteger('commission_percentage')
                  ->nullable()
                  ->comment('The commission that is given the to the affiliate when a checkout is completed');

            $table->string('type')
            ->default('referrer')
                  ->comment('Can be type referrer or type fixed. Fixed type will always have a commission no matter if there is a referrer id or not');

            $table->date('starts_at')
                  ->nullable()
                  ->comment('The start date for the affiliate commission process');

            $table->date('ends_at')
                  ->nullable()
                  ->comment('The end date for the affiliate commission process');

            $table->softDeletes();

            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();

            $table->string('code');
            $table->string('name');
            $table->decimal('ppp_index', 10, 2)
                  ->nullable();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->nullable()
                  ->comment('Relatable course id');

            $table->string('uuid')
                  ->comment('The current payment session uuid. If it is different from the client session id then the payment checkout data gets refreshed')
            ->unique();

            $table->string('name')
                  ->comment('Product name used in the Paddle');

            $table->string('type')
            ->default('default')
                  ->nullable()
                  ->comment('Product type to differ product prices/affiliates inside the the same course');

            $table->unsignedInteger('discount_percentage')
            ->default(0)
                  ->comment('A global discount percentage that will be used on any paddle pricing source');

            $table->ipAddress('testing_ip')
                  ->nullable()
                  ->comment('If filled, will simulated a client IP address');

            $table->unsignedInteger('paddle_product_id')
                  ->nullable()
                  ->comment('Paddle product id');

            $table->unsignedInteger('paddle_vendor_id')
                  ->nullable()
                  ->comment('Paddle vendor id');

            $table->string('paddle_auth_code')
                  ->nullable()
                  ->comment('Paddle authorization code');

            $table->longText('paddle_public_key')
                  ->nullable()
                  ->comment('Paddle public key');

            $table->boolean('using_ppp')
            ->default(false)
                  ->comment('If it has PPP means the price will always be computed with PPP given the user ip address');

            $table->boolean('using_session')
            ->default(false)
                  ->comment('Uses payment session logic, not. The paylink session is also overriden with this value');

            $table->boolean('using_testing_environment')
            ->default(true)
                  ->comment('Affects the paddle api in case we are using a testing environment or not');

            $table->index(['course_id',  'type']);

            $table->softDeletes();

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')
                  ->default(false)
                  ->comment('if admin, can login into Nova, and Horizon, besides additional features in the backend')
                  ->after('password');

            $table->boolean('session_delete')
                  ->default(false)
                  ->comment('In case we want to force delete all session variables')
                  ->after('is_admin');
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->boolean('is_active')
            ->default(false)
                  ->comment('If a course is active then it can be bought and there is no pre-subscription');

            $table->longText('meta_tags')
                  ->nullable()
                  ->comment('The HTML meta attributes that are used for social integration (twitter and facebook)');

            $table->string('meta_image')
                  ->nullable()
                  ->comment('Course social image');

            $table->dateTimeTz('launched_at')
                  ->nullable()
                  ->comment('The launch date time of your course');
        });

        Schema::create('application_log', function (Blueprint $table) {
            $table->id();

            $table->string('session_id')
                  ->nullable();

            $table->unsignedBigInteger('causable_id')
                  ->nullable();

            $table->string('causable_type')
                  ->comment('The causable can be a visitor id (if not contexted as user) or an user id')
                  ->nullable();

            $table->unsignedBigInteger('relatable_id')
                  ->nullable();

            $table->string('relatable_type')
                  ->comment('The relatable can be any model instance that we would like to relate')
                  ->nullable();

            $table->string('group')
                  ->nullable()
                  ->comment('A process label code that will allow to group loggings');

            $table->string('description')
                  ->nullable()
                  ->comment('A natural description of the activity');

            $table->longText('properties')
                  ->nullable();

            $table->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->nullable();

            $table->string('email')
                  ->unique();

            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('subscriber_id')
                  ->comment('The respective subscriber in case it exists')
                  ->nullable()
                  ->after('email')
                  ->constrained();
        });

        Schema::create('series', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')
                  ->nullable()
                  ->comment('Series uuid for routing reasons');

            $table->unsignedInteger('index')
                  ->nullable()
                  ->comment('The series index, autonumbered');

            $table->foreignId('course_id')
                  ->nullable()
                  ->comment('Relatable course id');

            $table->string('title')
                  ->comment('The series title');

            $table->timestamps();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')
                  ->comment('Chapter uuid for routing reasons')
                  ->nullable();

            $table->foreignId('series_id')
                  ->nullable()
                  ->comment('Related series (3.x, 4.x, etc)');

            $table->string('title')
                  ->comment('The chapter title');

            $table->foreignId('course_id')
                  ->nullable();

            $table->string('introduction')
                  ->comment('The short, straight, chapter introduction')
                  ->nullable();

            $table->longText('details')
                  ->comment('A longer version of the introduction')
                  ->nullable();

            $table->unsignedInteger('index')
                  ->comment('The chapter index, giving the correct chapter order')
                  ->nullable();

            $table->boolean('is_enabled')
                  ->default(false);

            $table->string('meta_image')
                  ->comment('Chapter social image')
                  ->nullable();

            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')
                  ->comment('Video uuid for routing reasons')
                  ->nullable();

            $table->string('title')
                  ->comment('The video title');

            $table->string('introduction')
                  ->nullable()
                  ->comment('The short, straight, video introduction');

            $table->longText('details')
                  ->comment('A longer version of the introduction')
                  ->nullable();

            $table->foreignId('series_id')
                  ->nullable()
                  ->comment('Related series (3.x, 4.x, etc)');

            $table->unsignedBigInteger('vimeo_id')
                  ->comment('Vimeo video id (should be not visible in Vimeo in case it is a premium video)')
                  ->unique()
                  ->nullable();

            $table->unsignedInteger('duration')
                  ->comment('Video duration, in seconds')
                  ->nullable();

            $table->string('meta_image')
                  ->comment('Video social image')
                  ->nullable();

            $table->foreignId('chapter_id')
                  ->comment('The respective chapter that the video belongs to')
                  ->nullable()
                  ->constrained();

            $table->unsignedInteger('index')
                  ->comment('The video index, giving the correct videos chapter orderings')
                  ->nullable();

            $table->boolean('is_enabled')
                  ->default(false)
                  ->comment('If enabled that it can be possible to be seen (published_at < now()) or clicked (published_at >= now())');

            $table->boolean('is_free')
                  ->default(false)
                  ->comment('Allows a video to be seen without purchasing a specific course');

            $table->string('filename')
                  ->unique()
                  ->nullable()
                  ->comment('Stores the Backblaze bucket video filename');

            $table->timestamps();
        });

        Schema::create('videos_completed', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('video_id');
            $table->unsignedInteger('user_id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained();

            $table->foreignId('video_id')
                  ->constrained();

            $table->softDeletes();

            $table->timestamps();
        });

        Schema::create('watch_later', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained();

            $table->foreignId('video_id')
                  ->constrained();

            $table->softDeletes();

            $table->timestamps();
        });

        Schema::create('marked_as_seen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained();

            $table->foreignId('video_id')
                  ->constrained();

            $table->softDeletes();

            $table->timestamps();
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
