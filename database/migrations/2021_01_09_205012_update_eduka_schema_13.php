<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema13 extends Migration
{
    /**
     * This script:
     * 1. Adds the courses.templates json column. So we can configure the
     * view path for the email templates.
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->longText('templates')
                  ->after('logo_filename')
                  ->nullable();
        });
    }

    public function down()
    {
        //
    }
}
