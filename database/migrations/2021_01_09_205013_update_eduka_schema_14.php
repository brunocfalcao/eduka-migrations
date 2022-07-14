<?php

use Eduka\Cube\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

class UpdateEdukaSchema14 extends Migration
{
    /**
     * This script:
     * 1. Uploads countries csv data.
     */
    public function up()
    {
        $assetsPath = str_replace('\\', '/', __DIR__).'/../assets/';

        // CSV countries.csv
        File::processCsv($assetsPath.'countries.csv', function ($line) {
            Country::create([
                'code' => $line[1],
                'name' => $line[2],
                'ppp_index' => $line[3],
            ]);
        });
    }

    public function down()
    {
        //
    }
}
