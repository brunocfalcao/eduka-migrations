<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'redis_queue_prefix')) {
                $table->dropColumn('redis_queue_prefix');
            }

            if (Schema::hasColumn('courses', 'admin_name')) {
                $table->renameColumn('admin_name', 'from_name');
            }

            if (Schema::hasColumn('courses', 'admin_email')) {
                $table->renameColumn('admin_email', 'from_email');
            }

            if (! Schema::hasColumn('courses', 'config_name')) {
                $table->string('config_name')
                      ->comment('Your config filename. E.g. "nova-advanced-ui"')
                      ->nullable()
                      ->after('postmark_token');
            }
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
