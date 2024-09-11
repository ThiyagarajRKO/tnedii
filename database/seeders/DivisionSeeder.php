<?php

namespace Database\Seeders;
use DB;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisionsData =[
            [
                'name' => 'EDP',
                'is_enabled' => 1
            ],
            [
                'name' => 'IEDP',
                'is_enabled' => 1
            ],
            [
                'name' => 'Innovation & Incubation',
                'is_enabled' => 1
            ],
            [
                'name' => 'ICT',
                'is_enabled' => 1
            ],
            [
                'name' => 'CDP',
                'is_enabled' => 1
            ],
            [
                'name' => 'Accounts',
                'is_enabled' => 1
            ],
            [
                'name' => 'Establishment',
                'is_enabled' => 1
            ],
            [
                'name' => 'Coordination',
                'is_enabled' => 1
            ]
        ];

        foreach ($divisionsData as $division) {
            $divisionExist = DB::table('divisions')->where($division)->get()->first();
            if (empty($divisionExist)) {
                DB::table('divisions')->insert($division);
            } else {
                DB::table('divisions')->where('id', $divisionExist->id )->update($division);
            }
        }
    }
}
