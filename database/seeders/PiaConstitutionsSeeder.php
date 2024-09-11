<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PiaConstitutionsSeeder extends Seeder
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
                'attribute' => 'pia_constitutions',
                'name' => ' Government'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => 'Non-Government'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => ' Quasi Government'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => ' Proprietor'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => ' Partnership'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => 'Private ltd'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => ' Public ltd'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => 'NGO / Trust'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => 'Association'
            ],
            [
                'attribute' => 'pia_constitutions',
                'name' => 'College / university'
            ],
            [
                'attribute' => 'pia_mainactivities',
                'name' => 'Training'
            ],
            [
                'attribute' => 'pia_mainactivities',
                'name' => 'Mentoring'
            ],
            [
                'attribute' => 'pia_mainactivities',
                'name' => 'Hands on service'
            ],
            [
                'attribute' => 'pia_mainactivities',
                'name' => 'Follow-up'
            ],
            [
                'attribute' => 'pia_mainactivities',
                'name' => 'Academic'
            ],
            [
                'attribute' => 'pia_mainactivities',
                'name' => 'Industry service'
            ],
            [
                'attribute' => 'pia_mainactivities',
                'name' => 'Above all'
            ],
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
