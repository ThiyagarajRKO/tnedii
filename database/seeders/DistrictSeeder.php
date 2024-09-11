<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $districts = [
            [
                'name' => 'Ariyalur',
                'code' => 'ARI',
                'region' => 'Thiruchirappalli'
            ],
            [
                'name' => 'Chengalpattu',
                'code' => 'CGL',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Chennai',
                'code' => 'CHN',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Coimbatore',
                'code' => 'CBE',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Cuddalore',
                'code' => 'CUD',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Dharmapuri',
                'code' => 'DPI',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Dindigul',
                'code' => 'DGL',
                'region' => 'Madurai'
            ],
            [
                'name' => 'Erode',
                'code' => 'ERD',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Kallakurichi',
                'code' => 'KKI',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Kancheepuram',
                'code' => 'KPM',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Kanniyakumari',
                'code' => 'KKM',
                'region' => 'Tirunelveli'
            ],
            [
                'name' => 'Karur',
                'code' => 'KAR',
                'region' => 'Thiruchirappalli'
            ],
            [
                'name' => 'Krishnagiri',
                'code' => 'KGI',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Madurai',
                'code' => 'MDU',
                'region' => 'Madurai'
            ],
            [
                'name' => 'Mayiladuthurai',
                'code' => 'MYL',
                'region' => 'Thiruchirappalli'
            ],
            [
                'name' => 'Nagapattinam',
                'code' => 'NGP',
                'region' => 'Thiruchirappalli'
            ],
            [
                'name' => 'Namakkal',
                'code' => 'NKL',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Nagercoil',
                'code' => 'NGL',
                'region' => 'Tirunelveli'
            ],
            [
                'name' => 'Perambalur',
                'code' => 'PMB',
                'region' => 'Thiruchirappalli'
            ],
            [
                'name' => 'Pudukkottai',
                'code' => 'PDK',
                'region' => 'Madurai'
            ],
            [
                'name' => 'Ramanathapuram',
                'code' => 'RMD',
                'region' => 'Madurai'
            ],
            [
                'name' => 'Ranipet',
                'code' => 'RNP',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Salem',
                'code' => 'SLM',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Sivagangai',
                'code' => 'SVG',
                'region' => 'Madurai'
            ],
            [
                'name' => 'Tenkasi',
                'code' => 'TKS',
                'region' => 'Tirunelveli'
            ],
            [
                'name' => 'Thanjavur',
                'code' => 'TNJ',
                'region' => 'Thiruchirappalli'
            ],
            [
                'name' => 'The Nilgiris',
                'code' => 'NLG',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Theni',
                'code' => 'THN',
                'region' => 'Madurai'
            ],
            [
                'name' => 'Thiruchirappalli',
                'code' => 'TRI',
                'region' => 'Thiruchirappalli'
            ],
            [
                'name' => 'Thiruvarur',
                'code' => 'TVR',
                'region' => 'Thiruchirappalli'
            ],
            [
                'name' => 'Thoothukudi',
                'code' => 'TUT',
                'region' => 'Tirunelveli'
            ],
            [
                'name' => 'Tirunelveli',
                'code' => 'TNV',
                'region' => 'Tirunelveli'
            ],
            [
                'name' => 'Tirupattur',
                'code' => 'TPT',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Tiruppur',
                'code' => 'TPR',
                'region' => 'Coimbatore'
            ],
            [
                'name' => 'Tiruvallur',
                'code' => 'TLR',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Tiruvannamalai',
                'code' => 'TVM',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Vellore',
                'code' => 'VEL',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Villupuram',
                'code' => 'VPM',
                'region' => 'Chennai'
            ],
            [
                'name' => 'Virudhunagar',
                'code' => 'VNR',
                'region' => 'Madurai'
            ],
        ];
         foreach ($districts as $district) {
            $rowExist = \DB::table('district')->where('name', $district['name'])->get()->first();
            $district['region_id'] = getIdfromValue('regions', ['name'=>$district['region']]);
            \Arr::forget($district,'region');
            if (empty($rowExist)) {
                $district['status'] = 1;
                $district['country_id'] = setting('default_country');
                $district['created_at'] = date('Y-m-d H:i:s');
                \DB::table('district')->insert($district);
            } else {
                $district['updated_at'] = date('Y-m-d H:i:s');
                \DB::table('district')->where('id', $rowExist->id )->update($district);
            }
        }
    }
}
