<?php

use Eduka\Database\Seeders\InitialSchemaSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEdukaSchema extends Migration
{
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->comment('Organization name. An organization aggregates courses');

            $table->text('description')
                ->nullable();

            $table->string('domain')
                ->comment('The backend domain where users will log in');

            $table->string('provider_namespace')
                ->comment('Class for the backend management package');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->comment('Course marketing name');

            $table->string('admin_name')
                ->comment('The course admin user name (a.k.a. the instructor)');

            $table->string('admin_email')
                ->comment('The course admin user email');

            $table->uuid('uuid');

            $table->string('canonical')
                ->unique();

            $table->string('twitter_handle')
                ->nullable();

            $table->string('linkedin_url')
                ->nullable();

            $table->string('instagram_handle')
                ->nullable();

            $table->longText('meta_names')
                ->nullable()
                ->comment('Array of meta SEO tags for the HEADER tag, key=tag name, value=tag value');

            $table->longText('meta_properties')
                ->nullable()
                ->comment('Array of meta SEO tags for the HEADER tag, key=tag name, value=tag value');

            $table->string('filename')
                ->nullable()
                ->comment('SEO image for social integration');

            $table->string('domain')
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

            $table->foreignId('organization_id')
                ->constrained()
                ->comment('The related organization');

            $table->unsignedInteger('progress')
                ->default(0)
                ->comment('The current course completion progress, for release');

            $table->boolean('is_ppp_enabled')
                ->default(true)
                ->comment('Does the course enables PPP capability');

            $table->string('lemon_squeezy_store_id')
                ->comment('The LS store id, even if they are multiple variants, they will all belong to the same store');

            $table->text('lemon_squeezy_api_key')
                ->comment('The LS api key, for checkout generation scope');

            $table->text('lemon_squeezy_hash_key')
                ->comment('The LS hash key, for webhook calls verification');

            $table->string('vimeo_uri')
                ->nullable()
                ->comment('The Vimeo folder URI, for sub-folders creation. Please refer to the Vimeo API reference');

            $table->string('vimeo_folder_id')
                ->nullable()
                ->comment('The Vimeo folder ID, for folder renaming. Please refer to the Vimeo API reference');

            $table->string('backblaze_bucket_name')
                ->nullable()
                ->comment('backblaze bucket id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {

            // Dropping columns that somehow can't be changed.
            $table->dropColumn([
                'email_verified_at',
                'email',
                'password',
            ]);

            $table->string('name')
                ->nullable()
                ->change();

            $table->timestamp('previous_logged_in_at')
                ->nullable()
                ->after('remember_token')
                ->comment('This column and the last_logged_in_at allows to create a date interval to compute actions that happened between the current and last login');

            $table->timestamp('last_logged_in_at')
                ->nullable()
                ->after('previous_logged_in_at');

            $table->softDeletes();
        });

        /**
         * Strangely we can't apply a change() for a text() type
         * so we need to drop the column and recreate it.
         *
         * Email can be nullable because later on we will use OAuth
         * platforms to authenticate the user.
         */
        Schema::table('users', function (Blueprint $table) {
            $table->text('email')
                ->nullable()
                ->after('name');

            $table->string('password')
                ->nullable()
                ->after('email');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->comment('The tag name');

            $table->longText('description')
                ->nullable()
                ->comment('The tag description');

            $table->foreignId('course_id')
                ->constrained();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['id', 'course_id']);
        });

        Schema::create('course_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained();

            $table->foreignId('user_id')
                ->constrained();

            $table->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained();

            $table->string('name')
                ->nullable();

            $table->text('email');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('variants', function (Blueprint $table) {
            $table->id();

            $table->uuid();

            $table->string('name');

            $table->string('canonical')
                ->unique();

            $table->text('description')
                ->nullable()
                ->comment('The variant description, to understand what it is');

            $table->foreignId('course_id')
                ->constrained()
                ->comment('Related course');

            $table->longText('lemon_squeezy_data')
                ->nullable()
                ->comment('Related Lemon Squeezy repository data');

            $table->string('lemon_squeezy_variant_id')
                ->nullable();

            $table->decimal('lemon_squeezy_price_override', 10, 2)
                ->nullable()
                ->comment('In case we would like to override the variant lemonsqueezy default price');

            $table->boolean('is_default')
                ->default(false)
                ->comment('In case no variant is passed to the Eduka payments gateway, it will use the variant id from the default one here');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_variant', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained();

            $table->foreignId('variant_id')
                ->constrained();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained();

            $table->foreignId('course_id')
                ->constrained();

            $table->foreignId('variant_id')
                ->constrained()
                ->comment('This is the related variants.id FK, not the lemon squeezy variant id!');

            $table->string('provider')
                ->default('lemon-squeezy')
                ->comment('Normally is paddle or lemon-squeezy');

            $table->string('country')
                ->nullable()
                ->comment('Cloudflare country code (// https://developers.cloudflare.com/fundamentals/reference/http-request-headers/)');

            $table->longText('response_body');

            $table->longText('custom_data')
                ->nullable();

            $table->string('event_name')
                ->nullable();

            $table->string('store_id')
                ->nullable();

            $table->string('customer_id')
                ->nullable();

            $table->string('order_number')
                ->nullable();

            $table->string('user_name')
                ->nullable();

            $table->string('user_email')
                ->nullable();

            $table->string('subtotal_usd')
                ->nullable();

            $table->string('discount_total_usd')
                ->nullable();

            $table->string('tax_usd')
                ->nullable();

            $table->string('total_usd')
                ->nullable();

            $table->string('tax_name')
                ->nullable();

            $table->string('status')
                ->nullable();

            $table->boolean('refunded')
                ->nullable();

            $table->string('refunded_at')
                ->nullable();

            $table->string('order_id')
                ->nullable();

            $table->string('lemon_squeezy_product_id')
                ->nullable();

            $table->string('lemon_squeezy_variant_id')
                ->nullable();

            $table->string('lemon_squeezy_product_name')
                ->nullable();

            $table->string('lemon_squeezy_variant_name')
                ->nullable();

            $table->string('price');

            $table->text('receipt')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained();

            $table->string('name')
                ->comment('The chapter name');

            $table->unsignedInteger('index')
                ->comment('Index number inside the course');

            $table->longText('description')
                ->nullable()
                ->comment('Some extra details about this chapter subject');

            $table->longText('meta_names')
                ->nullable()
                ->comment('Array of meta SEO tags for the HEADER tag, key=tag name, value=tag value');

            $table->longText('meta_properties')
                ->nullable()
                ->comment('Array of meta SEO tags for the HEADER tag, key=tag name, value=tag value');

            $table->string('filename')
                ->nullable();

            $table->string('vimeo_uri')
                ->nullable()
                ->comment('The Vimeo folder URI, for sub-folders creation. Please refer to the Vimeo API reference');

            $table->string('vimeo_folder_id')
                ->nullable()
                ->comment('The Vimeo folder ID, for folder renaming. Please refer to the Vimeo API reference');

            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('old_id')
                ->nullable();

            $table->string('name')
                ->comment('The title of the video');

            $table->longText('description')
                ->nullable()
                ->comment('More information about this video');

            $table->foreignId('course_id')
                ->constrained();

            $table->foreignId('chapter_id')
                ->nullable()
                ->constrained();

            $table->unsignedInteger('index')
                ->comment('The video index in the respective chapter');

            $table->uuid('uuid')
                ->unique()
                ->comment('The url uuid to direct link this video. Unique identifier');

            $table->longText('meta_names')
                ->nullable()
                ->comment('Array of meta names SEO tags for the HEADER tag, key=tag name, value=tag value');

            $table->longText('meta_properties')
                ->nullable()
                ->comment('Array of meta properties SEO tags for the HEADER tag, key=tag name, value=tag value');

            $table->string('canonical')
                ->unique()
                ->comment('The kebab case video name');

            $table->string('temp_filename_path')
                ->nullable()
                ->comment('The physical filename path, where a physical file is added, used to upload the video to Vimeo/Youtube/Backblaze');

            $table->string('vimeo_uri')
                ->nullable()
                ->comment('Vimeo uri path, when the video is uploaded to Vimeo');

            $table->unsignedInteger('duration')
                ->nullable()
                ->comment('Video duration, in seconds');

            $table->boolean('is_visible')
                ->default(false)
                ->comment('If the video can be presented on screen (doesnt mean is clickable)');

            $table->boolean('is_active')
                ->default(false)
                ->comment('If when the video appears, it can be clickable, interactable, etc');

            $table->boolean('is_free')
                ->default(false)
                ->comment('When a video is free it doesnt need to be accessible via a logged/paid in page');

            $table->string('filename')
                ->nullable()
                ->comment('Used only on the moment we are locally storing the video for uploads to external platforms (YouTube, Vimeo, Backblaze, etc)');

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
                ->constrained()
                ->nullable()
                ->comment('Related video id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('series', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->comment('The series name');

            $table->longText('description')
                ->nullable()
                ->comment('Some extra details about this series subject');

            $table->foreignId('course_id')
                ->constrained()
                ->comment('Relatable course id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('series_video', function (Blueprint $table) {
            $table->id();

            $table->foreignId('series_id')
                ->constrained();

            $table->foreignId('video_id')
                ->constrained();

            $table->unsignedInteger('index')
                ->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tag_video', function (Blueprint $table) {
            $table->id();

            $table->foreignId('video_id')
                ->constrained();

            $table->foreignId('tag_id')
                ->constrained();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chapter_variant', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chapter_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('variant_id')
                ->constrained()
                ->onDelete('cascade');

            $table->unsignedInteger('index')
                ->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_video_seen', function (Blueprint $table) {
            $table->id();

            $table->foreignId('video_id')
                ->constrained()
                ->nullable()
                ->comment('Related video id');

            $table->foreignId('user_id')
                ->constrained()
                ->nullable()
                ->comment('Related video id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_video_bookmarked', function (Blueprint $table) {
            $table->id();

            $table->foreignId('video_id')
                ->constrained()
                ->nullable()
                ->comment('Related video id');

            $table->foreignId('user_id')
                ->constrained()
                ->nullable()
                ->comment('Related video id');

            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * This is part of the eduka middleware log, where we log
         * all the requests that were called, what site context was detected
         * and what request payload was received, http headers, etc.
         */
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable();

            $table->string('referrer')
                ->nullable()
                ->comment('The referer header in case it exists');

            $table->text('url')
                ->comment('The full URL request, including querystring values');

            $table->longText('payload')
                ->nullable();

            $table->longText('headers')
                ->nullable();

            $table->string('route')
                ->nullable();

            $table->longText('parameters')
                ->nullable();

            $table->longText('middleware')
                ->nullable();

            $table->foreignId('organization_id')
                ->nullable();

            $table->foreignId('course_id')
                ->nullable();

            $table->timeStamps();
        });

        // Run initial framework schema seeder.
        $seeder = new InitialSchemaSeeder();
        $seeder->run();
    }

    public function down()
    {
        //
    }
}
