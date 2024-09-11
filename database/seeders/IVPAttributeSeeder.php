<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IVPAttributeSeeder extends Seeder
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
                'attribute' => 'voucher_type',
                'name' => 'Voucher A'
            ],
            [
                'attribute' => 'voucher_type',
                'name' => 'Voucher B'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Education'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Health'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Water and Sanitation'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Government and Civil Society'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Peace and Security'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Transport and Storage'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Communication'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Energy Generation and Supply'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Banking and Financial Sevices'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Business related services'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Agriculture'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Forestry'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Fishing'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Industry'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Mineral Resources and Mining'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Construction'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Trade Policies & Regulations and Trade-related agreement'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Tourism'
            ],
            [
                'attribute' => 'sector',
                'name' => 'General Environmental Protection'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Food Science'
            ],
            [
                'attribute' => 'sector',
                'name' => 'Other'
            ],
            [
                'attribute' => 'msme_scheme',
                'name' => 'NEEDS'
            ],
            [
                'attribute' => 'msme_scheme',
                'name' => 'UYEGP'
            ],
            [
                'attribute' => 'msme_scheme',
                'name' => 'AABCS'
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
