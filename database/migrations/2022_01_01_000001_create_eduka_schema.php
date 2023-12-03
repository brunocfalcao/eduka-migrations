<?php

use Eduka\Database\Seeders\InitialSchemaSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEdukaSchema extends Migration
{
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The title of the video');

            $table->longText('description')
                  ->nullable()
                  ->comment('More information about this video');

            $table->string('vimeo_id')
                  ->nullable()
                  ->comment('Vimeo video related id');

            $table->unsignedInteger('duration')
                  ->nullable()
                  ->comment('Video duration, in seconds');

            $table->uuid('uuid')
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

            $table->string('meta_title')
                  ->nullable();

            $table->string('meta_description')
                  ->nullable();

            $table->string('meta_canonical_url')
                  ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The chapter name');

            $table->longText('description')
                  ->nullable()
                  ->comment('Some extra details about this chapter subject');

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

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The tag name');

            $table->longText('description')
                  ->nullable()
                  ->comment('The tag description');

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

        Schema::create('videos_completed', function (Blueprint $table) {
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
                  ->after('remember_token')
                  ->comment('Used for e.g. view user data');

            $table->boolean('receives_notifications')
                  ->default(true)
                  ->after('remember_token')
                  ->comment('Global flag that enables or disables notifications sent to the user');

            $table->unsignedInteger('old_id')
                  ->nullable()
                  ->after('id');

            $table->timestamp('deleted_at', 0)
                  ->after('updated_at')
                  ->nullable();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('Course marketing name');

            $table->string('canonical')
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
                  ->comment('Admin name, for notifications');

            $table->string('admin_email')
                  ->comment('Admin email, for notifications');

            $table->string('twitter_handle')
                  ->nullable()
                  ->comment('Twitter handle, for email signatures, without the (at) symbol and without url prefix');

            $table->string('provider_namespace')
                  ->comment("Service provider namespace. E.g.: 'MasteringNova\\MasteringNovaServiceProvider'");

            $table->string('lemonsqueezy_store_id')
                  ->nullable()
                  ->comment('The LS store id, even if they are multiple variants, they will all belong to the same store');

            $table->boolean('is_decommissioned')
                  ->default(false)
                  ->comment('Global flag to disable a course. When a course is decommissioned, it cannot be purchased');

            $table->dateTime('launched_at')
                  ->nullable()
                  ->comment('The date where the course was/will be launched');

            $table->boolean('enable_purchase_power_parity')
                  ->default(false);

            $table->string('vimeo_project_id')
                  ->nullable()
                  ->comment('folder id');

            $table->string('backblaze_bucket_name')
                  ->nullable()
                  ->comment('backblaze bucket id');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('variants', function (Blueprint $table) {
            $table->id();

            $table->uuid()
                  ->comment('The UUID used in webpages');

            $table->string('canonical')
                  ->unique()
                  ->comment('Unique canonical to get the variant by this value');

            $table->foreignId('course_id')
                  ->constrained()
                  ->required();

            $table->longText('description')
                  ->nullable()
                  ->comment('The variant description, to understand what it is');

            $table->string('lemonsqueezy_variant_id')
                  ->nullable();

            $table->decimal('lemonsqueezy_price_override', 10, 2)
                  ->nullable()
                  ->comment('In case we would like to override the variant lemonsqueezy default price');

            $table->boolean('is_default')
                  ->default(false)
                  ->comment('In case no variant is passed to the Eduka payments gateway, it will use the variant id from the default one here');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('Domain name, without HTTP preffix neither "www.". E.g.: "cnn.com"');

            $table->foreignId('course_id')
                  ->constrained()
                  ->nullable()
                  ->comment('The related course instance');

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

        Schema::create('series_variant', function (Blueprint $table) {
            $table->id();

            $table->foreignId('series_id')
                  ->constrained();

            $table->foreignId('variant_id')
                  ->constrained();

            $table->unsignedInteger('index')
                  ->default(1);

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

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained();

            $table->string('email');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->string('code');

            $table->longText('description');

            $table->integer('discount_amount');

            $table->integer('discount_percentage');

            $table->foreignId('course_id')
                  ->constrained();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id');
            $table->integer('variant_id');

            $table->longText('response_body')
                  ->nullable();
            // @todo break down everything from response body

            $table->string('remote_reference_order_id')
                  ->nullable()
                  ->comment('should not be nullable.the order id that was created on 3rd party payment provider. eg: lemon squeezy');

            $table->string('remote_reference_customer_id')
                  ->nullable()
                  ->comment('should not be nullable.the customer id that was created on 3rd party payment provider. eg: lemon squeezy');

            $table->string('remote_reference_order_attribute_id')
                  ->nullable()
                  ->comment('should not be nullable.the payload.data.attributes.id that was created on 3rd party payment provider. eg: lemon squeezy, 5688a31e-cf51-4fa8-8615-c52c54327e4e');

            $table->string('currency_id')
                  ->nullable();

            $table->string('remote_reference_payment_status')
                  ->nullable();

            $table->timestamp('refunded_at')
                  ->nullable()
                  ->comment('nullable means it was not refunded');

            $table->integer('tax')
                  ->default(0)
                  ->comment('in cents');

            $table->integer('discount_total')
                  ->default(0)
                  ->comment('in cents');

            $table->integer('subtotal')
                  ->default(0)
                  ->comment('in cents');

            $table->integer('total')
                  ->default(0)
                  ->comment('in cents');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('video_storages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')
                  ->constrained();

            $table->string('vimeo_id')
                  ->nullable()
                  ->comment('vimeo_ids are videos/1234');

            $table->string('backblaze_id')
                  ->nullable();

            $table->string('path_on_disk')
                  ->nullable();

            $table->timestamps();
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
