<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProficiencyLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $attributeData =[

            [
                'attribute' => 'proficiency_level',
                'name' => 'Beginner'
            ],
            [
                'attribute' => 'proficiency_level',
                'name' => 'Intermidiate'
            ],
            [
                'attribute' => 'proficiency_level',
                'name' => 'Expert'
            ],
            [
                'attribute' => 'proficiency_level',
                'name' => 'Professional'
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
