<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            [
                "name" => "Chennai",
                "code" => "CHN"
            ],
            [
                "name" => "Coimbatore",
                "code" => "CBE"
            ],
            [
                "name" => "Madurai",
                "code" => "MDU"
            ],
            [
                "name" => "Tirunelveli",
                "code" => "TNV"
            ],
            [
                "name" => "Thiruchirappalli",
                "code" => "TRI"
            ],
        ];
        foreach ($regions as $region) {
            $regionExist = DB::table('regions')->where($region)->get()->first();
            if (empty($regionExist)) {
                $region['created_at'] = date('Y-m-d H:i:s');
                DB::table('regions')->insert($region);
            } else {
                $region['updated_at'] = date('Y-m-d H:i:s');
                DB::table('regions')->where('id', $regionExist->id )->update($region);
            }
        }
    }
}
