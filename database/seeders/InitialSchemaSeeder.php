<?php

namespace Eduka\Database\Seeders;

use Eduka\Cube\Models\Student;
use Illuminate\Database\Seeder;

class InitialSchemaSeeder extends Seeder
{
    public function run()
    {
        // Create a super admin user, if it exists on .ENV.
        if (env('EDUKA_SUPER_ADMIN_NAME') &&
           env('EDUKA_SUPER_ADMIN_EMAIL') &&
           env('EDUKA_SUPER_ADMIN_PASSWORD')) {
            Student::create([
                'name' => env('EDUKA_SUPER_ADMIN_NAME'),
                'email' => env('EDUKA_SUPER_ADMIN_EMAIL'),
                'password' => bcrypt(env('EDUKA_SUPER_ADMIN_PASSWORD')),
            ]);
        }
    }
}
