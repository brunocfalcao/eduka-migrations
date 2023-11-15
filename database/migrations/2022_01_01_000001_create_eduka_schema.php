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
                ->comment('The url uuid to direct link this video. Unique identifier');

            $table->boolean('is_visible')
                ->default(false)
                ->comment('If the video can be presented on screen (doesnt mean is clickable)');

            $table->boolean('is_active')
                ->default(false)
                ->comment('If when the video appears, it can be clickable, interactable, etc');

            $table->boolean('is_free')
                ->default(false)
                ->comment('When a video is free it doesnt need to be accessible via a logged/paid in page');

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_canonical_url')->nullable();

            $table->timestamps();
            $table->softDeletes();
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

            $table->string('canonical')
                  ->required()
                  ->unique();

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

            $table->string('payment_provider_variant_id')
                ->nullable()
                ->comment('product id of the current payment provider. eg: lemon squeezy (variant id), stripe etc');

            $table->string('payment_provider_store_id')
                ->nullable()
                ->comment('store id of the current payment provider. eg: lemon squeezy, stripe etc');

            $table->decimal('course_price', 8, 2)->comment('Do not use cents. For 100$ course, use 100.')->nullable();
            $table->boolean('enable_purchase_power_parity')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });

        /**
         * Domains are used to connect an url to a respective course.
         * We can have 1-N relationships, meaning several domains pointing
         * to the same course. Each entry will always be a course
         * domain, and not the backend domain.
         */
        Schema::create('domains', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->comment('Domain name, without HTTP preffix neither "www.". E.g.: "cnn.com"');

            $table->foreignId('course_id')
                ->nullable()
                ->comment('The related course instance');

            $table->timestamps();
            $table->softDeletes();
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

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id');
            $table->string('email');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->decimal('discount_amount');
            $table->boolean('is_flat_discount')->default(true);
            $table->string('remote_reference_id')->nullable()->comment('coupon id in lemon squeezy or equivalent');
            $table->string('country_iso_code')->nullable();
            $table->string('coupon_code_template')
                ->comment('The template provides a way to create coupons in a specific pattern.')
                ->nullable();

            $table->foreignId('course_id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id');
            $table->integer('course_id');
            $table->text('response_body')->nullable();
            // @todo break down everything from response body

            $table->string('remote_reference_order_id')->nullable()->comment('should not be nullable.the order id that was created on 3rd party payment provider. eg: lemon squeezy');
            $table->string('remote_reference_customer_id')->nullable()->comment('should not be nullable.the customer id that was created on 3rd party payment provider. eg: lemon squeezy');
            $table->string('remote_reference_order_attribute_id')->nullable()->comment('should not be nullable.the payload.data.attributes.id that was created on 3rd party payment provider. eg: lemon squeezy, 5688a31e-cf51-4fa8-8615-c52c54327e4e');

            $table->string('currency_id')->nullable();
            $table->string('remote_reference_payment_status')->nullable();
            $table->timestamp('refunded_at')->nullable()->comment('nullable means it was not refunded');

            $table->integer('tax')->default(0)->comment('in cents');
            $table->integer('discount_total')->default(0)->comment('in cents');
            $table->integer('subtotal')->default(0)->comment('in cents');
            $table->integer('total')->default(0)->comment('in cents');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('video_storages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id');
            $table->string('vimeo_id')->nullable()->comment('vimeo_ids are videos/1234');
            $table->string('backblaze_id')->nullable();
            $table->timestamps();
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
