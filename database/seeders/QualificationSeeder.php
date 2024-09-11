<?php

namespace Database\Seeders;
use DB;
use Illuminate\Database\Seeder;

class QualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qualificationsData =[
            [                
                'name' => 'School Education',
                'department' => 'School Education',
                'is_enabled' => 1,
            ],
            [                
                'name' => 'Graduate Engineering',
                'department' => 'Graduate Engineering',
                'is_enabled' => 1,
            ],
            [                
                'name' => 'Graduate Arts',
                'department' => 'Graduate Arts',
                'is_enabled' => 1,
            ],
            [                
                'name' => 'Graduate Science',
                'department' => 'Graduate Science',
                'is_enabled' => 1,
            ],
            [                
                'name' => 'Diploma',
                'department' => 'Diploma',
                'is_enabled' => 1,
            ],
            [                
                'name' => 'Postgraduate',
                'department' => 'Postgraduate',
                'is_enabled' => 1,
            ],
            [                
                'name' => 'Doctorate',
                'department' => 'Doctorate',
                'is_enabled' => 1,
            ],
            [                
                'name' => 'ITI',
                'department' => 'ITI',
                'is_enabled' => 1,
            ],
        ];

        foreach ($qualificationsData as $qualification) {
            $qualificationExist = DB::table('qualifications')->where($qualification)->get()->first();
            if (empty($qualificationExist)) {
                DB::table('qualifications')->insert($qualification);
            } else {
                DB::table('qualifications')->where('id', $qualificationExist->id )->update($qualification);
            }
        }
    }
}
