<?php

namespace Impiger\Blog\Services;

use Impiger\Blog\Models\Post;
use Impiger\Blog\Services\Abstracts\StoreCategoryServiceAbstract;
use Illuminate\Http\Request;

class StoreCategoryService extends StoreCategoryServiceAbstract
{

    /**
     * @param Request $request
     * @param Post $post
     * @return mixed|void
     */
    public function execute(Request $request, Post $post)
    {
        $categories = $request->input('categories');
        if (!empty($categories) && is_array($categories)) {
            $post->categories()->sync($categories);
            if(in_array(getCategoryId(NEWSLETTER_CATEGORY_SLUG),$categories)){
                \App\Utils\CrudHelper::sendNewsletter($post);
            }
        }
    }
}
