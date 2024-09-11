<?php

namespace Database\Seeders;

use Impiger\Base\Models\MetaBox as MetaBoxModel;
use Impiger\Base\Supports\BaseSeeder;
use Impiger\Language\Models\LanguageMeta;
use Impiger\Page\Models\Page;
use Impiger\Slug\Models\Slug;
use Html;
use Illuminate\Support\Str;
use SlugHelper;

class FrontPageSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'en_US' => [
                [
                    'name'     => 'Institutions',
                    'content'  =>
                         Html::tag('div','[institution-form-sc][/institution-form-sc]')
                    ,
                    'template' => 'no-sidebar',
                ],
                
                [
                    'name'     => 'Form Response',
                    'content'  => 
                        Html::tag('div','[form-response][/form-response]'),
                    'template' => 'default',
                ],
            ],
            
        ];

        foreach ($data as $locale => $pages) {
            foreach ($pages as $index => $item) {
                $item['user_id'] = 1;
                $page = Page::create($item);

                Slug::create([
                    'reference_type' => Page::class,
                    'reference_id'   => $page->id,
                    'key'            => Str::slug($page->name),
                    'prefix'         => SlugHelper::getPrefix(Page::class),
                ]);

                $originValue = null;

                if ($locale !== 'en_US') {
                    $originValue = LanguageMeta::where([
                        'reference_id'   => $index + 1,
                        'reference_type' => Page::class,
                    ])->value('lang_meta_origin');
                }

                LanguageMeta::saveMetaData($page, $locale, $originValue);
            }
        }
    }
}
