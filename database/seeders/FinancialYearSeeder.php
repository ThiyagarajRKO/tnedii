<?php

namespace Database\Seeders;
use DB;
use Illuminate\Database\Seeder;

class FinancialYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //financial_year
        $yearData =[
            [
                'session_year' => 'Apr-2020 to Mar-2021',
                'session_start' => 'Apr-2020',
                'session_end' => 'Mar-2021',
                'description' => 'Apr-2020 to Mar-2021',
                'is_running' => 0,
                'is_enabled' => 0
            ],
            [
                'session_year' => 'Apr-2021 to Mar-2022',
                'session_start' => 'Apr-2021',
                'session_end' => 'Mar-2022',
                'description' => 'Apr-2021 to Mar-2022',
                'is_running' => 0,
                'is_enabled' => 0
            ],
            [
                'session_year' => 'Apr-2022 to Mar-2023',
                'session_start' => 'Apr-2022',
                'session_end' => 'Mar-2023',
                'description' => 'Apr-2022 to Mar-2023',
                'is_running' => 1,
                'is_enabled' => 1
            ],
            [
                'session_year' => 'Apr-2023 to Mar-2024',
                'session_start' => 'Apr-2023',
                'session_end' => 'Mar-2024',
                'description' => 'Apr-2023 to Mar-2024',
                'is_running' => 0,
                'is_enabled' => 0
            ],
            [
                'session_year' => 'Apr-2024 to Mar-2025',
                'session_start' => 'Apr-2024',
                'session_end' => 'Mar-2025',
                'description' => 'Apr-2024 to Mar-2025',
                'is_running' => 0,
                'is_enabled' => 0
            ],
            [
                'session_year' => 'Apr-2025 to Mar-2026',
                'session_start' => 'Apr-2025',
                'session_end' => 'Mar-2026',
                'description' => 'Apr-2025 to Mar-2026',
                'is_running' => 0,
                'is_enabled' => 0
            ],
            
        ];

        foreach ($yearData as $year) {
            $yearExist = DB::table('financial_year')->where($year)->get()->first();
            if (empty($yearExist)) {
                DB::table('financial_year')->insert($year);
            } else {
                DB::table('financial_year')->where('id', $yearExist->id )->update($year);
            }
        }
    }
}
