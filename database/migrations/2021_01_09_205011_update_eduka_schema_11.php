<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEdukaSchema11 extends Migration
{
    /**
     * This script:
     * 1. Removes the table domains.
     * 2. Changes the courses.url to courses.domain column name.
     *
     * 3. Removes the column courses.admin_password.
     * 4. Renames the column courses.from_name to courses.email_name.
     * 5. Renames the column courses.from_email to courses.email_address.
     *
     * 6. Adds a new column "is_admin" to the course_user table.
     * 7. Removes the is_admin from the users table.
     *
     * In resume the logic for this migration is to remove the user logic
     * from the courses table, and start having a relationship between
     * the users and the courses that are part of the admin profile.
     * Meaning we can have several users that are admins for the same course
     * or for different courses at the same time.
     */
    public function up()
    {
        Schema::dropIfExists('domains');
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('url', 'domain');
            $table->renameColumn('from_name', 'email_name');
            $table->renameColumn('from_email', 'email_address');
            $table->dropColumn('admin_password');
        });

        Schema::table('course_user', function (Blueprint $table) {
            $table->boolean('is_admin')
                  ->default(false)
                  ->after('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }

    public function down()
    {
        //
    }
}
