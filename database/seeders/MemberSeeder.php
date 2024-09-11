<?php

namespace Database\Seeders;

use Impiger\Base\Supports\BaseSeeder;
use Impiger\Member\Models\Member;
use Impiger\Member\Models\MemberActivityLog;
use Faker\Factory;

class MemberSeeder extends BaseSeeder
{
    public function run()
    {
        $faker = Factory::create();

        Member::truncate();
        MemberActivityLog::truncate();

        Member::create([
            'first_name'   => 'John',
            'last_name'    => 'Smith',
            'email'        => 'john.smith@impigertech.com',
            'password'     => bcrypt('12345678'),
            'dob'          => $faker->dateTime,
            'phone'        => $faker->phoneNumber,
            'description'  => $faker->realText(30),
            'confirmed_at' => now(),
        ]);

        for ($i = 0; $i < 10; $i++) {
            Member::create([
                'first_name'   => $faker->firstName,
                'last_name'    => $faker->lastName,
                'email'        => $faker->email,
                'password'     => bcrypt('12345678'),
                'dob'          => $faker->dateTime,
                'phone'        => $faker->phoneNumber,
                'description'  => $faker->realText(30),
                'confirmed_at' => now(),
            ]);
        }
    }
}
