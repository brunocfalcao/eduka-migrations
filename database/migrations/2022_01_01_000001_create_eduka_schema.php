<?php

use Eduka\Database\Seeders\InitialSchemaSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEdukaSchema extends Migration
{
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('Course marketing name');

            $table->string('canonical')
                  ->unique();

            $table->longText('meta')
                  ->nullable()
                  ->comment('Array of meta SEO tags for the HEADER tag, key=tag name, value=tag value');

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

            $table->boolean('is_ppp_enabled')
                  ->default(true)
                  ->comment('Does the course enables PPP capability');

            $table->string('lemon_squeezy_store_id')
                  ->nullable()
                  ->comment('The LS store id, even if they are multiple variants, they will all belong to the same store');

            $table->string('vimeo_uri_key')
                  ->nullable()
                  ->comment('The Vimeo folder ID, please refer to the Vimeo API reference');

            $table->string('backblaze_bucket_name')
                  ->nullable()
                  ->comment('backblaze bucket id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {

            // No need to use the email verification.
            $table->dropColumn(['email_verified_at']);

            // Name and password are not mandatory. Only email is.
            $table->string('name')
                  ->nullable()
                  ->change();

            $table->string('password')
                  ->change();

            $table->string('twitter_handle')
                  ->nullable()
                  ->after('password');

            $table->foreignId('course_id_as_admin')
                  ->after('twitter_handle')
                  ->constrained()
                  ->nullable()
                  ->comment('The course id where the user has an admin role');
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

            $table->string('email');

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

        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The title of the video');

            $table->longText('description')
                  ->nullable()
                  ->comment('More information about this video');

            $table->foreignId('course_id')
                  ->constrained();

            $table->uuid('uuid')
                  ->unique()
                  ->comment('The url uuid to direct link this video. Unique identifier');

            $table->string('canonical')
                  ->unique()
                  ->comment('The kebab case video name');

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

            $table->string('vimeo_id')
                  ->nullable()
                  ->comment('Vimeo video related id');

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

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained();

            $table->string('name')
                  ->comment('The chapter name');

            $table->longText('description')
                  ->nullable()
                  ->comment('Some extra details about this chapter subject');

            $table->string('vimeo_uri_key')
                  ->nullable()
                  ->comment('The Vimeo folder ID, please refer to the Vimeo API reference');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chapter_video', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chapter_id')
                  ->constrained();

            $table->foreignId('video_id')
                  ->constrained();

            $table->unsignedInteger('index')
                  ->default(1);

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

        Schema::create('variants', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('canonical')
                  ->unique();

            $table->longText('description')
                  ->nullable()
                  ->comment('The variant description, to understand what it is');

            $table->foreignId('course_id')
                  ->constrained()
                  ->comment('Related course');

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

        Schema::create('chapter_variant', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chapter_id')
                  ->constrained();

            $table->foreignId('variant_id')
                  ->constrained();

            $table->unsignedInteger('index')
                  ->default(1);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_video_completed', function (Blueprint $table) {
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

        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained();

            $table->foreignId('variant_id')
                  ->constrained()
                  ->comment('This is the related variants.id FK, not the lemon squeezy variant id!');

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

            $table->string('lemon_squeezy_product_id');

            $table->string('lemon_squeezy_variant_id');

            $table->string('lemon_squeezy_product_name');

            $table->string('lemon_squeezy_variant_name');

            $table->string('price');

            $table->text('receipt')
                  ->nullable();

            $table->timestamps();
            $table->softDeletes();
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
