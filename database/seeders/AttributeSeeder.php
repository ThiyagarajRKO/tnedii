<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
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
                'attribute' => 'industries',
                'name' => 'Accounting and Finance'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Marketing'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Sales'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Sustainability'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Technology and Internet'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Training'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Getting Started'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Human Resources'
            ],
            [
                'attribute' => 'industries',
                'name' => 'International operation'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Law and Legal'
            ],
            [
                'attribute' => 'industries',
                'name' => 'Management'
            ],
            [
                'attribute' => 'experiences',
                'name' => '0 - 5 year'
            ],
            [
                'attribute' => 'experiences',
                'name' => '5 - 10 years'
            ],
            [
                'attribute' => 'experiences',
                'name' => '10 - 20 years'
            ],
            [
                'attribute' => 'experiences',
                'name' => '20 years and above'
            ],
            [
                'attribute' => 'last_uses',
                'name' => 'Currently in the same sector'
            ],
            [
                'attribute' => 'last_uses',
                'name' => '1 - 4 years ago'
            ],
            [
                'attribute' => 'last_uses',
                'name' => '5 years ago'
            ],
            [
                'attribute' => 'categories',
                'name' => 'Entrepreneur / Business Sector'
            ],
            [
                'attribute' => 'categories',
                'name' => 'Bank / Financial Institution'
            ],
            [
                'attribute' => 'categories',
                'name' => 'Training Institution'
            ],
            [
                'attribute' => 'categories',
                'name' => 'Colleges'
            ],
            [
                'attribute' => 'categories',
                'name' => 'Govt. Department'
            ],
            [
                'attribute' => 'categories',
                'name' => 'MSME Associations'
            ],
            [
                'attribute' => 'categories',
                'name' => 'Consultant'
            ],
            [
                'attribute' => 'categories',
                'name' => 'Other'
            ],
            [
                'attribute' => 'type_of_college',
                'name' => 'Government'
            ],
            [
                'attribute' => 'type_of_college',
                'name' => 'Private'
            ],
            [
                'attribute' => 'type_of_college',
                'name' => 'Autonomous'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Anna University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Annamalai University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Bharathiar University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Bharathidasan University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Madurai Kamaraj University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Manonmaniam Sundaranar University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Periyar University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil Nadu Agricultural University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil Nadu Dr. Ambedkar Law University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil Nadu Fisheries University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil Nadu Horticulture University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil Nadu Open University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil Nadu Physical Education and Sports University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil Nadu Teachers Education University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil Nadu Veterinary and Animal Sciences University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'The Tamil Nadu Dr. M. G. R. Medical University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Tamil University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'Thiruvalluvar University'
            ],
            [
                'attribute' => 'university_types',
                'name' => 'University of Madras'
            ],
            [
                'attribute' => 'bank_categories',
                'name' => 'Mutual Fund'
            ],
            [
                'attribute' => 'bank_categories',
                'name' => 'Insurance'
            ],
            [
                'attribute' => 'bank_categories',
                'name' => 'Venture Capital'
            ],
            [
                'attribute' => 'bank_categories',
                'name' => 'Angel Funding'
            ],
            [
                'attribute' => 'bank_categories',
                'name' => 'Bank'
            ],
            [
                'attribute' => 'stream_of_institutions',
                'name' => 'University / Deemed University'
            ],
            [
                'attribute' => 'stream_of_institutions',
                'name' => 'Engineering College'
            ],
            [
                'attribute' => 'stream_of_institutions',
                'name' => 'Medical / Paramedical College'
            ],
            [
                'attribute' => 'stream_of_institutions',
                'name' => 'Arts & Science / Humanities College'
            ],
            [
                'attribute' => 'stream_of_institutions',
                'name' => 'Business School / Management College'
            ],
            [
                'attribute' => 'stream_of_institutions',
                'name' => 'Industrial Training Institute (ITI)'
            ],
            [
                'attribute' => 'stream_of_institutions',
                'name' => 'Polytechnic Institute/College'
            ],
            [
                'attribute' => 'stream_of_institutions',
                'name' => 'Others'
            ],
            [
                'attribute' => 'locality_type',
                'name' => 'Rural'
            ],
            [
                'attribute' => 'locality_type',
                'name' => 'Urban'
            ],
            [
                'attribute' => 'coeducation_type',
                'name' => 'Co-Educational'
            ],
            [
                'attribute' => 'coeducation_type',
                'name' => 'Men Only'
            ],
            [
                'attribute' => 'coeducation_type',
                'name' => 'Women Only'
            ],
            [
                'attribute' => 'program_level',
                'name' => 'Research & Doctoral (PhD)'
            ],
            [
                'attribute' => 'program_level',
                'name' => 'Post-Graduate (PG)'
            ],
            [
                'attribute' => 'program_level',
                'name' => 'Under-Graduate (UG)'
            ],
            [
                'attribute' => 'program_level',
                'name' => 'Diploma/Certificate/Others'
            ],
            [
                'attribute' => 'care_of',
                'name' => 'S/o'
            ],
            [
                'attribute' => 'care_of',
                'name' => 'D/o'
            ],
            [
                'attribute' => 'care_of',
                'name' => 'W/o'
            ],
            [
                'attribute' => 'gender',
                'name' => 'Male'
            ],
            [
                'attribute' => 'gender',
                'name' => 'Female'
            ],
            [
                'attribute' => 'gender',
                'name' => 'Transgender'
            ],
            [
                'attribute' => 'gender',
                'name' => 'A.Imran Ali'
            ],
            [
                'attribute' => 'religion',
                'name' => 'Hindu'
            ],
            [
                'attribute' => 'religion',
                'name' => 'Muslim'
            ],
            [
                'attribute' => 'religion',
                'name' => 'Christian'
            ],
            [
                'attribute' => 'religion',
                'name' => 'Buddhist'
            ],
            [
                'attribute' => 'religion',
                'name' => 'Jain'
            ],
            [
                'attribute' => 'religion',
                'name' => 'Other Religion'
            ],
            [
                'attribute' => 'community',
                'name' => 'BC'
            ],
            [
                'attribute' => 'community',
                'name' => 'MBC'
            ],
            [
                'attribute' => 'community',
                'name' => 'FC'
            ],
            [
                'attribute' => 'community',
                'name' => 'SC'
            ],
            [
                'attribute' => 'community',
                'name' => 'ST'
            ],
            [
                'attribute' => 'candidate_type',
                'name' => 'Student'
            ],
            [
                'attribute' => 'candidate_type',
                'name' => 'SpokeStudent'
            ],
            [
                'attribute' => 'candidate_type',
                'name' => 'Entrepreneur'
            ],
            [
                'attribute' => 'candidate_type',
                'name' => 'Startup'
            ],
            [
                'attribute' => 'candidate_type',
                'name' => 'Employed'
            ],
            [
                'attribute' => 'candidate_type',
                'name' => 'UnEmployed'
            ],
            [
                'attribute' => 'prefixes',
                'name' => 'Mr.'
            ],
            [
                'attribute' => 'prefixes',
                'name' => 'Mrs.'
            ],
            [
                'attribute' => 'prefixes',
                'name' => 'Miss.'
            ],
            [
                'attribute' => 'prefixes',
                'name' => 'Thiru.'
            ],
            [
                'attribute' => 'prefixes',
                'name' => 'Tmt.'
            ],
            [
                'attribute' => 'prefixes',
                'name' => 'Selvi.'
            ],
            [
                'attribute' => 'prefixes',
                'name' => 'Dr.'
            ],
            [
                'attribute' => 'prefixes',
                'name' => 'Prof.'
            ],
            [
                'attribute' => 'entrepreneurial_category',
                'name' => 'Manufacturial'
            ],
            [
                'attribute' => 'entrepreneurial_category',
                'name' => 'Services'
            ],
            [
                'attribute' => 'entrepreneurial_category',
                'name' => 'Trading'
            ],
            [
                'attribute' => 'student_type',
                'name' => 'School'
            ],
            [
                'attribute' => 'student_type',
                'name' => 'College'
            ],
            [
                'attribute' => 'course_year',
                'name' => 'First Year'
            ],
            [
                'attribute' => 'course_year',
                'name' => 'Second Year'
            ],
            [
                'attribute' => 'course_year',
                'name' => 'Third Year'
            ],
            [
                'attribute' => 'course_year',
                'name' => 'Fourth Year'
            ],
            [
                'attribute' => 'course_year',
                'name' => 'Fifth Year'
            ],
            [
                'attribute' => 'tnsi_ideas',
                'name' => 'Agriculture'
            ],
            [
                'attribute' => 'tnsi_ideas',
                'name' => 'Automobile'
            ],
            [
                'attribute' => 'tnsi_ideas',
                'name' => 'Educational Tech'
            ],
            [
                'attribute' => 'tnsi_ideas',
                'name' => 'Healthcare'
            ],
            [
                'attribute' => 'tnsi_ideas',
                'name' => 'Industry 4.0'
            ],
            [
                'attribute' => 'tnsi_ideas',
                'name' => 'FinTech'
            ],
            [
                'attribute' => 'tnsi_ideas',
                'name' => 'Tech based'
            ], [
                'attribute' => 'tnsi_sectors',
                'name' => 'Manufacturing'
            ],
            [
                'attribute' => 'tnsi_sectors',
                'name' => 'Service'
            ],
            [
                'attribute' => 'tnsi_sectors',
                'name' => 'Trade'
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
