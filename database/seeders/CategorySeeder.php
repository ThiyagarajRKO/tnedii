<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Impiger\Blog\Models\Category;
use Impiger\Slug\Models\Slug;
use Illuminate\Support\Str;
use SlugHelper;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                "name" => "Our Services",
                "is_system" => 1,
                'status' => "published"
            ],
            [
                "name" => "Default",
                "is_system" => 1,
                'status' => "published"
            ],
            [
                "name" => "News Letters",
                "is_system" => 1,
                'status' => "published"
            ],
            
        ];
        foreach ($categories as $row) {
            $categoryExist = DB::table('categories')->where($row)->get()->first();
            if (empty($categoryExist)) {
                $category = Category::create($row);
                Slug::create([
                    'reference_type' => Category::class,
                    'reference_id'   => $category->id,
                    'key'            => Str::slug($category->name),
                    'prefix'         => SlugHelper::getPrefix(Category::class),
                ]);
               
            } else {
                $category['updated_at'] = date('Y-m-d H:i:s');
                DB::table('categories')->where('id', $categoryExist->id )->update($category);
            }
        }
    }
}
