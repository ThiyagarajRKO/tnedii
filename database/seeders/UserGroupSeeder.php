<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Impiger\Usergroups\Models\Usergroups;
use Illuminate\Support\Str;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userGroups = [

        ];
        foreach($userGroups as $userGroup){
          $userGroupExist = Usergroups::where('name',$userGroup['name'])->first();
          if (empty($userGroupExist)) {
//                Usergroups::create($userGroup);
            }
        }
        /* update slug field */
        $data = Usergroups::all();
        if(!empty($data)){
            foreach($data as $row){
                if(!$row->slug){
                    $slugName = Str::slug($row->name);
                    Usergroups::where('id',$row->id)
                            ->update(['slug' => $slugName]);
                }
            }
        }

    }
}
