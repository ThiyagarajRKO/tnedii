<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OnlineSeesionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributeData = [

            [
                'attribute' => 'online_sessions',
                'name' => ' Session I'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session II'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session III'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session IV'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session V'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session VI'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session VII'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session VIII'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session IX'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session X'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session XI'
            ],
            [
                'attribute' => 'online_sessions',
                'name' => ' Session XII'
            ]
        ];
        foreach ($attributeData as $attribute) {
            $attributeExist = DB::table('attribute_options')->where($attribute)->get()->first();
            if (empty($attributeExist)) {
                $slugName = $attribute['name']."_".$attribute['attribute'];
                $attribute['slug'] = Str::slug($slugName);
                DB::table('attribute_options')->insert($attribute);
            } else {
                DB::table('attribute_options')->where('id', $attributeExist->id )->update($attribute);
            }
        }

        /* update slug field */
        $attributeOptions = DB::table('attribute_options')->get();
        if(!empty($attributeOptions)){
            foreach($attributeOptions as $attributeOption){
                if(!$attributeOption->slug){
                    $slugName = $attributeOption->name."_".$attributeOption->attribute;
                    $slug = Str::slug($slugName);
                    DB::table('attribute_options')->where('id',$attributeOption->id)
                            ->update(['slug' => $slug]);
                }
            }
        }
    }
}
