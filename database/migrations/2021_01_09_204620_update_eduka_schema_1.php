<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('name')
              ->nullable()
              ->comment('The course title')
              ->after('id');

            $table->string('provider_namespace')
              ->nullable()
              ->comment('The Laravel service provider namespace class to be imported given the loaded URL')
              ->after('name');

            $table->string('url')
              ->nullable()
              ->comment('The main url, without https:// neither www. E.g.: "masteringnova.com"')
              ->after('provider_namespace');

            $table->string('postmark_token')
                  ->nullable()
                  ->comment('The postmark token id to send emails')
                  ->after('url');

            $table->string('admin_name')
                  ->nullable()
                  ->comment('The admin name, to operate the backoffice, horizon, etc')
                  ->after('postmark_token');

            $table->string('admin_email')
                  ->nullable()
                  ->comment('The admin email, to send emails, log in into Nova, etc')
                  ->after('admin_name');

            $table->string('admin_password')
                  ->nullable()
                  ->comment('The admin password, encrypted')
                  ->after('admin_email');

            $table->string('redis_queue_prefix')
                  ->nullable()
                  ->comment('The redis queue prefix, since the suffix is given by your environment name. If it is "local" it will be e.g. nova-advanced-ui-local')
                  ->after('admin_password');

            $table->dateTimeTz('launched_at')
                  ->default(null)
                  ->change();
        });

        Schema::create('domains', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                  ->comment('A possible domain name for a course');

            $table->foreignId('course_id')
                  ->nullable();
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
