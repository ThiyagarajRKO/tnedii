<?php

namespace Theme\Tvet\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Blog\Repositories\Interfaces\PostInterface;
use Impiger\Theme\Http\Controllers\PublicController;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Theme;
use Theme\Tvet\Http\Resources\PostResource;
use Session;
use Cookie;

class TvetController extends PublicController
{
    /**
     * {@inheritDoc}
     */
    public function getIndex()
    {
        $minutes = 15;
        if(isset($_GET['lang']) && $_GET['lang'] == "en")
        {
            Session::put('lang', "en");
            setcookie('googtrans', '/en/en');
            return redirect()->route('public.single');
        }
        else if(isset($_GET['lang']) && $_GET['lang'] == "ta")
        {
            Session::put('lang', "ta");
            setcookie('googtrans', '/en/ta');
            return redirect()->route('public.single');
        }
        else if(!Session::has('lang'))
        {
            Session::put('lang', "en");
            setcookie('googtrans', '/en/en');
        }
        return parent::getIndex();
    }

    /**
     * {@inheritDoc}
     */
    public function getView($key = null)
    {
        return parent::getView($key);
    }

    /**
     * {@inheritDoc}
     */
    public function getSiteMap()
    {
        return parent::getSiteMap();
    }

    /**
     * Search post
     *
     * @bodyParam q string required The search keyword.
     *
     * @group Blog
     *
     * @param Request $request
     * @param PostInterface $postRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     *
     * @throws FileNotFoundException
     */
    public function getSearch(Request $request, PostInterface $postRepository, BaseHttpResponse $response)
    {
        $query = $request->input('q');
        if (!empty($query)) {
            $posts = $postRepository->getSearch($query);

            $data = [
                'items' => Theme::partial('search', compact('posts')),
                'query' => $query,
                'count' => $posts->count(),
            ];

            if ($data['count'] > 0) {
                return $response->setData(apply_filters(BASE_FILTER_SET_DATA_SEARCH, $data, 10, 1));
            }
        }

        return $response
            ->setError()
            ->setMessage(__('No results found, please try with different keywords.'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param PostInterface $postRepository
     * @return BaseHttpResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function ajaxGetPosts(Request $request, BaseHttpResponse $response, PostInterface $postRepository)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $posts = $postRepository->getFeatured(3, ['slugable']);

        return $response
            ->setData(PostResource::collection($posts))
            ->toApiResponse();
    }
}
