<?php

namespace Eduka\Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Eduka\Cube\Models\Course;
use Eduka\Cube\Models\Domain;
use Illuminate\Database\Seeder;

class InitialSchemaSeeder extends Seeder
{
    public function run()
    {
        /**
         * Create the Mastering Nova course data. At the moment, this course
         * is created under the eduka framework because it's a course that
         * is already live. So, here lives the data migration logic, from the
         * current live course to the eduka course instance.
         */
        $course = Course::create([
            'name' => 'Mastering Nova',
            'admin_name' => 'Bruno',
            'admin_email' => 'bruno@masteringnova.com',
            'twitter_handle' => 'brunocfalcao',
            'provider_namespace' => 'MasteringNova\\MasteringNovaServiceProvider',
        ]);

        if (app()->environment() != 'production') {
            $domain = Domain::create([
                'suffix' => 'masteringnova.local',
                'course_id' => $course->id,
            ]);
        } else {
            $domain = Domain::create([
                'suffix' => 'nova-advanced-ui.com',
                'course_id' => $course->id,
            ]);
        }
    }
}
