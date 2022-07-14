<?php

use Eduka\Cube\Models\Course;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema12 extends Migration
{
    /**
     * This script:
     * 1. Adds the courses.logo_filename column.
     *
     * The filename should be in PNG format, and as big as it can be,
     * because eduka will convert it to smaller resolutions to be used in
     * emails, social integrations, etc.
     *
     * These conversions are stored on:
     * storage/app/public/<course.uuidcode>/<image-type>
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {

            $table->string('uuid')
                  ->comment('The unique course uuid')
                  ->unique()
                  ->nullable()
                  ->after('id');

            $table->string('logo_filename')
                  ->comment('Course image, in the highest resolution and in PNG transparent background type')
                  ->nullable()
                  ->after('is_active');
        });
    }

    public function down()
    {
        //
    }
}
