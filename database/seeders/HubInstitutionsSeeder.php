<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HubInstitutionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $institutions = [
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'CED',
                'name'    => 'Centre for Entrepreneurship development, Anna University', 
                'city'  => 'Chennai',
                'district' => 'Chennai'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'AURC-CBE',
                'name'    => 'Anna University Regional Campus', 
                'city'  => 'Coimbatore',
                'district' => 'Coimbatore'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'AURC-TNV',
                'name'    => 'Anna University Regional Campus', 
                'city'  => 'Tirunelveli',
                'district' => 'Tirunelveli'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'AURC-TNV',
                'name'    => 'Anna University Regional Campus', 
                'city'  => 'Tirunelveli',
                'district' => 'Tirunelveli'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'SU',
                'name'    => 'Sastra University', 
                'city'  => 'Thanjavur',
                'district' => 'Thanjavur'
              ],
            [ 
                'hub_type_id' => 3,
                'hub_code'  => 'TCE',
                'name'    => 'Thiagarajar College of Engineering', 
                'city'  => 'Madurai',
                'district' => 'Madurai'
              ],
            [ 
                'hub_type_id' => 3,
                'hub_code'  => 'SCT',
                'name'    => 'Sona College of Technology', 
                'city'  => 'Salem',
                'district' => 'Salem'
              ],
            [ 
                'hub_type_id' => 2,
                'hub_code'  => 'UCE',
                'name'    => 'University College of Engineering', 
                'city'  => 'Villupuram',
                'district' => 'Villupuram'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'BU',
                'name'    => 'Bharathiar University', 
                'city'  => 'Coimbatore',
                'district' => 'Coimbatore'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'MIT',
                'name'    => 'Madras Institute of Technology (MIT)', 
                'city'  => 'Chennai',
                'district' => 'Chennai'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'UM',
                'name'    => 'University of Madras', 
                'city'  => 'Chennai',
                'district' => 'Chennai'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'UCE',
                'name'    => 'University College of Engineering', 
                'city'  => 'Nagercoil',
                'district' => 'Nagercoil'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'PU',
                'name'    => 'Periyar University', 
                'city'  => 'Salem',
                'district' => 'Salem'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'MKU',
                'name'    => 'Madurai Kamaraj University', 
                'city'  => 'Madurai',
                'district' => 'Madurai'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'BU',
                'name'    => 'Bharathithasan University', 
                'city'  => 'Thiruchirappalli',
                'district' => 'Thiruchirappalli'
              ],
            [ 
                'hub_type_id' => 3,
                'hub_code'  => 'UCE-BIT',
                'name'    => 'University College of Engineering, BIT Campus', 
                'city'  => 'Thiruchirappalli',
                'district' => 'Thiruchirappalli'
              ],
            [ 
                'hub_type_id' => 2,
                'hub_code'  => 'UCE',
                'name'    => 'University College of Engineering', 
                'city'  => 'Arani',
                'district' => 'Thiruchirappalli'
              ],
            [ 
                'hub_type_id' => 2,
                'hub_code'  => 'MSU',
                'name'    => 'Manonmaniam Sundaranar University', 
                'city'  => 'Tirunelveli',
                'district' => 'Tirunelveli'
              ],
            [ 
                'hub_type_id' => 2,
                'hub_code'  => 'MSU',
                'name'    => 'Mother Teresa University', 
                'city'  => 'Kodaikanal',
                'district' => 'Dindigul'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'AU',
                'name'    => 'Annamalai Unversity', 
                'city'  => 'Chidambaram',
                'district' => 'Chidambaram'
              ],
            [ 
                'hub_type_id' => 1,
                'hub_code'  => 'CPC',
                'name'    => 'Central Polytechnic College', 
                'city'  => 'Taramani',
                'district' => 'Chennai'
              ],
            [ 
                'hub_type_id' => 2,
                'hub_code'  => 'TPPC',
                'name'    => 'Thanthai Periyar  Polytechnic College', 
                'city'  => 'Vellore',
                'district' => 'Vellore'
              ],
            [ 
                'hub_type_id' => 2,
                'hub_code'  => 'GPC-CBE',
                'name'    => 'Government Polytechnic College', 
                'city'  => 'Coimbatore',
                'district' => 'Coimbatore'
              ],
            [ 
                'hub_type_id' => 2,
                'hub_code'  => 'GPC-TRI',
                'name'    => 'Government Polytechnic College', 
                'city'  => 'Thiruchirappalli',
                'district' => 'Thiruchirappalli'
              ],
            [ 
                'hub_type_id' => 2,
                'hub_code'  => 'GPC-TUT',
                'name'    => 'Government Polytechnic College', 
                'city'  => 'Thoothukudi',
                'district' => 'Thoothukudi'
              ],
            
        ];
        foreach ($institutions as $institution) {
            $institution['district'] = getIdfromValue('district', ['name'=>$institution['district']]);
            $rowExist = \DB::table('hub_institutions')->where('name', $institution['name'])->get()->first();
            if (empty($rowExist)) {                
                $institution['created_at'] = date('Y-m-d H:i:s');
                \DB::table('hub_institutions')->insert($institution);
            } else {
                $institution['updated_at'] = date('Y-m-d H:i:s');
                \DB::table('hub_institutions')->where('id', $rowExist->id )->update($institution);
            }
        }
    }
}
