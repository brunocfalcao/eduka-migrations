<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticsSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_addresses', function (Blueprint $table) {
            $table->id();

            $table->ipAddress('ip_address');

            $table->boolean('is_blacklisted')
                  ->default(false);

            $table->boolean('is_throttled')
                  ->default(false);

            $table->unsignedBigInteger('hits')
                  ->default(1);

            $table->timestamps();
        });

        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            $table->string('session_id')
                  ->nullable()
                  ->comment('Session id (php session id) from the visit source');

            $table->boolean('is_bot')
                  ->default(false);

            $table->foreignId('user_id')
                  ->nullable()
                  ->comment('Relatable user id, if existing');

            $table->string('path')
                  ->comment('The url path, not including the full url');

            $table->string('route_name')
                  ->nullable()
                  ->comment('Route name in case it exists');

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

            $table->string('referrer_utm_source')
                  ->nullable()
                  ->comment('A possible referrer utm_source querystring, e.g.: ?utm_source=xxx');

            $table->string('referrer_domain')
                  ->nullable()
                  ->comment('A possible referrer domain http header');

            $table->string('campaign')
                  ->nullable()
                  ->comment('If there is a querystring with the name ?cmpg=xxx then it is recorded here');

            $table->string('hash') // GDPR reasons. Identifies a visit source.
                  ->nullable()
                  ->comment('The visit hashable identity. GDPR, is encrypted');

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

        /*
         * Visit goals:
         * nth-time: A visitor that visited, more than once, the website.
         * purchase-click: A visitor that clicked on the purchase.
         * purchased-completed: A visitor that completed the purchase.
         * purchase-abandoned: A visitor that abandoned the purchase.
         * from-promotion: A visitor that refered from a promotional campaign.
         * from-referal: A visitor that refered from another named referal.
         * refunded: A visitor that requested a refund.
         *
         * The visitor gates are built on custom classes that will be loaded
         * by the GateTracing middleware. Each time there is a new routing
         * being hit, it will reload all the gate classes and trigger the
         * necessary database logging.
         **/
        Schema::create('goals', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('The goal name, compact description. E.g.: "first visit"');

            $table->string('description')
                  ->comment('If necessary it shows a more detailed goal description');

            $table->longText('attributes')
                  ->comment('Extra attribute for the saved goal')
                  ->nullable();

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
